<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed');

//header
$this->template->title = $this->title = 'Tập quy luật lọc hồ sơ';
$this->active_menu = $this->template->active_menu =  'quan_tri_ho_so';
$this->template->display('dsp_header.php');
?>
<form name="frmMain" id="frmMain" action="" method="POST" class="form-horizontal"><?php
    echo $this->hidden('controller',$this->get_controller_url());
    echo $this->hidden('hdn_item_id','0');
    echo $this->hidden('hdn_item_id_list','');
    
    echo $this->hidden('hdn_dsp_single_method','dsp_single_rule');
    echo $this->hidden('hdn_dsp_all_method', 'dsp_all_rule');
    echo $this->hidden('hdn_update_method','update_rule');
    echo $this->hidden('hdn_delete_method','delete_rule');
    ?>
    <div id="procedure">
        <?php if ($this->load_xml('xml_rule_list.xml')):?>
            <?php echo $this->render_form_display_all($arr_all_rule);?>
        <?php endif;?>
    </div>
    <div id="dyntable_length" class="dataTables_length">
        <?php echo $this->paging2($arr_all_rule);?>
    </div>
    
    <div class="form-actions">
        <button type="button" name="addnew" class="btn btn-primary" onclick="btn_addnew_onclick();" accesskey="2"><i class="icon-plus"></i><?php echo __('add new');?></button>
        <button type="button" name="addnew" class="btn" onclick="btn_delete_onclick();"><i class="icon-trash"></i><?php echo __('delete');?></button>
        <button type="button" name="addnew" class="btn" onclick="btn_batchrule_onclick();"><i class="icon-cogs"></i>Các luật xử lý theo lô</button>
    </div>
</form>
<script>
function btn_batchrule_onclick()
{
	var url = '<?php echo $this->get_controller_url();?>dsp_plaintext_auto_lock_unlock/';

	url += QS + 'pop_win=1';

    showPopWin(url, 650, 550);
}
    </script>
<?php $this->template->display('dsp_footer.php');