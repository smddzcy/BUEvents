<?php
require_once "Config.php";
if (!array_key_exists("function", $_POST)) die();
$func = $_POST["function"];

switch ($func) {

    case "getClubs":
        $db = new EventDB();
        die(json_encode($db->getClubNames()));
        break;

    case "getCategories":
        $db = new EventDB();
        die(json_encode($db->getCategories()));
        break;

    case "search":
        $searchVal = trim($_POST["data"]);
        $db = new EventDB();
        die(json_encode($db->searchEventByName($searchVal)));
        break;

    case "filter":
        $db = new EventDB();
        if (!array_key_exists("data", $_POST)) die("Data is not given");
        if (!array_key_exists("clubs", $_POST["data"])) $clubs = ["all"];
        else $clubs = $_POST["data"]["clubs"];
        if (!array_key_exists("categories", $_POST["data"])) $cats = ["all"];
        else $cats = $_POST["data"]["categories"];
        if (!array_key_exists("date", $_POST["data"]) || count($_POST["data"]["date"]) < 2) {
            $start = null;
            $end = null;
        } else {
            $start = $_POST["data"]["date"][0];
            $end = $_POST["data"]["date"][1];
        }
        if (count($clubs) != 1 || $clubs[0] != "all") $db->filterByClub($clubs);
        if (count($cats) != 1 || $cats[0] != "all") $db->filterByCategory($cats);
        $db->filterByDate($start, $end);
        die(json_encode($db->getFilteredEvents()));
        break;

    case "getEvents":
        if (!array_key_exists("data", $_POST)) die("Data is not given");
        $lim1 = $_POST["data"][0];
        $lim2 = $_POST["data"][1];
        $db = new EventDB();
        die(json_encode($db->getEventsBetween($lim1, $lim2)));
        break;

}
