<?php
/**
 * Copyright (c) 2019 Elias Häußler <mail@elias-haeussler.de>. All rights reserved.
 */
declare(strict_types=1);
namespace EliasHaeussler\Api\Routing\Slack;

use EliasHaeussler\Api\Controller\SlackController;
use EliasHaeussler\Api\Exception\AuthenticationException;
use EliasHaeussler\Api\Exception\ClassNotFoundException;
use EliasHaeussler\Api\Exception\InvalidRequestException;
use EliasHaeussler\Api\Frontend\Message;
use EliasHaeussler\Api\Routing\BaseRoute;
use EliasHaeussler\Api\Service\LogService;
use EliasHaeussler\Api\Utility\LocalizationUtility;

/**
 * Authenticate router for Slack API controller.
 *
 * This class defines the concrete router for the "authenticate" route inside the Slack API controller. It is ued to
 * authenticate a Slack user at the Slack API and stores the authentication data in the local database. The route is
 * normally requested as return uri for a Slack OAuth API request. It requests a OAuth token for the current user and
 * stores it in the local database for further API requests.
 *
 * @author Elias Häußler <mail@elias-haeussler.de>
 * @license MIT
 */
class AuthenticateRoute extends BaseRoute
{
    /** @var SlackController Slack API Controller */
    protected $controller;

    /**
     * Process requested user authentication.
     *
     * Processes the requested user authentication. In general, this method uses a provided code together with the
     * app credentials to request an access token at the Slack API. This will then be stored locally and used for
     * further API requests to authenticate the user at the Slack API.
     *
     * @throws InvalidRequestException if API request failed or contains an invalid answer
     * @throws AuthenticationException if the user needs to re-authenticate himself
     * @throws ClassNotFoundException  if the {@see Message} class is not available
     */
    public function processRequest()
    {
        LogService::log("Processing user authentication", LogService::DEBUG);

        // Send API call
        $result = $this->controller->api("oauth.access", [
            "client_id" => $this->controller->getClientId(),
            "client_secret" => $this->controller->getClientSecret(),
            "code" => $this->controller->getRequestParameter("code"),
        ], false, false);

        $this->controller->checkApiResult($result);
        $result = json_decode($result, true);

        // Check if user is already available in database
        $queryBuilder = $this->controller->getDatabase()->createQueryBuilder();
        $userIsAvailable = $queryBuilder->select("user")
            ->from("slack_auth")
            ->where($queryBuilder->expr()->eq("user", ":user"))
            ->setParameter("user", $result["user_id"])
            ->execute()
            ->rowCount() > 0;
        $queryBuilder->resetQueryParts();

        LogService::log(sprintf("User \"%s\" is %savailable", $result["user_id"], $userIsAvailable ? "" : "not "), LogService::DEBUG);

        // Save authentication credentials
        if ($userIsAvailable) {
            $dbResult = $queryBuilder->update("slack_auth")
                ->set("token", ":token")
                ->set("scope", ":scope")
                ->where($queryBuilder->expr()->eq("user", ":user"))
                ->setParameter("token", $result["access_token"])
                ->setParameter("scope", $result["scope"])
                ->setParameter("user", $result["user_id"])
                ->execute();
        } else {
            $dbResult = $queryBuilder->insert("slack_auth")
                ->values([
                    "user" => ":user",
                    "token" => ":token",
                    "scope" => ":scope",
                ])
                ->setParameter("user", $result["user_id"])
                ->setParameter("token", $result["access_token"])
                ->setParameter("scope", $result["scope"])
                ->execute();
        }

        // Show status message
        if ($dbResult > 0) {
            echo $this->controller->buildMessage(
                Message::MESSAGE_TYPE_SUCCESS,
                LocalizationUtility::localize("authentication.success.header", "slack"),
                LocalizationUtility::localize("authentication.success.message", "slack")
            );
        } else {
            echo $this->controller->buildMessage(
                Message::MESSAGE_TYPE_WARNING,
                LocalizationUtility::localize("authentication.error.header", "slack"),
                LocalizationUtility::localize("authentication.error.message", "slack")
            );
        }

        LogService::log("Finished user authentication", LogService::DEBUG);
    }

    /**
     * {@inheritdoc}
     *
     * @throws AuthenticationException if the requested state does not equals the expected state
     */
    protected function initializeRequest()
    {
        if (!$this->isValidAuthState()) {
            throw new AuthenticationException(
                LocalizationUtility::localize("exception.1545662028", "slack"),
                1545662028
            );
        }
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
        return $this->controller->getRequestParameter("state") == $this->controller->getAuthState();
    }
}
