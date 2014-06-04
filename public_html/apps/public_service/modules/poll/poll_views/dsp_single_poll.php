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
<?php if (!defined('SERVER_ROOT')) { exit('No direct script access allowed');} ?>
<?php
$v_poll_id         = isset($arr_single_poll['PK_POLL'])?$arr_single_poll['PK_POLL']:'';  
$v_poll_name       = isset($arr_single_poll['C_NAME'])?$arr_single_poll['C_NAME']:'';
$v_poll_slug       = isset($arr_single_poll['C_SLUG'])?$arr_single_poll['C_SLUG']:'';
$v_poll_status     = isset($arr_single_poll['C_STATUS'])?$arr_single_poll['C_STATUS']:'';

$v_begin_date_time  = isset($arr_single_poll['C_BEGIN_DATE'])?$arr_single_poll['C_BEGIN_DATE']:'';
$arr_begin_date_time= explode(' ', $v_begin_date_time);
$v_begin_date       = isset($arr_begin_date_time[0])?$arr_begin_date_time[0]:'';
$v_begin_time       = isset($arr_begin_date_time[1])?$arr_begin_date_time[1]:date("H:i:s");

$v_begin_end_time   = isset($arr_single_poll['C_END_DATE'])?$arr_single_poll['C_END_DATE']:'';
$arr_end_date_time  = explode(' ', $v_begin_end_time);
$v_end_date         = isset($arr_end_date_time[0])?$arr_end_date_time[0]:'';
$v_end_time         = isset($arr_end_date_time[1])?$arr_end_date_time[1]:date("H:i:s");

?>
<style>
    .Row
    {
        margin-left: 0px !important
    }
