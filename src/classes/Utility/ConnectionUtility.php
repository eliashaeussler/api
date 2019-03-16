<?php
/**
 * Copyright (c) 2019 Elias Häußler <mail@elias-haeussler.de>. All rights reserved.
 */
declare(strict_types=1);
namespace EliasHaeussler\Api\Utility;

use EliasHaeussler\Api\Service\LogService;

/**
 * Connection utility functions.
 *
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
     * @param string $uri         The request uri
     * @param array  $postData    POST data, will be added to the request
     * @param array  $httpHeaders Additional HTTP headers, will be merged with default headers
     * @param array  $options     Additional options, will be merged with the default cURL options
     * @param bool   $json        Define whether to send a JSON request instead of raw POST request
     *
     * @return bool|string The cURL request result
     */
    public static function sendRequest(
        string $uri,
        array $postData = [],
        array $httpHeaders = [],
        array $options = [],
        bool $json = false
    ) {
        // Initialize request
        $ch = curl_init();

        // Set default options
        $requestData = [
            CURLOPT_HTTPHEADER => [],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL => $uri,
        ];

        // Handle POST requests
        if (count($postData) > 0) {
            // Mark current request as POST request
            $requestData[CURLOPT_POST] = true;

            // Convert raw POST data to JSON data
            if ($json) {
                $postData = json_encode($postData);
                $requestData[CURLOPT_HTTPHEADER] = [
                    "Content-Type: " . "application/json; charset=utf-8",
                    "Content-Length: " . strlen($postData),
                ];
            }

            // Add POST data
            $requestData[CURLOPT_POSTFIELDS] = $postData;
        }

        // Set HTTP headers
        $requestData[CURLOPT_HTTPHEADER] = array_merge($requestData[CURLOPT_HTTPHEADER], $httpHeaders);

        // Merge default options with custom options
        foreach ($options as $key => $option) {
            if (is_array($option)) {
                $requestData[$key] = array_merge($requestData[$key] ?? [], $option);
            } else {
                $requestData[$key] = $option;
            }
        }
        curl_setopt_array($ch, $requestData);

        LogService::log(
            sprintf(
                "Sending request to \"%s\" with data %s",
                $uri,
                GeneralUtility::convertArrayToString($requestData)
            ),
            LogService::DEBUG
        );

        // Send request and store result
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }
}
