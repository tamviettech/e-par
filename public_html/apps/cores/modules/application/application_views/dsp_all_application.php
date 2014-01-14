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
$this->template->title = 'Quản trị ứng dụng';
$this->template->display('dsp_header.php');
?>
<form name="frmMain" id="frmMain" action="" method="POST">
    <?php
    echo $this->hidden('controller', $this->get_controller_url());

    echo $this->hidden('hdn_item_id', '0');
    echo $this->hidden('hdn_item_id_list', '');

    echo $this->hidden('hdn_dsp_single_method', 'dsp_single_application');
    echo $this->hidden('hdn_delete_method', 'delete_application');
    ?>
    <!-- Toolbar -->
    <h2 class="module_title">Danh mục ứng dụng</h2>
    <!-- /Toolbar -->
    <?php
    $xml_file = strtolower('xml_application_list.xml');
    if ($this->load_xml($xml_file))
    {
        echo $this->render_form_display_all($VIEW_DATA['arr_all_application']);
    }

    ?>
    <div class="button-area">
        <input type="button" name="btn_addnew" class="button add" value="<?php echo __('add new');?>" onclick="btn_addnew_onclick();"/>
        <input type="button" name="btn_delete" class="button delete" value="<?php echo __('delete');?>" onclick="btn_delete_onclick();"/>
	</div>
</form>
<?php $this->template->display('dsp_footer.php');