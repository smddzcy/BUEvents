<?php

class EventDB
{
    private static $db;
    const DB_SORTBYTIME_DESC = 0;
    const DB_SORTBYTIME_ASC = 1;
    const DB_SORTBYNAME_DESC = 2;
    const DB_SORTBYNAME_ASC = 3;
    const DB_SORTBYID_DESC = 4;
    const DB_SORTBYID_ASC = 5;
    private $sortQueries = [
        0 => "ORDER BY start_time DESC",
        1 => "ORDER BY start_time ASC",
        2 => "ORDER BY name DESC",
        3 => "ORDER BY name ASC",
        4 => "ORDER BY id DESC",
        5 => "ORDER BY id ASC",
    ];
    private $sortQuery = 0; // Default sort order is time descending
    private $filterSql = [
        "category" => null,
        "club" => null,
        "date" => null
    ];
    private $overwrite = true;

    public function __construct()
    {
        try {
            self::$db = new PDO(Config::DB_TYPE . ":host=" . Config::DB_HOST . ";port=" . Config::DB_PORT . ";dbname=" . Config::DB_NAME . ";charset=" . Config::DB_CHARSET, Config::DB_USER, Config::DB_PASS);
            self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Database connection error: " . $e->getMessage() . PHP_EOL;
            echo "Stack trace:" . $e->getTrace() . PHP_EOL;
        }
    }

    /**
     * Get filtered events from database
     *
     * @return array|bool Filtered event, or FALSE on failure
     */
    public function getFilteredEvents()
    {
        $events = [];
        $clubs = [];
        $categories = [];
        $categoryCount = 0;
        $clubCount = 0;

        $sql = /** @lang text */
            "SELECT * FROM events WHERE ";
        if (!is_null($this->filterSql["club"])) {
            $clubs = (array)$this->filterSql["club"];
            $clubCount = count($clubs);
            if ($clubCount > 0) {
                $sql .= "(";
                $i = 0;
                for (; $i < count($clubs) - 1; ++$i) {
                    $sql .= "fbpageid = :club{$i} OR ";
                }
                $sql .= "fbpageid = :club{$i}) ";
            }
        }
        if (!is_null($this->filterSql["category"])) {
            if (substr(trim($sql), -5) != "WHERE") $sql .= "AND (";
            $categories = (array)$this->filterSql["category"];
            $categoryCount = count($categories);
            if ($categoryCount > 0) {
                $i = 0;
                for (; $i < count($categories) - 1; ++$i) {
                    $sql .= "category = :cat{$i} OR ";
                }
                $sql .= "category = :cat{$i}) ";
            }
        }
        if (!is_null($this->filterSql["date"])) {
            if (substr(trim($sql), -5) != "WHERE") $sql .= "AND ";
            $dateStart = $this->filterSql["date"]["start"];
            if (empty($dateStart)) $dateStart = date("Y-m-d") . " 00:00:01";
            else if (strpos(" ", $dateStart) === false) { // doesn't include h:m:s precision
                $dateStart .= " 00:00:01";
            }
            $dateEnd = $this->filterSql["date"]["end"];
            if (empty($dateEnd)) $dateEnd = "2100-12-30 23:59:59";
            else if (strpos(" ", $dateEnd) === false) { // doesn't include h:m:s precision
                $dateEnd .= " 23:59:59";
            }
            $sql .= "(start_time BETWEEN :date AND :date2) ";
        }
        $sql .= $this->sortQueries[$this->sortQuery];
        $query = self::$db->prepare($sql);
        for ($i = 0; $i < $clubCount; $i++) {
            $query->bindValue(":club{$i}", $clubs[$i]);
        }
        for ($i = 0; $i < $categoryCount; $i++) {
            $query->bindValue(":cat{$i}", $categories[$i]);
        }
        if (isset($dateStart) && isset($dateEnd)) {
            $query->bindValue(":date", $dateStart);
            $query->bindValue(":date2", $dateEnd);
        }
        if ($query->execute() === false) return false;
        return $this->unserializeArray($query->fetchAll());
    }

    /**
     * Filter events by club, used for getFilteredEvents method
     *
     * @param String $clubName Club name
     */
    public function filterByClub($clubName)
    {
        $this->filterSql["club"] = $clubName;
    }

