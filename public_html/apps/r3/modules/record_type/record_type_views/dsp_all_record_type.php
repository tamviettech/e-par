<?php
/**
Copyright (C) 2012 Tam Viet Tech.

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

<?php if (!defined('SERVER_ROOT')) {exit('No direct script access allowed');}
//header
$this->template->title = 'Quản trị loại hồ sơ';
$this->template->display('dsp_header.php');

$arr_all_record_type    = $VIEW_DATA['arr_all_record_type'];

$arr_filter             = $VIEW_DATA['arr_filter'];
$v_filter               = $arr_filter['txt_filter'];
$v_rows_per_page        = $arr_filter['sel_rows_per_page'];
$v_page                 = $arr_filter['sel_goto_page'];
?>
<form name="frmMain" id="frmMain" action="#" method="POST">
    <?php
    echo $this->hidden('controller',$this->get_controller_url());
    echo $this->hidden('hdn_item_id','0');
    echo $this->hidden('hdn_item_id_list','');

    echo $this->hidden('hdn_dsp_single_method','dsp_single_record_type');
    echo $this->hidden('hdn_dsp_all_method', 'dsp_all_record_type');
    echo $this->hidden('hdn_update_method','update_record_type');
    echo $this->hidden('hdn_delete_method','delete_record_type');
    ?>

    <!-- filter -->
    <div class="clear"></div>
    <div id="div_filter">
        <label>Mã, hoặc tên loại hồ sơ</label>
        <input type="text" name="txt_filter"
            value="<?php echo $v_filter;?>"
            class="inputbox" size="30" autofocus="autofocus"
            onkeypress="txt_filter_onkeypress(this.form.btn_filter,event);"
        />
        <input type="button" class="filter_button search" onclick="btn_filter_onclick();" name="btn_filter" value="<?php echo __('filter');?>"
        />
    </div>
    <?php
    $xml_file = strtolower('xml_record_type_list.xml');
    if ($this->load_xml($xml_file))
    {
        echo $this->render_form_display_all($arr_all_record_type);
    }
    
    //Phan trang
    echo $this->paging2($arr_all_record_type);
    ?>
    <div class="clear"></div>
    <div class="button-area">
        <input type="button" name="addnew" class="button add" value="<?php echo __('add new');?>" onclick="btn_addnew_onclick();"/>
        <input type="button" name="trash" class="button delete" value="<?php echo __('delete');?>" onclick="btn_delete_onclick();"/>
    </div>
</form>
<script>
    $(document).ready(function() {

        //Check Contraint
    });
</script>
<?php $this->template->display('dsp_footer.php');