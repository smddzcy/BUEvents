<?php

require_once "Config.php";

class EventFetcher
{
    protected static $curlHandler;
    protected static $db;
    private $events = [];

    public function __construct()
    {
        if (date_default_timezone_set(Config::DEFAULT_TIMEZONE) === false) {
            throw new Exception("Timezone is not valid, change it in Config.php");
        }
        self::$curlHandler = new CurlRequest();
        self::$db = new EventDB();
    }

    /**
     * Builds Facebook Graph API URL for getting events of a page
     *
     * @param string $pageName Page name
     * @return string URL
     */
    public function buildUrlByPage(string $pageName)
    {
        return sprintf(Config::FB_GRAPHAPI, urlencode($pageName), Config::FB_ACCESSTOKEN);
    }

    /**
     * Builds Facebook Graph API URL for searching an event
     *
     * @param string $searchQuery Search query
     * @return string URL
     */
    public function buildUrlBySearch(string $searchQuery)
    {
        return sprintf(Config::FB_GRAPHAPI_SEARCH, urlencode($searchQuery), Config::FB_ACCESSTOKEN);
    }

    /**
     * Fetch events from a Facebook page
     *
     * @param string $pageName Facebook page ID
     * @return array|bool Array of events, or FALSE on failure
     */
    public function fetchEventsByPage(string $pageName)
    {
        $eventData = self::$curlHandler->get($this->buildUrlByPage($pageName));
        $eventData = json_decode($eventData, true);
        if (array_key_exists("events", $eventData)) $eventData = $eventData["events"];

        return $this->processEvents($pageName, $eventData);
    }

    /**
     * Fetch events from Facebook with a search query
     *
     * @param string $searchQuery Search query
     * @return array|bool Array of events, or FALSE on failure
     */
    public function fetchEventsBySearch(string $searchQuery)
    {
        $eventData = self::$curlHandler->get($this->buildUrlBySearch($searchQuery));
        $eventData = json_decode($eventData, true);
        while (array_key_exists("paging", $eventData) && array_key_exists("next", $eventData["paging"])) {
            $nextData = json_decode(self::$curlHandler->get($eventData["paging"]["next"]), true);

            if (array_key_exists("data", $nextData)) {
                $eventData["data"] = array_merge($eventData["data"], $nextData["data"]);
            }

            if (array_key_exists("paging", $nextData)) {
                $eventData["paging"] = $nextData["paging"];
            } else {
                unset($eventData["paging"]);
            }
        }
        $eventData["data"] = array_unique($eventData["data"], SORT_REGULAR); // better do it here than on database

        return $this->processEvents("Unknown Host", $eventData);
    }

    /**
     * @param string $pageName
     * @param $eventData
     * @return array|bool
     */
    public function processEvents(string $pageName, $eventData)
    {
        if (!array_key_exists("data", $eventData)) return false; // no data

        $events = [];
        foreach ($eventData["data"] as $eventData) {
            // Filter out the events from other countries
            $hasCountryData = array_key_exists("place", $eventData) &&
              array_key_exists("location", $eventData["place"]) &&
              array_key_exists("country", $eventData["place"]["location"]);
            if($hasCountryData && $eventData["place"]["location"]["country"] != "Turkey") {
              continue;
            }

            // Set start and end times of the event, if they exist
            if (array_key_exists("start_time", $eventData)) {
                $eventData["start_time"] = date("Y-m-d H:i:s", strtotime($eventData["start_time"]));
            }
            if (array_key_exists("end_time", $eventData)) {
                $eventData["end_time"] = date("Y-m-d H:i:s", strtotime($eventData["end_time"]));
            }

            $event = new Event($eventData);
            $events[] = $event;
        }
        $this->events[$pageName] = $events;
        return $events;
    }

    /**
     * Fetch all events from all known clubs' Facebook pages
     *
     */
    public function fetchAllEvents()
    {
        $fbPageNames = self::$db->getClubs();
        foreach ($fbPageNames as $fbPageName) {
            $this->fetchEventsByPage($fbPageName[0]);
        }
        foreach (Config::FB_EVENT_SEARCHQUERIES as $searchQuery) {
            $this->fetchEventsBySearch($searchQuery);
        }
        return $this->events;
    }

    /**
     * Save fetched events to database
     *
     */
    public function saveEvents()
    {
        self::$db->saveEvents($this->events);
    }

    /**
     * Clear fetched events from memory
     *
     */
    public function clearEvents()
    {
        $this->events = [];
    }

    /**
     * Get database object
     *
     * @return EventDB
     */
    public function getDB()
    {
        return self::$db;
    }

}
