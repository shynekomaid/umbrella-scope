<?php

require_once("../utilites.php");

$input = getPostJson();


if (!isset($input["dateFrom"])) {
    returnJson(null, "noStartData");
}

if (!isset($input["dateTo"])) {
    returnJson(null, "noEndData");
}

// parse dates "2024-10-15T19:11" and check it both > -60 day before now
$dateFrom = DateTime::createFromFormat('Y-m-d\TH:i', $input["dateFrom"]);
$dateTo = DateTime::createFromFormat('Y-m-d\TH:i', $input["dateTo"]);

if (!$dateFrom || !$dateTo) {
    returnJson(null, "invalidDateFormat");
}

$sixtyDaysAgo = new DateTime();
$sixtyDaysAgo->modify('-60 days ');

if ($dateFrom < $sixtyDaysAgo) {
    returnJson(null, "dateFromTooOld");
}

if ($dateTo < $sixtyDaysAgo) {
    returnJson(null, "dateToTooOld");
}

$page = 1;

if (isset($input["page"])) {
    $page = (int)$input["page"];
}

$limit = 100;

if ($page < 1) {
    $page = 1;
}

$page--; // Human page for js

$result = getLead($input["dateFrom"], $input["dateTo"], $page, $limit);

if ($result[0]) {
    returnJson(
        [
            "received" => [
                "data" => [$result[1]],
                "page" => $page,
                "limit" => $limit
            ],
            "error" => "none"
        ]
    );
} else {
    returnJson($result[1], "serverReturnError");
}
