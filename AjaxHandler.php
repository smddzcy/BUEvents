<?php
require_once "Config.php";
if (!array_key_exists("function", $_POST)) die();
$func = $_POST["function"];

switch ($func) {

    case "getClubs":
        $db = new EventDB();
        die(json_encode($db->getClubs()));
        break;

    case "getCategories":
        $db = new EventDB();
        die(json_encode($db->getCategories()));
        break;

    case "search":
        $searchVal = trim($_POST["data"]["val"]);
        $db = new EventDB();
        applySortOrder($db, $_POST);
        die(json_encode($db->searchEventByName($searchVal)));
        break;

    case "filter":
        $db = new EventDB();
        applySortOrder($db, $_POST);
        if (!array_key_exists("data", $_POST)) die("Data is not given");
        if (!array_key_exists("clubs", $_POST["data"])) $clubs = ["All clubs"];
        else $clubs = $_POST["data"]["clubs"];
        if (!array_key_exists("categories", $_POST["data"])) $cats = ["All categories"];
        else $cats = $_POST["data"]["categories"];
        if (!array_key_exists("date", $_POST["data"])) {
            $start = null;
            $end = null;
        } else {
            $start = $_POST["data"]["date"][0];
            $end = $_POST["data"]["date"][1];
        }
        if (count($clubs) != 1 || strtolower($clubs[0]) != "all clubs") $db->filterByClub($clubs);
        if (count($cats) != 1 || strtolower($cats[0]) != "all categories") $db->filterByCategory($cats);
        $db->filterByDate($start, $end);
        die(json_encode($db->getFilteredEvents()));
        break;

    case "getEvents":
        if (!array_key_exists("data", $_POST)) die("Data is not given");
        $start = $_POST["data"][0];
        $count = $_POST["data"][1];
        $db = new EventDB();
        applySortOrder($db, $_POST);
        die(json_encode($db->getEvents($start, $count)));
        break;

    case "updateEvents":
        $fetcher = new EventFetcher();
        if (array_key_exists("data", $_POST) && array_key_exists("overwrite", $_POST["data"])) {
            $fetcher->getDB()->setOverwrite(boolval($_POST["data"]["overwrite"]));
        }
        $fetcher->fetchAllEvents();
        $fetcher->saveEvents();
        die(json_encode("Fetching completed. Date/Time: " . date("d-m-Y H:i:s")));
        break;
}

function applySortOrder(EventDB &$db, array $postData)
{
    if (array_key_exists("sort", $postData["data"])) {
        $order = "DB_" . trim($postData["data"]["sort"]);
        $db->setSortOrder(constant("EventDB::{$order}"));
    }
}