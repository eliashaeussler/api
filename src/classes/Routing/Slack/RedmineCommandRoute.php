<?php
/**
 * Copyright (c) 2019 Elias Häußler <mail@elias-haeussler.de>. All rights reserved.
 */
declare(strict_types=1);
namespace EliasHaeussler\Api\Routing\Slack;

use EliasHaeussler\Api\Controller\SlackController;
use EliasHaeussler\Api\Exception\InvalidEnvironmentException;
use EliasHaeussler\Api\Exception\InvalidRequestException;
use EliasHaeussler\Api\Exception\IssueNotFoundException;
use EliasHaeussler\Api\Frontend\Message;
use EliasHaeussler\Api\Helpers\SlackMessage;
use EliasHaeussler\Api\Routing\BaseRoute;
use EliasHaeussler\Api\Service\LogService;
use EliasHaeussler\Api\Utility\ConnectionUtility;
use EliasHaeussler\Api\Utility\GeneralUtility;
use EliasHaeussler\Api\Utility\LocalizationUtility;

/**
 * Redmine router for Slack API controller.
 *
 * This class defines the concrete router for the "redmine" route inside the Slack API controller. It enables Slack
 * users to get information about an issue directly from Redmine. This can be done by typing `/issue <id>` in Slack.
 * The data will be requested from the Redmine API. For this, it's necessary to set a base URI as well as an active
 * API key which will be used for authenticating an active user at the Redmine API.
 *
 * @package EliasHaeussler\Api\Routing\Slack
 * @author Elias Häußler <mail@elias-haeussler.de>
 * @license MIT
 */
class RedmineCommandRoute extends BaseRoute
{
    /** @var string XML as API request method */
    const REQUEST_MODE_XML = "xml";

    /** @var string JSON as API request method */
    const REQUEST_MODE_JSON = "json";

    /** @var string Plain request mode for default URIs */
    const REQUEST_MODE_PLAIN = "";

    /** @var SlackController Slack API Controller */
    protected $controller;

    /** @var string Base URI for API requests */
    protected $baseUri;

    /** @var string API key to be used for API requests */
    protected $apiKey;

