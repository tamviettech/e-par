<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed'); ?>
<?php
//header

$this->template->title =  $this->title = $this->title = __('cadre evaluation report');
require_once(SERVER_ROOT . 'apps' . DS . $this->app_name . DS . 'dsp_header.php');

?>
<div class="container-fluid">
    <ul class="breadcrumb">
    	<li><a href="<?php echo SITE_ROOT;?>" class="icon-home"></a><span class="divider "><i class="icon-angle-right"></i></span></li>
    	<li class="active">Quản trị hệ thống<span class="divider"><i class="icon-angle-right"></i></span></li>
    	<li class="active">Báo cáo đánh giá cán bộ tiếp nhận</li>
    </ul>
    
    <form name="frmMain" id="frmMain" action="" method="POST" class="form-horizontal"><?php
        echo $this->hidden('controller',$this->get_controller_url());
        echo $this->hidden('hdn_item_id','0');
        echo $this->hidden('hdn_item_id_list','');
        echo $this->hidden('hdn_dsp_all_method','dsp_all_report');
    
        ?>
        <div class="row-fluid">
            <div class="widget-head blue">
    			<h3>Báo cáo đánh giá cán bộ</h3>
    		</div>
    		
    		<div id="div_filter">
                    Lọc theo tên
                    <div class="input-append">
                        <input  type="text" name="txt_filter" style="height: 32px"
                                value="<?php echo $v_filter;?>"
                                class="input-xlargr" size="30" autofocus="autofocus"
                                onkeypress="txt_filter_onkeypress(this.form.btn_filter,event);"
            		/>
                        <button type="button" class="btn btn-file" onclick="btn_filter_onclick();" name="btn_filter"><i class="icon-search"></i><?php echo __('filter');?></button>
    		    </div>
                    <button style="float: right;margin-right: 10px;" onclick="btn_print_onclick()" class="btn" type="button">
                        <i class="icon-list"></i>
                        Báo cáo kết quả đánh giá
                    </button>
    		</div>
            <table width="100%" class="table table-bordered table-striped">
                <col width="70%" />
            	<col width="30%" />
            	<thead>
            		<tr>
            			<th class="center">Tên cán bộ</th>
            			<th class="center">Kết quả</th>
            		</tr>
            	</thead>
                <?php 
                    if(is_array($arr_all_staff)):
                        foreach($arr_all_staff as $row):
                ?>
                    <tr class="row0" height="20" role="presentation" data-item_id="51">
            			<td><?php echo $row['C_NAME']; ?></td>
            			<td class="center"><a href="javascript:void(0)" onclick="show_result('<?php echo $row['PK_USER']; ?>')">Xem kết quả</a></td>
            		</tr>
                <?php
                        endforeach;
                    endif;
                ?>
            </table>
            <div id="dyntable_length" class="dataTables_length">
                <?php echo $this->paging2($arr_all_staff);?>
            </div>
		</div>
    </form>
</div>
<script type="text/javascript">
    function show_result(user_id) {
        url = '<?php echo $this->get_controller_url() ?>' + 'dsp_single_report/' + user_id;
        showPopWin(url, 500, 340, function() {
            $('#frmMain').submit();
        });
    }
    
    /**
    *   Show modal dialog báo cáo 
    */
    function btn_print_onclick()
    {
        var v_url = '<?php echo $this->get_controller_url() ?>' + 'dsp_cadre_report/';
        showPopWin(v_url,1000,600,false,true);
    }
</script>
<?php require SERVER_ROOT . 'apps' . DS . $this->app_name . DS . 'dsp_footer.php';