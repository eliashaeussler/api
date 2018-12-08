<?php
/**
 * Copyright (c) 2018 Elias Häußler <mail@elias-haeussler.de>. All rights reserved.
 */

namespace EliasHaeussler\Api\Controller;

use EliasHaeussler\Api\Exception\AuthenticationException;

/**
 * @todo documentation needed
 *
 * @package EliasHaeussler\Api\Controller
 * @author Elias Häußler <mail@elias-haeussler.de>
 */
class SlackController extends BaseController
{
    /** @var string Base API url of Slack */
    const API_URL = "https://slack.com/api/";

    /** @var string Signing secret from Slack App, used for authentication */
    const SIGNING_SECRET = "0d5b309cb4c07a5922ea8154186fcaf9";

    /**
     * @var string Slack authentication type
     */
    protected $authType;

    /**
     * @var string Slack authentication token
     */
    protected $authToken;


    /**
     * @todo add doc
     */
    protected function initializeRequest()
    {
        $this->signingSecret = getenv("SLACK_SIGNING_SECRET");
        $this->authType = getenv("SLACK_AUTH_TYPE");
        $this->authToken = getenv("SLACK_AUTH_TOKEN");
    }

    /**
     * @todo add doc
     *
     * @return mixed|void
     * @throws AuthenticationException
     * @throws \Exception
     */
    public function call()
    {
        $this->prepareCall();

        // Set data to be sent during call
        $data = $this->prepareDataForCall();

        // Configure Slack API call
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL => self::API_URL . "users.profile.set",
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => [
                "Content-Type: " . "application/json; charset=utf-8",
                "Authorization: " . sprintf("%s %s", $this->authType, $this->authToken),
                "Content-Length: " . strlen($data),
            ],
        ]);

        // Send API call and store result
        $result = curl_exec($ch);
        curl_close($ch);

        // @todo continue with result
    }

    /**
     * @todo add doc
     *
     * @throws AuthenticationException
     * @throws \Exception
     */
    protected function prepareCall()
    {
        // Get timestamp and signature from request
        $requestTimestamp = $this->getRequestHeader("X-Slack-Request-Timestamp");
        $requestSignature = $this->getRequestHeader("X-Slack-Signature");

        if (!$this->isRequestVerified($requestTimestamp, $requestSignature)) {
            throw new AuthenticationException(
                "Authentication failed. Please contact your Slack admin.",
                1543541836
            );
        }
    }

    /**
     * @todo add doc
     *
     * @return string
     */
    protected function prepareDataForCall(): string
    {
        // List of available emojis for status
        $emojis = [
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

        // @todo check whether to set or REset the status

        // Status update
        $data = [
            "profile" => [
                "status_text" => "I'm having lunch!",
                "status_emoji" => $emojis[array_rand($emojis)],
            ],
        ];

        return json_encode($data);
    }

    /**
     * @todo add doc
     *
     * @param string $timestamp
     * @param string $signature
     * @return bool
     * @throws \Exception
     * @see https://api.slack.com/docs/verifying-requests-from-slack
     */
    protected function isRequestVerified(string $timestamp, string $signature): bool
    {
        // Check if request is older than 5 minutes
        $validInterval = 5;
        $currentTime = new \DateTime();
        $requestTime = (new \DateTime())->setTimestamp((int) $timestamp);
        $interval = $requestTime->diff($currentTime);
        if ($interval->format('%i') >= $validInterval) return false;

        // Test if request is authenticated
        $apiVersionNumber = "v0";
        $baseString = implode(":", [$apiVersionNumber, $timestamp, $this->requestBody]);
        $hashString = hash_hmac("sha256", $baseString, $this->signingSecret);
        $calculatedSignature = sprintf("%s=%s", $apiVersionNumber, $hashString);
        if ($calculatedSignature != $signature) return false;

        return true;
    }
}
