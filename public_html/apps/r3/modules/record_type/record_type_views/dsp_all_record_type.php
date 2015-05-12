<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed'); ?>
<?php
//header
$this->active_menu = $this->template->active_menu =  'quan_tri_ho_so';
$this->template->title =  $this->title = $this->title = 'Quản trị loại hồ sơ';
require_once(SERVER_ROOT . 'apps' . DS . $this->app_name . DS . 'dsp_header.php');

$arr_all_record_type    = $VIEW_DATA['arr_all_record_type'];

$arr_filter             = $VIEW_DATA['arr_filter'];
$v_filter               = $arr_filter['txt_filter'];
$v_rows_per_page        = $arr_filter['sel_rows_per_page'];
$v_page                 = $arr_filter['sel_goto_page'];
?>
<div class="container-fluid">
    <ul class="breadcrumb">
    	<li><a href="<?php echo SITE_ROOT;?>" class="icon-home"></a><span class="divider "><i class="icon-angle-right"></i></span></li>
        <li class="active">Quản trị hệ thống<span class="divider"><i class="icon-angle-right"></i></span></li>
        <li class="active">Loại hồ sơ </li>
    </ul>
    <form name="frmMain" id="frmMain" action="#" method="POST" class="form-horizontal"><?php
        echo $this->hidden('controller',$this->get_controller_url());
        echo $this->hidden('hdn_item_id','0');
        echo $this->hidden('hdn_item_id_list','');

        echo $this->hidden('hdn_dsp_single_method','dsp_single_record_type');
        echo $this->hidden('hdn_dsp_all_method', 'dsp_all_record_type');
        echo $this->hidden('hdn_update_method','update_record_type');
        echo $this->hidden('hdn_delete_method','delete_record_type');
        ?>

        <div class="row-fluid">
            <div class="widget-head blue">
        		<h3>Danh sách loại hồ sơ</h3>
        	</div>
            
            <div id="div_filter">
                Mã, hoặc tên loại hồ sơ
                <div class="input-append">
                    <input type="text" name="txt_filter" class="txt-search"
                        value="<?php echo $v_filter;?>"
                        class="inputbox" size="30" autofocus="autofocus"
                        onkeypress="txt_filter_onkeypress(this.form.btn_filter,event);"
                    />
                    <button type="button" class="btn btn-file" onclick="btn_filter_onclick();" name="btn_filter"><i class="icon-search"></i><?php echo __('filter');?></button>
                </div>
            </div>
            
            <?php
            $xml_file = strtolower('xml_record_type_list.xml');
            if ($this->load_xml($xml_file))
            {
                echo $this->render_form_display_all($arr_all_record_type);
            }
            ?>
            <div id="dyntable_length" class="dataTables_length">
                <?php echo $this->paging2($arr_all_record_type);?>
            </div>
            
            <div class="form-actions">
                <button type="button" name="addnew" class="btn btn-primary" onclick="btn_addnew_onclick();"><i class="icon-plus"></i><?php echo __('add new');?></button>
    			<button type="button" name="trash" class="btn" onclick="btn_delete_onclick();"><i class="icon-trash"></i><?php echo __('delete');?></button>
    		</div>
        </div>
    </form>
</div>
<?php require_once(SERVER_ROOT . 'apps' . DS . $this->app_name . DS . 'dsp_footer.php');