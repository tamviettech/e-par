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
function call_soap_service($client,$function,$arr_param = array(),$recursive = false,$index=0)
{
    $result = $client->__soapCall($function, $arr_param);

    if($result == true)
    {
        return $result;
    }
    else if($index <= 5 && $recursive == true && $result !== true)
    {
        $index++;
        return call_soap_service($client,$function,$arr_param,$recursive,$index);
    }
    else
    {
        return false;
    }

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
    return TRUE;
    /*
    $v_http_referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/';
    if ( (DEBUG_MODE < 1) &&  ! is_sub_string($v_http_referer, FULL_SITE_ROOT))
    {
        require_once SERVER_ROOT . '403.php';
        exit;
    }
    */
}

/**
 * Verifies that an email is valid.
 *
 * @param string $email Email address to verify.
 * @return string|bool Either false or the valid email address.
 */
function is_email( $email ) 
{
    $atom = '[-a-z0-9!#$%&\'*+/=?^_`{|}~]';    // allowed characters for part before "at" character
    $domain = '([a-z]([-a-z0-9]*[a-z0-9]+)?)'; // allowed characters for part after "at" character

    $regex = '^' . $atom . '+' .         // One or more atom characters.
    '(\.' . $atom . '+)*'.               // Followed by zero or more dot separated sets of one or more atom characters.
    '@'.                                 // Followed by an "at" character.
    '(' . $domain . '{1,63}\.)+'.        // Followed by one or max 63 domain characters (dot separated).
    $domain . '{2,63}'.                  // Must be followed by one set consisting a period of two
    '$';                                 // or max 63 domain characters.
    if (eregi($regex, $email))
    {
        return TRUE;
    }
    return FALSE;
}


// Fixes the encoding to uf8 
function fixEncoding($in_str)
{
    $cur_encoding = mb_detect_encoding($in_str);
    if ($cur_encoding == "UTF-8" && mb_check_encoding($in_str, "UTF-8"))
    {
        return $in_str;
    }
    else
    {
        return utf8_encode($in_str);
    }
}
// fixEncoding 

function cookie_password_encode($password_string)
{
    if ( ! defined('CONST_COOKIE_PASSWORD_ENCODE_KEY'))
    {
        define('CONST_COOKIE_PASSWORD_ENCODE_KEY','tamviettech.vn');
    }
    
    return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5(CONST_COOKIE_PASSWORD_ENCODE_KEY), $password_string, MCRYPT_MODE_CBC, md5(md5(CONST_COOKIE_PASSWORD_ENCODE_KEY))));
}

function cookie_password_decode($encrypted_password_string)
{
    if ( ! defined('CONST_COOKIE_PASSWORD_ENCODE_KEY'))
    {
        define('CONST_COOKIE_PASSWORD_ENCODE_KEY','tamviettech.vn');
    }
    
    return rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5(CONST_COOKIE_PASSWORD_ENCODE_KEY), base64_decode($encrypted_password_string), MCRYPT_MODE_CBC, md5(md5(CONST_COOKIE_PASSWORD_ENCODE_KEY))), "\0");
}

/**
 * Rut gon mot chuoi theo so ky tu can hien thi
 * @param string $text 
 * @param int $word_count So ky tu con lai sau khi rut gon
 * @return string Mang da rut gon
 */
function get_leftmost_words($text, $word_count)
{
    $s            = chr(32);
    $text         = preg_replace('/\s+/u', $s, $text);
    $arr_all_word = explode($s, $text);
    $ret          = '';
    if(count($arr_all_word) > $word_count)
    {
        for ($i = 0; $i < $word_count - 1; $i++)
        {
            if (isset($arr_all_word[$i]))
            {
                $ret .= $arr_all_word[$i] . $s;
            }
            else
            {
                return $ret;
            }
        }
        return $ret . '...';
    }
    else
    {
        return $text;
    }

}

/*
 * Tao chuoi signature chong loi Session Hijacking
 */