    /**
     * Filter events by date, used for getFilteredEvents method
     *
     * @param String $start Start date
     * @param String|null $end End date
     */
    public function filterByDate(String $start = null, String $end = null)
    {
        $this->filterSql["date"]["start"] = !is_null($start) ? trim($start) : null;
        $this->filterSql["date"]["end"] = !is_null($end) ? trim($end) : null;
    }

    /**
     * Filter events by category, used for getFilteredEvents method
     *
     * @param String $category Category name
     */
    public function filterByCategory(String $category)
    {
        $this->filterSql["category"] = $category;
    }

    /**
     * Clear applied filters, used for getFilteredEvents method
     *
     */
    public function clearFilters()
    {
        $this->filterSql = [
            "category" => null,
            "club" => null,
            "date" => null
        ];
    }

    /**
     * Get club facebook page ids and page names from database
     *
     * @return array Facebook page id => page name key-val paired array of clubs
     */
    public function getClubs()
    {
        $query = self::$db->query("SELECT fbpageid,fbpagename FROM pages");
        return $query->fetchAll(PDO::FETCH_NUM);
    }

    /**
     * Get category names from database
     *
     * @return array Category names
     */
    public function getCategories()
    {
        $query = self::$db->query("SELECT name FROM categories");
        return $query->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Get events by date
     *
     * @param String $dateStart
     * @param String|null $dateEnd If set, function returns events in $dateStart an $dateEnd interval
     * @return array|bool
     */
    public function getEventsByDate(String $dateStart, String $dateEnd = null)
    {
        $query = self::$db->prepare("SELECT * FROM events WHERE start_time BETWEEN :date AND :date2 {$this->sortQueries[$this->sortQuery]}");
        $query->bindValue(":date", $dateStart);
        if (is_null($dateEnd)) $dateEnd = "2100-12-30 23:59:59";
        else if (strpos(" ", $dateEnd) === false) { // doesn't include h:m:s precision
            $dateEnd .= " 23:59:59";
        }
        $query->bindValue(":date2", $dateEnd);
        if ($query->execute() === false) return false;
        return $this->unserializeArray($query->fetchAll());
    }

    /**
     * Get events by club name (actually, facebook page ID/name)
     *
     * @param string $club Facebook page ID/name
     * @return array|false Events, or FALSE on failure
     */
    public function getEventsByClub(String $club)
    {
        $query = self::$db->prepare("SELECT * FROM events WHERE fbpageid = :club {$this->sortQueries[$this->sortQuery]}");
        if ($query->execute([
                ":club" => $club
            ]) === false
        )
            return false;
        return $this->unserializeArray($query->fetchAll());
    }

    /**
     * Get events by category
     *
     * @param String $category
     * @return array|false Events, or FALSE on failure
     */
    public function getEventsByCategory(String $category)
    {
        $query = self::$db->prepare("SELECT * FROM events WHERE category = :cat {$this->sortQueries[$this->sortQuery]}");
        if ($query->execute([
                ":cat" => $category
            ]) === false
        )
            return false;
        return $this->unserializeArray($query->fetchAll());
    }

    /**
     * Save a single event to database
     *
     * @param string $pageName Facebook page ID/name
     * @param Event $event Event data
     * @return boolean TRUE on success, FALSE on failure
     */
    public function saveEvent(string $pageName, Event $event)
    {
        if ($this->getEvent($event->getId()) !== false) {
            if ($this->overwrite) {
                $query = self::$db->prepare("UPDATE events SET fbpageid = :pageName, name = :name, description = :description, start_time = :start_time, end_time = :end_time, place = :place, owner = :owner, category = :category, cover = :cover, attending_count = :attending_count, ticket_uri = :ticket_uri, timezone = :timezone WHERE id = :id");
            } else {
                return false;
            }
        } else {
            $query = self::$db->prepare("INSERT INTO events (fbpageid,id,name,description,start_time,end_time,place,owner,category,cover,attending_count,ticket_uri,timezone) VALUES (:pageName,:id,:name,:description,:start_time,:end_time,:place,:owner,:category,:cover,:attending_count,:ticket_uri,:timezone)");
        }
        try {
            $res = $query->execute([
                ":pageName" => $pageName,
                ":id" => $event->getId(),
                ":name" => $event->getName(),
                ":description" => $event->getDescription(),
                ":start_time" => $event->getStartTime(),
                ":end_time" => $event->getEndTime(),
                ":place" => serialize($event->getPlace()),
                ":owner" => $event->getOwner(),
                ":category" => "",
                ":cover" => serialize($event->getCover()),
                ":attending_count" => (int)$event->getAttendingCount(),
                ":ticket_uri" => $event->getTicketUri(),
                ":timezone" => $event->getTimezone()
            ]);
        } catch (PDOException $e) {
            echo "Error on database query while saving the event: " . $e->getMessage() . PHP_EOL;
            echo "Stack trace: " . json_encode($e->getTrace(), JSON_PRETTY_PRINT) . PHP_EOL;
        }
        return (isset($res) && $res === true) ? true : false;
    }

    /**
     * Save events to database in bulk
     *
     * @param array $events Key value pairs must be "pagename" => Event
     * @return null
     */
    public function saveEvents(array $events)
    {
        if (empty($events)) return null;
        foreach ($events as $pageName => $pageEvents) {
            foreach ($pageEvents as $event)
                $this->saveEvent($pageName, $event);
        }
    }

    /**
     * Get a single event data by its id
     *
     * @param string $eventID Facebook event id
     * @return array|false Event data, or FALSE on failure
     */
    public function getEvent($eventID)
    {
        $query = self::$db->prepare("SELECT * FROM events WHERE id = :id");
        if ($query->execute([":id" => $eventID]) === false) return false;
        return $query->fetch();
    }

    /**
     * Get all events from database
     *
     * @return array|false Events, or FALSE on failure
     */
    public function getAllEvents()
    {
        $query = self::$db->prepare("SELECT * FROM events {$this->sortQueries[$this->sortQuery]}");
        if ($query->execute() === false) return false;
        return $this->unserializeArray($query->fetchAll());
    }

    /**
     * @param int $start
     * @param int $count
     * @return array|bool
     */
    public function getEvents(int $start, int $count)
    {
        $query = self::$db->prepare("SELECT * FROM events {$this->sortQueries[$this->sortQuery]} LIMIT :start,:count");
        $query->bindValue(":start", $start, PDO::PARAM_INT);
        $query->bindValue(":count", $count, PDO::PARAM_INT);
        if ($query->execute() === false) return false;
        return $this->unserializeArray($query->fetchAll());
    }

    /**
     * Delete an event from database
     *
     * @param string $eventID Facebook event id
     * @return boolean TRUE on success, FALSE on failure
     */
    public function deleteEvent($eventID)
    {
        if ($this->getEvent($eventID) === false) return false;
        $query = self::$db->prepare("DELETE FROM events WHERE id = :id");
        return $query->execute([":id" => $eventID]);
    }

    /**
     * Get total event count from database
     *
     * @return int Total event count, or -1 on FAILURE
     */
    public function getTotalEventCount()
    {
        $query = self::$db->prepare("SELECT COUNT(*) FROM events");
        if ($query->execute() === false) return -1;
        $res = array_values($query->fetch());
        if (!empty($res))
            return (int)$res[0];
        return 0;
    }

    /**
     * Get an event from database with its name
     *
     * @param String $name Event name, full or part of it, or FALSE on failure
     * @return array|false Array of results, or FALSE on failure
     */
    public function searchEventByName(String $name)
    {
        $query = self::$db->prepare("SELECT * FROM events WHERE name LIKE :name {$this->sortQueries[$this->sortQuery]}");
        $query->bindValue(":name", "%{$name}%");
        if ($query->execute() === false) return false;
        return $this->unserializeArray($query->fetchAll());
    }

    /**
     * Set sort order on database queries, default order is time descending
     *
     * @param int $sortOrder Choose one from constants, ex.: setSortOrder(EventDB::DB_SORTBYTIME_ASC);
     */
    public function setSortOrder(int $sortOrder)
    {
        if (array_key_exists($sortOrder, $this->sortQueries))
            $this->sortQuery = $sortOrder;
    }

    /**
     * Unserialize every possible element on an array
     *
     * @param array $a
     * @return array
     */
    private function unserializeArray(array $a)
    {
        foreach ($a as $i => &$el) {
            if (is_array($el)) $el = $this->unserializeArray($el);
            else if ($this->isSerial($el)) {
                $a[$i] = unserialize($el);
            }
        }
        return $a;
    }

    /**
     * Check if a string is serialized
     *
     * @param string $string
     * @return bool
     */
    public function isSerial($string)
    {
        return (@unserialize($string) !== false);
    }

    /**
     * @param bool $overwrite If true, save methods update existing event data on the database
     *
     */
    public function setOverwrite(boolean $overwrite)
    {
        $this->overwrite = $overwrite;
    }
}