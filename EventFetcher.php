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

    public function buildUrl(String $pageName)
    {
        return sprintf(Config::FB_URL_GRAPHAPI, $pageName, Config::FB_ACCESSTOKEN);
    }

    /**
     * Fetch events from a Facebook page
     *
     * @param String $pageName Facebook page ID
     * @return array|bool Array of events, or FALSE on failure
     */
    public function fetchEvents(String $pageName)
    {
        $pageData = self::$curlHandler->get($this->buildUrl($pageName));
        $pageData = json_decode($pageData, true);
        if (!array_key_exists("events", $pageData) || !array_key_exists("data", $pageData["events"])) return false;
        $events = [];
        foreach ($pageData["events"]["data"] as $eventData) {
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
            $this->fetchEvents($fbPageName[0]);
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