</style>
<form name="frmMain" id="frmMain" action="" method="POST">
    <?php
    echo $this->hidden('controller',$this->get_controller_url());
    echo $this->hidden('hdn_item_id',$v_poll_id);
    echo $this->hidden('hdn_item_id_list','');

    echo $this->hidden('hdn_dsp_single_method','dsp_single_poll');
    echo $this->hidden('hdn_dsp_all_method','dsp_all_poll');
    echo $this->hidden('hdn_update_method','update_poll');
    echo $this->hidden('hdn_delete_method','delete_poll');
    echo $this->hidden('XmlData','');
    
    echo $this->hidden('hdn_item_id_list_old','');
    echo $this->hidden('hdn_item_id_list_new','');
    echo $this->hidden('hdn_id_delete_answer_list','');
    //Luu dieu kien loc
    ?>
    <!-- Toolbar -->
    <h2 class="module_title">Chi tiết bình chọn</h2>
    <!-- /Toolbar -->
    <div>
        <div class="Row">
            <div class="left-Col">
              Câu hỏi
            </div>
            <div class="right-Col">
                <input type="textbox" name="txt_poll_name" id="txt_poll_name" value="<?php echo $v_poll_name;?>" 
                        data-allownull="no" data-validate="text" 
                        data-name="Câu hỏi" 
                        data-xml="no" data-doc="no" 
                        autofocus="autofocus" 
                        size="70" style="width: 460px">
            </div>
        </div>
         <div class="Row">
            <div class="left-Col">
               Câu trả lời
            </div>
            <div class="right-Col">
                <div name="div_poll_answer" id="div_poll_answer">
                    <?php 
                    foreach ($arr_all_answer as $row_answer):
                        $v_answer_id   = $row_answer['PK_POLL_DETAIL'];
                        $v_poll_answer = $row_answer['C_ANSWER'];
                        $v_vote        = $row_answer['C_VOTE'];
                    ?>
                    <div class="Row" id="row_<?php echo $v_answer_id;?>">
                        <input type="checkbox" name="chk_old" id="chk_old" value="<?php echo $v_answer_id;?>" data-poll_answer="<?php echo $v_poll_answer;?>"/>
                        <input type="textbox" name="txt_poll_answer[]" id="txt_poll_answer" value ="<?php echo $v_poll_answer;?>" size="50"/>
                        <?php echo $v_vote;?>
                    </div>
                    <?php endforeach;?>
                </div>
                <div class="button-area">
                   <?php if(check_permission('SUA_CUOC_THAM_DO_Y_KIEN','public_service') == TRUE OR check_permission('THEM_MOI_CUOC_THAM_DO_Y_KIEN','public_service') == TRUE):?>
                   <input type="button" name="btn_add_article" id="btn_add_article" class="ButtonAdd" onclick="btn_add_answer_onclick();" value="Thêm câu trả lời">
                    <input type="button" name="btn_delete_article" id="btn_delete_article" class="ButtonDelete" onclick="btn_delete_answer_onclick();" value="Xoá">
                    
                    <?php endif?>
                </div>
            </div> 
        </div>
        <div class="Row">
            <div class="left-Col">
               Ngày bắt đầu
            </div>
            <div class="right-Col">
                <input type="textbox" name="txt_begin_date" value="<?php echo $v_begin_date;?>" id="txt_begin_date" 
                       data-allownull="no" data-validate="date" 
                       data-name="Ngày bắt đầu" 
                       data-xml="no" data-doc="no" 
                       autofocus="autofocus" 
                />
                &nbsp;
                <img src="<?php echo SITE_ROOT . "apps/layout/public_service/admin/images/calendar.png"; ?>" onclick="DoCal('txt_begin_date')">
                &nbsp; : &nbsp;
                <input type="textbox" name="txt_begin_time" value="<?php echo $v_begin_time;?>"/>
            </div>
        </div>
        
        <div class="Row">
            <div class="left-Col">
               Ngày kết thúc
            </div>
            <div class="right-Col">
                <input type="textbox" name="txt_end_date" value="<?php echo $v_end_date;?>" id="txt_end_date"
                       data-allownull="no" data-validate="date" 
                       data-name="Ngày kết thúc" 
                       data-xml="no" data-doc="no" 
                       autofocus="autofocus" 
                />
                &nbsp;
                <img src="<?php echo SITE_ROOT . "apps/layout/public_service/admin/images/calendar.png"; ?>" onclick="DoCal('txt_end_date')">
                &nbsp; : &nbsp;
                <input type="textbox" name="txt_end_time" value="<?php echo $v_end_time;?>"/>
            </div>
        </div>
        
        <div class="Row">
            <div class="left-Col">
                <?php echo __('status'); ?>
            </div>
            <div class="right-Col" >
                <select name="poll_status" id="poll_status" onchange="onchange_status(this)">
                    <option value="0">Không hiển thị</option>
                    <option value="1" <?php echo ($v_poll_status==1)?'selected':'';?>>Hiển thị</option>
                </select> 
                &nbsp;
            </div>
        </div>
    </div>
        <br>
        <label class="required" id="message_err"></label>
        <br>
        <div class="button-area">
        <?php if(check_permission('SUA_CUOC_THAM_DO_Y_KIEN','public_service') == TRUE OR check_permission('THEM_MOI_CUOC_THAM_DO_Y_KIEN','public_service') == TRUE):?>
           <input type="button" name="btn_update" id="btn_update" class="ButtonAccept" value="Cập nhật" onclick="btn_accept_onclick();">
        <?php endif;?>
            <input type="button" name="btn_back" id="btn_cancel" class="ButtonBack" value="Quay lại" onclick="btn_back_onclick();">
        </div>
</form>
<script>
 
var arr_answer_delete = new Array();
function btn_add_answer_onclick()
{
   var html='';
   html+='<div class="Row" id="row_new">';
   html+='<input type="checkbox" name="chk_new" id="chk_new"/>';
   html+='<input type="textbox" name="txt_poll_answer_new" id="txt_poll_answer" value ="" size="50"/>0</div>';
   $('#div_poll_answer').append(html);
}
function btn_delete_answer_onclick()
{
   
    $('#div_poll_answer input[type="checkbox"]').each(function(index){
      if($(this).is(':checked'))
      {
          if($(this).attr('id')=='chk_old')
          {
              arr_answer_delete.push($(this).val());
          }
          $(this).parent().remove();
      }
    });
    $('#hdn_id_delete_answer_list').val(arr_answer_delete.join());
}
function btn_accept_onclick()
{
    var arr_answer_old = new Array();
    var arr_answer_new = new Array();
     $('#div_poll_answer input[name="chk_old"]').each(function(index){
         v_answer_id   = $(this).val();
         arr_answer_old.push(v_answer_id);
     });
     $('#hdn_item_id_list_old').val(arr_answer_old.join());
     
    $('#div_poll_answer input[name="txt_poll_answer_new"]').each(function(index){
        v_answer_id   = $(this).val();
        arr_answer_new.push(v_answer_id);
    });
    $('#hdn_item_id_list_new').val(arr_answer_new.join());
    //check da co cau hoi hien thi thi hong duoc submit
    btn_update_onclick();
}

</script>