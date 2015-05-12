<?php
if (!defined('SERVER_ROOT'))
{
    exit('No direct script access allowed');
}
if(!defined('CONST_TRIGGER_SEND_SMS') OR CONST_TRIGGER_SEND_SMS != true)
{
    exit('Chức năng này chưa được kích hoạt. Vui lòng liên hệ với nhà quản trị');
}
deny_bad_http_referer();

//display header
$this->template->title = 'Gửi thông báo sms đến công dân';

$v_pop_win = isset($_REQUEST['pop_win']) ? '_pop_win' : '';
$this->template->display('dsp_header' . $v_pop_win . '.php');

?>
<style>
    #box-citizen-phone li
    {
        list-style: none;
    }
    #solid-button
    {
        text-align: center;
        margin: 0 auto;
        padding: 5px;
    }
</style>

<form name="frmMain" id="frmMain" action="<?php echo $this->get_controller_url() . 'do_send_sms'?>" method="POST" style="background-color: white;" >
    <?php
    echo $this->hidden('controller', $this->get_controller_url());
    echo $this->hidden('hdn_item_id_list', isset($arr_all_record[0]['C_LIST_RECORD_ID']) ? $arr_all_record[0]['C_LIST_RECORD_ID'] : '');
    
    echo $this->hidden('hdn_add_comment_token', Session::get('add_comment_token'));
    echo $this->user_token();
    ?>
     <!-- Thong tin chung trong Ho so -->
    <div class="group" style="padding-bottom: 5px; float: left;width: 100%">
        <div class="widget-head blue">
            <h3>
                Danh sách hồ sơ được lựa chọn gửi thông báo sms
            </h3>
        </div>
        <div class="widget-container"  id="box-citizen-phone">
            
            <table  width="100%" class="adminlist table table-bordered table-striped">
                <colgroup>
                   <col width="5%">
                   <col width="20%">
                   <col width="30%">
                   <col width="16%">
                   <col width="20%">
                   <col width="10%">
                   <col width="10%">
                   <col width="*">
                </colgroup>
                <thead>
                   <tr>
                      <th>#</th>
                      <th>Mã hồ sơ</th>
                      <th>Người đăng ký</th>
                      <th>Ngày giờ tiếp nhận</th>
                      <th>Ngày giờ hẹn trả</th>
                      <th>Số điện thoại</th>
                   </tr>
                </thead>
                <tbody>
                    <?php for($i =0;$i<count($arr_all_record);$i ++):?>
                        <?php
                            $v_record_no = $arr_all_record[$i]['C_RECORD_NO'];
                            $v_record_name = $arr_all_record[$i]['C_CITIZEN_NAME'];
                            $v_record_phone = $arr_all_record[$i]['C_RETURN_PHONE_NUMBER'];
                            $v_record_receive = $arr_all_record[$i]['C_RECEIVE_DATE'];
                            $v_record_return_date = $arr_all_record[$i]['C_RETURN_DATE'];
                            $stt = $i + 1;
                        ?>
                        <tr class="row0" role="presentation" data-item_id="38622" data-item-type="KDS01A" data-deleted="0" data-owner="1">
                           <td class="center"><?php echo $stt?></td>
                           <td><?php echo $v_record_no?></td>
                           <td><?php echo $v_record_name?> </td>
                           <td><?php echo $v_record_receive?></td>
                           <td><?php echo $v_record_return_date?></td>
                           <td>
                               <?php 
                                    echo "  <input name='txt_phone[]' class='txt_phone' type='text' value='$v_record_phone' >
                                            <input name='txt_record_code[]' class='txt_record_code' type='hidden' value='$v_record_no' >
                                            <input name='txt_record_name[]' class='txt_record_name' type='hidden' value='$v_record_name' >
                                    ";
                               ?>
                           </td>
                        </tr>
                   <?php endfor;?>
                </tbody>
             </table>
         
        </div>
    </div>
    <div class="clear">&nbsp;</div>
    <!--tab widget-->
   
    <!--solid button-->
     <div class="clear" style="height: 10px">&nbsp;</div>
     <div id="solid-button">
         <button type="button" name="trash" class="btn" onclick="btn_send_sms()" >
            Gửi
        </button> 
        <!--Button close window-->
        <?php $v_back_action = ($v_pop_win === '') ? 'btn_back_onclick();' : 'try{window.parent.hidePopWin();}catch(e){window.close();};'; ?>
        <button type="button" name="trash" class="btn" onclick="<?php echo $v_back_action; ?>" >
            <i class="icon-reply"></i>
            <?php echo __('close window'); ?>
        </button> 
    </div>
</form>
<script>
    $(document).ready(function(){
        var height_box_phong =  $('#box-citizen-phone').height()|| 0;
        if(height_box_phong > 400)
        {
            $('#box-citizen-phone').slimscroll({
                height:400
            });
        }
       
       
    });
    function btn_send_sms() 
    {
        var check_phone_is_null= false;
        $('.txt_phone').each(function(){
            var phone_current = $(this).val() || '';
            if(trim(phone_current) == '')
            {
                check_phone_is_null=  true;
            }
        });
        if(check_phone_is_null == true)
        {
            alert('Bạn cần nhập đầy đủ số điện thoại của công dân trước khi gửi sms');
            return false;
        }
        var f = document.frmMain;
        f.submit();
    }
</script>

<?php
$this->template->display('dsp_footer' . $v_pop_win . '.php');