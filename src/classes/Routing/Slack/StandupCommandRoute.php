<?php
/**
 * Copyright (c) 2019 Elias Häußler <mail@elias-haeussler.de>. All rights reserved.
 */
declare(strict_types=1);
namespace EliasHaeussler\Api\Routing\Slack;

use EliasHaeussler\Api\Controller\SlackController;
use EliasHaeussler\Api\Frontend\Message;
use EliasHaeussler\Api\Helpers\SlackMessage;
use EliasHaeussler\Api\Routing\BaseRoute;
use EliasHaeussler\Api\Utility\GeneralUtility;
use EliasHaeussler\Api\Utility\LocalizationUtility;

/**
 * Standup router for Slack API controller.
 *
 * This class defines the concrete router for the "standup" route inside the Slack API controller. It enables Slack
 * users to notify other channel users about the user being ready for stand-up. It also sends a link to the platform
 * serving the stand-up call in case the appropriate environment variable is set.
 *
 * @author Elias Häußler <mail@elias-haeussler.de>
 * @license MIT
 */
class StandupCommandRoute extends BaseRoute
{
    /** @var SlackController Slack API Controller */
    protected $controller;

    /** @var string Uri for the standup taking place */
    protected $standupUri = '';

    /**
     * {@inheritdoc}
     */
    public function processRequest()
    {
        if ($this->standupUri) {
            $attachments = [
                [
                    'fallback' => 'Join the call at ' . $this->standupUri,
                    'actions' => [
                        [
                            'type' => 'button',
                            'text' => LocalizationUtility::localize(
                                'standup.message.button',
                                'slack',
                                'Join the call',
                                SlackMessage::emoji('telephone_receiver')
                            ),
                            'url' => $this->standupUri,
                            'style' => 'primary',
                        ],
                    ],
                ],
            ];
        } else {
            $attachments = [];
        }

        echo $this->controller->buildBotMessage(
            Message::MESSAGE_TYPE_NOTICE,
            LocalizationUtility::localize(
                'standup.message',
                'slack',
                '%s %s is ready for stand-up!',
                SlackMessage::mention('channel'),
                SlackMessage::mention($this->controller->getRequestData('user_id')),
                SlackMessage::bold(LocalizationUtility::localize('standup.message.highlight', 'slack'))
            ),
            $attachments,
            true
        );
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

        // Get standup URI
        $this->standupUri = GeneralUtility::getEnvironmentVariable('SLACK_STANDUP_URI');
    }
}
