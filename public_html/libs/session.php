<?php
/**
This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
?>

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
        session_destroy();
    }
    
    public static function check_login()
    {
        session::init();
        $login_name = session::get('login_name');        
        $cur_signature = session::get('signature');
        
        $signature = build_signature();
        
        if ($login_name == NULL OR $cur_signature != $signature)
        {
            session::destroy();
            header('location:' . SITE_ROOT . 'login.php');
            return FALSE;
        }
        return TRUE;
    }

}

Class Cookie {
    function __construct() {

    }

    public static function set($key, $val,  $expire = 0, $path = NULL)
    {
        if ($path != NULL)
        {
            setcookie($key, $val, $expire, $path);
        }
        else 
        {
            setcookie($key, $val, $expire); //default
        }
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