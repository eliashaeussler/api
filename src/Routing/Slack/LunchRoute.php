<?php
/**
 * Copyright (c) 2018 Elias Häußler <mail@elias-haeussler.de>. All rights reserved.
 */
declare(strict_types=1);
namespace EliasHaeussler\Api\Routing\Slack;

use EliasHaeussler\Api\Controller\SlackController;
use EliasHaeussler\Api\Exception\ClassNotFoundException;
use EliasHaeussler\Api\Exception\InvalidRequestException;
use EliasHaeussler\Api\Frontend\Message;
use EliasHaeussler\Api\Routing\BaseRoute;

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
 * @package EliasHaeussler\Api\Routing\Slack
 * @author Elias Häußler <mail@elias-haeussler.de>
 * @license MIT
 */
class LunchRoute extends BaseRoute
{
    /** @var array List of available emojis to be set in request */
    const EMOJI_LIST = [
        ":pancakes:",
        ":cut_of_meat:",
        ":hamburger:",
        ":pizza:",
        ":stuffed_flatbread:",
        ":shallow_pan_of_food:",
        ":stew:",
        ":green_salad:",
        ":curry:",
        ":ramen:",
        ":spaghetti:",
    ];

    /** @var string Status message to be set in request */
    const STATUS_MESSAGE = "I'm having lunch!";

    /** @var int Default status expiration in minutes */
    const DEFAULT_EXPIRATION = 45;

    /** @var SlackController Slack API Controller */
    protected $controller;

    /** @var bool Defines whether the status has already been set */
    protected $statusAlreadySet = false;

    /** @var string Selected emoji for status */
    protected $emoji = ":pizza:";

    /** @var int Timestamp of status expiration */
    protected $expiration;


    /**
     * @inheritdoc
     * @throws InvalidRequestException if API request failed or contains an invalid answer
     * @throws \Exception if calculating the status expiration failed
     */
    protected function initializeRequest()
    {
        // Check whether to set or reset current status
        $this->statusAlreadySet = $this->checkIfStatusIsSet();

        // Status update
        $this->emoji = self::EMOJI_LIST[array_rand(self::EMOJI_LIST)];
        $this->requestData = [
            "profile" => [
                "status_text" => $this->statusAlreadySet ? "" : self::STATUS_MESSAGE,
                "status_emoji" => $this->statusAlreadySet ? "" : $this->emoji,
                "status_expiration" => $this->statusAlreadySet ? "" : $this->calculateExpiration(),
            ],
        ];
    }

    /**
     * @inheritdoc
     * @throws InvalidRequestException if API request failed or contains an invalid answer
     * @throws ClassNotFoundException if the `Message` class is not available
     * @throws \Exception if setting the status expiration failed
     */
    public function processRequest()
    {
        // Send API call
        $result = $this->controller->api("users.profile.set", $this->requestData);
        $this->controller->checkApiResult($result);

        // Show success message
        if ($this->statusAlreadySet) {
            $message = ":rocket: Welcome back to work!";
        } else {
            $expiration = new \DateTime();
            $expiration->setTimestamp($this->expiration);
            $message = sprintf(
                "%s Your lunch break will expire at %s. Bon appétit!",
                $this->emoji,
                $expiration->format("H:i")
            );
        }

        echo $this->controller->buildMessage($message, Message::MESSAGE_TYPE_SUCCESS);
    }

    /**
     * Check whether the status has already been set and is still active.
     *
     * @return bool `true` if the status has already been set and is still active, `false` otherwise
     * @throws InvalidRequestException if API request failed or contains an invalid answer
     */
    protected function checkIfStatusIsSet(): bool
    {
        // Set API parameters
        $data = [
            "token" => $this->controller->getAuthToken(),
            "user" => $this->controller->getRequestData("user_id"),
        ];

        // Send API call
        $result = $this->controller->api("users.profile.get", $data, false);

        $this->controller->checkApiResult($result);
        $result = json_decode($result, true);

        return $result["profile"]["status_text"] == self::STATUS_MESSAGE;
    }

    /**
     * Calculate status expiration.
     *
     * Calculates the status expiration by considering multiple data:
     *
     * 1. Slack parameter
     * 2. Default expiration
     *
     * Note that expiration needs to be provided in minutes.
     *
     * @return int Calculated exiration in minutes
     * @throws \Exception if the expiration cannot be calculated
     */
    protected function calculateExpiration(): int
    {
        $now = new \DateTime();
        $expiration = (int) $this->controller->getRequestData("text") ?: self::DEFAULT_EXPIRATION;
        $this->expiration = $now->getTimestamp() + $expiration * 60;

        return $this->expiration;
    }
}
