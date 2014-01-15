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

//Frag function

function replace_bad_char($str)
{
    $str = stripslashes($str);
    $str = str_replace("&", '&amp;', $str);
    $str = str_replace('<', '&lt;', $str);
    $str = str_replace('>', '&gt;', $str);
    $str = str_replace('"', '&#34;', $str);
    $str = str_replace("'", '&#39;', $str);

    return $str;
}

/**
 * 
 * @return bool Giờ hiện tại trong giờ hành  chính không
 */
function in_office_hour()
{
    $morning_start   = date_create_from_format('H:i', _CONST_MORNING_BEGIN_WORKING_TIME);
    $morning_end     = date_create_from_format('H:i', _CONST_MORNING_END_WORKING_TIME);
    $afternoon_start = date_create_from_format('H:i', _CONST_AFTERNOON_BEGIN_WORKING_TIME);
    $afternoon_end   = date_create_from_format('H:i', _CONST_AFTERNOON_END_WORKING_TIME);
    date_add($afternoon_end, date_interval_create_from_date_string('1 hours'));
    $now             = new DateTime('now');

    $morning_start->d   = $morning_end->d     = $afternoon_start->d = $afternoon_end->d   = date('d');
    $morning_start->m   = $morning_end->m     = $afternoon_start->m = $afternoon_end->m   = date('m');
    $morning_start->y   = $morning_end->y     = $afternoon_start->y = $afternoon_end->y   = date('Y');

    return (
            ($morning_start <= $now && $morning_end >= $now) OR //Trong buổi sáng
            ($afternoon_start <= $now && $afternoon_end >= $now) //Trong buổi chiều
            );
}

//end func replace_bad_char

function create_single_xml_node($name, $value, $cdata = FALSE)
{
    $node = '<' . $name . '>';
    $node .= ($cdata) ? '<![CDATA[' . $value . ']]>' : $value;
    $node .= '</' . $name . '>';

    return $node;
}

//end func create_single_xml_node

function hidden($name, $value = '')
{
    if (strpos($value, '"') !== FALSE)
    {
        return '<input type="hidden" name="' . $name . '" id="' . $name . '" value=\'' . $value . '\' />';
    }
    else
    {
        return '<input type="hidden" name="' . $name . '" id="' . $name . '" value="' . $value . '" />';
    }
}

function page_calc(&$v_start, &$v_end)
{
    //Luu dieu kien loc
    $v_page          = isset($_POST['sel_goto_page']) ? replace_bad_char($_POST['sel_goto_page']) : 1;
    $v_rows_per_page = isset($_POST['sel_rows_per_page']) ? replace_bad_char($_POST['sel_rows_per_page']) : _CONST_DEFAULT_ROWS_PER_PAGE;

    $v_start = $v_rows_per_page * ($v_page - 1) + 1;
    $v_end   = $v_start + $v_rows_per_page - 1;
}

function is_id_number($id)
{
    return (preg_match('/^\d*$/', trim($id)) == 1);
}

function get_post_var($html_object_name, $default_value = '', $is_replace_bad_char = TRUE)
{
    $var = isset($_POST[$html_object_name]) ? $_POST[$html_object_name] : $default_value;

    if ($is_replace_bad_char && !is_array($var))
    {
        return replace_bad_char($var);
    }

    return $var;
}

function get_request_var($html_object_name, $default_value = '', $is_replace_bad_char = TRUE)
{
    $var = isset($_REQUEST[$html_object_name]) ? $_REQUEST[$html_object_name] : $default_value;

    if ($is_replace_bad_char)
    {
        return replace_bad_char($var);
    }

    return $var;
}

function get_filter_condition($arr_html_object_name = array())
{
    $arr_filter = array();
    foreach ($arr_html_object_name as $v_html_object_name)
    {
        $arr_filter[$v_html_object_name] = get_request_var($v_html_object_name);
    }

    return $arr_filter;
}

function get_role($task_code)
{
    return trim(preg_replace('/[A-Z0-9_]*[:]+/', '', $task_code));
}

function xml_remove_declaration($xml_string)
{
    return trim(preg_replace('/\<\?xml(.*)\?\>/', '', $xml_string));
}

function xml_add_declaration($xml_string, $utf8_encoding = TRUE)
{
    $xml_string = xml_remove_declaration($xml_string);

    if ($utf8_encoding)
    {
        return '<?xml version="1.0" encoding="UTF-8"?>' . $xml_string;
    }

    return '<?xml version="1.0" standalone="yes"?>' . $xml_string;
}

function get_xml_value($dom, $xpath)
{
    $r = $dom ? $dom->xpath($xpath) : array();
    if (isset($r[0]))
    {
        return strval($r[0]);
    }

    return NULL;
}

define('XPATH_STRING', 10);
define('XPATH_DOM', 20);
define('XPATH_ARRAy', 30);

/**
 * Ánh xạ của SimpleXMLElement::xpath nhưng kiểm tra điều kiện để không gây FALTAL ERROR
 * @param \SimpleXMLElement $dom
 * @param string $xpath
 * @param mixed $return Kiểu dữ liệu trả về
 * @return \SimpleXMLElement
 */
