<?php
if (!defined('SERVER_ROOT')) {
    exit('No direct script access allowed');
}
//Gioi han limit hien thi so thu tuc
define('_CONTS_LIMIT_GUIDANCE_LIST', 10);

//config show item page home
define('_CONST_SPACING_ITEM', 10);
define('_CONST_WIDTH_ITEM', 300);
define('_CONST_HEIHGT_ITEM', 145);

//Thiết lập màu background color cho các item
function hx_set_bgcolor_item()
{
    return $v_arr_color_item = array(
                            'item-0'=>'blue',
                            'item-1'=>'red',
                            'item-2'=>'red',
                            'item-3'=>'red',
                            'item-4'=>'blue',
                            'item-5'=>'yllow',
                            'item-6'=>'red'
        );
                        
}
?>
