<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed');

//display header
$this->template->title = 'Kết quả hồ sơ';

$v_pop_win = isset($_REQUEST['pop_win']) ? '_pop_win' : '';
$this->template->display('dsp_header' . $v_pop_win . '.php');
?>
<form name="frmMain" id="frmMain" action="<?php echo $this->get_controller_url();?>do_return_exchange_record" method="POST">
	<?php
	echo $this->hidden('controller',$this->get_controller_url());
	echo $this->hidden('hdn_item_id',$v_record_id);
	echo $this->hidden('hdn_item_id_list',$v_record_id);
	echo $this->hidden('sel_record_type',$record_type);
	echo $this->hidden('hdn_update_method','do_return_exchange_record');
	echo $this->hidden('pop_win','1');

    echo $this->hidden('XmlData','');
    ?>
    <div id="record_result">
        <div class="widget-head blue"><h3>Kết quả hồ sơ</h3></div>
        <div class="clear" style="height: 10px;"></div>
        <div class="Row">
            <div class="left-Col">Kết Quả hồ sơ <span class="required">(*)</span>: </div>
            <div class="right-Col">
                <textarea name="txt_result" style="width: 80%;min-height: 100px"></textarea>
            </div>
        </div>
    </div>
    <!-- Button -->
	<div class="button-area">
        <button type="button" name="update" class="btn btn-primary" onclick="btn_update_onclick();" accesskey="2">
            <i class="icon-save"></i>
            <?php echo __('update'); ?>
        </button>
        <?php $v_back_action = ($v_pop_win === '') ? 'btn_back_onclick();' : 'try{window.parent.hidePopWin();}catch(e){window.close();};';?>
        <button type="button" name="cancel" class="btn" onclick="<?php echo $v_back_action;?>" >
            <i class="icon-remove"></i>
            <?php echo __('close window'); ?>
        </button> 
	</div>
</form>
<script>
    $(document).ready(function() {
        <?php 
        $dom_workflow = simplexml_load_file($this->get_xml_config($record_type, 'workflow'));
        $dom_workflow_record_result = $dom_workflow->xpath('//results');
        $arr_workflow_record_result = $dom_workflow_record_result[0]->result;
        	
        if ($arr_workflow_record_result != NULL)
        {
            foreach ($arr_workflow_record_result as $v_result_id)
            {
                $v_result_id      = strval($v_result_id);
                echo "$('#$v_result_id').attr('checked','checked');\n";
            }//end foreach $arr_workflow_record_result
        }
        ?>
    });
</script>
<?php $this->template->display('dsp_footer' .$v_pop_win . '.php');