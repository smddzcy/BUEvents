<?php

class Config
{
    const BASEDIR = "/Library/WebServer/Documents/BUEvents";
    const DEFAULT_TIMEZONE = "Europe/Istanbul";

    /**
     * DB Info
     */
    const DB_HOST = "localhost";
    const DB_PORT = 3306;
    const DB_USER = "root";
    const DB_PASS = "1233211";
    const DB_NAME = "buevents";
    const DB_TYPE = "mysql";
    const DB_CHARSET = "utf8";

    /**
     * Facebook stuff
     */

    // TODO: Implement auto access token generator
    const FB_APPID = "";
    const FB_APPSECRET = "";
    const FB_ACCESSTOKEN = "ACCESS-TOKEN";

    /**
     * Don't change if the app is OK
     */
    const FB_URL_GRAPHAPI = "https://graph.facebook.com/v2.5/%s?fields=events{name,description,start_time,end_time,place,timezone,cover,owner,ticket_uri,attending_count,id,category}&access_token=%s";

    public static function load($className)
    {
        include_once "./{$className}.php";
    }
}

spl_autoload_register("Config::load");