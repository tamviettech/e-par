<?php
/**
Copyright (C) 2012 Tam Viet Tech. All rights reserved.

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
    
<?php if (!defined('SERVER_ROOT')) {exit('No direct script access allowed');}?>
<?php
//header
$this->template->title = __('list manager');
$this->template->display('dsp_header.php');
?>
<h2 class="module_title"><?php echo __('list manager');?></h2>
<form name="frmMain" id="frmMain" action="" method="POST">
    <?php
    echo $this->hidden('controller',$this->get_controller_url());
    echo $this->hidden('hdn_item_id','0');
    echo $this->hidden('hdn_item_id_list','');

    echo $this->hidden('hdn_dsp_single_method','dsp_single_list');
    echo $this->hidden('hdn_dsp_all_method','dsp_all_list');
    echo $this->hidden('hdn_update_method','update_list');
    echo $this->hidden('hdn_delete_method','delete_list');
    ?>
    <!-- filter -->
    <div id="div_filter">
    	<?php echo __('listtype')?>
        <select name="sel_listtype_filter" onchange="sel_listtype_filter_onchange(this.value);" style="Z-INDEX:-1;">
            <?php echo $this->generate_select_option($VIEW_DATA['arr_all_listtype_option'],$v_listtype_id);?>
        </select>

        <?php echo __('filter by list name');?>
		<input type="text" name="txt_filter"
            value="<?php echo $v_filter;?>"
            class="inputbox" size="30" autofocus="autofocus"
            onkeypress="txt_filter_onkeypress(this.form.btn_filter,event);"
		/>
		<input type="button" class="filter_button" onclick="btn_filter_onclick();"
		      name="btn_filter" value="<?php echo __('filter');?>"
		/>
	</div>
	<!-- /filter -->
    <?php
    $this->load_xml('xml_list.xml');
    echo $this->render_form_display_all($arr_all_list);

    //Phan trang
    echo $this->paging2($arr_all_list);
    ?>

    <div class="button-area">
	    <input type="button" name="addnew" class="button" value="<?php echo __('add new');?>" onclick="btn_addnew_onclick();"/>
	    <input type="button" name="trash" class="button" value="<?php echo __('delete');?>" onclick="btn_delete_onclick();"/>
	</div>
</form>
<?php $this->template->display('dsp_footer.php');