    /**
     * {@inheritdoc}
     *
     * @throws InvalidEnvironmentException
     */
    protected function initializeRequest()
    {
        $this->baseUri = GeneralUtility::getEnvironmentVariable("SLACK_REDMINE_BASE_URI");
        $this->apiKey = GeneralUtility::getEnvironmentVariable("SLACK_REDMINE_API_KEY");

        if (!$this->baseUri || parse_url($this->baseUri) === false) {
            throw new InvalidEnvironmentException(
                LocalizationUtility::localize("exception.1552348174", "slack"),
                1552348174
            );
        }
        if (!$this->apiKey) {
            throw new InvalidEnvironmentException(
                LocalizationUtility::localize("exception.1552348219", "slack"),
                1552348219
            );
        }
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Exception if the issues' start date cannot be instantiated as {@see DateTime} object
     */
    public function processRequest()
    {
        // Get requested issue ID
        $issueID = (int) $this->controller->getRequestData("text");

        // Request issue data from API
        $uri = $this->buildUri(["issues", $issueID]);
        $authHeader = $this->buildAuthenticationHeader();
        $request = ConnectionUtility::sendRequest($uri, [], [$authHeader], [], true);

        LogService::log(sprintf("Got API result from Redmine: %s", $request), LogService::DEBUG);

        // Show error if API could not be accessed
        if ($request === false) {
            throw new InvalidRequestException(
                LocalizationUtility::localize("exception.1552348511", "slack"),
                1552348511
            );
        }

        // Print issue data to Slack
        if ($result = json_decode($request, true))
        {
            $issue = $result["issue"];

            // Set link to issue
            $link = $this->buildUri(["issues", $issue["id"]], self::REQUEST_MODE_PLAIN);

            // Get priorities
            $uri = $this->buildUri(["enumerations", "issue_priorities"]);
            $request = ConnectionUtility::sendRequest($uri, [], [$authHeader], [], true);

            LogService::log(sprintf("Got API result from Redmine: %s", $request), LogService::DEBUG);

            $priorities = json_decode($request, true);

            // Set action color based on priority
            $defaultPriority = current(array_filter($priorities["issue_priorities"], function ($priority) {
                return $priority["is_default"];
            }));
            if ($issue["priority"]["id"] == $defaultPriority["id"]) {
                $actionColor = "good";
            } else if ($issue["priority"]["id"] > $defaultPriority["id"]) {
                $actionColor = "danger";
            } else {
                $actionColor = "";
            }

            // Build attachments for Slack message
            $attachments = [
                [
                    "title" => sprintf("%s #%s: %s", $issue["tracker"]["name"], $issue["id"], $issue["subject"]),
                    "title_link" => $link,
                    "mrkdwn_in" => ["fields"],
                    "fallback" => $link,
                    "color" => $actionColor,
                    "author_name" => $issue["author"]["name"],
                    "author_link" => $this->buildUri(["users", $issue["author"]["id"]], self::REQUEST_MODE_PLAIN),
                    "author_icon" => $this->buildUri(["favicon.ico"], self::REQUEST_MODE_PLAIN),
                    "text" => mb_strimwidth($issue["description"], 0, 200, "..."),
                ],
                [
                    "color" => $actionColor,
                    "fields" => [
                        [
                            "title" => LocalizationUtility::localize("redmine.message.project", "slack"),
                            "value" => SlackMessage::link(
                                $this->buildUri(["projects", $issue["project"]["id"]], self::REQUEST_MODE_PLAIN),
                                $issue["project"]["name"]
                            ),
                        ],
                        isset($issue["assigned_to"]) ? [
                            "title" => LocalizationUtility::localize("redmine.message.assignedTo", "slack"),
                            "value" => SlackMessage::link(
                                $this->buildUri(["users", $issue["assigned_to"]["id"]], self::REQUEST_MODE_PLAIN),
                                $issue["assigned_to"]["name"]
                            ),
                        ] : null,
                        [
                            "title" => LocalizationUtility::localize("redmine.message.status", "slack"),
                            "value" => $issue["status"]["name"],
                            "short" => true,
                        ],
                        [
                            "title" => LocalizationUtility::localize("redmine.message.done", "slack"),
                            "value" => sprintf("%s%%", $issue["done_ratio"]),
                            "short" => true,
                        ],
                        [
                            "title" => LocalizationUtility::localize("redmine.message.priority", "slack"),
                            "value" => $issue["priority"]["name"],
                            "short" => true,
                        ],
                        [
                            "title" => LocalizationUtility::localize("redmine.message.startDate", "slack"),
                            "value" => SlackMessage::date(new \DateTime($issue["start_date"]), "{date_short_pretty}"),
                            "short" => true,
                        ],
                    ],
                    "actions" => [
                        [
                            "type" => "button",
                            "text" => LocalizationUtility::localize(
                                "redmine.button.showIssue", "slack", "", SlackMessage::emoji("bug")
                            ),
                            "url" => $link,
                            "style" => "primary",
                        ],
                        [
                            "type" => "button",
                            "text" => LocalizationUtility::localize(
                                "redmine.button.editIssue", "slack", "", SlackMessage::emoji("pencil2")
                            ),
                            "url" => $link . "/edit",
                        ],
                        [
                            "type" => "button",
                            "text" => LocalizationUtility::localize(
                                "redmine.button.logTime", "slack", "", SlackMessage::emoji("alarm_clock")
                            ),
                            "url" => $this->buildUri(["issues", $issue["id"], "time_entries", "new"], self::REQUEST_MODE_PLAIN)
                        ],
                    ],
                ],
            ];

            // Print message to Slack
            echo $this->controller->buildBotMessage(Message::MESSAGE_TYPE_SUCCESS, "", $attachments, true);

        } else {
            throw new IssueNotFoundException(
                LocalizationUtility::localize("exception.1552347666", "slack", "", $issueID),
                1552347666
            );
        }
    }

    /**
     * Build Redmine URI containing scopes, request method and optional arguments.
     *
     * @param array $scopes API scopes, will be combined with slash character (/)
     * @param string $method Request method, can be XML or JSON
     * @param array $arguments Optional GET parameters
     * @return string The generated API request URI
     */
    protected function buildUri(array $scopes, string $method = self::REQUEST_MODE_JSON, array $arguments = []): string
    {
        $allowed_methods = [self::REQUEST_MODE_XML, self::REQUEST_MODE_JSON, self::REQUEST_MODE_PLAIN];

        if (!in_array($method, $allowed_methods)) {
            throw new \InvalidArgumentException(
                LocalizationUtility::localize("exception.1552241288", implode("`, `", $allowed_methods)),
                1552241288
            );
        }

        // Build base URI
        $scopes = array_merge([$this->baseUri], array_map("trim", $scopes));
        $uri = implode("/", $scopes);

        if (strlen($method) > 0) {
            $uri .= "." . $method;
        }

        // Add arguments, if set
        if (count($arguments) > 0) {
            $uri = strtolower($uri) . "?" . http_build_query($arguments);
        }

        return $uri;
    }

    /**
     * Build Redmine authentication header.
     *
     * @return string Redmine authentication header
     */
    protected function buildAuthenticationHeader(): string
    {
        return "X-Redmine-API-Key: ". $this->apiKey;
    }
}
