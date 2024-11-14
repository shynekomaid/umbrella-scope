<?php

require_once("../utilites.php");

$input = getPostJson();


if (!isset($input["firstName"])) {
    returnJson(null, "noFirstName");
}

if (!isset($input["lastName"])) {
    returnJson(null, "noLastName");
}

if (!isset($input["phone"])) {
    returnJson(null, "noPhone");
}

if (!isset($input["email"])) {
    returnJson(null, "noEmail");
}

$result = addLead(
    $input["firstName"],
    $input["lastName"],
    $input["phone"],
    $input["email"]
);

if ($result[0]) {
    returnJson();
} else {
    returnJson($result[1], "serverReturnError");
}
