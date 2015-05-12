<?php
if (!defined('SERVER_ROOT'))
    exit('No direct script access allowed');

count($VIEW_DATA['arr_single_record']) > 0 OR DIE();

$arr_single_record    = $VIEW_DATA['arr_single_record'];
$arr_single_task_info = $VIEW_DATA['arr_single_task_info'];
$v_record_id_list     = $VIEW_DATA['record_id'];
$arr_all_exec_user    = $VIEW_DATA['arr_all_exec_user'];

$v_record_type_code = $arr_single_task_info['C_RECORD_TYPE_CODE'];
$v_record_type_name = $arr_single_task_info['C_RECORD_TYPE_NAME'];
$v_group_name       = $arr_single_task_info['C_GROUP_NAME'];

//display header
$this->template->title = 'Thay đổi Phân công thụ lý hồ sơ';

$v_pop_win = isset($_REQUEST['pop_win']) ? '_pop_win' : '';
$this->template->display('dsp_header' . $v_pop_win . '.php');

//Thông tin về lần phân công trước.
$d               = simplexml_load_string($arr_single_record['C_XML_PROCESSING']);
$r               = $d->xpath('//next_task');
$prev_allot_info = $r[0];

$v_prev_allot_task_code = $v_record_type_code . _CONST_XML_RTT_DELIM . _CONST_PHAN_CONG_ROLE;
$v_user_code            = Session::get('user_code');
$r                      = $d->xpath("//step[contains(@code,'$v_prev_allot_task_code') and user_code='$v_user_code'][last()]/datetime");
$v_prev_allot_date      = $r[0];
?>

<style>
    .span_co_exec_user{
        margin-left: 5px;
    }
    .span_co_exec_user label{
        margin: 0;
    }
    </style>
