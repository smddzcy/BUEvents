<?php

class Config
{
    const BASEDIR = "/Library/WebServer/Documents/BUEvents";

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

    const FB_APPID = "232079303795149";
    const FB_APPSECRET = "e94707b04560ef567adc9666dc09ca63";
    const FB_ACCESSTOKEN = "CAADTEyx2Yc0BAOdsreOVS3CnN3ZC34eggW86cHhGOjzWbp98TkLixZCpTv5H47XUIBmk5RJXFWCmD9UW8Q9c1FFm7pIBCJT6IjE7JZAKpiZAxgJZCes7E3lOroG3UhdpK1PkZCcdQ1w8lcaNKV9HZBLBSyQeTDAUNHmivguUfnqy6axMMkm0wviuK3nADeYRtsZD";

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