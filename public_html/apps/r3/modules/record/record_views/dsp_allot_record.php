<?php
if (!defined('SERVER_ROOT'))
    exit('No direct script access allowed');

count($VIEW_DATA['arr_all_record']) > 0 OR DIE();

$arr_all_record    = $VIEW_DATA['arr_all_record'];
$v_record_id_list  = $VIEW_DATA['record_id_list'];
$arr_all_exec_user = $VIEW_DATA['arr_all_exec_user'];
$v_group_name      = $VIEW_DATA['group_name'];

$v_record_type_code = $arr_all_record[0]['C_RECORD_TYPE_CODE'];
$v_record_type_name = $arr_all_record[0]['C_RECORD_TYPE_NAME'];

//Step name ??
$v_task_code  = $arr_all_record[0]['C_NEXT_TASK_CODE'];
$dom_workflow = simplexml_load_file($this->get_xml_config($v_record_type_code, 'workflow'));
$v_step_name  = $dom_workflow->xpath("//step[task[@code='$v_task_code']]/@name");
$v_step_name  = $v_step_name[0];

//display header
$this->template->title = 'Phân công thụ lý hồ sơ';

$v_pop_win = isset($_REQUEST['pop_win']) ? '_pop_win' : '';
$this->template->display('dsp_header' . $v_pop_win . '.php');
?>
<form name="frmMain" method="post" class="div-slimscroll" id="frmMain" action="<?php echo $this->get_controller_url(); ?>do_allot_record">
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

    <div class="clear" style="height: 10px">&nbsp;</div>
    <div class="widget-head blue">
        <h3>Danh sách hồ sơ chờ phân công:</h3>
    </div>

    <div class="modal_record_type_name">
        <?php echo $v_record_type_code; ?> - <?php echo $v_record_type_name; ?>
    </div>
    <!-- Record list -->
    <table  class="adminlist table table-bordered table-striped thead">
        <colgroup>
            <col width="5%">
            <col width="*">
            <col width="20%">
            <col width="20%">
            <col width="20%">
        </colgroup>
        <tr>
            <th>STT</th>
            <th>Mã hồ sơ</th>
            <th>Người đăng ký</th>
            <th>Ngày nhận</th>
            <th>Hẹn trả công dân</th>
        </tr>
    </table>
    <div id="box-slimscroll" class="dsp-allot-record">
        <table class="adminlist table table-bordered table-striped ">
            <colgroup>
                <col width="5%">
                <col width="*">
                <col width="20%">
                <col width="20%">
                <col width="20%">
            </colgroup>
            <?php for ($i = 0; $i < count($arr_all_record); $i++): ?>
                <tr>
                    <td class="right"><?php echo ($i + 1); ?></td>
                    <td><?php echo $arr_all_record[$i]['C_RECORD_NO']; ?></td>
                    <td><?php echo $arr_all_record[$i]['C_CITIZEN_NAME']; ?></td>
                    <td><?php echo jwDate::yyyymmdd_to_ddmmyyyy($arr_all_record[$i]['C_RECEIVE_DATE'], TRUE); ?></td>
                    <td><?php echo r3_View::return_date_by_text($arr_all_record[$i]['C_RETURN_DATE']); ?></td>
                </tr>
            <?php endfor; ?>
        </table>
    </div>
    <!-- End: Record list -->
    <div class="clear" style="height: 10px">&nbsp;</div>
    <!--    <div class="widget-head blue">
            <h3>
    <?php echo $v_step_name; ?>
            </h3>
    <div class="clear" style="height: 10px">&nbsp;</div>
        </div>-->
    <div class="well">
        <div class="Row">
            <div class="left-Col">
                <label>
                    Phòng thụ lý:
                </label>
            </div>
            <div class="right-Col">
                <?php echo $v_group_name; ?>
            </div>
        </div>
        <div class="Row">
            <div class="left-Col">
                <label>
                    Người thụ lý chính:
                </label>
            </div>
            <div class="right-Col">
                <select style="width:203px;border:1px solid #D5D5D5;" name="sel_exec_user"
                        id="sel_exec_user" onchange="sel_exec_user_onchange(this);">
                    <option value="">--- Chọn người thụ lý chính ---</option>
                    <?php foreach ($arr_all_exec_user as $user): ?>
                        <option value="<?php echo $user['C_USER_LOGIN_NAME']; ?>"><?php echo $user['C_NAME']; ?></option>
                    <?php endforeach; ?>
                </select>&nbsp;

                <i>
                    Số ngày thực hiện:
                    <b style="color:red;"><?php echo $VIEW_DATA['exec_task_time']; ?></b> ngày
                </i>            

            </div>
        </div>

        <div class="Row">
            <div class="left-Col">
                <label>
                    Người phối hợp thụ lý:
                </label>
            </div>
            <div class="right-Col">
                <span id="spantreeuser">
                    <?php reset($arr_all_exec_user); ?>
                    <?php foreach ($arr_all_exec_user as $user): ?>

                        <label class="label_co_exec_user" id="label_co_exec_user_<?php echo $user['C_USER_LOGIN_NAME']; ?>">
                            <input type="checkbox" value="<?php echo $user['C_USER_LOGIN_NAME']; ?>"
                                   name="chk_co_exec_user" id="chk_co_exec_user_<?php echo $user['C_USER_LOGIN_NAME']; ?>"
                                   />
                                   <?php echo $user['C_NAME']; ?>
                        </label>
                    <?php endforeach; ?>
                </span>
            </div>
        </div>
        <div class="clear">&nbsp;</div>

        <!-- Buttons -->
        <div class="button-area">
            <hr>
            <!--button xet duyet-->
            <button type="button" name="trash" class="btn btn-primary" onclick="btn_do_allot_onclick();">
                <i class="icon-ok-sign"></i>
                Phân công thụ lý
            </button>
            <!--Button close window-->
            <?php $v_back_action = ($v_pop_win === '') ? 'btn_back_onclick();' : 'try{window.parent.hidePopWin();}catch(e){window.close();};'; ?>
            <button type="button" name="trash" class="btn" onclick="<?php echo $v_back_action; ?>" >
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
                        q = '#' + 'label_co_exec_user_' + v_direct_exec_user_code;
                        ;
                        $(q).find('input[type="checkbox"]').attr('checked', false);
                        $(q).hide();
                    }
                }
                function show_all_co_exec_user()
                {
                    $(".label_co_exec_user").each(function () {
                        $(this).show();
                    });
                }
</script>
<?php
$this->template->display('dsp_footer' . $v_pop_win . '.php');
