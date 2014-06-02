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
<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed'); ?>
<?php
//header
$this->active_menu =  'quan_tri_he_thong';
$this->template->title =  $this->title = $this->title = __('listtype manager');
require_once(SERVER_ROOT . 'apps' . DS . $this->app_name . DS . 'dsp_header.php');

?>
<div class="container-fluid">
    <ul class="breadcrumb">
    	<li><a href="<?php echo SITE_ROOT;?>" class="icon-home"></a><span class="divider "><i class="icon-angle-right"></i></span></li>
    	<li class="active">Quản trị hệ thống<span class="divider"><i class="icon-angle-right"></i></span></li>
    	<li class="active">Loại danh mục</li>
    </ul>
    
    <form name="frmMain" id="frmMain" action="" method="POST" class="form-horizontal"><?php
        echo $this->hidden('controller',$this->get_controller_url());
        echo $this->hidden('hdn_item_id','0');
        echo $this->hidden('hdn_item_id_list','');
    
        echo $this->hidden('hdn_dsp_single_method','dsp_single_listtype');
        echo $this->hidden('hdn_dsp_all_method','dsp_all_listtype');
        echo $this->hidden('hdn_update_method','update_listtype');
        echo $this->hidden('hdn_delete_method','delete_listtype');
    
        ?>
        <div class="row-fluid">
            <div class="widget-head blue">
    			<h3><?php echo __('listtype manager');?></h3>
    		</div>
    		
    		<div id="div_filter">
    		    <?php echo __('filter by listtype name')?>
    		    <div class="input-append">
            		<input type="text" name="txt_filter"
                        value="<?php echo $v_filter;?>"
                        class="input-xlargr" size="30" autofocus="autofocus"
                        onkeypress="txt_filter_onkeypress(this.form.btn_filter,event);"
            		/>
        		    <button type="button" class="btn btn-file" onclick="btn_filter_onclick();" name="btn_filter"><i class="icon-search"></i><?php echo __('filter');?></button>
    		    </div>
    		</div>
    		<div class="form-actions">
                <button type="button" name="btn_addnew" class="btn btn-primary" onclick="btn_addnew_onclick();"><i class="icon-plus"></i><?php echo __('add new');?></button>
    			<button type="button" class="btn btn-danger" onclick="btn_delete_onclick();"><i class="icon-trash"></i><?php echo __('delete');?></button>
    		</div>
            <?php
            //==========================================================================
            $arr_all_listtype = $VIEW_DATA['arr_all_listtype'];
            $this->load_xml('xml_listtype.xml');
            echo $this->render_form_display_all($arr_all_listtype);
            ?>
            <div id="dyntable_length" class="dataTables_length">
                <?php echo $this->paging2($arr_all_listtype);?>
            </div>
            
            <div class="form-actions">
                <button type="button" name="btn_addnew" class="btn btn-primary" onclick="btn_addnew_onclick();"><i class="icon-plus"></i><?php echo __('add new');?></button>
    			<button type="button" class="btn btn-danger" onclick="btn_delete_onclick();"><i class="icon-trash"></i><?php echo __('delete');?></button>
    		</div>
		</div>
    </form>
</div>
<?php require SERVER_ROOT . 'apps' . DS . $this->app_name . DS . 'dsp_footer.php';