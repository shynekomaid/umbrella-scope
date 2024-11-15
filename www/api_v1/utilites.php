<?php

// This is a module and cannot be run directly
if (basename(__FILE__) === basename($_SERVER["SCRIPT_FILENAME"])) {
    returnJson(null, "direct_run_forbidden");
}

/* ============================ CONFIGURATIONS ============================= */

// Determinate that debug mode is on or off
// Enabled debug can brake page render and API response!!!
define("debug", false);
define("remote_domain", "https://crm.belmar.pro");
define("box_id", 28);
define("offer_id", 5);
define("countryCode", "GB");
define("language", "en");
define("password", "qwerty12");
define("token", "ba67df6a-a17c-476f-8e95-bcdb75ed3958");

/* ============================== PREREQUISITES  =========================== */

if (debug) {
    ini_set("error_reporting", E_ALL);
    ini_set("display_errors", 1);
    ini_set("display_startup_errors", 1);
}


if (!empty($_SERVER["HTTP_CLIENT_IP"])) {
    $userIp = $_SERVER["HTTP_CLIENT_IP"];
} elseif (!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
    $userIp = $_SERVER["HTTP_X_FORWARDED_FOR"];
} else {
    $userIp = $_SERVER["REMOTE_ADDR"];
}

$protocol = (
    !empty($_SERVER["HTTPS"]) &&
    $_SERVER["HTTPS"] !== "off" ||
    $_SERVER["SERVER_PORT"] == 443
) ?
    "https://" :
    "http://";


$host = $_SERVER["HTTP_HOST"];

$serverURL = $protocol . $host;


/**
 * Return JSON response to client
 * @param mixed $data response data
 * @param string $error error message
 * @return void
 */
