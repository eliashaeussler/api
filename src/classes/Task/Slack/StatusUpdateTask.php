<?php
/**
 * Copyright (c) 2019 Elias Häußler <elias@haeussler.dev>. All rights reserved.
 */
declare(strict_types=1);
namespace EliasHaeussler\Api\Task\Slack;

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
use EliasHaeussler\Api\Exception\InvalidRequestException;
use EliasHaeussler\Api\Exception\MissingParameterException;
use EliasHaeussler\Api\Service\LogService;
use EliasHaeussler\Api\Task\AbstractTask;
use EliasHaeussler\Api\Utility\LocalizationUtility;

/**
 * Slack status update scheduler task.
 *
 * This class provides a scheduler task to update the status of a Slack user. It can be used for example
 * to restore a specific status.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0+
 */
class StatusUpdateTask extends AbstractTask
{
    /** @var string Class name of the appropriate controller */
    protected $controllerClassName = SlackController::class;

    /** @var SlackController Controller to be used for executing tasks */
    protected $controller;

    /**
     * Set a status text and status emoji of a given Slack user.
     *
     * Sets the status text and status emoji of a given Slack user in case the user is already authenticated.
     * If not, this task cannot be used to update the status and the user needs to authenticate himself first.
     * If both status text and emoji are empty, the task won't change the status. This is also the case if
     * a status is currently set for the specified user.
     *
     * @param string      $userId      User id of the Slack user whose status should be updated
     * @param string|null $statusText  New status message for the given Slack user
     * @param string|null $statusEmoji New status emoji for the given Slack user
     *
     * @throws AuthenticationException   if the user is not authenticated or needs to re-authenticate himself
     * @throws InvalidRequestException   if API request failed or contains an invalid answer
     * @throws MissingParameterException if the `$userId` parameter is not set or empty
     *
     * @return bool `true` if the execution was successful, `false` otherwise
     */
    public function run(?string $userId = null, ?string $statusText = null, ?string $statusEmoji = null): bool
    {
        if (empty($userId)) {
            throw new MissingParameterException(
                LocalizationUtility::localize('exception.1555778525', 'slack', null, 'userId'),
                1555778525
            );
        }

        // Do not update status if status text and emoji are not set
        if (empty($statusText) && empty($statusEmoji)) {
            LogService::log(
                'Skipped update of status as both text and emoji are not defined.',
                LogService::NOTICE
            );

            return true;
        }

        // Set user id
        $this->controller->setRequestDataForKey('user_id', $userId);
        $this->controller->loadUserData();

        // Get current status
        $userData = $this->controller->getUserInformation();

        if (empty($userData['user']['profile']['status_text'])) {
            $result = $this->controller->api('users.profile.set', [
                'profile' => [
                    'status_text' => $statusText,
                    'status_emoji' => $statusEmoji,
                ],
            ]);
            $this->controller->checkApiResult($result);
        } else {
            LogService::log(
                'Skipped update of status as user has changed the status manually in the meanwhile.',
                LogService::NOTICE
            );
        }

        return true;
    }
}
