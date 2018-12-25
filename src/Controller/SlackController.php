<?php
/**
 * Copyright (c) 2018 Elias Häußler <mail@elias-haeussler.de>. All rights reserved.
 */
declare(strict_types=1);
namespace EliasHaeussler\Api\Controller;

use Dotenv\Dotenv;
use Dotenv\Exception\InvalidPathException;
use EliasHaeussler\Api\Method\BaseMethod;
use EliasHaeussler\Api\Exception\AuthenticationException;
use EliasHaeussler\Api\Exception\InvalidRequestException;
use EliasHaeussler\Api\Method\Slack\LunchMethod;
use EliasHaeussler\Api\Page\Frontend;
use EliasHaeussler\Api\Routing\PageRouter;
use EliasHaeussler\Api\Utility\GeneralUtility;

/**
 * @todo documentation needed
 *
 * @package EliasHaeussler\Api\Controller
 * @author Elias Häußler <mail@elias-haeussler.de>
 */
class SlackController extends BaseController
{
    /**
     * @var string Base API uri of Slack
     */
    const API_URI = "https://slack.com/api/";

    /** @var string Base Authentication uri of Slack */
    const AUTHORIZE_URI = "https://slack.com/oauth/authorize";

    /** @var string File name pattern of user-based .env files */
    const ENV_FILENAME_PATTERN = "slack.env.%s";

    /** @var string Route for authentication */
    const ROUTE_AUTH = "authenticate";

    /** @var array Classes for each available route */
    const ROUTE_MAPPINGS = [
        "lunch" => LunchMethod::class,
    ];

    /**
     * @var string Client ID of Slack App
     */
    protected $clientId;

    /**
     * @var string Client secret of Slack App
     */
    protected $clientSecret;

    /**
     * @var string Signing secret from Slack App, used for authentication
     */
    protected $signingSecret;

    /**
     * @var string Slack authentication type
     */
    protected $authType;

    /**
     * @var string Slack authentication token
     */
    protected $authToken;

    /**
     * @var string Slack authentication state string
     */
    protected $authState;

    /**
     * @var array Data from Slack API, passed in request
     */
    protected $requestData;


    /**
     * @todo add doc
     *
     * @throws InvalidRequestException
     * @throws AuthenticationException
     */
    protected function initializeRequest()
    {
        // Set base app credentials and authentication settings
        $this->clientId = getenv("SLACK_CLIENT_ID") ?: "";
        $this->clientSecret = getenv("SLACK_CLIENT_SECRET") ?: "";
        $this->signingSecret = getenv("SLACK_SIGNING_SECRET") ?: "";
        $this->authState = getenv("SLACK_AUTH_STATE") ?: "";

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
        $this->authType = getenv("SLACK_AUTH_TYPE") ?: "";
        $this->authToken = getenv("SLACK_AUTH_TOKEN") ?: "";
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
        // Check if request is valid
        if (!$this->matchesRoute(self::ROUTE_AUTH)) {
            $this->prepareCall();
        }

        if (array_key_exists($this->route, self::ROUTE_MAPPINGS)) {
            /** @var BaseMethod $method */
            $class = self::ROUTE_MAPPINGS[$this->route];
            $method = GeneralUtility::makeInstance($class, $this);
            $method->processRequest();
        }
    }

