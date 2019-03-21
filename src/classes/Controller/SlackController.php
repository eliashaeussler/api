<?php
/**
 * Copyright (c) 2019 Elias Häußler <mail@elias-haeussler.de>. All rights reserved.
 */
declare(strict_types=1);
namespace EliasHaeussler\Api\Controller;

use Doctrine\DBAL\Connection;
use EliasHaeussler\Api\Exception\AuthenticationException;
use EliasHaeussler\Api\Exception\ClassNotFoundException;
use EliasHaeussler\Api\Exception\InvalidParameterException;
use EliasHaeussler\Api\Exception\InvalidRequestException;
use EliasHaeussler\Api\Frontend\Message;
use EliasHaeussler\Api\Helpers\SlackMessage;
use EliasHaeussler\Api\Routing\Slack\AuthenticateRoute;
use EliasHaeussler\Api\Routing\Slack\LunchCommandRoute;
use EliasHaeussler\Api\Routing\Slack\RedmineCommandRoute;
use EliasHaeussler\Api\Service\ConnectionService;
use EliasHaeussler\Api\Service\LogService;
use EliasHaeussler\Api\Service\RoutingService;
use EliasHaeussler\Api\Utility\ConnectionUtility;
use EliasHaeussler\Api\Utility\ConsoleUtility;
use EliasHaeussler\Api\Utility\GeneralUtility;
use EliasHaeussler\Api\Utility\LocalizationUtility;

/**
 * Slack API controller.
 *
 * This API controller converts API requests to valid requests for the Slack API and processes them. Each available
 * route is mapped to an appropriate routing class which should be an instance of {@see BaseRoute} class.
 *
 * @author Elias Häußler <mail@elias-haeussler.de>
 * @license MIT
 */
class SlackController extends BaseController
{
    use UserEnvironmentRequired;

    /** @var string Base API uri of Slack */
    const API_URI = "https://slack.com/api/";

    /** @var string Base authentication uri of Slack */
    const AUTHORIZE_URI = "https://slack.com/oauth/authorize";

    /** @var string Route for authentication */
    const ROUTE_AUTH = "authenticate";

    /** @var array Classes for each available route */
    const ROUTE_MAPPINGS = [
        "lunch" => LunchCommandRoute::class,
        "redmine" => RedmineCommandRoute::class,
        self::ROUTE_AUTH => AuthenticateRoute::class,
    ];

    /** @var array Scopes which are required for each route during authentication */
    const REQUIRED_SCOPES = [
        "lunch" => [
            "users.profile:write",
            "users:read",
        ],
    ];

    /** @var Connection Database connection */
    protected $database;

    /** @var string Client ID of Slack App */
    protected $clientId;

    /** @var string Client secret of Slack App */
    protected $clientSecret;

    /** @var string Signing secret from Slack App, used for authentication */
    protected $signingSecret;

    /** @var string Slack authentication type */
    protected $authType;

    /** @var string Slack authentication state string */
    protected $authState;

    /** @var array Data from Slack API, passed in request */
    protected $requestData;

    /** @var string Slack authentication token */
    protected $authToken = "";