<form name="frmMain" method="post" id="frmMain" action="<?php echo $this->get_controller_url(); ?>do_reallot_record">
    <?php
    echo $this->hidden('controller', $this->get_controller_url());

    echo $this->hidden('hdn_dsp_single_method', 'dsp_single_record');
    echo $this->hidden('hdn_dsp_all_method', 'dsp_all_record');
    echo $this->hidden('hdn_update_method', 'update_record');
    echo $this->hidden('hdn_delete_method', 'delete_record');

    echo $this->hidden('pop_win', $v_pop_win);
    echo $this->hidden('hdn_item_id_list', $v_record_id_list);

    //Ten dangnhap cua CB thu ly chinh
    echo $this->hidden('hdn_direct_exec_user_code', '');
    echo $this->hidden('hdn_direct_exec_user_name', '');

    //Danh sach (ten dang nhap) cua CB phoi hop thu ly
    echo $this->hidden('hdn_co_exec_user_code_list', '');

    //Ma Loai HS
    echo $this->hidden('hdn_record_type_code', $v_record_type_code);
    ?>
    <div class="widget-head blue">
        <h3>Thay đổi phân công thụ lý</h3>
    </div>
    <div class="modal_record_type_name">
        <?php echo $v_record_type_code; ?> - <?php echo $v_record_type_name; ?>
    </div>
    <!-- Record list -->
    <table width="100%" class="adminlist table table-bordered table-striped">
        <tr>
            <th>STT</th>
            <th>Mã hồ sơ</th>
            <th>Người đăng ký</th>
            <th>Ngày nhận</th>
            <th>Hẹn trả công dân</th>
        </tr>
        <tr>
            <td class="right">1</td>
            <td><?php echo $arr_single_record['C_RECORD_NO']; ?></td>
            <td><?php echo $arr_single_record['C_CITIZEN_NAME']; ?></td>
            <td><?php echo jwDate::yyyymmdd_to_ddmmyyyy($arr_single_record['C_RECEIVE_DATE'], TRUE); ?></td>
            <td><?php echo r3_View::return_date_by_text($arr_single_record['C_RETURN_DATE']); ?></td>
        </tr>
    </table>
    <!-- End: Record list -->

    <br>
    <table class="none" width="100%" cellspacing="0" cellpadding="4" border="0">
        <tr>
            <td style="font-weight: bold" width="15%">
                Ngày phân công: 
            </td>
            <td>
                <?php echo jwDate::yyyymmdd_to_ddmmyyyy($v_prev_allot_date, 1); ?>
            </td>
        </tr>
        <tr>
            <td style="font-weight: bold" width="15%">
                Thụ lý chính: 
            </td>
            <td>
                <?php echo $prev_allot_info->attributes()->user_name ?>
                <?php if ($prev_allot_info->attributes()->user_job_title != ''): ?>
                    <i>(<?php echo $prev_allot_info->attributes()->user_job_title; ?>)</i>
                <?php endif; ?>
            </td>
        </tr>
        <?php $co_user = trim($arr_single_record['C_NEXT_CO_USER_CODE'], ' ,') ?>
        <?php if ($co_user): ?>
            <tr>
                <td style="font-weight: bold" width="15%">
                    Người phối hợp thụ lý: 
                </td>
                <td>
                    <?php
                    $co_user = str_replace(',', ', ', $co_user);
                    echo $co_user;
                    ?>
                </td>
            </tr>
        <?php endif; ?>
    </table>
    <div class="well">
        <table class="none" width="100%" cellspacing="0" cellpadding="4" border="0">
            <tr>
                <td style="font-weight: bold" width="15%">
                    Phòng thụ lý:
                </td>
                <td>
                    <?php echo $v_group_name; ?>
                </td>
            </tr>
            <tr>
                <td style="font-weight: bold" width="15%">
                    Người thụ lý chính:
                </td>
                <td>
                    <select style="width:203px;border:1px solid #D5D5D5;" name="sel_exec_user"
                            id="sel_exec_user" onchange="sel_exec_user_onchange(this);">
                        <option value="">--- Chọn người thụ lý chính ---</option>
                        <?php foreach ($arr_all_exec_user as $user): ?>
                            <?php $v_selected = ($user['C_USER_LOGIN_NAME'] == $prev_allot_info->attributes()->user) ? ' selected' : ''; ?>
                            <option value="<?php echo $user['C_USER_LOGIN_NAME']; ?>" <?php echo $v_selected; ?>><?php echo $user['C_NAME']; ?></option>
                        <?php endforeach; ?>
                    </select>&nbsp;

                    <span>Số ngày thực hiện:</span>
                    <b style="color:red;"><?php echo $VIEW_DATA['exec_task_time']; ?></b> ngày
                </td>
            </tr>
            <tr>
                <td style="font-weight: bold" width="15%">
                    Người phối hợp thụ lý:
                </td>
                <td>
                    <span id="spantreeuser">
                        <?php reset($arr_all_exec_user); ?>
                        <?php foreach ($arr_all_exec_user as $user): ?>
                            <span class="span_co_exec_user" id="span_co_exec_user_<?php echo $user['C_USER_LOGIN_NAME']; ?>" style="">
                                <label for="chk_co_exec_user_<?php echo $user['C_USER_LOGIN_NAME']; ?>">
                                    <input type="checkbox" value="<?php echo $user['C_USER_LOGIN_NAME']; ?>"
                                           name="chk_co_exec_user" id="chk_co_exec_user_<?php echo $user['C_USER_LOGIN_NAME']; ?>"
                                           />
                                           <?php echo $user['C_NAME']; ?>
                                </label>
                            </span>
                        <?php endforeach; ?>
                    </span>
                </td>
            </tr>
        </table>
        <div class="clear">&nbsp;</div>
        <!-- Buttons -->
        <div class="button-area">
            <button type="button" name="btn_do_allot" class="btn btn-primary" onclick="btn_do_allot_onclick();" accesskey="2">
                <i class="icon-save"></i>
                <?php echo __('update'); ?>
            </button>
            <?php $v_back_action = ($v_pop_win === '') ? 'btn_back_onclick();' : 'try{window.parent.hidePopWin();}catch(e){window.close();};'; ?>
            <button type="button" name="cancel" class="btn" onclick="<?php echo $v_back_action; ?>" >
                <i class="icon-remove"></i>
                <?php echo __('close window'); ?>
            </button> 
        </div>
    </div>
</form>
<script>
                function btn_do_allot_onclick()
                {
                    var f = document.frmMain;

                    //CB thu ly chinh
                    if (f.hdn_direct_exec_user_code.value == "")
                    {
                        alert('Phải xác định cán bộ thụ lý chính!');
                        f.sel_exec_user.focus();
                        return false;
                    }

                    //Lay danh sach CB phoi hop thu ly
                    f.hdn_co_exec_user_code_list.value = get_all_checked_checkbox(f.chk_co_exec_user, ",");

                    f.submit();
                }
                function sel_exec_user_onchange(obj)
                {
                    show_all_co_exec_user();

                    v_direct_exec_user_code = obj.options[obj.selectedIndex].value;
                    $("#hdn_direct_exec_user_code").val(v_direct_exec_user_code);
                    $("#hdn_direct_exec_user_name").val(obj.options[obj.selectedIndex].text);

                    if (v_direct_exec_user_code != '')
                    {
                        q = '#' + 'span_co_exec_user_' + v_direct_exec_user_code;
                        ;
                        $(q).children('input[type="checkbox"]').attr('checked', false);
                        $(q).hide();
                    }
                }
                function show_all_co_exec_user()
                {
                    $(".span_co_exec_user").each(function () {
                        $(this).show();
                    });
                }
</script>
<?php
$this->template->display('dsp_footer' . $v_pop_win . '.php');