function xpath($dom, $xpath, $return = XPATH_ARRAy)
{
    $dom OR trigger_error('xpath: $dom is not instance of SimpleXMLElement', E_USER_WARNING);
    $r = $dom ? $dom->xpath($xpath) : array();
    switch ($return)
    {
        case XPATH_STRING:
            return isset($r[0]) ? strval($r[0]) : '';
            break;
        case XPATH_DOM:
            return isset($r[0]) ? $r[0] : new SimpleXMLElement('<root/>');
            break;
        case XPATH_ARRAy:
        default:
            return $r ? $r : array();
    }
}

/**
 * Tính số ngày chênh lệch giữa 2 ngày
 * @param string $begin_date Ngay bat dau, dang in yyyy-mm-dd
 * @param string $end_date Ngay ket thuc, dang yyyy-mm-dd
 * @return Int
 */
function days_diff($begin_date_yyyymmdd, $end_date_yyyymmdd)
{
    $b = date_create($begin_date_yyyymmdd);
    $e = date_create($end_date_yyyymmdd);

    $interval = date_diff($b, $e);
    return intval($interval->format('%R%a'));
}

function is_past_date($date_yyyymmdd)
{
    $today = Date('y-m-d');
    return days_diff($date_yyyymmdd, $today) > 0;
}

function is_future_date($date_yyyymmdd)
{
    $today = Date('y-m-d');
    return days_diff($date_yyyymmdd, $today) < 0;
}

function check_permission($function_code, $app_code)
{
    @Session::init();
    if (Session::get('is_admin') == 1)
    {
        return true;
    }
    $function_code = strtoupper($app_code . '::' . $function_code);
    return in_array($function_code, Session::get('arr_function_code'));
}

/**
 * Lay gia tri cho boi dau hieu mau
 *
 * @param string $html_content Xau can lay
 * @param string dau hieu bat dau $bp
 * @param string dau hieu ket thuc $ep
 * @return string xau thu duoc
 */
function get_value_by_pattern($html_content, $bp, $ep)
{
    preg_match("/$bp(.+)$ep/eUim", $html_content, $arr_matches);
    if (count($arr_matches) >= 1)
    {
        return ($arr_matches[1]);
    }
    else
    {
        return '';
    }
}

/**
 * Xoa het cac ky tu dieu khien trong doan html text
 *
 * 		o Dau xuong dong
 * 		o Dong moi
 * 		o Cac dau cach thua
 *
 * @param string $text Xau ky tu vao
 * @return string Xau thu duoc sau khi da xoa het ky tu dieu khien
 * @author Ngo Duc Lien
 */
function delete_control_characters($text)
{
    $ret_text = preg_replace(
            array(
        '/\s+/u'      // Any space(s)
        , '/^\s+/u'      // Any space(s) at the beginning
        , '/\s+$/u'      // Any space(s) at the end
        , '/\n+/u'      // Any New line(s)
        , '/\r+/u'      // Any Rn(s)
            ), array(
        ' '    // ... one space
        , ''    // ... nothing
        , ''     // ... nothing
        , ''     // ... nothing
        , ''     // ... nothing
            ), $text);
    return $ret_text;
}

function parse_boolean($str)
{
    if ($str == '')
    {
        return FALSE;
    }
    switch (strtolower($str))
    {
        case 'true':
        case '1':
        case 'yes':
        case 'y':
            return TRUE;
    }

    return FALSE;
}

function toStrictBoolean($_val, $_trueValues = array('yes', 'y', 'true'), $_forceLowercase = true)
{
    if (is_string($_val))
    {
        return (in_array(
                        ($_forceLowercase ? strtolower($_val) : $_val)
                        , $_trueValues)
                );
    }
    else
    {
        return (boolean) $_val;
    }
}

function write_xml_file($v_xml_string, $v_xml_file_path)
{
    $ok           = TRUE;
    $v_error_code = 0; //Khong loi; 1: loi khong welform, 2: Khong ghi duoc file
    $v_message    = 'Cập nhật dữ liệu thất bại!. ';
    //Kiem tra xml welform
    $dom_flow     = @simplexml_load_string($v_xml_string);
    if ($dom_flow === FALSE)
    {
        return 1;
    }

    //Ghi file
    if ($ok)
    {
        $v_dir = dirname($v_xml_file_path);
        if (!is_dir($v_dir))
        {
            @mkdir($v_dir);
        }
        $r = @file_put_contents($v_xml_file_path, $v_xml_string);
        if ($r === FALSE OR $r === 0)
        {
            return 2;
        }
    }

    return 0;
}

function check_htacces_file()
{
    if (file_exists(SERVER_ROOT . '.htaccess'))
    {
        return TRUE;
    }
    return FALSE;
}

function build_url($url)
{
    if (check_htacces_file())
    {
        return $url;
    }

    return 'index.php?url=' . $url;
}

function get_session_token()
{
    @session_start();
    return md5(session_id());
}

function validate_session_token($token)
{
    @session_start();
    return (md5(session_id()) == $token) ? true : false;
}

function check_user_token()
{
    if (get_request_var('user_token','user_token') === session::get('user_token'))
    {
        return TRUE;
    }
    
    return FALSE;
}

function deny_bad_user_token()
{
    if (! check_user_token())
    {
        require_once (SERVER_ROOT . '403.php');
        die();
    }
}

function deny_bad_http_referer()
{
    $v_http_referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/';
    if ( (DEBUG_MODE < 1) &&  ! is_sub_string($v_http_referer, FULL_SITE_ROOT))
    {
        require_once SERVER_ROOT . '403.php';
        exit;
    }
}