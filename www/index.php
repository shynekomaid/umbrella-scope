<?php

define("DIR", __DIR__);
ini_set("default_socket_timeout", 29);

require_once(DIR . "/api_v1/utilites.php");

$url = parse_url($_SERVER["REQUEST_URI"]);
parse_str($url["query"] ?? "", $query);


$page = trim($url["path"], "/");

switch ($page) {
    case "":
    case "add_lead":
        include(DIR . "/templates/add_lead.php");
        break;
    case "get_lead":
        include(DIR . "/templates/get_lead.php");
        break;
    default:
        http_response_code(404);
        include(DIR . "/templates/404.php");
        break;
}
