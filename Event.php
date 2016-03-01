<?php

class Event
{
    private $name, $description, $start_time, $end_time, $ticket_uri;
    // @var array [name, [city, country, latitude, longitude, street, zip...]]
    private $place;
    // @var string
    private $owner = ["name" => null]; // Initiated as an array, converted to string later on to contain only name of the owner
    // @var array [offset_x, offset_y, source]
    private $cover;
    // todo: set default cover
    private $attending_count = -1;
    private $timezone = "Europe/Istanbul";
    private $id = -1;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return array
     */
    public function getCover()
    {
        return $this->cover;
    }

    /**
     * @return string
     */
    public function getStartTime()
    {
        return $this->start_time;
    }

    /**
     * @return string
     */
    public function getEndTime()
    {
        return $this->end_time;
    }

    /**
     * @return array
     */
    public function getPlace()
    {
        return $this->place;
    }

    /**
     * @return string
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @return string
     */
    public function getTimezone()
    {
        return $this->timezone;
    }

    /**
     * @return string
     */
    public function getTicketUri()
    {
        return $this->ticket_uri;
    }

    /**
     * @return int
     */
    public function getAttendingCount()
    {
        return $this->attending_count;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public function __construct(array $eventData)
    {
        if (empty($eventData)) return;
        foreach ($eventData as $k => $v) {
            if (property_exists($this, $k)) {
                $this->$k = $v;
            }
        }
        $this->owner = $this->owner["name"];
    }
}