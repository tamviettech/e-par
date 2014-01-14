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
$this->template->title = __('listtype manager');
$this->template->display('dsp_header.php');

?>
<h2 class="module_title"><?php echo __('listtype manager');?></h2>
<form name="frmMain" id="frmMain" action="" method="POST"><?php
    echo $this->hidden('controller',$this->get_controller_url());
    echo $this->hidden('hdn_item_id','0');
    echo $this->hidden('hdn_item_id_list','');

    echo $this->hidden('hdn_dsp_single_method','dsp_single_listtype');
    echo $this->hidden('hdn_dsp_all_method','dsp_all_listtype');
    echo $this->hidden('hdn_update_method','update_listtype');
    echo $this->hidden('hdn_delete_method','delete_listtype');

    ?>
    <!-- filter -->
    <div id="div_filter">
    	<?php echo __('filter by listtype name')?>
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
    //==========================================================================
    $arr_all_listtype = $VIEW_DATA['arr_all_listtype'];
    $this->load_xml('xml_listtype.xml');
    echo $this->render_form_display_all($arr_all_listtype);

    //Phan trang
    echo $this->paging2($arr_all_listtype);
    ?>
    <div class="button-area">
	    <input type="button" name="btn_addnew" class="button" value="<?php echo __('add new');?>" onclick="btn_addnew_onclick();"/>
	    <input type="button" name="btn_delete" class="button" value="<?php echo __('delete');?>" onclick="btn_delete_onclick();"/>
	</div>
</form>
<?php $this->template->display('dsp_footer.php');