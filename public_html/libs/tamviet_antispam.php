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

class tamviet_antispam
{

    const HASH_KEY       = ')*^%gM;.?';
    const PREFIX         = 'antispam';
    const SESS_TOKEN     = 'antispam_token';
    const SESS_HONEY_POT = 'antispam_honey_pot';

    static protected $_token;
    static protected $_honey_pot;
    static protected $_css_class;

    static function get_token()
    {
        @session_start();
        if (!self::$_token)
        {
            self::$_token = (isset($_SESSION[self::SESS_TOKEN]) && $_SESSION[self::SESS_TOKEN]) ?
                    $_SESSION[self::SESS_TOKEN] :
                    self::_encrypt(uniqid());
            $_SESSION[self::SESS_TOKEN] = self::$_token;
        }
        return self::$_token;
    }

    static function get_honey_pot()
    {
        @session_start();
        if (!self::$_honey_pot)
        {
            self::$_honey_pot = (isset($_SESSION[self::SESS_HONEY_POT]) && $_SESSION[self::SESS_HONEY_POT]) ?
                    $_SESSION[self::SESS_HONEY_POT] :
                    substr(self::_encrypt(uniqid()), 0, 8);
            $_SESSION[self::SESS_HONEY_POT] = self::$_honey_pot;
        }
        return self::$_honey_pot;
    }

    protected static function _encrypt($str)
    {
        return md5(self::HASH_KEY . $str);
    }

    protected static function _get_css_class()
    {
        if (!self::$_css_class)
        {
            self::$_css_class = 'c' . substr(self::get_token(), rand(1, 3), rand(1, 3)) . substr(self::get_token(), rand(1, 3), rand(1, 3));
        }
        return self::$_css_class;
    }

    static function get_antispam_fields()
    {
        ob_start();
        $class = self::_get_css_class();
        ?>
        <style>
            <?php echo ".{$class}" ?>{display:none;}
        </style>
        <input type="text" class="<?php echo $class ?>" name="<?php echo self::get_token() ?>" value="<?php echo rand() ?>"/>
        <input type="text" class="<?php echo $class ?>" name="<?php echo self::get_honey_pot() ?>" value=""/>
        <?php
        return ob_get_clean();
    }

    static function validate()
    {
        @session_start();
        $token     = isset($_REQUEST[self::get_token()]) ? true : false;
        $honey_pot = isset($_REQUEST[self::get_honey_pot()]) && $_REQUEST[self::get_honey_pot()] ? false : true;

        var_dump($token);
        var_dump($honey_pot);
        $_SESSION[self::SESS_TOKEN] = null;
        $_SESSION[self::SESS_HONEY_POT] = null;

        return ($token && $honey_pot);
    }

}