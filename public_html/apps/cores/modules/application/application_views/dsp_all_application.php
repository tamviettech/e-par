<?php if (!defined('SERVER_ROOT')) {exit('No direct script access allowed');}

//header
$this->title = 'Quản trị ứng dụng';
$this->show_left_side_bar = FALSE;
$this->active_menu =  'quan_tri_he_thong';
require_once(SERVER_ROOT . 'apps' . DS . $this->app_name . DS . 'dsp_header.php');?>

<div class="container-fluid">
    <ul class="breadcrumb">
    	<li><a href="<?php echo SITE_ROOT;?>" class="icon-home"></a><span class="divider "><i class="icon-angle-right"></i></span></li>
    	<li class="active">Quản trị hệ thống<span class="divider"><i class="icon-angle-right"></i></span></li>
    	<li class="active">Ứng dụng</li>
    </ul>
    					
    <form name="frmMain" id="frmMain" action="" method="POST">
        <?php
        echo $this->hidden('controller', $this->get_controller_url());
    
        echo $this->hidden('hdn_item_id', '0');
        echo $this->hidden('hdn_item_id_list', '');
    
        echo $this->hidden('hdn_dsp_single_method', 'dsp_single_application');
        echo $this->hidden('hdn_delete_method', 'delete_application');
        ?>
        
        <div class="row-fluid">
            <div class="widget-head blue">
    			<h3>Danh sách ứng dụng</h3>
    		</div>
            <?php
            $xml_file = strtolower('xml_application_list.xml');
            if ($this->load_xml($xml_file))
            {
                echo $this->render_form_display_all($VIEW_DATA['arr_all_application']);
            }
            ?>
            <div class="form-actions">
                <button type="button" name="btn_addnew" class="btn btn-primary" onclick="btn_addnew_onclick();"><i class="icon-plus"></i><?php echo __('add new');?></button>
    			<button type="button" class="btn" onclick="btn_delete_onclick();"><i class="icon-trash"></i><?php echo __('delete');?></button>
    		</div>
    	</div><!-- / .row-fluid -->
    </form>
</div><!--  /.container-fluid -->
<?php require SERVER_ROOT . 'apps' . DS . $this->app_name . DS . 'dsp_footer.php';