<?php
/**
 * Copyright (c) 2019 Elias Häußler <elias@haeussler.dev>. All rights reserved.
 */
declare(strict_types=1);
namespace EliasHaeussler\Api\Routing\Slack;

/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 */

use EliasHaeussler\Api\Controller\SlackController;
use EliasHaeussler\Api\Exception\AuthenticationException;
use EliasHaeussler\Api\Exception\ClassNotFoundException;
use EliasHaeussler\Api\Exception\DatabaseException;
use EliasHaeussler\Api\Exception\InvalidRequestException;
use EliasHaeussler\Api\Frontend\Message;
use EliasHaeussler\Api\Helpers\SlackMessage;
use EliasHaeussler\Api\Routing\BaseRoute;
use EliasHaeussler\Api\Service\LogService;
use EliasHaeussler\Api\Service\SchedulerService;
use EliasHaeussler\Api\Task\Slack\StatusUpdateTask;
use EliasHaeussler\Api\Utility\GeneralUtility;
use EliasHaeussler\Api\Utility\LocalizationUtility;

/**
 * Lunch router for Slack API controller.
 *
 * This class defines the concrete router for the "lunch" route inside the Slack API controller. It enables Slack users
 * to set or unset their status message with a notice to the current working state. If a user starts his lunch break,
 * he can used the Slash Command `/lunch` with optional parameter `duration` (in minutes) to update his Slack status.
 * The Slack API will then call this API which routes the request to this class which will call the Slack API back with
 * a specific API call. Disabling the status is possible by sending the `/lunch` command again, but it will expire by
 * default after either the given duration or a default time.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0+
 */
class LunchCommandRoute extends BaseRoute
{
    /** @var array List of available emojis to be set in request */
    const EMOJI_LIST = [
        'pancakes',
        'cut_of_meat',
        'hamburger',
        'pizza',
        'stuffed_flatbread',
        'shallow_pan_of_food',
        'stew',
        'green_salad',
        'curry',
        'ramen',
        'spaghetti',
    ];

    /** @var string Status message to be set in request */
    const STATUS_MESSAGE = "I'm having lunch!";

    /** @var int Default status expiration in minutes */
    const DEFAULT_EXPIRATION = 45;

    /** @var string API request parameter for showing the help */
    const REQUEST_PARAMETER_HELP = 'help';

    /** @var string API request parameter for setting the default expiration time */
    const REQUEST_PARAMETER_EXPIRE = 'default';

    /** @var SlackController Slack API Controller */
    protected $controller;

    /** @var mixed Provided API request parameters */
    protected $requestParameters;

    /** @var bool Defines whether the status has already been set */
    protected $statusAlreadySet = false;

    /** @var string Selected emoji for status */
    protected $emoji = 'pizza';

    /** @var int Timestamp of status expiration */
    protected $expiration = 0;

    /** @var int Expiration period */
    protected $expirationPeriod = self::DEFAULT_EXPIRATION;

    /** @var array API result data for the current user */
    protected $userInformation = [];

    /**
     * {@inheritdoc}
     *
     * @throws InvalidRequestException if API request failed or contains an invalid answer
     * @throws ClassNotFoundException  if the {@see Message} class is not available
     * @throws AuthenticationException if the user needs to re-authenticate himself
     * @throws \Exception              if setting the status expiration failed
     */
    public function processRequest()
    {
        // Show help text if request parameter starts with "help" keyword
        if (stripos($this->requestParameters, self::REQUEST_PARAMETER_HELP) === 0) {
            $this->showHelpText();

            return;
        }

        // Set default expiration if request parameter starts with "expire" keyword
        if (stripos($this->requestParameters, self::REQUEST_PARAMETER_EXPIRE) === 0) {
            $this->setDefaultExpirationTime();

            return;
        }

        // Send API call
        $result = $this->controller->api('users.profile.set', $this->requestData);
        $this->controller->checkApiResult($result);

        // Show success message
        if ($this->statusAlreadySet) {
            $message = LocalizationUtility::localize(
                'lunch.message.endBreak', 'slack', null,
                SlackMessage::emoji('rocket')
            );

            // Run scheduled tasks
            $tasks = SchedulerService::getScheduledTasks(StatusUpdateTask::class, null, 1, true);
            if ($tasks) {
                foreach ($tasks as $task) {
                    $result = SchedulerService::executeTask($task['task'], $task['arguments'], $task['scheduled_execution'], false);
                    SchedulerService::finalizeExecution($task['uid'], $result);
                }
            }
        } else {
            $expiration = new \DateTime();
            $expiration->setTimestamp($this->expiration);
            $message = LocalizationUtility::localize(
                'lunch.message.startBreak', 'slack', null,
                $this->emoji,
                $expiration->format('H:i')
            );

            // Schedule task to restore status text and emoji
            $statusText = $this->userInformation['user']['profile']['status_text'];
            $statusEmoji = $this->userInformation['user']['profile']['status_emoji'];

            if (!empty($statusText) || !empty($statusEmoji)) {
                SchedulerService::scheduleTask(
                    StatusUpdateTask::class,
                    $expiration,
                    [
                        'userId' => $this->controller->getRequestData('user_id'),
                        'statusText' => $statusText,
                        'statusEmoji' => $statusEmoji,
                    ]
                );
            }
        }

        echo $this->controller->buildMessage(Message::MESSAGE_TYPE_SUCCESS, $message);
    }

