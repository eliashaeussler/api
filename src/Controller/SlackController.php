<?php
/**
 * Copyright (c) 2018 Elias Häußler <mail@elias-haeussler.de>. All rights reserved.
 */
declare(strict_types=1);
namespace EliasHaeussler\Api\Controller;

use Dotenv\Dotenv;
use Dotenv\Exception\InvalidPathException;
use EliasHaeussler\Api\Exception\AuthenticationException;
use EliasHaeussler\Api\Exception\ClassNotFoundException;
use EliasHaeussler\Api\Exception\InvalidRequestException;
use EliasHaeussler\Api\Frontend\Message;
use EliasHaeussler\Api\Routing\Slack\LunchRoute;
use EliasHaeussler\Api\Utility\GeneralUtility;
use EliasHaeussler\Api\Utility\RoutingUtility;

/**
 * Slack API controller.
 *
 * This API controller converts API requests to valid requests for the Slack API and processes them. Each available
 * route is mapped to an appropriate routing class which should be an instance of `BaseRoute` class.
 *
 * @package EliasHaeussler\Api\Controller
 * @author Elias Häußler <mail@elias-haeussler.de>
 * @license MIT
 */
class SlackController extends BaseController
{
    /** @var string Base API uri of Slack */
    const API_URI = "https://slack.com/api/";

    /** @var string Base authentication uri of Slack */
    const AUTHORIZE_URI = "https://slack.com/oauth/authorize";

    /** @var string File name pattern of user-based .env files */
    const ENV_FILENAME_PATTERN = "slack.env.%s";

    /** @var string Route for authentication */
    const ROUTE_AUTH = "authenticate";

    /** @var array Classes for each available route */
    const ROUTE_MAPPINGS = [
        "lunch" => LunchRoute::class,
    ];

    /** @var string Client ID of Slack App */
    protected $clientId;

    /** @var string Client secret of Slack App */
    protected $clientSecret;

    /** @var string Signing secret from Slack App, used for authentication */
    protected $signingSecret;

    /** @var string Slack authentication type */
    protected $authType;

    /** @var string Slack authentication token */
    protected $authToken;

    /** @var string Slack authentication state string */
    protected $authState;

    /** @var array Data from Slack API, passed in request */
    protected $requestData;


    /**
     * @inheritdoc
     * @throws AuthenticationException if provided authentication state is invalid
     * @throws InvalidRequestException if user authentication or API request failed
     * @throws ClassNotFoundException if the `Message` class is not available
     */
    protected function initializeRequest()
    {
        // Set base app credentials and authentication settings
        $this->clientId = GeneralUtility::getEnvironmentVariable("SLACK_CLIENT_ID");
        $this->clientSecret = GeneralUtility::getEnvironmentVariable("SLACK_CLIENT_SECRET");
        $this->signingSecret = GeneralUtility::getEnvironmentVariable("SLACK_SIGNING_SECRET");
        $this->authState = GeneralUtility::getEnvironmentVariable("SLACK_AUTH_STATE");

        // Store data from request body and user-specific environment variables
        $this->storeRequestData();
        $this->setAccessType();

        if ($this->matchesRoute(self::ROUTE_AUTH)) {
            $this->processUserAuthentication();
        } else if ($this->isRequestValid() && $this->isUserAuthenticated()) {
            $this->loadUserEnvironment();
        } else {
            $this->showUserAuthenticationUri();
        }

        // Set user-specific authentication settings
        $this->authType = GeneralUtility::getEnvironmentVariable("SLACK_AUTH_TYPE");
        $this->authToken = GeneralUtility::getEnvironmentVariable("SLACK_AUTH_TOKEN");
    }

    /**
     * @inheritdoc
     * @throws AuthenticationException if the authentication process failed
     * @throws ClassNotFoundException if the routing class is not available
     */
    public function call()
    {
        // Check if request is valid
        if (!$this->matchesRoute(self::ROUTE_AUTH)) {
            $this->prepareCall();
        }

        // Call concrete API route
        parent::call();
    }

