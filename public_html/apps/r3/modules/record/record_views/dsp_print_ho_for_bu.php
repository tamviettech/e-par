<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed');

count($VIEW_DATA['arr_all_record']) > 0 OR DIE();

$report_data = array(
    'arr_all_record'             => $VIEW_DATA['arr_all_record']
    ,'arr_single_task_info'      => $VIEW_DATA['arr_single_task_info']
);

$v_xml_ho_for_bu_template_file   = $this->get_xml_config($arr_single_task_info['C_RECORD_TYPE_CODE'],'ho_for_bu_template');
$v_xml_form_struct_full_path = str_replace('\\', '/', $this->get_xml_config($arr_single_task_info['C_RECORD_TYPE_CODE'], 'form_struct'));
$v_ho = get_request_var('ho','0');
?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>In giấy bàn giao hồ sơ</title>
        <link rel="stylesheet" href="<?php echo SITE_ROOT;?>public/css/reset.css" type="text/css" media="all" />
        <link rel="stylesheet" href="<?php echo SITE_ROOT;?>public/css/text.css" type="text/css" media="screen" />
        <link rel="stylesheet" href="<?php echo SITE_ROOT;?>public/css/printer.css" type="text/css" media="all" />
        <script src="<?php echo SITE_ROOT;?>public/js/jquery/jquery.min.js" type="text/javascript"></script>
        
        <!--adapter-->
        <script src="<?php echo SITE_ROOT; ?>public/js/advchat/adapter.js" type="text/javascript"></script>
    </head>
    <body>
        <?php
            echo $this->hidden('hdn_ho',$v_ho);
        ?>
        <div class="print-button">
            <input type="button" value="In trang" onclick="window.print(); return false;" />
            <input type="button" value="Đóng cửa sổ" onclick="window.parent.hidePopWin()" />
        </div>
        <div>
            <?php create_handover_info($report_data, '(Liên 1: Lưu)', $v_xml_ho_for_bu_template_file,$v_xml_form_struct_full_path);?>
            <h4 class="page-break"></h4>
            <?php create_handover_info($report_data, '(Liên 2: Giao cho bên nhận)', $v_xml_ho_for_bu_template_file,$v_xml_form_struct_full_path);?>
        </div>
        <script>
            $(document).ready(function(){
                if($('#hdn_ho').val() == '1')
                {
                    if(webrtcDetectedBrowser == 'chrome')
                    {
                        var mediaQueryList = window.matchMedia('print');
                        mediaQueryList.addListener(function(mql) {
                            if (!mql.matches) 
                            {
                                retval();
                            }
                        });
                    }
                    else
                    {
                        window.onafterprint  = function () {
                            retval();
                        }
                    }
                }
            });
            
            function retval()
            {
                data = {ho:$('#hdn_ho').val()};
                returnVal = data;
                window.parent.hidePopWin(true);
            }
        </script>
    </body>