    /**
     * {@inheritdoc}
     *
     * @throws DatabaseException       if ensuring the availability of user data for the current user failed
     * @throws InvalidRequestException if API request failed or contains an invalid answer
     * @throws AuthenticationException if the user needs to re-authenticate himself
     * @throws \Exception              if calculating the status expiration failed
     */
    protected function initializeRequest()
    {
        // Ensure user data is available in database
        if (!$this->ensureUserDataIsAvailable()) {
            throw new DatabaseException(
                LocalizationUtility::localize('exception.1547920929', 'slack'),
                1547920929
            );
        }

        // Set provided request parameters
        $this->requestParameters = trim($this->controller->getRequestData('text'));

        // Check whether to set or reset current status
        $this->statusAlreadySet = $this->checkIfStatusIsSet();

        LogService::log(
            sprintf('Slack status for user "%s" is %s set',
                $this->controller->getRequestData('user_id'),
                $this->statusAlreadySet ? 'already' : 'not'
            ),
            LogService::DEBUG
        );

        // Calculate expiration time
        $this->calculateExpiration();

        // Status update
        $this->emoji = SlackMessage::emoji(self::EMOJI_LIST[array_rand(self::EMOJI_LIST)]);
        $this->requestData = [
            'profile' => [
                'status_text' => $this->statusAlreadySet ? '' : self::STATUS_MESSAGE,
                'status_emoji' => $this->statusAlreadySet ? '' : $this->emoji,
                'status_expiration' => $this->statusAlreadySet ? '' : $this->expiration,
            ],
        ];
    }

    /**
     * Check whether the status has already been set and is still active.
     *
     * @throws InvalidRequestException if API request failed or contains an invalid answer
     * @throws AuthenticationException if the user needs to re-authenticate himself
     *
     * @return bool `true` if the status has already been set and is still active, `false` otherwise
     */
    protected function checkIfStatusIsSet(): bool
    {
        $this->userInformation = $this->controller->getUserInformation();

        return $this->userInformation['user']['profile']['status_text'] == self::STATUS_MESSAGE;
    }

    /**
     * Calculate status expiration.
     *
     * Calculates the status expiration by considering multiple data:
     *
     * 1. Slack parameter
     * 2. Default user expiration (user setting)
     * 3. Default expiration
     *
     * Note that expiration needs to be provided in minutes.
     *
     * @throws \Exception if the expiration cannot be calculated
     *
     * @return int Calculated expiration in minutes
     */
    protected function calculateExpiration(): int
    {
        // Expiration time from request
        $expiration = (int) $this->requestParameters;

        // Select expiration time from user data in database if request time is not valid or set
        if (!$expiration) {
            $queryBuilder = $this->controller->getDatabase()->createQueryBuilder();
            $result = $queryBuilder->select('default_expiration')
                ->from('slack_userdata')
                ->where('user = :user_id')
                ->setParameter('user_id', $this->controller->getRequestData('user_id'))
                ->execute()
                ->fetch();

            if ($result && (int) $result['default_expiration']) {
                $expiration = (int) $result['default_expiration'];
            } else {
                // Use default expiration time if no expiration time is set in user data
                $expiration = self::DEFAULT_EXPIRATION;
            }
        }

        // Set expiration period
        $this->expirationPeriod = $expiration;

        // Calculate expiration time from current time on
        $interval = \DateInterval::createFromDateString(sprintf('%s min', $expiration));
        $this->expiration = (int) (new \DateTime())->add($interval)->format('U');

        return $this->expiration;
    }

