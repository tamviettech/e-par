<?php

class Session {

    function __construct() {
        @session_start();
    }

    public static function init(){
        @session_start();
    }

    public static function get($key){
        return isset($_SESSION[$key]) ? $_SESSION[$key]: null ;
    }

    public static function set($key, $val){
        $_SESSION[$key] = $val;
    }

    public static function destroy(){
        @session_destroy();
    }

}

Class Cookie {
    function __construct() {

    }

    public static function set($key, $val)
    {
        setcookie($key, $val);
    }

    public static function get($key)
    {
        return isset($_COOKIE[$key]) ? $_COOKIE[$key] : NULL;;
    }
    public static function destroy($key=''){
        if ($key != '')
        {
            unset($_COOKIE[$key]);
        }
        else
        {
            $_COOKIE = array();
        }
    }
}