<?php
/**


This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
?>
<?php
if (!defined('SERVER_ROOT')) exit('No direct script access allowed');

count($VIEW_DATA['arr_all_record']) > 0 OR DIE();

$arr_all_record         = $VIEW_DATA['arr_all_record'];
$v_record_id_list       = $VIEW_DATA['record_id_list'];
$arr_all_exec_user      = $VIEW_DATA['arr_all_exec_user'];
$v_group_name           = $VIEW_DATA['group_name'];

$v_record_type_code = $arr_all_record[0]['C_RECORD_TYPE_CODE'];
$v_record_type_name = $arr_all_record[0]['C_RECORD_TYPE_NAME'];

//Step name ??
$v_task_code = $arr_all_record[0]['C_NEXT_TASK_CODE'];
$dom_workflow = simplexml_load_file($this->get_xml_config($v_record_type_code, 'workflow'));
$v_step_name = $dom_workflow->xpath("//step[task[@code='$v_task_code']]/@name");
$v_step_name = $v_step_name[0];

//Ho so thuoc xa, phuong nao????
$dom_xml_data = simplexml_load_string($arr_all_record[0]['C_XML_DATA']);

$v_xa_phuong = get_xml_value($dom_xml_data, "//item[@id='ddlXaPhuong']/value[last()]");
//display header
$this->template->title = 'Chuyển yêu cầu xác nhận xuống xã';

$v_pop_win = isset($_REQUEST['pop_win']) ? '_pop_win' : '';
$this->template->display('dsp_header' . $v_pop_win . '.php');
?>
<form name="frmMain" method="post" id="frmMain" action="<?php echo $this->get_controller_url();?>do_send_confirmation_request_record">
    <?php
    echo $this->hidden('controller', $this->get_controller_url());

    echo $this->hidden('hdn_dsp_single_method', 'dsp_single_record');
    echo $this->hidden('hdn_dsp_all_method', 'dsp_all_record');
    echo $this->hidden('hdn_update_method', 'update_record');
    echo $this->hidden('hdn_delete_method', 'delete_record');

    echo $this->hidden('pop_win', $v_pop_win);
    echo $this->hidden('hdn_item_id_list', $v_record_id_list);

    //Ten dangnhap cua CB thu ly chinh
    echo $this->hidden('hdn_receiver_user_code', '');
    echo $this->hidden('hdn_receiver_name', '');

    //Ma Loai HS
    echo $this->hidden('hdn_record_type_code', $v_record_type_code);
    
    //Chuyen tiep hs
    echo $this->hidden('hdn_go_to', '1');//1-> Chuyen xuong xa, 2-> Chuyen quay ve lanh dao phong.
    ?>
    <div class="panel_color_form">Danh sách hồ sơ yêu cầu xác nhận</div>
    <div class="Row">
        <div class="left-Col">
            <label for="Loại hồ sơ: ">Loại hồ sơ: </label>
        </div>
        <div class="right-Col">
            <?php echo $v_record_type_code;?> - <?php echo $v_record_type_name;?>
        </div>
    </div>

    <!-- Record list -->
    <table cellpadding="4" cellspacing="0" width="100%" class="list">
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
    
    <div class="panel_color_form"><?php echo $v_step_name;?></div>
    <!--Tab -->
    <div id="tabs_statistics">
        <ul>
            <li><a href="#go_forward" onclick="go_to(1);">Chuyển yêu cầu xác nhận xuống xã</a></li>
            <li><a href="#back_forward" onclick="go_to(2);">Chuyển lại cho lãnh đạo phòng</a></li>
        </ul>
	    <div id="go_forward">
		    <div class="Row">
		        <div class="left-Col">
		            <label>
		                Nơi nhận:
		            </label>
		        </div>
		        <div class="right-Col">
		            <?php echo $v_group_name;?>
		        </div>
		    </div>
		    <div id="divLead" class="Row">
		        <div class="left-Col">
		            Nơi nhận yêu cầu:<span class="required">*</span>
		        </div>
		        <div class="right-Col">
		            <ul id="signer">            
		            <?php for ($i=0; $i<count($arr_all_exec_user); $i++):
		                $dom_xml_group_code = simplexml_load_string('<root>' . $arr_all_exec_user[$i]['C_XML_GROUP_CODE'] . '</root>');
		                $check = (sizeof($arr_all_record) ==1) &&  get_xml_value($dom_xml_group_code, "//row[@C_CODE='$v_xa_phuong']/@C_CODE[last()]");
		                ?>
		                <li>
		                    <input type="radio" value="<?php echo $arr_all_exec_user[$i]['C_USER_LOGIN_NAME'];?>"
		                        data-receiver_name="<?php echo $arr_all_exec_user[$i]['C_NAME'];?>"
		                       id="rad_receiver_<?php echo $i;?>" name="rad_receiver"
		                       <?php echo ($check != NULL) ? ' checked' : '';?> onclick="this.form.hdn_receiver_user_code.value=this.value" />
		                    <label for="rad_receiver_<?php echo $i;?>">
		                        <?php echo $arr_all_exec_user[$i]['C_NAME'];?> <i>(<?php echo $arr_all_exec_user[$i]['C_JOB_TITLE'];?>)</i>
		                    </label>
		                </li>
		            <?php endfor;?>
		            </ul>
		        </div>
		    </div>
		    <div id="request_content" class="Row">
		        <div class="left-Col">
		            Nội dung yêu cầu:
		        </div>
		        <div class="right-Col">
		            <textarea rows="6" cols="80" name="txt_request_message_content" id="txt_request_message_content"></textarea>
		        </div>
		    </div>
	    </div><!-- Go Forward -->
	    <div id="back_forward">
	    	<div class="Row">
		        <div class="left-Col">
		            <label>
		                Đề nghị:<span class="required">*</span>
		            </label>
		        </div>
		        <div class="right-Col">
		            <label><input type="radio" name="rad_promote" id="rad_promote" value="SUPPLEMENT" />Bổ sung hồ sơ</label>
		            <label><input type="radio" name="rad_promote" value="REJECT" />Từ chối hồ sơ</label>
		        </div>
		    </div>
	    	<div class="Row">
		        <div class="left-Col">
		            <label>
		                Lý do: <span class="required">*</span>
		            </label>
		        </div>
		        <div class="right-Col">
		            <textarea rows="6" cols="80" name="txt_back_message_content" id="txt_back_message_content"></textarea>
		        </div>
		    </div>	   
		    <!-- 
		    <div class="button-area">
		    	<input type="button" name="btn_do_send_back" class="button allot" value="Chuyển" onclick="btn_do_send_back_onclick();" />
		    	<?php $v_back_action = ($v_pop_win === '') ? 'btn_back_onclick();' : 'try{window.parent.hidePopWin();}catch(e){window.close();};';?>
			    <input type="button" name="cancel" class="button close" value="<?php echo __('close window'); ?>" onclick="<?php echo $v_back_action;?>"/>
		    </div> -->
	    </div>
    </div><!-- End tabs -->
	<!-- Buttons -->
    <div class="clear">&nbsp;</div>
    <div class="button-area">
        <hr/>
        <input type="button" name="btn_do_send_confirmation_request" class="button allot" value="Chuyển" onclick="btn_do_send_confirmation_request_onclick();" />
        <?php $v_back_action = ($v_pop_win === '') ? 'btn_back_onclick();' : 'try{window.parent.hidePopWin();}catch(e){window.close();};';?>
	    <input type="button" name="cancel" class="button close" value="<?php echo __('close window'); ?>" onclick="<?php echo $v_back_action;?>"/>
    </div>    
</form>
<script>
	$(document).ready(function() {
	    var tab_statistics = $( "#tabs_statistics" ).tabs();
	});

	function go_to(a)
	{
		$("#hdn_go_to").val(a);
	}
	
    function btn_do_send_confirmation_request_onclick()
    {
        var f = document.frmMain;

        var v_go_to = $("#hdn_go_to").val();

        if (v_go_to == 1)
        {
            //Chuyen xuong xa
	        if (trim(f.txt_request_message_content.value) == "")
	        {
	            alert('Bạn chưa nhập Nội dung yêu cầu');
	            f.txt_request_message_content.focus();
	            return;
	        }
	        
	        //Lay code, name cua CB xa
	        $('input[name="rad_receiver"]').each(function(index) {
	        	if ($(this).is(':checked'))
	        	{
	        		$("#hdn_receiver_name").val($(this).attr('data-receiver_name'));
	        	}
	        });
	        
	        //Phai chon it nhat mot don vi cap xa
	        if ($("#hdn_receiver_name").val() == '')
	        {
	        	alert('Bạn chưa chọn Nơi nhận yêu cầu!');
	            return;
	        }
	        f.submit();
        }
        else if (v_go_to == 2)
        {
          	//Phai chon it nhat mot de nghi
          	if ($('input[name="rad_promote"]:checked').length == 0)
	        {
	        	alert('Bạn chưa chọn Đề nghị!');
	            return;
	        }
          	//Ly do
	        if (trim(f.txt_back_message_content.value) == "")
	        {
	            alert('Bạn chưa nhập Lý do');
	            f.txt_back_message_content.focus();
	            return;
	        }

	        f.submit();
        }
    }

</script>
<?php $this->template->display('dsp_footer' .$v_pop_win . '.php');