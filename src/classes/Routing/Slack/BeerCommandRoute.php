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
use EliasHaeussler\Api\Exception\MissingParameterException;
use EliasHaeussler\Api\Exception\PersistenceFailedException;
use EliasHaeussler\Api\Frontend\Message;
use EliasHaeussler\Api\Helpers\SlackMessage;
use EliasHaeussler\Api\Routing\BaseRoute;
use EliasHaeussler\Api\Utility\GeneralUtility;
use EliasHaeussler\Api\Utility\LocalizationUtility;

/**
 * Beer router for Slack API controller.
 *
 * This class defines the concrete router for the "beer" route inside the Slack API controller. It enables Slack
 * users to spend beer on their team members.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0+
 */
class BeerCommandRoute extends BaseRoute
{
    /** @var string API request parameter for showing the help */
    const REQUEST_PARAMETER_HELP = 'help';

    /** @var SlackController Slack API Controller */
    protected $controller;

    /** @var mixed Provided API request parameters */
    protected $requestParameters;

    /**
     * {@inheritdoc}
     *
     * @throws PersistenceFailedException
     * @throws MissingParameterException
     */
    public function processRequest()
    {
        // Show help text if request parameter starts with "help" keyword
        if (stripos($this->requestParameters, self::REQUEST_PARAMETER_HELP) === 0) {
            $this->showHelpText();

            return;
        }

        // Get donor and receiver
        $donorId = $this->controller->getRequestData('user_id');
        list($receiverId) = $this->controller->getUserDataFromString($this->requestParameters);

        // Show error message if no receiver was provided
        if (empty($receiverId)) {
            throw new MissingParameterException(LocalizationUtility::localize('exception.1556723086', 'slack'), 1556723086);
        }

        // Show message if donor equals receiver
        if ($donorId == $receiverId) {
            echo $this->controller->buildBotMessage(
                Message::MESSAGE_TYPE_NOTICE,
                LocalizationUtility::localize('beer.donorEqualsReceiver', 'slack', '', SlackMessage::emoji('point_up::skin-tone-3'))
            );

            return;
        }

        // Add beer to database
        $queryBuilder = $this->controller->getDatabase()->createQueryBuilder();
        $result = $queryBuilder->insert('slack_spent_beers')
            ->values([
                'donor' => $queryBuilder->createNamedParameter($donorId),
                'receiver' => $queryBuilder->createNamedParameter($receiverId),
            ])
            ->execute();

        if ($result) {
            $numberOfBeers = $this->getNumberOfBeersForUser($receiverId);

            // Show success message
            if ($numberOfBeers == 1) {
                echo $this->controller->buildBotMessage(
                    Message::MESSAGE_TYPE_SUCCESS,
                    LocalizationUtility::localize(
                        'beer.success.single',
                        'slack',
                        '',
                        SlackMessage::emoji('beer'),
                        SlackMessage::mention($receiverId)
                    ),
                    [],
                    true
                );
            } else {
                echo $this->controller->buildBotMessage(
                    Message::MESSAGE_TYPE_SUCCESS,
                    LocalizationUtility::localize(
                        'beer.success.multiple',
                        'slack',
                        '',
                        SlackMessage::emoji('beers'),
                        SlackMessage::mention($receiverId),
                        $numberOfBeers
                    ),
                    [],
                    true
                );
            }
        } else {
            // Show error message if persistence failed
            throw new PersistenceFailedException(
                LocalizationUtility::localize('exception.1556721904', 'slack'),
                1556721904
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function initializeRequest()
    {
        // Get user-preferred language in case user is already authenticated
        if ($this->controller->isUserAuthenticated()) {
            try {
                $this->controller->loadUserData();
                $this->controller->getUserInformation();
            } catch (\Exception $e) {
                // Intended fallthrough as language is not necessarily needed
            }
        }

        // Set provided request parameters
        $this->requestParameters = trim($this->controller->getRequestData('text'));
    }

    /**
     * Show help text for this command.
     */
    protected function showHelpText(): void
    {
        $numberOfBeers = $this->getNumberOfBeersForUser($this->controller->getRequestData('user_id'));
        $helpText = LocalizationUtility::localize(
            'beer.help.text',
            'slack',
            '',
            $numberOfBeers,
            LocalizationUtility::localize('beer.' . ($numberOfBeers == 1 ? 'singular' : 'plural'), 'slack')
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

    /**
     * Get number of beers a user has received.
     *
     * Returns the number of beers a user has received so far.
     *
     * @param string $userId ID of the user whose number of received beers should be returned
     *
     * @return int Number of beers the given user has received so far
     */
    protected function getNumberOfBeersForUser(string $userId): int
    {
        $queryBuilder = $this->controller->getDatabase()->createQueryBuilder();

        return $queryBuilder->select('receiver')
            ->from('slack_spent_beers')
            ->where($queryBuilder->expr()->eq('receiver', $queryBuilder->createNamedParameter($userId)))
            ->execute()
            ->rowCount();
    }
}
