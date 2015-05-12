<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed'); ?>
<?php
$arr_all_list_guidance  = isset($arr_all_list_guidance) ? $arr_all_list_guidance : array();
$arr_all_list           = isset($arr_all_list) ? $arr_all_list : array();
$v_total_rows           = isset($arr_all_list_guidance[0]['C_TOTAL']) ? $arr_all_list_guidance[0]['C_TOTAL'] : 0;
$v_total_page           = ceil($v_total_rows / CONTS_LIMIT_GUIDANCE_LIST);

$sel_record_list        = isset($_REQUEST['sel_record_list']) ? $_REQUEST['sel_record_list'] :'';
$v_record_type_code     = isset($_REQUEST['txt_record_type_code']) ? $_REQUEST['txt_record_type_code'] :'';
$sel_record_type        = isset($_REQUEST['sel_record_type']) ? $_REQUEST['sel_record_type'] :'';
$sel_record_leve        = isset($_REQUEST['sel_record_leve']) ? $_REQUEST['sel_record_leve'] :'';

page_calc($v_start, $v_end);
?>

<div class="clear"></div>
<div  class="group-option" id="page-guidance"> 
    <div id="page-search" class="span12" >
        <!--End #box-chart-->
        <form class="form-horizontal" name="frmMain" id="frmMain" action="" method="post"  >
            <?php
            echo $this->hidden('controller', $this->get_controller_url());
            echo $this->hidden('hdn_item_id', 0);
            echo $this->hidden('hdn_record_type','');
            ?>
            <div id="box-search" >
                <div class="content-widgets light-gray span12">
                    <div class="widget-head blue">
                        <h3>Tra cứu</h3>
                    </div>
                    <!--End .widget-head blue-->
                    
                    <div class="widget-container" id="filter">
                        <div class="Row">
                            <div class="left-Col">
                                <label >Lĩnh vực</label>
                            </div>
                            <div class="right-Col">
                                <select class="span6" name="sel_record_list" id="sel_record_list" onchange="sel_record_list_onchange(this)">
                                    <option value="">----------- Chọn lĩnh vực ----------</option>
                                    <?php
                                    echo $this->generate_select_option($arr_all_list, $sel_record_list);
                                    ?>
                                </select>
                            </div>
                        </div>
                        <!--End #sel_record_list-->
                        <div class="Row">
                            <div class="left-Col">
                                <label>Mã thủ tục </label>
                            </div>
                            <div class="right-Col">
                                <input type="text" class="input-small" style="height: 14px;"
                                       name="txt_record_type_code" id="txt_record_type_code"
                                       value="<?php echo $v_record_type_code; ?>"
                                       class="inputbox upper_text" size="10" maxlength="10"                                       
                                       onkeypress="txt_record_type_code_onkeypress(event);"
                                       onchange="txt_record_type_onchane(this);"
                                autofocus="autofocus" accesskey="1" />&nbsp;
                                    
                                <select name="sel_record_type" id="sel_record_type"
                                        style="width: 37.2%; color: #000000;"
                                        onchange="sel_record_type_onchange(this)">
                                    <option value="">-- Chọn loại hồ sơ --</option>
                                    <?php
                                    $arr_all_record_type = isset($arr_all_record_type) ? $arr_all_record_type : array();
                                    echo $this->generate_select_option($arr_all_record_type, $sel_record_type);
                                    ?>
                                </select>
                            </div>
                        </div>                            
                            
                        <div class="Row">
                            <div class="left-Col">
                                <label>Dịch vụ cấp độ</label>
                            </div>
                            <div class="right-Col">
                                <select class="span6" name="sel_record_leve" id="sel_record_leve">
                                    <?php
                                        echo $this->generate_select_option('',$sel_record_leve,'xml_muc_do_dich_vu_cong_truc_tuyen.xml');
                                    ?>
                                </select>
                            </div>
                        </div>
                        <!--End #txt_type_code-->
                            
                        <div class="Row">
                            <div class="left-Col">
                                <label></label>
                            </div>
                            <div class="right-Col">
                                <button type="submit" class="btn btn-primary"> Tìm <i class="icon-search"></i></button>
                            </div>
                        </div>
                        <!--End btn submit-->
                    </div>
                </div>
            </div>
            <!--End #box-serach-->
        
        <div class="row-fluid">
            <div class="span12">
                <input type="hidden" name="controller" id="controller" value="<?php echo $this->get_controller_url(); ?>">
                <input type="hidden" name="hdn_item_id" id="hdn_item_id" value="0">
                <input type="hidden" name="record_type_code" id="record_type_code" value="">    <!-- filter -->

                <div class="group">
                    <div class="widget-head blue">
                        <h3> Kết quả tra cứu:</h3>
                    </div>
                </div>

                <div class="clear">&nbsp;</div>
                <div id="procedure">
                    <table border="0" cellpadding="0" cellspacing="0">
                        <colgroup>
                            <col style="width: 5%">
                            <col style="width: 7%">
                            <col style="width: 50%;" />
                            <col style="width: 20%; "/>
                            <col style="width: 12%" />
                        </colgroup>
                        <thead style="cursor: context-menu;background: rgba(250, 250, 250, 0.29);" >
                        <th style=" color: rgb(94, 86, 86);; font-weight: bold; cursor: context-menu ">STT</th>
                        <th style="width:100px;color:  rgb(94, 86, 86);; font-weight: rgb(94, 86, 86);;cursor: context-menu ">Mã thủ tục</th>
                        <th style="color:white;text-align: left;padding-left: 20px;color:  rgb(94, 86, 86);; font-weight: bold;cursor: context-menu ">Danh sách các thủ tục</th>
                        <th style="color:white;text-align: center;padding-left: 20px;color:  rgb(94, 86, 86);; font-weight: bold;cursor: context-menu ">Tệp đính kèm</th>
                        <th style="color:white;text-align: center;padding-left: 20px;color:  rgb(94, 86, 86);; font-weight: bold;cursor: context-menu ">Phạm vi</th>
                        </thead>
                        <?php
                        //$stt đánh số thu tu cho cac tu tuc khi chuyen trang
                        $v_stt = 0;
                        if (count($arr_all_list_guidance) <= 0) {
                            if (isset($_GET['keyword'])) {
                                echo '<tr style="background:white;"><td colspan="3">';
                                echo '<h1 style="color:red; width:100%;text-align:center ;margin:20px 0">Không tìn thầy thủ tục nào phù hợp. <a style="color:blue;" href="javascript::void()" onclick="window.history.back(-1)" >Quay lại trang trước</a></h1>';
                                echo '</tr></td>';
                            } else {
                                echo '<tr><td colspan="5" style="text-align:center;">';
                                echo '<h2 class="mes-error">Không tìm thấy kết quả nào phù hợp.</h2>';
                                echo '</tr></td>';
                            }
                        } else {
                            for ($i = 0; $i < count($arr_all_list_guidance); $i++):
                                ?>
                                <?php
                                $v_name             = $arr_all_list_guidance[$i]['C_NAME'];
                                $v_id               = $arr_all_list_guidance[$i]['PK_RECORD_TYPE'];
                                $v_code             = $arr_all_list_guidance[$i]['C_CODE'];                                
                                $v_village_name     = isset($arr_all_list_guidance[$i]['C_SCOPE']) ? $arr_all_list_guidance[$i]['C_SCOPE'] : '';
                                $v_send_internet    = isset($arr_all_list_guidance[$i]['C_SEND_OVER_INTERNET']) ? (int)$arr_all_list_guidance[$i]['C_SEND_OVER_INTERNET'] : 0;
                                $arr_all_file       = isset($arr_all_list_guidance[$i]['arr_all_file']) ? $arr_all_list_guidance[$i]['arr_all_file'] : array();
                                 
                                $v_url = $this->get_controller_url() . 'dsp_single_guidance/' . $v_id;

                                ?>

                                <tr class="<?php echo ($i % 2) ? 'odd' : 'even'; ?>">
                                    <td class="stt" style="text-align: center"><?php echo $v_start; ?></td>
                                    <td class="stt mtt" style="text-align: center"><?php echo $v_code; ?></td>
                                    <td style="padding-left: 5px;">
                                        <a href="<?php echo $v_url ?>"><span class="all-list-content"><?php echo $v_name; ?></span></a>
                                    </td>
                                    <td>
                                        <?php foreach ($arr_all_file as $single_file):?>
                                        <?php
                                            $v_file_name        = $single_file['file_name'];
                                            $v_name             = $single_file['name'];
                                            $v_file_type        = $single_file['type'];
                                            
                                            $arr_all_icon_file  = json_decode(CONTS_ICON_FILE_GUIDANCE, TRUE);
                                            $v_url_icon = FULL_SITE_ROOT."apps/public_service/images/default_tempalte.png";
                                            
                                            if(key_exists($v_file_type, $arr_all_icon_file))
                                            {
                                                $v_url_icon =  FULL_SITE_ROOT.'apps/public_service/images/'.$arr_all_icon_file[$v_file_type];
                                            }
                                            $v_url = $this->get_controller_url().'download?file_name='. md5($v_file_name) .'&record_code=' . $v_code . '&name='.$v_name;
                                        ?>
                                        <a class="icon-file-dowload" style="padding:5px; padding-top: 3px;padding-bottom: 3px;display: block;float: left" title="<?php echo $v_name;?>" href="<?php echo $v_url;?>"><img src="<?php echo $v_url_icon;?>" width="15px" height="auto" ></a>
                                        <?php  endforeach; ?>
                                    </td>
                                    <td class="acction">
                                <?php echo $v_village_name; ?>
                                        <div class="down-regis">
                                            <?php if((int)$v_send_internet >0 ): ?>                                               
                                                <a href="<?php echo FULL_SITE_ROOT .'nop_ho_so/nhap_thong_tin/'.$v_id; ?>" > Đăng ký</button</a>
                                            <?php endif;?>
                                        </div>
                                    </td>
                                </tr>

                    <?php 
                        $v_start++;
                        endfor; 
                    ?>
                                <?php } ?>

                    </table>
                </div>
                <div>
                    <div class="pager" id="pager">
                        <div><?php echo $this->paging2($arr_all_list_guidance); ?></div>
                    </div>
                </div>

                <!-- Context menu -->
                <ul id="myMenu" class="contextMenu">
                    <li class="statistics"><a href="#statistics">Xem tiến độ</a></li>
                </ul>
            </div>
        </div>
        </form>
    </div>
    <!--End .span12-->
</div>
<script>
    function txt_record_type_onchane(selector)
    {                                       
        var record_type_code = $(selector).val() || '';
        $('#sel_goto_page').val('1');
        $(selector).val($(selector).val().toUpperCase());
        if(record_type_code == '' || typeof(record_type_code) == 'undefined')
        {
             $('#sel_record_type').find('option').removeAttr('selected')
        }
        $('#sel_record_type').find('option').removeAttr('selected')
        .end().find('[value="' + record_type_code + '"]').attr('selected','selected');
    }    
    function sel_record_list_onchange(selector)
    {
        var record_listtype_id = $(selector).val() || 0;
        $('#txt_record_type_code').val('');       
        $('#sel_goto_page').val('1');
        document.forms.frmMain.submit();
    }
    function sel_record_type_onchange(selector) 
    {
        var record_type_code  =  $(selector).val() || '';
        $('#txt_record_type_code').val(record_type_code);
        $('#sel_goto_page').val('1');
        document.forms.frmMain.submit();
    }
</script>