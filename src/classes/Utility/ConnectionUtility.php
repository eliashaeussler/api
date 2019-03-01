<?php
/**
 * Copyright (c) 2019 Elias Häußler <mail@elias-haeussler.de>. All rights reserved.
 */
declare(strict_types=1);
namespace EliasHaeussler\Api\Utility;

/**
 * Connection utility functions.
 *
 * @package EliasHaeussler\Api\Utility
 * @author Elias Häußler <mail@elias-haeussler.de>
 * @license MIT
 */
class ConnectionUtility
{

    /**
     * Send cURL POST request to given uri.
     *
     * Sends a cURL POST request to the given uri and adds the appropriate post data and options to the request.
     * If `$json` is set to `true`, the request will be sent as JSON-formatted string.
     *
     * @param string $uri The request uri
     * @param array $postData POST data, will be added to the request
     * @param array $options Additional options, will be merged with the default cURL options
     * @param bool $json Define whether to send a JSON request instead of raw POST request
     * @return bool|string The cURL request result
     */
    public static function sendRequest(string $uri, array $postData = [], array $options = [], bool $json = false)
    {
        $ch = curl_init();

        if ($json)
        {
            $postData = json_encode($postData);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Content-Type: " . "application/json; charset=utf-8",
                "Content-Length: " . strlen($postData),
            ]);
        }

        curl_setopt_array($ch, array_replace_recursive([
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL => $uri,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $postData,
        ], $options));

        // Send API call and store result
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

}