    /**
     * @todo add doc
     *
     * @param string $function
     * @param string|array $data
     * @param bool $json
     * @param bool $authorize
     * @return bool|string
     */
    public function api(string $function, $data, bool $json = true, bool $authorize = true)
    {
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

        // Add authorization, if requested to do so
        $this->addApiHeaders($ch, $data, $json, $authorize);

        // Send API call and store result
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    /**
     * @todo add doc
     *
     * @param string $uri
     * @param string $text
     * @return string
     */
    public function buildUri(string $uri, string $text = ""): string
    {
        if (empty(trim($text))) {
            $text = $uri;
        }

        return sprintf("<%s|%s>", $uri, $text);
    }

    public function buildMessage(string $message, string $type = Frontend::MESSAGE_TYPE_NOTICE): string
    {
        if (PageRouter::getAccess() != PageRouter::ACCESS_TYPE_CLI) {
            return parent::buildMessage($message, $type);
        }

        // Set correct content header
        header("Content-Type: application/json");

        // Remove header from message
        if ($type == Frontend::MESSAGE_TYPE_ERROR) {
            $messageBody = implode("\r\n", array_splice(explode("\r\n", $message), 1));
            $message = ":no_entry: " . $messageBody;
        }

        return json_encode([
            "response_type" => "ephemeral",
            "text" => $message,
        ]);
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
     */
    protected function storeRequestData()
    {
        parse_str($this->requestBody, $this->requestData);
    }

    /**
     * @todo add doc
     */
    protected function setAccessType()
    {
        if (strpos($_SERVER["HTTP_USER_AGENT"], "Slackbot") !== false) {
            PageRouter::setAccess(PageRouter::ACCESS_TYPE_CLI);
        }
    }

    /**
     * @todo add doc
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
     * @todo add doc
     *
     * @return bool
     * @throws InvalidRequestException
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
     * @todo add doc
     *
     * @return bool
     */
    protected function isUserAuthenticated(): bool
    {
        $fileName = ROOT_PATH . "/" . sprintf(self::ENV_FILENAME_PATTERN, $this->requestData['user_id']);
        return file_exists($fileName);
    }

    /**
     * @todo add doc
     *
     * @param string $expected
     * @return bool
     */
    protected function isValidAuthState(string $expected): bool
    {
        return $expected ? $_GET['state'] == $expected : true;
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
        $calculatedSignature = $apiVersionNumber . "=" . $hashString;
        if ($calculatedSignature != $signature) return false;

        return true;
    }

    /**
     * @todo add doc
     *
     * @param resource $ch
     * @param string|array $data
     * @param bool $json
     * @param bool $authorize
     * @internal Used in `SlackController::api` to build HTTP headers for API request
     */
    protected function addApiHeaders(&$ch, $data, bool $json, bool $authorize)
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
     * @todo add doc
     */
    protected function showUserAuthenticationUri()
    {
        $uri = $this->buildUserAuthenticationUri();
        echo $this->buildMessage(":warning: Please " . $this->buildUri($uri, "authenticate") . " first to use this command.");
    }

    /**
     * @todo add doc
     *
     * @return string
     */
    protected function buildUserAuthenticationUri()
    {
        $parameters = [
            "scope" => "users.profile:write,users.profile:read",
            "client_id" => $this->clientId,
            "state" => $this->authState,
        ];
        return self::AUTHORIZE_URI . "?" . http_build_query($parameters);
    }

    /**
     * @todo add doc
     *
     * @throws AuthenticationException
     * @throws InvalidRequestException
     */
    protected function processUserAuthentication()
    {
        if (!$this->isValidAuthState($this->authState)) {
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
        echo Frontend::success(
            "Yay, the authentication was successful.",
            "Please re-send your command and everything should be fine."
        );
    }

    /**
     * @todo add doc
     *
     * @param string $result
     * @throws InvalidRequestException
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
     * @todo add doc
     *
     * @param string $fileName
     * @param array $result
     * @param array $mappings
     */
    protected function writeToFile(string $fileName, array $result, array $mappings)
    {
        $handler = fopen($fileName, "w");

        $content = "";
        foreach ($mappings as $resKey => $envKey) {
            $content .= $envKey . "=" . $result[$resKey] . "\r\n";
        }

        fwrite($handler, $content);
        fclose($handler);
    }

    /**
     * @todo add doc
     *
     * @return string
     */
    public function getAuthToken(): string
    {
        return $this->authToken;
    }

    /**
     * @todo add doc
     *
     * @param string $key
     * @return array|string
     */
    public function getRequestData(string $key = "")
    {
        if ($key) {
            return $this->requestData[$key] ?: "";
        } else {
            return $this->requestData;
        }
    }
}