function returnJson($data = null, $error = "none")
{
    if (!debug) {
        header("Content-Type: application/json; charset=utf-8");
    }
    if (!is_null($data)) {
        echo json_encode(["response" => $data, "error" => $error], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode(["error" => $error], JSON_UNESCAPED_UNICODE);
    }
    exit();
}

/**
 * Get JSON from POST request body
 * @return array associative array with data from JSON
 */
function getPostJson()
{
    $body = file_get_contents("php://input");
    return json_decode($body, true);
}

/**
 * Sends an HTTP request to a specified URL.
 *
 * This function supports both GET and POST methods. For POST requests,
 * it allows sending data either as URL-encoded form data or as JSON,
 * based on the $postIsQuery parameter. Custom headers can be included
 * in the request.
 *
 * @param string $method The HTTP method to use for the request, e.g., "GET" or "POST".
 * @param string $url The endpoint URL to which the request is sent.
 * @param mixed|null $bodyReq The request body data, either as an associative array or object.
 * @param array|null $headers An array of additional headers to include in the request.
 * @param bool $postIsQuery Specifies whether to send POST data as URL-encoded (true) or JSON (false).
 *
 * @return string The response from the server.
 */
function sendHTTPrequest(
    $method,
    $url,
    $bodyReq = null,
    $headers = null,
    $postIsQuery = false
) {
    if ($method === "POST") {
        $options = [
            "http" => [
                "method" => "POST",
            ],
        ];
        if ($bodyReq !== null) {
            if ($postIsQuery) {
                $options["http"]["header"] = "Content-type: application/x-www-form-urlencoded\r\n";
                $options["http"]["content"] = http_build_query($bodyReq);
            } else {
                $options["http"]["header"] = "Content-type: application/json\r\n";
                $options["http"]["content"] = encodeJsonForRequest($bodyReq);
            }
        }
        if ($headers !== null && is_array($headers)) {
            $options["http"]["header"] .= implode("\r\n", $headers);
        }
        $context = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        return $response;
    } else if ($method === "GET") {
        if ($bodyReq !== null) {
            $url = $url . "?" . http_build_query($bodyReq);
        }
        $response = file_get_contents($url);
        return $response;
    }
}


/**
 * Encodes data to JSON format for HTTP requests.
 *
 * This function takes an input array or object and encodes it into a JSON
 * string with unescaped Unicode characters. It is useful for preparing data
 * to be sent in HTTP requests where JSON formatting is required.
 *
 * @param mixed $data The data to be encoded to JSON. This can be an array
 *                    or an object.
 * @return string A JSON encoded string representation of the input data.
 */
function encodeJsonForRequest($data)
{
    return json_encode($data, JSON_UNESCAPED_UNICODE);
}


/**
 * Adds a lead to the Bitrek CRM.
 *
 * This function takes four parameters for the lead's first name, last name,
 * phone number, and email address. It makes a POST request to the Bitrek CRM
 * API with the supplied data, and returns an array containing a boolean
 * indicating whether the request was successful, and an array with the lead's
 * ID, email address, and autologin string if the request was successful.
 *
 * @param string $firstName The lead's first name.
 * @param string $lastName The lead's last name.
 * @param string $phone The lead's phone number.
 * @param string $email The lead's email address.
 * @return array An array containing a boolean indicating whether the request
 *               was successful, and an array with the lead's ID, email address,
 *               and autologin string if the request was successful.
 */
function addLead($firstName, $lastName, $phone, $email)
{
    global $serverURL, $userIp;
    $domain = remote_domain;
    $remoteUrl = $domain . "/api/v1/addlead";
    $response = sendHTTPrequest(
        "POST",
        $remoteUrl,
        [
            "firstName" => $firstName,
            "lastName" => $lastName,
            "phone" => $phone,
            "email" => $email,
            "box_id" => box_id,
            "countryCode" => countryCode,
            "offer_id" => offer_id,
            "landingUrl" => $serverURL,
            "ip" => $userIp,
            "password" => password,
            "language" => language,
            "clickId" => "",
            "quizAnswers" => "",
            "custom1" => "",
            "custom2" => "",
            "custom3" => ""
        ],
        [
            "token: " . token
        ]
    );

    $decoded = json_decode($response, true);
    if ($decoded === null) {
        return [false, "json_decode_error"];
    }
    if (!is_array($decoded)) {
        return [false, "no_result"];
    }

    if ((bool)$decoded["status"] === true) {
        return [
            true,
            [
                "id" => @$decoded["id"],
                "email" => @$decoded["email"],
                "autologin" => @$decoded["autologin"]
            ]
        ];
    } else {
        return [false, $decoded["error"]];
    }
}

/**
 * Retrieves lead statuses from the remote CRM.
 *
 * This function sends a POST request to the CRM API to retrieve lead statuses
 * within a specified date range and pagination information. The function returns
 * an array indicating the success status of the request, the retrieved data, and
 * pagination details if successful. In case of an error, it returns an error message.
 *
 * @param string $start The start date for retrieving statuses.
 * @param string $end The end date for retrieving statuses.
 * @param int $page The page number for paginated data.
 * @param int $limit The maximum number of records per page.
 * @return array An array containing a boolean indicating success, an array of
 *               data if successful, the current page number, and the limit of records per page.
 *               If unsuccessful, it returns a boolean and an error message.
 */
function getLead($start, $end, $page, $limit)
{
    $domain = remote_domain;
    $remoteUrl = $domain . "/api/v1/getstatuses";
    $response = sendHTTPrequest(
        "POST",
        $remoteUrl,
        [
            "date_from" => $start,
            "date_to" => $end,
            "page" => $page,
            "limit" => $limit,
        ],
        [
            "token: " . token
        ]
    );

    $decoded = json_decode($response, true);
    if ($decoded === null) {
        return [false, "json_decode_error"];
    }
    if (!is_array($decoded)) {
        return [false, "no_result"];
    }

    if ((bool)$decoded["status"] === true) {
        $page = @(int)$decoded["page"] ?? 0;
        $page++; // Human page for js
        return [
            true,
            @$decoded["data"] ?? [],
            $page,
            @$decoded["limit"]
        ];
    } else {
        return [false, @$decoded["error"] ?? "serverReturnError"];
    }
}
