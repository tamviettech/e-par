<?php
/**
 * @copyright	Copyright (C) 2012 Tam Viet Tech. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author		Ngo Duc Lien <liennd@gmail.com>
 * @author		Luong Thanh Binh <ltbinh@gmail.com>
 */
?>
<?php
if (!defined('SERVER_ROOT'))
{
    exit('No direct script access allowed');
}
//display header
$this->template->title = __('update ou info');

$v_pop_win = isset($_REQUEST['pop_win']) ? '_pop_win' : '';
$this->template->display('dsp_header' . $v_pop_win . '.php');
//------------------------------------------------------------------------------

$arr_single_ou      = $VIEW_DATA['arr_single_ou'];
$arr_parent_ou_path = $VIEW_DATA['arr_parent_ou_path'];

if (isset($arr_single_ou['PK_OU']))
{
    $v_ou_id     = $arr_single_ou['PK_OU'];
    $v_name      = $arr_single_ou['C_NAME'];
    $v_order     = $arr_single_ou['C_ORDER'];
    $v_status    = $arr_single_ou['C_STATUS'];
    $v_xml_data  = $arr_single_ou['C_XML_DATA'];
    $v_level     = $arr_single_ou['C_LEVEL'];
    $v_parent_id = $arr_single_ou['FK_OU'];
}
else
{
    $v_ou_id    = 0;
    $v_name     = '';
    $v_order    = $arr_single_ou['C_ORDER'];
    $v_status   = 1;
    $v_xml_data = '';
    $v_level    = 1;
    $v_parent_id = replace_bad_char($_REQUEST['parent_ou_id']);;
}
$v_parent_patch = implode('/', $arr_parent_ou_path);

?>
<div class="container-fluid">
    <form name="frmMain" method="post" id="frmMain" action="" class="form-horizontal"><?php
        echo $this->hidden('controller', $this->get_controller_url());

        echo $this->hidden('hdn_dsp_single_method', 'dsp_single_ou');
        echo $this->hidden('hdn_dsp_all_method', 'dsp_all_ou');
        echo $this->hidden('hdn_update_method', 'update_ou');
        echo $this->hidden('hdn_delete_method', 'delete_ou');

        echo $this->hidden('hdn_item_id', $v_ou_id);
        echo $this->hidden('XmlData', $v_xml_data);

        echo $this->hidden('pop_win', $v_pop_win);
        ?>
        
        <div class="row-fluid">
            <div class="content-widgets light-gray">
                <div class="widget-head blue">
        			<h3><?php echo __('update ou'); ?></h3>
        		</div>
        		
        		<div class="widget-container">
                    <div class="control-group">
            	        <label class="control-label"><?php echo __('in ou')?></label>
            	        <div class="controls">
                            <?php echo $this->hidden('hdn_parent_ou_id', $v_parent_id);?>
                            <div class="input-append">
                                <input type="text" id="txt_ou_patch" name="txt_ou_patch" value="<?php echo $v_parent_patch?>"  disabled class="uneditable-input span7"/>
                                <button type="button" onclick="dsp_all_ou_to_add()" class="btn btn-file" title="Chọn đơn vị">
                                    <i class="icon-folder-open"></i>
                                </button>
                            </div>
            			</div>
            		</div>
                    
                    <div class="control-group">
            	        <label class="control-label">Tên đơn vị <span class="required">(*)</span></label>
            	        <div class="controls">
            	            <input type="text" name="txt_name" id="txt_name" value="<?php echo $v_name; ?>"
                                class="input" maxlength="255" style="width:80%"
                                onKeyDown="return handleEnter(this, event);"
                                data-allownull="no" data-validate="text"
                                data-name="Tên đơn vị"
                                data-xml="no" data-doc="no"
                                autofocus="autofocus"
                            />
            			</div>
            		</div>
                    
                    <div class="control-group">
            	        <label class="control-label"><?php echo __('order'); ?> <span class="required">(*)</span></label>
            	        <div class="controls">
            	            <input type="text" name="txt_order" value="<?php echo $v_order; ?>" id="txt_order"
                                class="inputbox" size="4" maxlength="3"
                                data-allownull="no" data-validate="number"
                                data-name="<?php echo __('order'); ?>"
                                data-xml="no" data-doc="no"
                            />
            			</div>
            		</div>
                    
                    <div class="control-group">
            	        <label class="control-label">Cấp đơn vị</label>
            	        <div class="controls">
                            <?php
                            $arr_levels = array(1 => 'Cấp Sở/Uỷ ban nhân dân huyện'
                                , 2 => 'Phòng ban chuyên môn thuộc Sở/Huyện'
                                , 3 => 'Uỷ ban nhân dân cấp xã');
                            ?>

            	            <?php foreach ($arr_levels as $k => $v): ?>
                                <?php $v_checked = $v_level == $k ? 'checked' : '' ?>
                                <label>
                                    <input 
                                        type="radio" <?php echo $v_checked ?> 
                                        name="rad_level" value="<?php echo $k ?>"
                                        />
                                        <?php echo $v ?>
                                </label>
                            <?php endforeach; ?>
            			</div>
            		</div>
                    
                    <!-- Button -->
                    <div class="form-actions">
                        <button type="button" name="update" class="btn btn-primary" onclick="btn_update_onclick();"><i class="icon-save"></i><?php echo __('update');?></button>
                        <?php $v_back_action = ($v_pop_win === '') ? 'btn_back_onclick();' : 'try{window.parent.hidePopWin();}catch(e){window.close();};';?>
                        <button type="button" class="btn" onclick="<?php echo $v_back_action;?>"><i class="icon-reply"></i><?php echo __('cancel');?></button>
                    </div>
                </div><!-- /.widget-container --->
            </div><!-- /.content-widgets -->
        </div><!-- /.row-fluid -->
    </form>
</div><!-- /.container-fluid -->
<script>
    function dsp_all_ou_to_add()
    {
        var url = '<?php echo $this->get_controller_url();?>dsp_all_ou_to_add/&pop_win=1';
        
        showPopWin(url, 450, 350, add_ou);
    }
    
    function add_ou(respond)
    {
        var ou_patch = respond[0].ou_patch;
        var ou_id    = respond[0].ou_id;
        
        $('#hdn_parent_ou_id').val(ou_id);
        $('#txt_ou_patch').val(ou_patch);
    }
</script>
<?php
$this->template->display('dsp_footer' . $v_pop_win . '.php');