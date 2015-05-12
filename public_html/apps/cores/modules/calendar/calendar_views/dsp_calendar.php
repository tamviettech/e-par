<?php
//display header
$this->template->active_menu = $this->active_menu =  'quan_tri_he_thong';
$this->template->title = $this->title = 'Lịch làm việc';
require_once(SERVER_ROOT . 'apps' . DS . $this->app_name . DS . 'dsp_header.php');

?>
<div class="container-fluid">
    <ul class="breadcrumb">
    	<li><a href="<?php echo SITE_ROOT;?>" class="icon-home"></a><span class="divider "><i class="icon-angle-right"></i></span></li>
    	<li class="active">Quản trị hệ thống<span class="divider"><i class="icon-angle-right"></i></span></li>
    	<li class="active">Ngày làm việc/ngày nghỉ</li>
    </ul>
    <form name="frmMain" id="frmMain" action="#" method="POST" class="form-horizontal"><?php
        echo $this->hidden('controller', $this->get_controller_url());
        echo $this->hidden('hdn_update_method', 'update_calendar');
        echo $this->hidden('hdn_date_off', '');
        echo $this->hidden('hdn_date_work', '');
        ?>
        <div class="row-fluid">
            <div class="span12">
                <div class="content-widgets light-gray">
                    <div class="widget-head blue">
                        <h3>Thiết lập ngày nghỉ/ngày làm việc trong năm</h3>
                    </div>
                    
                    <div class="widget-container">
                        <!-- filter -->
                        <div id="div_filter">
                            Năm:
                            <select name="sel_year" onchange="this.form.submit()" class="input input-small">
                                <?php
                                $v_current_year = jwDate::dateNow('%Y');
                                for ($i = $v_current_year; $i <= $v_current_year + 1; $i++)
                                {
                                    echo '<option value="' . $i . '"';
                                    echo ($i == $v_year_filter) ? ' selected >' : ' >';
                                    echo $i . '</option>';
                                }
                                ?>
                            </select>
                        </div><!-- /#div_filter -->
                    
                        <div class="clear"></div>
                        <div class="controls controls-row">
                            <div class="span4">
                                <div class="widget-head orange">
                                    <h3>Ngày nghỉ trong năm <?php echo $v_year_filter; ?></h3>
                                </div>
                                <select name="sel_date_off" id="off" size="12" multiple="multiple" style="width:100%">
                                    <?php echo $this->generate_select_option($arr_all_date_off, ''); ?>
                                </select>
                                
                            </div>
                            <div class="span4 switch_date">
                                <span class="ds_arrow">
                                    <?php if (Session::get('is_admin') == 1): ?>
                                        <button class="btn ds_prev orange" name="work2off" type="button"><i class="icon-chevron-left"></i>Chuyển sang ngày nghỉ &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
                                        <br/>
                                        <br/>
                                        <button class="btn ds_next magenta" name="off2work" type="button">Chuyển sang ngày làm việc <i class="icon-chevron-right"></i></button>
                                    <?php endif;?>
                                </span>
                            </div>
                            <div class="span4">
                                <div class="widget-head magenta">
                                    <h3>Ngày làm việc trong năm <?php echo $v_year_filter; ?></h3>
                                </div>
                                
                                <select name="sel_date_working" id ="work" size="12" multiple="multiple" style="width:100%">
                                    <?php echo $this->generate_select_option($arr_all_date_working, ''); ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <?php if (Session::get('is_admin') == 1): ?>
                                <button type="button" name="btn_update_calendar" class="btn btn-primary" onclick="btn_update_calendar_onclick();"><i class="icon-save"></i><?php echo __('update');?></button>
                            <?php endif;?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<script type="text/javascript">
            $(document).ready(function() {
                $(".switch_date .btn").click(function() {
                    var arr = $(this).attr("name").split("2");
                    var from = arr[0];
                    var to = arr[1];
                    $("#" + from + " option:selected").each(function() {
                        var op = $("#" + to).find('option:first-child');
                        $("#" + to).append($(this).clone());
                        $(this).remove();
                    });
                });
                
                return false;
            });

            function btn_update_calendar_onclick()
            {
                var f = document.frmMain;

                //Lay tat ca option value cua ngay nghi
                //Chon tat
                $("#off option").each(function() {
                    $(this).attr('selected', 'selected');
                });
                var arr_date_off = $("#off").val() || [];
                f.hdn_date_off.value = arr_date_off.join(", ");

                //Lay tat ca option cua ngay lam viec
                $("#work option").each(function() {
                    $(this).attr('selected', 'selected');
                });
                var arr_date_work = $("#work").val() || [];
                f.hdn_date_work.value = arr_date_work.join(", ");

                m = $("#controller").val() + f.hdn_update_method.value + '/0/';
                $("#frmMain").attr("action", m);
                f.submit();
            }
</script>
<?php require SERVER_ROOT . 'apps' . DS . $this->app_name . DS . 'dsp_footer.php';