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

    // For now, generate manually:
    // https://developers.facebook.com/tools/explorer/232079303795149
    // http://www.slickremix.com/facebook-60-day-user-access-token-generator/
    const FB_APPID = "";
    const FB_APPSECRET = "";
    const FB_ACCESSTOKEN = "EAADTEyx2Yc0BAPpqbzzAqMWCCwZBzy7IMpZCJ7L4S9pVQfWy7FYJsGeppyKrKYHQ3jd2xanrMlsU4A81wWjYWZAb1klPJyx67rsIXXoS6ZAlXZCYCbHUtZBQE0ZB97VLNYEbOsFdarJ6nxbeZBSUGZCVM12SGOZBKuNZBwZD";
    const FB_EVENT_SEARCHQUERIES = ["bogazici universite", "bogazici universitesi guney kampus", "bogazici universitesi kuzey kampus", "ibrahim bodur oditoryumu bogazici", "bogazici universitesi garanti", "albert long hall", "bogazici universitesi demir demirgil", "bogazici universitesi kennedy", "bümed"];
    /**
     * Don't change if the app is OK
     */
    const FB_GRAPHAPI = "https://graph.facebook.com/v2.5/%s?fields=events{name,description,start_time,end_time,place,timezone,cover,owner,ticket_uri,attending_count,id,category}&access_token=%s";
    const FB_GRAPHAPI_SEARCH = "https://graph.facebook.com/search?q=%s&type=event&fields=name,description,start_time,end_time,place,timezone,cover,owner,ticket_uri,attending_count,id,category&access_token=%s";

    public static function load($className)
    {
        include_once "./{$className}.php";
    }
}

spl_autoload_register("Config::load");