</html>
<?php
function create_handover_info($report_data, $distribute = '(Liên 1: Lưu)', $v_xml_ho_for_bu_template_file,$v_xml_form_struct_full_path)
{
    $arr_all_record         = $report_data['arr_all_record'];
    $arr_single_task_info   = $report_data['arr_single_task_info'];

    $dom_unit_info = simplexml_load_file(SERVER_ROOT . 'public/xml/xml_unit_info.xml');
    
    $v_scope                    = $arr_all_record[0]['C_SCOPE'];
    
    $v_la_thu_tuc_lien_thong = 0;
    $v_unit_full_name = Session::get('root_ou_name');
    if ($v_scope >=2) //Thu tuc cap huyen -> lay ten Huyen
    {
        $v_unit_full_name = Session::get('root_ou_name');
    }
    else //Thu tuc cap xa -> lay ten xa
    {
        $v_unit_full_name = Session::get('ou_name');
        $v_la_thu_tuc_lien_thong = 1;
    }
    ?>
    <!-- header -->
    <table border="0" cellpadding="0" cellspacing="0" width="100%" class="header">
        <tr>
            <td align="center" class="unit_full_name">
                <?php echo $v_unit_full_name;?><br/>
                <strong>
                    <u style="font-size: 13px">BỘ PHẬN TIẾP NHẬN VÀ TRẢ HỒ SƠ</u>
                </strong>
            </td>
            <td align="center">
                <span style="font-size: 12px">
                    <strong>CỘNG HOÀ XÃ HỘI CHỦ NGHĨA VIỆT NAM</strong>
                </span>
                <br/>
                <strong>
                    <u style="font-size: 10px">Độc lập - Tự do - Hạnh phúc</u>
                </strong>
            </td>
        </tr>
        <tr>
            <td colspan="2" class="report-title">
                <span class="title-1">GIẤY BÀN GIAO HỒ SƠ</span><br/>
                <span class="title-2"><?php echo $distribute;?></span>
            </td>
        </tr>
    </table>

    <!-- report body -->
    <table cellpadding="0" cellspacing="0" width="100%" class="word-wrap">
        <colgroup>
            <col width="30%" />
            <col width="70%" />
        </colgroup>
        <tr>
            <td>
                <strong>Người bàn giao:</strong>
            </td>
            <td>
                <span style="text-transform: uppercase;"><?php echo Session::get('user_name');?></span>
            </td>
        </tr>
        <tr>
            <td>
                <strong>Bên bàn giao:</strong>
            </td>
            <td>
                <span class="address">Bộ phận một cửa</span>
            </td>
        </tr>
        <tr>
            <td>
                <strong>Bên nhận bàn giao:</strong>
            </td>
            <td>
                <?php echo $arr_single_task_info['C_GROUP_NAME'];?>
            </td>
        </tr>
        <tr>
            <td>
                <strong>Số lượng hồ sơ:</strong>
            </td>
            <td>
                <span><?php echo count($arr_all_record);?></span>
            </td>
        </tr>
        <tr>
            <td>
                <strong>Loại hồ sơ:</strong>
            </td>
            <td>
                <?php echo $arr_all_record[0]['C_RECORD_TYPE_NAME'];?>
            </td>
        </tr>
        <tr>
            <td>
                <strong>Thời gian bàn giao:</strong>
            </td>
            <td>
                <?php echo Date('d-m-Y H:i');?>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <strong>Danh sách hồ sơ bàn giao:</strong>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <!-- Record list -->
                <table cellpadding="4" cellspacing="0" width="100%" class="list word-wrap">
                    <?php if (!is_file($v_xml_ho_for_bu_template_file)): ?>
                        <tr>
                            <th>STT</th>
                            <th>Mã hồ sơ</th>
                            <th>Người đăng ký</th>
                            <th>Ngày nhận</th>
                            <th>Ngày hẹn trả</th>
                        </tr>
                        <?php for ($i=0; $i<count($arr_all_record); $i++): ?>
                            <tr>
                                <td class="right"><?php echo ($i+1);?></td>
                                <td><?php echo $arr_all_record[$i]['C_RECORD_NO'];?></td>
                                <td><?php echo $arr_all_record[$i]['C_CITIZEN_NAME'];?></td>
                                <td><?php echo jwDate::yyyymmdd_to_ddmmyyyy($arr_all_record[$i]['C_RECEIVE_DATE'], TRUE);?></td>
                                <td><?php echo r3_View::return_date_by_text($arr_all_record[$i]['C_RETURN_DATE']);?></td>
                            </tr>
                        <?php endfor;?>
                    <?php else: ?>
                        <?php
                        $dom_unit_info_record_list = simplexml_load_file($v_xml_ho_for_bu_template_file);
                        $cols            = $dom_unit_info_record_list->xpath('//list/item');

                        //Header
                        $html = '<tr>';
                        foreach ($cols as $col)
                        {
                            $html .= '<th width="' . strval($col->attributes()->size) . '" align="center"><b>' . trim($col->attributes()->name) . '&nbsp;</b></th>';
                        }//end foreach
                        $html .= '</tr>';

                        //List
                        for ($i=0; $i<count($arr_all_record); $i++)
                        {
                            $v_xml_data     = isset($arr_all_record[$i]['C_XML_DATA']) ? $arr_all_record[$i]['C_XML_DATA'] : '<root/>';
                            $dom_unit_info_xml_data   = simplexml_load_string($v_xml_data);
                            
                            reset($cols);
                            $html .= '<tr>';
                            foreach ($cols as $col)
                            {
                                $v_col_data = '';
                                //Cell data
                                $v_col_id   = strval($col->attributes()->id);
                                $v_align    = isset($col->attributes()->align) ? ' align="' . $col->attributes()->align . '"' : '';

                                if ($v_col_id == 'RN')
                                {
                                    $html .= '<td width="' . $col->attributes()->size . '"' . $v_align . '>' . strval($i+1) . '</td>';
                                }
                                else
                                {
                                    if (strpos($v_col_id , 'xml/') !== FALSE) //Cot du lieu nam trong XML
                                    {
                                        //lay loai du lieu xml
                                        $col_type_xml = strval($col->attributes()->type);
                                        //dang formstruct
                                        if($col_type_xml == 'formstruct')
                                        {
                                            $v_col_id       = str_replace('xml/', '', $v_col_id);
                                            if($v_col_id == 'doc')
                                            {
                                                $x_path = "//item[value='true' and @doc='true']/@id";
                                            }
                                            else
                                            {
                                                $x_path = "/data/item[@id='" . $v_col_id . "']/value";
                                            }
                                            
                                            $r  = $dom_unit_info_xml_data->xpath($x_path);
                                            //lay du lieu tu form struct
                                            $v_col_data .= '<ul>';
                                            foreach($r as $arr_id)
                                            {
                                                $id = $arr_id->id;
                                                $dom = simplexml_load_file($v_xml_form_struct_full_path);
                                                $x_path = "//line/item[@id='$id']/@title";
                                                $r = $dom->xpath($x_path);
                                                
                                                $v_col_data .= '<li>' . $r[0] . '</li>';
                                            }
                                            $v_col_data .= '</ul>';
                                        }
                                        elseif($col_type_xml == 'text_concat')//dang xml concat text
                                        {
                                            $arr_index = explode(',', $v_col_id);
                                            $v_col_data = '';
                                            foreach($arr_index as $index)
                                            {
                                                $v_col_id       = str_replace('xml/', '', $index);
                                                $r              = $dom_unit_info_xml_data->xpath("/data/item[@id='" . $v_col_id . "']/value");
                                                $v_data      = sizeof($r) ? $r[0] : '';
                                                //kiem tra neu la du lieu nam trong public xml dang select option
                                                $dom_formstruct = simplexml_load_file($v_xml_form_struct_full_path);
                                                $src_file = xpath($dom_formstruct, "//item[@id='$v_col_id']/@src_file", XPATH_STRING);
                                                if($src_file != '' && $v_data != '')
                                                {
                                                    $v_data = xpath(simplexml_load_file($src_file), "//item[@value='$v_data']/@name",XPATH_STRING);
                                                }
                                                if($v_col_data == '')
                                                {
                                                    $v_col_data .= $v_data;
                                                }
                                                else
                                                {
                                                    $v_col_data .= ', ' . $v_data;
                                                }
                                                
                                            }
                                            $v_col_data = "<pre>$v_col_data</pre>";
                                        }
                                        //dang default: text
                                        else
                                        {
                                            $v_col_id       = str_replace('xml/', '', $v_col_id);
                                            $r              = $dom_unit_info_xml_data->xpath("/data/item[@id='" . $v_col_id . "']/value");
                                            $v_col_data     = sizeof($r) ? $r[0] : '';
                                            $v_col_data     = '<pre>' . $v_col_data . '</pre>';
                                        }
                                    }
                                    else //Cot tuong minh
                                    {
                                        $v_col_data = $arr_all_record[$i][$v_col_id];
                                        if ($v_col_id == 'C_RECEIVE_DATE')
                                        {
                                            $v_col_data = jwDate::yyyymmdd_to_ddmmyyyy($v_col_data, TRUE);
                                        }
                                        elseif ($v_col_id == 'C_RETURN_DATE')
                                        {
                                            $v_col_data = r3_View::return_date_by_text($v_col_data);
                                        }
                                    }
                                    
                                    $v_col_data = str_replace('<pre>', '', $v_col_data);
                                    $v_col_data = str_replace('</pre>', '', $v_col_data);
                                    $html .= '<td width="' . $col->attributes()->size . '"' . $v_align . '>' . strval($v_col_data) . '</td>';
                                }//end if ($v_col_id == 'RN')
                            } //end foreach $cols
                            $html .= '<tr>';
                        } //end for

                        echo $html;
                        ?>
                    <?php endif;?>
                </table>
                <!-- End: Record list -->
            </td>
        </tr>
        <tr>
            <td colspan="2" class="sign_place">
                <br/>
                <?php echo get_xml_value($dom_unit_info, '/unit/name');?>, ngày <?php echo Date('d');?> tháng <?php echo Date('m');?> năm <?php echo Date('Y');?>
            </td>
        </tr>
    </table>
    <table border="0" cellpadding="0" cellspacing="0" width="100%" class="tbl-signer">
        <tr>
            <td>
                <strong>BÊN NHẬN BÀN GIAO</strong>
                <br />
                <i>(Ký, ghi rõ họ tên)</i>
            </td>
            <td style="height: 150px; align:center">
                <strong>BÊN BÀN GIAO</strong>
                <br />
                <i>(Ký, ghi rõ họ tên)</i>
            </td>
            <td style="height: 150px; align:center">
                <strong>XÁC NHẬN BÀN GIAO</strong><br />
                <i>(Ký, ghi rõ họ tên)</i>
            </td>
        </tr>
	    <tr>
            <td>
                <strong></strong>
            </td>
            <td style="height: 150px; align:center">
                 <strong><span style="text-transform: uppercase;"><?php echo Session::get('user_name');?></span></strong>
            </td>
            <td style="height: 150px; align:center;text-transform: uppercase;">
                <strong><?php echo get_xml_value($dom_unit_info, '/unit/supervisor');?></strong>
            </td>
        </tr>
		
		
    </table><?php
}
