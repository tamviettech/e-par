<?php
/**
 // File name   : session.php
 // Version     : 1.0.0.1
 // Begin       : 2012-12-01
 // Last Update : 2010-12-25
 // Author      : TamViet Technology, Ha Noi, Viet Nam. http://www.tamviettech.vn
 // License     : GNU-LGPL v3 (http://www.gnu.org/copyleft/lesser.html)
 // -------------------------------------------------------------------
 //Copyright (C) 2012-2013  TamViet Technology, Ha Noi, Viet Nam. http://www.tamviettech.vn

 // E-PAR is free software: you can redistribute it and/or modify it
 // under the terms of the GNU Lesser General Public License as
 // published by the Free Software Foundation, either version 3 of the
 // License, or (at your option) any later version.
 //
 // E-PAR is distributed in the hope that it will be useful, but
 // WITHOUT ANY WARRANTY; without even the implied warranty of
 // MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 // See the GNU Lesser General Public License for more details.
 //
 // See LICENSE.TXT file for more information.
 */

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