    /**
     * Call the Slack API with given function and data.
     *
     * Calls a specific function of the Slack API with the data provided to this method. Data can be provided as string
     * or array. When providing data as string, make sure that it is already JSON-encoded if `$json` is set to `true`.
     * Array data will be encoded as JSON if `$json` is set to `true`. Authorization headers can be send if `$authorize`
     * is set to `true`.
     *
     * @param string $function Slack API function
     * @param string|array $data Additional data to be sent during API request
     * @param bool $json Define whether to use JSON or POST to send data
     * @param bool $authorize Define whether to send authorization headers
     * @return bool|string The API result on success or `false` on failure
     * @see https://api.slack.com/web#methods
     */
    public function api(string $function, $data, bool $json = true, bool $authorize = true)
    {
        // Convert input data to required format
        if ($json && is_array($data)) {
            $data = json_encode($data);
        }
        if (!$json && is_string($data)) {
            $data = json_decode($data, true);
        }

        // Configure Slack API call
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL => self::API_URI . $function,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $data,
        ]);

        // Build headers for API request
        if ($json || $authorize) {
            $this->addApiHeaders($ch, $data, $json, $authorize);
        }

        // Send API call and store result
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    /**
     * Add HTTP headers to an open API request.
     *
     * Adds authorization and content length headers to an open API request. This will only take effect if the
     * appropriate variable (namely `$json` and `$authorize`) is set to `true`.
     *
     * @param resource $ch Open API request as cURL resource
     * @param string|array $data Additional data to be sent during API request
     * @param bool $json Define whether to use JSON as content type
     * @param bool $authorize Define whether to send authorization headers
     * @internal Used in `SlackController::api` to build HTTP headers for API request
     */
    protected function addApiHeaders(&$ch, $data, bool $json = true, bool $authorize = true)
    {
        $httpHeader = $json
            ? ["Content-Type" => "application/json; charset=utf-8"]
            : [];

        if ($authorize) {
            $httpHeader["Authorization"] = $this->authType . " " . $this->authToken;

            if ($json) {
                $httpHeader["Content-Length"] = strlen($data);
            }
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, array_map(function ($key, $value) {
            return $key . ": " . $value;
        }, array_keys($httpHeader), $httpHeader));
    }

    /**
     * @inheritdoc
     */
    public function buildMessage(string $type, $arg1, ...$_): string
    {
        if (RoutingUtility::getAccess() != RoutingUtility::ACCESS_TYPE_BOT) {
            return parent::buildMessage($type, $arg1, ...$_);
        }

        // Set correct content header
        header("Content-Type: application/json");

        // Get message
        if ($type == Message::MESSAGE_TYPE_ERROR) {
            /** @var \Exception $message */
            $message = $arg1;
            $message = ":no_entry: " . $message->getMessage();
        } else {
            $message = $arg1;
        }

        return json_encode([
            "response_type" => "ephemeral",
            "text" => $message,
        ]);
    }

    /**
     * Build URI inside message to API result.
     *
     * Builds an URI inside a message to an API result. This means, the URI will be printed directly in Slack and needs
     * therefore converted to the necessary format. If `$text` is not set or empty, the URI will be used as text.
     *
     * @param string $uri The URI to be linked inside the message
     * @param string $text The URI text to be shown for the URI
     * @return string The generated ready-to-use URI for the appropriate message in Slack
     */
    public function buildMessageUri(string $uri, string $text = ""): string
    {
        return sprintf("<%s|%s>", $uri, trim($text) ? $text : $uri);
    }

    /**
     * Check if request is verified before processing API request.
     *
     * Checks whether the current request is verified by walking through the authentication process of Slack. This
     * method is called right before the API request is being processed and will stop the process from being started
     * if the request is not verified.
     *
     * THEREFORE, IT IS VERY IMPORTANT TO ALWAYS CALL THIS METHOD BEFORE PROCESSING ANY API REQUEST!
     *
     * @throws AuthenticationException if the current request could not be verified
     * @throws \Exception if the difference between request send and receive time could not be calculated
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
     * Store raw request data by converting it into an array of data.
     */
    protected function storeRequestData()
    {
        parse_str($this->requestBody, $this->requestData);
    }

    /**
     * Set access type of current API request.
     *
     * The access type is defined by the user agent of the current request. If the request was generated by Slack, the
     * user agent will be any type of "Slackbot".
     */
    protected function setAccessType()
    {
        $userAgent = $this->getRequestHeader("User-Agent");
        $accessType = strpos($userAgent, "Slackbot") !== false
            ? RoutingUtility::ACCESS_TYPE_BOT
            : RoutingUtility::ACCESS_TYPE_BROWSER;
        RoutingUtility::setAccess($accessType);
    }

    /**
     * Load user-specific environment variables.
     *
     * Overloads the current environment variables with the user-specific variables.
     */
    protected function loadUserEnvironment()
    {
        try {
            $envFile = sprintf(self::ENV_FILENAME_PATTERN, $this->requestData['user_id']);
            $loader = new Dotenv(ROOT_PATH, $envFile);
            $loader->overload();

        } catch (InvalidPathException $e) {
            // Do not handle exception as default environment will be used as fallback
        }
    }

    /**
     * Check whether the current request is valid.
     *
     * Checks whether the request data is set and has been initialized yet. If not, an exception will be thrown. If the
     * request data is already set but does not include a user id, the request is marked as invalid.
     *
     * @return bool `true` if the request is valid, `false` otherwise
     * @throws InvalidRequestException if the request data is not set or has not been initialized yet
     */
    protected function isRequestValid(): bool
    {
        if (!$this->requestData) {
            throw new InvalidRequestException(
                "The request is invalid. Please refer to the command constructions in Slack for the correct usage.",
                1545685035
            );
        }

        return isset($this->requestData['user_id']);
    }

    /**
     * Check whether the current user is already authenticated.
     *
     * Checks whether the current user is already authenticated by testing if the appropriate .env file exists.
     *
     * @return bool `true` if the user is already authenticated, `false` otherwise
     */
    protected function isUserAuthenticated(): bool
    {
        $fileName = ROOT_PATH . "/" . sprintf(self::ENV_FILENAME_PATTERN, $this->requestData['user_id']);

        return file_exists($fileName);
    }

    /**
     * Check whether the provided state is valid.
     *
     * Checks whether the request state, provided by GET parameter, equals the expected state.
     *
     * @return bool `true` if the requested state equals the expected state
     */
    protected function isValidAuthState(): bool
    {
        return $_GET['state'] == $this->authState;
    }

    /**
     * Check whether API request can be verified.
     *
     * Checks whether the current API request can be verified. This is done in multiple steps. At first, the time
     * difference between request and current time is checked. If the difference is larger than five minutes, the
     * request cannot be verified. In the second step, a signature will be calculated an compared with a delivered
     * signature. If both signatures do not match, the request cannot be verified as well.
     *
     * @param string $timestamp Timestamp of the API request, sent by Slack as HTTP header
     * @param string $signature Signature of the API request, sent by Slack as HTTP header
     * @return bool `true` if the request can be verified, `false` otherwise
     * @throws \Exception if the difference between request send and receive time could not be calculated
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
        $calculatedSignature = $apiVersionNumber . "=" . $hashString;
        if ($calculatedSignature != $signature) return false;

        return true;
    }

    /**
     * Show message for necessary user authentication.
     *
     * @throws ClassNotFoundException if the `Frontend` class is not available
     */
    protected function showUserAuthenticationUri()
    {
        $uri = $this->buildUserAuthenticationUri();
        echo $this->buildMessage(
            Message::MESSAGE_TYPE_WARNING,
            ":warning: Please " . $this->buildMessageUri($uri, "authenticate") . " first to use this command."
        );
    }

    /**
     * Build URI for necessary user authentication.
     *
     * @param array $scopes Necessary scopes to be used in authentication process
     * @return string URI for user authentication
     */
    protected function buildUserAuthenticationUri(array $scopes = ["users.profile:write", "users.profile:read"])
    {
        return self::AUTHORIZE_URI . "?" . http_build_query([
            "scope" => implode(",", $scopes),
            "client_id" => $this->clientId,
            "state" => $this->authState,
        ]);
    }

    /**
     * Process requested user authentication.
     *
     * Processes the requested user authentication. In general, this method uses a provided code together with the
     * app credentials to request an access token at the Slack API. This will then be stored locally and used for
     * further API requests to authenticate the user at the Slack API.
     *
     * @throws AuthenticationException if provided authentication state is invalid
     * @throws InvalidRequestException if API request failed
     * @throws ClassNotFoundException if the `Frontend` class is not available
     */
    protected function processUserAuthentication()
    {
        if (!$this->isValidAuthState()) {
            throw new AuthenticationException(
                "Authentication failed due to an invalid state provided. Please contact your Slack admin.",
                1545662028
            );
        }

        // Send API call
        $result = $this->api("oauth.access", [
            "client_id" => $this->clientId,
            "client_secret" => $this->clientSecret,
            "code" => $_GET['code'],
        ], false);

        $this->checkApiResult($result);
        $result = json_decode($result, true);

        // Save authentication credentials
        $fileName = ROOT_PATH . "/" . sprintf(self::ENV_FILENAME_PATTERN, $result["user_id"]);
        $mappings = [
            "access_token" => "SLACK_AUTH_TOKEN",
            "scope" => "SLACK_AUTH_SCOPE",
        ];
        $this->writeToFile($fileName, $result, $mappings);

        // Show success message
        echo $this->buildMessage(
            Message::MESSAGE_TYPE_SUCCESS,
            "Yay, the authentication was successful.",
            "Please re-send your command and everything should be fine."
        );
    }

    /**
     * Check if result from API request is valid.
     *
     * Checks if the provided, raw JSON-encoded result from an API request is valid and contains a valid answer.
     *
     * @param string $result Raw result from API request, parsed as JSON
     * @throws InvalidRequestException if API request failed or contains an invalid answer
     */
    public function checkApiResult(string $result)
    {
        if (!$result) {
            throw new InvalidRequestException(
                "Empty result due to an error during API request. Please contact your Slack admin.",
                1545669514
            );
        }

        $result = json_decode($result, true);

        // Check for valid result from Slack
        if (!$result["ok"]) {
            throw new InvalidRequestException(
                sprintf("Error during API request: \"%s\". Please contact your Slack admin.", $result['error']),
                1545669514
            );
        }
    }

    /**
     * Write user-specific environment variables to file.
     *
     * @param string $fileName File name of user-specific .env file
     * @param array $apiResult Result from API request
     * @param array $mappings Data mappings for contents of .env file in format `"resultKey" => "envVariable"`
     */
    protected function writeToFile(string $fileName, array $apiResult, array $mappings)
    {
        // Open file handler
        $handler = fopen($fileName, "w");

        // Set content
        $content = "";
        foreach ($mappings as $resKey => $envKey) {
            $content .= sprintf("%s=%s\r\n", $envKey, $apiResult[$resKey]);
        }

        // Write content to file and close handler
        fwrite($handler, $content);
        fclose($handler);
    }

    /**
     * Get Slack authentication token
     *
     * @return string Slack authentication token
     */
    public function getAuthToken(): string
    {
        return $this->authToken;
    }

    /**
     * Get data from API request.
     *
     * Returns data from API request, either a specific key, if provided, or all available data. This method returns
     * an empty string, if no request data is available for the given key.
     *
     * @param string $key Key of API request to return. Optional.
     * @return array|string API request data
     */
    public function getRequestData(string $key = "")
    {
        return $key ? ($this->requestData[$key] ?? "") : $this->requestData;
    }
}
