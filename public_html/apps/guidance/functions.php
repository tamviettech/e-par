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

/**
 * Thiết lập mảng chứa tên ảnh ứng với mỗi lĩnh vưc, mã lĩnh vưc là mã lĩnh vực, tên ảnh là giá trị
 * @return array : mảng chứa danh sách tên ảnh  ứng với từng tên lĩnh vực tương ứng
 */

function hx_set_metro_logo_item()
{
    return $array_logo_items = array(
                                        'CT'     => 'metro_logo_CT.png',
                                        'DT'     => 'metro_logo_QLDT_small.jpg',
                                        'VH'     => 'metro_logo_VH_small.jpg',
                                        'XD'     => 'metro_logo_XD_small.jpg',
                                        'TP'     => 'metro_logo_TP_small.jpg',
                                        'TCKH'   => 'metro_logo_TCKH_small.jpg',
                                        'LD'     => 'metro_logo_LDTBXH_small.jpg',
                                        'GT'     => 'metro_logo_GTVT_small.jpg',
                                        'TN'     => 'metro_logo_TNMT_small.jpg',
                                        'TC'     => 'metro_logo_TCKH_small.jpg',
                                        'NN'     => 'metro_logo_NNPTNT_small.jpg',
                                        'NV'     => 'metro_logo_NV_small.jpg',
                                        'GD'     => 'metro_logo_GDDT_small.jpg',
                                        'PD'     => 'metro_logo_PD_small.jpg',
                                        'KT'     => 'metro_logo_KTHT_small.jpg'
                                    );
}




//Thiết lập màu background color cho các item( các lĩnh vực )
//Sử dụng với trường hợp không có ảnh logo minh họa
function hx_set_bgcolor_item()
{
    return $v_arr_color_item = array(
                            'item-0'=>'red',
                            'item-1'=>'red',
                            'item-2'=>'red',
                            'item-3'=>'red',
                            'item-4'=>'blue',
                            'item-5'=>'yllow',
                            'item-6'=>'red',
                            'item-7'=>'red',
                            'item-8'=>'red',
                            'item-9'=>'blue',
        );
}
?>
