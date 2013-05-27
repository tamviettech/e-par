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