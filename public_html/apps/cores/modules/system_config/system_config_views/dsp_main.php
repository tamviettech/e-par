<?php
defined('DS') or die('no direct access');
$this->active_menu = $this->template->active_menu = 'quan_tri_he_thong';
$this->template->title =  $this->title = $this->title = __('system config');
require_once(SERVER_ROOT . 'apps' . DS . $this->app_name . DS . 'dsp_header.php');
?>
<div class="container-fluid">
    <ul class="breadcrumb">
    	<li><a href="<?php echo SITE_ROOT;?>" class="icon-home"></a><span class="divider "><i class="icon-angle-right"></i></span></li>
    	<li class="active">Quản trị hệ thống<span class="divider"><i class="icon-angle-right"></i></span></li>
    	<li class="active">Cấu hình hệ thống</li>
    </ul>
    
    <form method="post" name="frmMain" id="frmMain" action="" class="form-horizontal"><?php
        echo $this->hidden('controller', $this->get_controller_url());
        echo $this->hidden('hdn_dsp_all_method', 'main');
        echo $this->hidden('hdn_update_method', 'update_options');

        echo $this->hidden('hdn_item_id', '1');
        echo $this->hidden('XmlData', $xml_data);
        ?>
        <div class="row-fluid">
            <div class="content-widgets light-gray">
                <div class="widget-head blue">
                    <h3><?php echo __('system config');?></h3>
                </div>
                <div class="widget-container">
                    <!-- XML data -->
                    <?php
                    $v_xml_file_name = 'options.xml';
                    if ($v_xml_file_name != '')
                    {
                        $this->load_xml($v_xml_file_name);
                        echo $this->render_form_display_single();
                    }
                    ?>

                    <div class="form-actions">
                        <button type="button" name="btn_update" class="btn btn-primary" onclick="btn_update_onclick();"><i class="icon-save"></i><?php echo __('update');?></button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<script>
    $(document).ready(function() {
        //Fill data
        var formHelper = new DynamicFormHelper('','',document.frmMain);
        formHelper.BindXmlData();
    });
</script>
<?php require SERVER_ROOT . 'apps' . DS . $this->app_name . DS . 'dsp_footer.php';
