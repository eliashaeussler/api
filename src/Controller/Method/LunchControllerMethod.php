<?php
/**
 * Copyright (c) 2018 Elias Häußler <mail@elias-haeussler.de>. All rights reserved.
 */
declare(strict_types=1);
namespace EliasHaeussler\Api\Controller\Method;

use EliasHaeussler\Api\Controller\SlackController;
use EliasHaeussler\Api\Page\Frontend;

/**
 * @todo add doc
 *
 * @package EliasHaeussler\Api\Controller\Slack
 * @author Elias Häußler <mail@elias-haeussler.de>
 */
class LunchControllerMethod
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

    /**
     * @var SlackController Controller
     */
    protected $controller;

    /**
     * @var array Data to be send in request
     */
    protected $requestData;

    /**
     * @var bool Defines whether the status to be set is already set
     */
    protected $statusAlreadySet = false;

    /**
     * @var string Selected emoji for status
     */
    protected $emoji = ":pizza:";

    /**
     * @var int Timestamp of status expiration
     */
    protected $expiration;


    /**
     * @todo add doc
     *
     * @param SlackController $controller
     */
    public function __construct(SlackController $controller)
    {
        $this->controller = $controller;
    }

    /**
     * @todo add doc
     *
     * @throws \EliasHaeussler\Api\Exception\InvalidRequestException
     * @throws \Exception
     */
    public function processRequest()
    {
        $this->initializeRequest();

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

        echo $this->controller->buildMessage($message, Frontend::MESSAGE_TYPE_SUCCESS);
    }

    /**
     * @todo add doc
     *
     * @throws \EliasHaeussler\Api\Exception\InvalidRequestException
     * @throws \Exception
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
     * @todo add doc
     *
     * @return bool
     * @throws \EliasHaeussler\Api\Exception\InvalidRequestException
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
     * @todo add doc
     *
     * @return int
     * @throws \Exception
     */
    protected function calculateExpiration(): int
    {
        $now = new \DateTime();
        $expiration = (int) $this->controller->getRequestData("text") ?: self::DEFAULT_EXPIRATION;
        $this->expiration = $now->getTimestamp() + $expiration * 60;

        return $this->expiration;
    }
}
