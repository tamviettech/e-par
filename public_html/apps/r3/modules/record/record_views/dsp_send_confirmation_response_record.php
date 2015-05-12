<?php 
/**
 * @copyright	Copyright (C) 2012 Tam Viet Tech. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author		Ngo Duc Lien <liennd@gmail.com>
 * @author		Luong Thanh Binh <ltbinh@gmail.com>
 */

if (!defined('SERVER_ROOT')) exit('No direct script access allowed');

count($VIEW_DATA['arr_all_record']) > 0 OR DIE('ohhh');

$arr_all_record         = $VIEW_DATA['arr_all_record'];
$arr_single_task_info   = $VIEW_DATA['arr_single_task_info'];
$v_record_id_list       = $VIEW_DATA['record_id_list'];

$v_record_type_code = $arr_single_task_info['C_RECORD_TYPE_CODE'];
$v_record_type_name = $arr_single_task_info['C_RECORD_TYPE_NAME'];
$v_group_name       = $arr_single_task_info['C_GROUP_NAME'];
//$arr_all_next_user  = $VIEW_DATA['arr_all_next_user'];

//display header
$this->template->title = 'Xác nhận hồ sơ liên thông';

$v_pop_win = isset($_REQUEST['pop_win']) ? '_pop_win' : '';
$this->template->display('dsp_header' . $v_pop_win . '.php');


$v_promote = _CONST_RECORD_APPROVAL_ACCEPT;

?>
<form name="frmMain" method="post" id="frmMain" action="<?php echo $this->get_controller_url();?>do_send_confirmation_response_record">
    <?php
    echo $this->hidden('controller', $this->get_controller_url());

    echo $this->hidden('hdn_dsp_single_method', 'dsp_single_record');
    echo $this->hidden('hdn_dsp_all_method', 'dsp_all_record');
    echo $this->hidden('hdn_update_method', 'update_record');
    echo $this->hidden('hdn_delete_method', 'delete_record');

    echo $this->hidden('pop_win', $v_pop_win);
    echo $this->hidden('hdn_item_id_list', $v_record_id_list);

    //Ma Loai HS
    echo $this->hidden('hdn_record_type_code', $v_record_type_code);
    echo $this->hidden('sel_record_type', $v_record_type_code);

    //KQ thu ly
    echo $this->hidden('hdn_approval_value', $v_promote);
    ?>

    <div class="group" style="padding-bottom: 5px; float: left;width: 100%">
        <div class="widget-head blue">
            <h3>
                Kết quả thụ lý hồ sơ liên thông
            </h3>
        </div>
        <div class="Row">
            <div class="left-Col">
                <label for="Loại hồ sơ: ">Loại hồ sơ: </label>
            </div>
            <div class="right-Col">
                <?php echo $v_record_type_code;?> - <?php echo $v_record_type_name;?>
            </div>
        </div>
    </div>

    <!-- Record list -->
    <table width="100%" class="adminlist table table-bordered table-striped">
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
    </table>
    <!-- End: Record list -->

   <fieldset class="default">
        <legend>Thông tin yêu cầu</legend>
        <table style="width:100%" class="none-border-table">
            <col width="20%"/>
            <col width="80%" />
            <?php
            $dom_processing = simplexml_load_string($arr_all_record[0]['C_XML_PROCESSING']);
            $v_sender = $dom_processing->xpath("//step[last()]");
            $v_sender = $v_sender[0];
            ?>
            <tr>
                <td>Nơi gửi:</td>
                <td><?php echo $v_sender->user_name;?></td>
            </tr>
            <tr>
                <td>Nội dung yêu cầu:</td>
                <td><?php echo $v_sender->promote;?></td>
            </tr>
            <tr>
                <td>Thời gian gửi yêu cầu:</td>
                <td><?php echo jwDate::yyyymmdd_to_ddmmyyyy($v_sender->datetime,1);?></td>
            </tr>
        </table>
    </fieldset>
    <fieldset class="default">
        <legend>Kết quả giải quyết</legend>
        <table style="width:100%" class="none-border-table">
            <col width="20%"/>
            <col width="80%" />
            <tr>
                <td>Cán bộ thực hiện:</td>
                <td><?php echo Session::get('user_name');?></td>
            </tr>
            <tr>
                <td>Kết quả:</td>
                <td>
                    <label for="rad_<?php echo _CONST_RECORD_APPROVAL_ACCEPT;?>">
                        <input type="radio" name="rad_approval" id="rad_<?php echo _CONST_RECORD_APPROVAL_ACCEPT;?>"
                            value="<?php echo _CONST_RECORD_APPROVAL_ACCEPT;?>" checked="checked""
                        />
                        Đồng ý
                    </label>

                    <label for="rad_<?php echo _CONST_RECORD_APPROVAL_REJECT;?>">
                        <input type="radio" name="rad_approval" id="rad_<?php echo _CONST_RECORD_APPROVAL_REJECT;?>"
                            value="<?php echo _CONST_RECORD_APPROVAL_REJECT;?>"
                        />
                        Từ chối
                    </label>
                </td>
            </tr>
            <tr>
                <td style="vertical-align: top;">Diễn giải <span class="required">(*)</span>:</td>
                <td>
                    <textarea rows="5" cols="120" name="txt_reason"></textarea>
                </td>
            </tr>
        </table>
    </fieldset>

    <!-- Buttons -->
    <div class="button-area">
        <hr/>
        <input type="button" name="btn_do_approval" class="button save" value="Cập nhật" onclick="btn_do_approval_onclick();" />
        <?php $v_back_action = ($v_pop_win === '') ? 'btn_back_onclick();' : 'try{window.parent.hidePopWin();}catch(e){window.close();};';?>
        <input type="button" name="cancel" class="button close" value="<?php echo __('close window'); ?>" onclick="<?php echo $v_back_action;?>"/>
    </div>
</form>
<script>

    function btn_do_approval_onclick()
    {
        var f = document.frmMain;
        if (trim(f.txt_reason.value) == '')
        {
            alert('"Diễn giải không được để trống!');
            f.txt_reason.focus();
            return;
        }

        f.submit();
    }
</script>
<?php $this->template->display('dsp_footer' .$v_pop_win . '.php');