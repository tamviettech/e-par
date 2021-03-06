<?php
if (!defined('SERVER_ROOT'))
    exit('No direct script access allowed');

//display header
$this->template->title = 'Chấp nhận hồ sơ';

$v_pop_win = isset($_REQUEST['pop_win']) ? '_pop_win' : '';
$this->template->display('dsp_header' . $v_pop_win . '.php');

$v_record_id           = $arr_single_record['PK_RECORD'];
$v_record_no           = $arr_single_record['C_RECORD_NO'];
$v_receive_date        = $arr_single_record['C_RECEIVE_DATE'];
$v_return_phone_number = $arr_single_record['C_RETURN_PHONE_NUMBER'];
$v_return_email        = $arr_single_record['C_RETURN_EMAIL'];
$v_citizen_name        = $arr_single_record['C_CITIZEN_NAME'];

$v_xml_data = $arr_single_record['C_XML_DATA'];

//Convert date
$v_receive_date = jwDate::yyyymmdd_to_ddmmyyyy($v_receive_date, TRUE);

//token
$v_user_token = session::get('user_token');

//Tinh toan ngay tra ket qua
$v_total_time          = $arr_single_record['C_TOTAL_TIME'];
$v_return_date         = $arr_single_record['C_RETURN_DATE'];
$v_return_date_by_text = $this->return_date_by_text($v_return_date);
?>
<form style="padding: 10px;" name="frmMain" method="post" id="frmMain" action="<?php echo $this->get_controller_url(); ?>do_accept_internet_record">
    <?php
    echo $this->hidden('controller', $this->get_controller_url());

    echo $this->hidden('hdn_dsp_single_method', 'dsp_single_record');
    echo $this->hidden('hdn_dsp_all_method', 'dsp_all_record');
    echo $this->hidden('hdn_update_method', 'update_record');
    echo $this->hidden('hdn_delete_method', 'delete_record');
    echo $this->hidden('hdn_item_id', $v_record_id);
    echo $this->hidden('record_type_code', $arr_single_record['C_RECORD_TYPE_CODE']);
    echo $this->hidden('hdn_return_email', $v_return_email);
    echo $this->hidden('hdn_return_date', $v_return_date);
    echo $this->hidden('hdn_return_phone_number', $v_return_phone_number);
    echo $this->hidden('hdn_total_time', $v_total_time);
    echo $this->hidden('hdn_xml_workflow_file_name', $v_xml_workflow_file_name);
    
    echo $this->hidden('user_token', $v_user_token);

    echo $this->hidden('pop_win', $v_pop_win);
    ?>
    <div class="page-title">Phản hồi công dân</div>
    <label class="checkbox inline"><input type="radio" name="rad_accept" value="email" id="rad_accept_email" checked="checked"/>Gửi Email</label>
    <label class="checkbox inline"><input type="radio" name="rad_accept" value="phone" id="rad_accept_phone" />Gọi điện thoại</label>
    <div class="clear" style="height: 10px;"></div>
    <textarea style="width:100%;height:350px" name="txt_email_content"><?php
        printf(_CONST_INTERNET_RECORD_ACCEPT_EMAIL
                , $v_citizen_name
                , $v_receive_date
                , $arr_single_record['C_RECORD_TYPE_NAME']
                , $arr_single_record['C_RECORD_NO']
                , $v_total_time, $v_return_date_by_text);
        ?></textarea>

    <!-- Buttons -->
    <div class="button-area">
        <!--button update-->
        <button type="button" name="trash" class="btn btn-primary" onclick="btn_do_approval_internet_record_onclick();" accesskey="2">
            <i class="icon-save"></i>
            <?php echo __('update'); ?> (Alt+2)"
        </button>
        <!--Button close window-->
        <?php $v_back_action = ($v_pop_win === '') ? 'btn_back_onclick();' : 'try{window.parent.hidePopWin();}catch(e){window.close();};'; ?>
        <button type="button" name="trash" class="btn" onclick="<?php echo $v_back_action; ?>" >
            <i class="icon-remove"></i>
            <?php echo __('close window'); ?>
        </button> 
    </div>
</form>
<script>
            /**
             * Comment
             */
            function btn_do_approval_internet_record_onclick()
            {
                $("#frmMain").attr('action', $("#controller").val() + 'do_accept_internet_record');
                $("#frmMain").submit();
            }
</script>
<?php
$this->template->display('dsp_footer' . $v_pop_win . '.php');