    /**
     * @param int $time
     *
     * @throws InvalidRequestException if `$time` is not set and the request does not contain a valid expiration time
     */
    protected function setDefaultExpirationTime(int $time = 0)
    {
        // Check if expiration time is set or delivered within request
        if ($time == 0) {
            $parameterComponents = GeneralUtility::trimExplode(' ', $this->requestParameters);
            if (count($parameterComponents) > 1) {
                $time = (int) $parameterComponents[1] ?: self::DEFAULT_EXPIRATION;
            } else {
                throw new InvalidRequestException(
                    LocalizationUtility::localize('exception.1547919413', 'slack'),
                    1547919413
                );
            }
        }

        // Check if expiration time is at least one minute
        if ($time <= 0) {
            throw new \InvalidArgumentException(
                LocalizationUtility::localize('exception.1547919926', 'slack'),
                1547919926
            );
        }

        LogService::log(
            sprintf(
                'Setting default expiration time to "%s" for user "%s"',
                $time,
                $this->controller->getRequestData('user_id')
            ),
            LogService::DEBUG
        );

        // Update user data with default expiration time
        $queryBuilder = $this->controller->getDatabase()->createQueryBuilder();
        $result = $queryBuilder->update('slack_userdata')
            ->set('default_expiration', ':expiration')
            ->where('user = :user_id')
            ->setParameter('expiration', $time)
            ->setParameter('user_id', $this->controller->getRequestData('user_id'))
            ->execute();

        // Show message depending on result of database update
        if ($result) {
            $message = LocalizationUtility::localize(
                'lunch.default.success', 'slack', null,
                SlackMessage::emoji('alarm_clock'),
                SlackMessage::bold(
                    sprintf(
                        '%s %s',
                        $time,
                        LocalizationUtility::localize('time.min' . ($time == 1 ? '.one' : ''), 'slack')
                    )
                )
            );
            echo $this->controller->buildBotMessage(Message::MESSAGE_TYPE_SUCCESS, $message);
        } else {
            $message = LocalizationUtility::localize(
                'lunch.default.alreadySet', 'slack', null,
                SlackMessage::emoji('thinking_face'),
                SlackMessage::bold(
                    sprintf(
                        '%s %s',
                        $time,
                        LocalizationUtility::localize('time.min' . ($time == 1 ? '.one' : ''), 'slack')
                    )
                )
            );
            echo $this->controller->buildBotMessage(Message::MESSAGE_TYPE_NOTICE, $message);
        }
    }

    /**
     * Ensures that user data for the current user is available in the database.
     *
     * @return bool `true` if user data is available, `false` otherwise
     */
    protected function ensureUserDataIsAvailable(): bool
    {
        $queryBuilder = $this->controller->getDatabase()->createQueryBuilder();
        $result = $queryBuilder->select('*')
            ->from('slack_userdata')
            ->where('user = :user_id')
            ->setParameter('user_id', $this->controller->getRequestData('user_id'))
            ->execute()
            ->fetchAll();

        if (!$result) {
            $queryBuilder->resetQueryParts();
            $result = $queryBuilder->insert('slack_userdata')
                ->values(['user' => ':user_id'])
                ->execute();

            if ($result == 0) {
                return false;
            }
        }

        return true;
    }

    /**
     * Show help text for this command.
     */
    protected function showHelpText()
    {
        $helpText = LocalizationUtility::localize(
            'lunch.help.text',
            'slack',
            '',
            self::DEFAULT_EXPIRATION,
            $this->expirationPeriod,
            $this->expirationPeriod == 1 ? '' : 's'
        );
        $message = SlackMessage::convertPlaceholders($helpText);
        $attachments = [
            $this->controller->buildAttachmentForBotMessage(
                '',
                '',
                '',
                GeneralUtility::getServerName(),
                true
            ),
        ];

        echo $this->controller->buildBotMessage(Message::MESSAGE_TYPE_NOTICE, $message, $attachments);
    }
}
