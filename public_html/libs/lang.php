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
class Lang
{
    private static  $_dom_lang;

    public static function load_lang($lang)
    {
        $v_xml_lang_file = SERVER_ROOT . 'langs' . DS . $lang . '.xml';
        if (file_exists($v_xml_lang_file))
        {
            self::$_dom_lang  = simplexml_load_file($v_xml_lang_file);
        }
        else
        {
            self::$_dom_lang  = simplexml_load_string('<lang/>');
        }
    }

    public static function translate($text)
    {
        $xpath = "//text[@name='$text'][last()]/@value";
        $r = self::$_dom_lang->xpath($xpath);
        return (sizeof($r) >0) ? $r[0] : ucfirst($text);
    }
}
function __($text)
{
    $text = trim($text);
    return Lang::translate($text);
}