    /**
     * {@inheritdoc}
     *
     * @throws AuthenticationException if the authentication process failed
     * @throws ClassNotFoundException  if the routing class is not available
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
     * @param string       $function  Slack API function
     * @param string|array $data      Additional data to be sent during API request
     * @param bool         $json      Define whether to use JSON or POST to send data
     * @param bool         $authorize Define whether to send authorization headers
     *
     * @return bool|string The API result on success or `false` on failure
     *
     * @see https://api.slack.com/web#methods
     */
    public function api(string $function, $data, bool $json = true, bool $authorize = true)
    {
        // Convert input data to required format
        if ($json && is_string($data)) {
            $data = json_decode($data, true);
        }

        // Add authorization header
        $httpHeaders = [];
        if ($authorize) {
            $httpHeaders = [
                sprintf("Authorization: %s %s", $this->authType, $this->authToken),
            ];
        }

        // Send API call and store result
        $result = ConnectionUtility::sendRequest(self::API_URI . $function, $data, $httpHeaders, [], $json);

        LogService::log(sprintf("Got API result from Slack: %s", $result), LogService::DEBUG);

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function buildMessage(string $type, $arg1, ...$_): string
    {
        if (RoutingService::getAccess() == RoutingService::ACCESS_TYPE_BOT) {
            return $this->buildBotMessage($type, $arg1);
        }

        return parent::buildMessage($type, $arg1, ...$_);
    }

    /**
     * Build message for Bot.
     *
     * @param string            $type        Message type
     * @param string|\Exception $message     Message
     * @param array             $attachments Attachments
     * @param bool              $public      Define whether to post message publicly
     *
     * @return string The rendered message
     */
    public function buildBotMessage(string $type, $message, array $attachments = [], bool $public = false): string
    {
        // Set correct content header
        header("Content-Type: application/json");

        // Set message
        if ($type == Message::MESSAGE_TYPE_ERROR) {
            /** @var \Exception $message */
            $message = sprintf("%s %s", SlackMessage::emoji("no_entry"), $message->getMessage());
        }

        LogService::log($message, LogService::NOTICE);

        return json_encode([
            "response_type" => $public ? "in_channel" : "ephemeral",
            "text" => $message,
            "attachments" => $attachments,
        ]);
    }

    /**
     * Generate attachment for bot message.
     *
     * @param string $header         Header text
     * @param string $body           Body text
     * @param string $preText        Additional pre text
     * @param string $fallback       Fallback text
     * @param bool   $addFooter      Define whether to add footer to attachment
     * @param array  $additionalData Additional data, will be merged with the default data
     * @param array  $markdown       Fields to format with Markdown
     *
     * @return array Attachment for bot message
     */
    public function buildAttachmentForBotMessage(
        string $header,
        string $body,
        string $preText = "",
        string $fallback = "",
        bool $addFooter = false,
        array $additionalData = [],
        array $markdown = ["fields"]
    ): array {
        $attachment = [
            "title" => $header,
            "pretext" => $preText,
            "text" => $body,
            "fallback" => $fallback,
            "mrkdwn_in" => $markdown,
        ];

        if ($addFooter) {
            $attachment["footer"] = $this->buildAttachmentFooter();
        }

        if ($additionalData) {
            $attachment = array_replace_recursive($attachment, $additionalData);
        }

        return $attachment;
    }

    /**
     * Build footer for Slack attachments.
     *
     * @return string Slack attachment footer
     */
    public function buildAttachmentFooter(): string
    {
        return sprintf(
            "%s | %s",
            GeneralUtility::getServerName(),
            ConsoleUtility::describeHistory(ConsoleUtility::HISTORY_TYPE_VERSION)
        );
    }

    /**
     * Check if result from API request is valid.
     *
     * Checks if the provided, raw JSON-encoded result from an API request is valid and contains a valid answer.
     *
     * @param string $result Raw result from API request, parsed as JSON
     *
     * @throws InvalidRequestException if API request failed or contains an invalid answer
     * @throws AuthenticationException if the user needs to re-authenticate himself
     */
    public function checkApiResult(string $result): void
    {
        if (!$result) {
            throw new InvalidRequestException(
                LocalizationUtility::localize("exception.1545669514", "slack"),
                1545669514
            );
        }

        $result = json_decode($result, true);

        // Check for valid result from Slack
        if ($result["ok"]) {
            return;
        }

        switch ($result["error"]) {
            case "not_authed":
            case "missing_scope":
            case "token_revoked":
                $authenticationUri = $this->buildUserAuthenticationUri();
                throw new AuthenticationException(
                    LocalizationUtility::localize(
                        "authentication.reauth.message", "slack", null,
                        SlackMessage::link(
                            $authenticationUri,
                            LocalizationUtility::localize("authentication.reauth.linkText", "slack")
                        )
                    ),
                    1551046280
                );

            default:
                throw new InvalidRequestException(
                    LocalizationUtility::localize("exception.1551040956", "slack", null, $result['error']),
                    1551040956
                );
        }
    }

    /**
     * Request user information from Slack API and set user-preferred language.
     *
     * @param bool $setLanguage Define whether to request and set user-preferred language
     *
     * @throws InvalidRequestException if API request failed or contains an invalid answer
     * @throws AuthenticationException if the user needs to re-authenticate himself
     *
     * @return mixed The user information result from Slack API
     */
    public function getUserInformation(bool $setLanguage = true)
    {
        // Set API parameters
        $data = [
            "token" => $this->authToken,
            "user" => $this->getRequestData("user_id"),
        ];

        // Request user-preferred language if requested
        if ($setLanguage) {
            $data["include_locale"] = true;
        }

        // Send API call
        $result = $this->api("users.info", $data, false);

        $this->checkApiResult($result);
        $result = json_decode($result, true);

        // Set user-preferred localization language
        if ($setLanguage) {
            LocalizationUtility::readUserPreferredLanguages($result["user"]["locale"]);
        }

        return $result;
    }

    /**
     * Get raw command name from full slash command.
     *
     * Returns the raw command name from the currently selected slash command. The raw command name contains only
     * the name of the slash command without its prepended slash.
     *
     * @throws InvalidRequestException   if no Slack command is set or no request data is available
     * @throws InvalidParameterException if an invalid slash command has been provided
     *
     * @return string The raw command name
     */
    public function getRawCommandName(): string
    {
        if (!$this->requestData || !$this->requestData["command"]) {
            throw new InvalidRequestException(LocalizationUtility::localize("exception.1552433612", "slack"), 1552433612);
        }

        // Get raw command name
        $command = $this->requestData["command"];
        preg_match("/^\\/([[:alnum:]]+)$/", $command, $matches);

        if (!$matches) {
            throw new InvalidParameterException(LocalizationUtility::localize("exception.1552434032", "slack"), 1552434032);
        }

        return strtolower($matches[1]);
    }

    /**
     * Get Slack authentication token.
     *
     * @return string Slack authentication token
     */
    public function getAuthToken(): string
    {
        return $this->authToken;
    }

    /**
     * Get Slack authentication state string.
     *
     * @return string Slack authentication state string
     */
    public function getAuthState(): string
    {
        return $this->authState;
    }

    /**
     * Get client ID of Slack app.
     *
     * @return string Client ID of Slack app
     */
    public function getClientId(): string
    {
        return $this->clientId;
    }

    /**
     * Get client secret of Slack app.
     *
     * @return string Client secret of Slack app
     */
    public function getClientSecret(): string
    {
        return $this->clientSecret;
    }

    /**
     * Get data from API request.
     *
     * Returns data from API request, either a specific key, if provided, or all available data. This method returns
     * an empty string, if no request data is available for the given key.
     *
     * @param string $key Key of API request to return. Optional.
     *
     * @return array|string API request data
     */
    public function getRequestData(string $key = "")
    {
        return $key ? ($this->requestData[$key] ?? "") : $this->requestData;
    }

    /**
     * Set value for a specific key of the current API request data.
     *
     * Updates a specific value of the current API request data with the provided value for the given key. This can
     * be useful i. e. to update the given text from Slack.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function setRequestDataForKey(string $key, $value): void
    {
        $this->requestData[$key] = $value;
    }

    /**
     * Get database connection.
     *
     * @return Connection Database connection
     */
    public function getDatabase(): Connection
    {
        return $this->database;
    }

    /**
     * Load user data from database.
     *
     * Loads the available user data from the database and stores them locally.
     *
     * @throws AuthenticationException if user data is missing in the database
     */
    public function loadUserData()
    {
        $queryBuilder = $this->database->createQueryBuilder();
        $result = $queryBuilder->select("*")
            ->from("slack_auth")
            ->where("user = :user_id")
            ->setParameter("user_id", $this->requestData['user_id'])
            ->execute()
            ->fetch();

        if (empty($result)) {
            throw new AuthenticationException(
                LocalizationUtility::localize("exception.1546798472", "slack"),
                1546798472
            );
        }

        $this->authToken = $result['token'];
    }

    /**
     * Check whether the current user is already authenticated.
     *
     * Checks whether the current user is already authenticated by testing if an appropriate database entry exists.
     *
     * @return bool `true` if the user is already authenticated, `false` otherwise
     */
    public function isUserAuthenticated(): bool
    {
        $queryBuilder = $this->database->createQueryBuilder();
        $result = $queryBuilder->select("COUNT(*) AS count")
            ->from("slack_auth")
            ->where("user = :user_id")
            ->setParameter("user_id", $this->requestData['user_id'])
            ->execute()
            ->fetch();

        return $result['count'] > 0;
    }

    /**
     * {@inheritdoc}
     *
     * @throws AuthenticationException if provided authentication state is invalid
     * @throws InvalidRequestException if user authentication or API request failed
     * @throws ClassNotFoundException  if the {@see Message} class is not available
     */
    protected function initializeRequest()
    {
        // Get database connection
        $this->database = GeneralUtility::makeInstance(ConnectionService::class)->getDatabase();

        // Set base app credentials and authentication settings
        $this->clientId = GeneralUtility::getEnvironmentVariable("SLACK_CLIENT_ID");
        $this->clientSecret = GeneralUtility::getEnvironmentVariable("SLACK_CLIENT_SECRET");
        $this->signingSecret = GeneralUtility::getEnvironmentVariable("SLACK_SIGNING_SECRET");
        $this->authState = GeneralUtility::getEnvironmentVariable("SLACK_AUTH_STATE");
        $this->authType = GeneralUtility::getEnvironmentVariable("SLACK_AUTH_TYPE");

        // Store data from request body and user-specific environment variables
        $this->storeRequestData();
        $this->setAccessType();

        // Do not handle request as default if authentication is in progress
        if ($this->matchesRoute(self::ROUTE_AUTH)) {
            return;
        }

        // Load user data
        if ($this->isRequestValid()) {
            if ($this->routeRequiresAuthentication()) {
                if ($this->isUserAuthenticated()) {
                    $this->loadUserData();
                } else {
                    $this->showUserAuthenticationUri();
                }
            }
        } else {
            $this->showUserAuthenticationUri();
        }
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
     * @throws \Exception              if the difference between request send and receive time could not be calculated
     */
    protected function prepareCall()
    {
        // Get timestamp and signature from request
        $requestTimestamp = $this->getRequestHeader("X-Slack-Request-Timestamp");
        $requestSignature = $this->getRequestHeader("X-Slack-Signature");

        if (!$this->isRequestVerified($requestTimestamp, $requestSignature)) {
            throw new AuthenticationException(
                LocalizationUtility::localize("exception.1543541836", "slack"),
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
            ? RoutingService::ACCESS_TYPE_BOT
            : RoutingService::ACCESS_TYPE_BROWSER;
        RoutingService::setAccess($accessType);

        LogService::log(sprintf("Setting access type \"%s\" for current request", $accessType), LogService::DEBUG);
    }

    /**
     * Check whether the current request is valid.
     *
     * Checks whether the request data is set and has been initialized yet. If not, an exception will be thrown. If the
     * request data is already set but does not include a user id, the request is marked as invalid.
     *
     * @throws InvalidRequestException if the request data is not set or has not been initialized yet
     *
     * @return bool `true` if the request is valid, `false` otherwise
     */
    protected function isRequestValid(): bool
    {
        if (!$this->requestData) {
            throw new InvalidRequestException(
                LocalizationUtility::localize("exception.1545685035", "slack"),
                1545685035
            );
        }

        return isset($this->requestData['user_id']);
    }

    /**
     * Get scopes which are required for the current route.
     *
     * @return array Required scopes for the current route
     */
    protected function getRequiredScopes(): array
    {
        return isset(self::REQUIRED_SCOPES[$this->route]) ? self::REQUIRED_SCOPES[$this->route] : [];
    }

    /**
     * Check if the current route requires user authentication.
     *
     * @return bool `true` if the current route requires authentication, `false` otherwise
     */
    protected function routeRequiresAuthentication(): bool
    {
        return count($this->getRequiredScopes()) > 0;
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
        return $this->requestParameters['state'] == $this->authState;
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
     *
     * @throws \Exception if the difference between request send and receive time could not be calculated
     *
     * @return bool `true` if the request can be verified, `false` otherwise
     *
     * @see https://api.slack.com/docs/verifying-requests-from-slack
     */
    protected function isRequestVerified(string $timestamp, string $signature): bool
    {
        // Check if request is older than 5 minutes
        $interval = \DateInterval::createFromDateString("5 minutes");
        $lowerBound = (new \DateTime())->sub($interval)->format('U');
        if ($lowerBound > $timestamp) {
            return false;
        }

        // Test if request is authenticated
        $apiVersionNumber = "v0";
        $baseString = implode(":", [$apiVersionNumber, $timestamp, $this->requestBody]);
        $hashString = hash_hmac("sha256", $baseString, $this->signingSecret);
        $calculatedSignature = $apiVersionNumber . "=" . $hashString;
        if ($calculatedSignature != $signature) {
            return false;
        }

        return true;
    }

    /**
     * Show message for necessary user authentication.
     *
     * @throws ClassNotFoundException if the {@see Frontend} class is not available
     */
    protected function showUserAuthenticationUri()
    {
        $uri = $this->buildUserAuthenticationUri();
        echo $this->buildMessage(
            Message::MESSAGE_TYPE_WARNING,
            LocalizationUtility::localize(
                "authentication.invite", "slack", null,
                SlackMessage::emoji("warning"),
                SlackMessage::link(
                    $uri,
                    LocalizationUtility::localize("authentication.invite.linkText", "slack")
                )
            )
        );
    }

    /**
     * Build URI for necessary user authentication.
     *
     * @return string URI for user authentication
     */
    protected function buildUserAuthenticationUri()
    {
        return self::AUTHORIZE_URI . "?" . http_build_query([
            "scope" => implode(",", $this->getRequiredScopes()),
            "client_id" => $this->clientId,
            "state" => $this->authState,
        ]);
    }
}