function build_signature()
{
    return md5($_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']);
}


function unicode_to_composite($str){
    ///unicode
    $unicode= preg_split( "/\,/", 'á,à,ả,ã,ạ,ă,ắ,ằ,ẳ,ẵ,ặ,â,ấ,ầ,ẩ,ẫ,ậ,é,è,ẻ,ẽ,ẹ,ê,ế,ề,ể,ễ,ệ,í,ì,ỉ,ĩ,ị,ó,ò,ỏ,õ,ọ,ô,ố,ồ,ổ,ỗ,ộ,ơ,ớ,ờ,ở,ỡ,ợ,ú,ù,ủ,ũ,ụ,ư,ứ,ừ,ử,ữ,ự,ý,ỳ,ỷ,ỹ,ỵ,đ,Á,À,Ả,Ã,Ạ,Ă,Ắ,Ằ,Ẳ,Ẵ,Ặ,Â,Ấ,Ầ,Ẩ,Ẫ,Ậ,É,È,Ẻ,Ẽ,Ẹ,Ê,Ế,Ề,Ể,Ễ,Ệ,Í,Ì,Ỉ,Ĩ,Ị,Ó,Ò,Ỏ,Õ,Ọ,Ô,Ố,Ồ,Ổ,Ỗ,Ộ,Ơ,Ớ,Ờ,Ở,Ỡ,Ợ,Ú,Ù,Ủ,Ũ,Ụ,Ư,Ứ,Ừ,Ử,Ữ,Ự,Ý,Ỳ,Ỷ,Ỹ,Ỵ,Đ');

    //unicode to hop
    $composite 	= preg_split( "/\,/", 'á,à,ả,ã,ạ,ă,ắ,ằ,ẳ,ẵ,ặ,â,ấ,ầ,ẩ,ẫ,ậ,é,è,ẻ,ẽ,ẹ,ê,ế,ề,ể,ễ,ệ,í,ì,ỉ,ĩ,ị,ó,ò,ỏ,õ,ọ,ô,ố,ồ,ổ,ỗ,ộ,ơ,ớ,ờ,ở,ỡ,ợ,ú,ù,ủ,ũ,ụ,ư,ứ,ừ,ử,ữ,ự,ý,ỳ,ỷ,ỹ,ỵ,đ,Á,À,Ả,Ã,Ạ,Ă,Ắ,Ằ,Ẳ,Ẵ,Ặ,Â,Ấ,Ầ,Ẩ,Ẫ,Ậ,É,È,Ẻ,Ẽ,Ẹ,Ê,Ế,Ề,Ể,Ễ,Ệ,Í,Ì,Ỉ,Ĩ,Ị,Ó,Ò,Ỏ,Õ,Ọ,Ô,Ố,Ồ,Ổ,Ỗ,Ộ,Ơ,Ớ,Ờ,Ở,Ỡ,Ợ,Ú,Ù,Ủ,Ũ,Ụ,Ư,Ứ,Ừ,Ử,Ữ,Ự,Ý,Ỳ,Ỷ,Ỹ,Ỵ,Đ');

    foreach( $unicode as $key => $val) $ret_str[$val]= $composite[$key];

    return strtr( $str, $ret_str);
}


function utf8_to_composite($str){
    ///unicode
    $utf8 = preg_split( "/\,/", 'Ã¡,Ã ,áº£,Ã£,áº¡,Äƒ,áº¯,áº±,áº³,áºµ,áº·,Ã¢,áº¥,áº§,áº©,áº«,áº­,Ã©,Ã¨,áº»,áº½,áº¹,Ãª,áº¿,á»,á»ƒ,á»…,á»‡,Ã­,Ã¬,á»‰,Ä©,á»‹,Ã³,Ã²,á»,Ãµ,á»,Ã´,á»‘,á»“,á»•,á»—,á»™,Æ¡,á»›,á»,á»Ÿ,á»¡,á»£,Ãº,Ã¹,á»§,Å©,á»¥,Æ°,á»©,á»«,á»­,á»¯,á»±,Ã½,á»³,á»·,á»¹,á»µ,Ä‘,Ã,Ã€,áº¢,Ãƒ,áº ,Ä‚,áº®,áº°,áº²,áº´,áº¶,Ã‚,áº¤,áº¦,áº¨,áºª,áº¬,Ã‰,Ãˆ,áºº,áº¼,áº¸,ÃŠ,áº¾,á»€,á»‚,á»„,á»†,Ã,ÃŒ,á»ˆ,Ä¨,á»Š,Ã“,Ã’,á»Ž,Ã•,á»Œ,Ã”,á»,á»’,á»”,á»–,á»˜,Æ ,á»š,á»œ,á»ž,á» ,á»¢,Ãš,Ã™,á»¦,Å¨,á»¤,Æ¯,á»¨,á»ª,á»¬,á»®,á»°,Ã,á»²,á»¶,á»¸,á»´,Ä');

    //unicode to hop
    $composite 	= preg_split( "/\,/", 'á,à,ả,ã,ạ,ă,ắ,ằ,ẳ,ẵ,ặ,â,ấ,ầ,ẩ,ẫ,ậ,é,è,ẻ,ẽ,ẹ,ê,ế,ề,ể,ễ,ệ,í,ì,ỉ,ĩ,ị,ó,ò,ỏ,õ,ọ,ô,ố,ồ,ổ,ỗ,ộ,ơ,ớ,ờ,ở,ỡ,ợ,ú,ù,ủ,ũ,ụ,ư,ứ,ừ,ử,ữ,ự,ý,ỳ,ỷ,ỹ,ỵ,đ,Á,À,Ả,Ã,Ạ,Ă,Ắ,Ằ,Ẳ,Ẵ,Ặ,Â,Ấ,Ầ,Ẩ,Ẫ,Ậ,É,È,Ẻ,Ẽ,Ẹ,Ê,Ế,Ề,Ể,Ễ,Ệ,Í,Ì,Ỉ,Ĩ,Ị,Ó,Ò,Ỏ,Õ,Ọ,Ô,Ố,Ồ,Ổ,Ỗ,Ộ,Ơ,Ớ,Ờ,Ở,Ỡ,Ợ,Ú,Ù,Ủ,Ũ,Ụ,Ư,Ứ,Ừ,Ử,Ữ,Ự,Ý,Ỳ,Ỷ,Ỹ,Ỵ,Đ');

//        foreach( $composite as $key => $val) $ret_str[ $val]= $utf8[ $key];
    foreach( $utf8 as $key => $val) $ret_str[ $val]= $composite[ $key];

    return strtr( $str, $ret_str);
}

function change_format_date($to,$from,$cur_date)
{
    $date = date_create_from_format($to, $cur_date);
    return date_format($date, $from);
}