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

$protocol = (!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off" || $_SERVER["SERVER_PORT"] == 443) ? "https://" : "http://";
$host = $_SERVER["HTTP_HOST"];
$requestUri = $_SERVER["REQUEST_URI"];
$serverURL = $protocol . $host . $requestUri;


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
 * Send HTTP request with proxy
 * @param string $method request method
 * @param string $url request URL
 * @param array|null $headers request headers
 * @return string response
 */
function sendHTTPrequest(
    $method,
    $url,
    $bodyReq = null,
    $headers = null
) {
    if ($method === "POST") {
        $options = [
            "http" => [
                "header" => "Content-type: application/x-www-form-urlencoded\r\n",
                "method" => "POST",
            ],
        ];
        if ($bodyReq !== null) {
            $options["http"]["content"] = http_build_query($bodyReq);
        }
        if ($headers !== null) {
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
            "countryCode" => countryCode,
            "language" => language,
            "box_id" => box_id,
            "offer_id" => offer_id,
            "clickId" => "",
            "quizAnswers" => "",
            "custom1" => "",
            "custom2" => "",
            "custom3" => "",

            "ip" => $userIp,
            "landingUrl" => $serverURL
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
    return [true, $decoded];
}
