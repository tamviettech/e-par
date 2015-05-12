<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed');
/* @var $this \View */
//header
$this->template->title = 'Báo cáo thống kê';

$this->template->arr_all_report_type = $arr_all_report_type;
$this->template->current_report_type = strtolower($report_type);

$this->template->display('dsp_header.php');

function check_vilage_id()
{
    $vilage_id = replace_bad_char($_SESSION['village_id']);
    if($vilage_id > 0)
    {
        return true;
    }
    else 
    {
        return false;
    }
}
?>
<form name="frmMain" id="frmMain" action="" method="POST">
      <div class="widget-head blue">
            <h3>
                <?php echo $repoer_title; ?>
            </h3>
        </div>
         <div class="widget-container" style="min-height: 90px;border: 1px solid #3498DB;">
            <?php
                echo $this->hidden('hdn_group_level','');
                function _dsp_print_button($url)
                {
                    echo '<center>
                            <button type="button" name="trash" class="btn" onclick="showPopWin(\'' . $url . '\', 1000, 600, null, true);">
                                <i class="icon-print"></i>
                                In báo cáo
                            </button>
                          </center>';
                }

                switch (strtolower($report_type))
                {
                    case '1':
                        ?>
                        <!--loai bao cao-->
                        <div class="row-fluid">
                            <div class="span2"><b>Loại báo cáo:</b></div>
                            <div class="span10">
                                <label style="display: inline">
                                    <input type="radio" name="rad_type" value="spec" checked />
                                    Theo Lĩnh vực
                                </label>
                                &nbsp;&nbsp;
                                <label style="display: inline">
                                    <input type="radio" name="rad_type" value="reocrd_type" />
                                    Theo TTHC
                                </label>
                            </div>
                        </div>
                        <!--don vi-->
                        <div class="row-fluid">
                            <div class="span2"><b>Đơn vị:</b></div>
                            <div class="span10">
                                <select name="sel_unit" id="sel_unit" style="min-width: 280px;">
                                    <?php echo $this->generate_select_option($arr_all_unit);?>
                                </select>
                            </div>
                        </div>
                        <!--don vi-->
                        <div class="row-fluid">
                            <div class="span2"><b>Báo cáo tình hình:</b></div>
                            <div class="span10">
                                <label style="display: inline">
                                    <input type="radio" name="rad_time" value="month" checked onclick="rad_time_onclick(this)"/>
                                    Tháng hiện tại
                                </label>
                                &nbsp;&nbsp;
                                <label style="display: inline">
                                    <input type="radio" name="rad_time" value="quater" onclick="rad_time_onclick(this)"/>
                                    Quý hiện tại
                                </label>
                                 &nbsp;&nbsp;
                                <label style="display: inline">
                                    <input type="radio" name="rad_time" value="year" onclick="rad_time_onclick(this)"/>
                                    Năm hiện tại
                                </label>
                                &nbsp;&nbsp;
                                <label style="display: inline">
                                    <input type="radio" name="rad_time" value="time" onclick="rad_time_onclick(this)"/>
                                    Theo ngày
                                </label>
                            </div>
                        </div>
                        <div class="row-fluid" id="div_condition" style="display: none;">
                            <div class="span2">&nbsp;</div>
                            <div class="span10" id="div_month" >
                                Từ ngày&nbsp;<input type="textbox" id="txt_begin_date" name="txt_begin_date" class="text valid" value=""/>
                                <img class="btndate" style="cursor:pointer" id="btnDate" src="<?php echo SITE_ROOT; ?>public/images/calendar.gif" onclick="DoCal('txt_begin_date')">
                                Đến ngày&nbsp;<input type="textbox" id="txt_end_date" name="txt_end_date" class="text valid" />
                                <img class="btndate" style="cursor:pointer" id="btnDate" src="<?php echo SITE_ROOT; ?>public/images/calendar.gif" onclick="DoCal('txt_end_date')">
                            </div>
                        </div>
                        <center>
                            <!--button in-->
                            <button type="button" name="trash" class="btn" onclick="btn_print_onclick();">
                                <i class="icon-print"></i>
                                In báo cáo
                            </button>
                        </center>
                        <script>
                            function rad_time_onclick(rad_time)
                            {
                                var type = $(rad_time).val();
                                $('#div_condition').hide();
                                if(type == 'time')
                                {
                                    $('#div_condition').show();
                                }
                            }
                            
                            function btn_print_onclick()
                            {
                                var report_type = $('input[name="rad_type"]:checked').val();
                                var report_time = $('input[name="rad_time"]:checked').val();
                                var sel_unit    = $('#sel_unit').val();
                                
                                var v_url = '<?php echo $this->get_controller_url() . 'type/' . $report_type . '/';?>';

                                v_url += QS + 'print=1';
                                v_url += '&type=' + report_type;
                                v_url += '&time=' + report_time;
                                v_url += '&unit=' + sel_unit;
                                if(report_time == 'time')
                                {
                                    v_url += '&begin_date=' + $('#txt_begin_date').val();
                                    v_url += '&end_date=' + $('#txt_end_date').val();
                                }
                                showPopWin(v_url, 1000, 600, null, true);
                            }
                        </script>
                        <?php
                        break;
                    case '2':
                        ?>
                        <!--don vi-->
                        <div class="row-fluid">
                            <div class="span2"><b>Đơn vị:</b></div>
                            <div class="span10">
                                <select name="sel_unit" id="sel_unit">
                                    <?php echo $this->generate_select_option($arr_all_unit);?>
                                </select>
                            </div>
                        </div>
                        <!--linh vuc-->
                        <div class="row-fluid">
                            <div class="span2"><b>Lĩnh vực:</b></div>
                            <div class="span10">
                                <select name='sel_spec' id='sel_spec'>
                                    <option value=''>--Tất cả lĩnh vực--</option>
                                    <?php echo $this->generate_select_option($arr_all_spec) ?>
                                </select>
                            </div>
                        </div>
                        <!--thoi gian-->
                        <div class="row-fluid">
                            <div class="span2"><b>Tiếp nhận từ:</b></div>
                            <div class="span10">
                                <input type="textbox" id="txt_begin_date" name="txt_begin_date" class="text valid" value=""/>
                                <img class="btndate" style="cursor:pointer" id="btnDate" src="<?php echo SITE_ROOT; ?>public/images/calendar.gif" onclick="DoCal('txt_begin_date')">
                                Đến ngày&nbsp;<input type="textbox" id="txt_end_date" name="txt_end_date" class="text valid" />
                                <img class="btndate" style="cursor:pointer" id="btnDate" src="<?php echo SITE_ROOT; ?>public/images/calendar.gif" onclick="DoCal('txt_end_date')">
                            </div>
                        </div>
                        <center>
                            <!--button in-->
                            <button type="button" name="trash" class="btn" onclick="btn_print_onclick();">
                                <i class="icon-print"></i>
                                In báo cáo
                            </button>
                        </center>
                        <script>
                            function btn_print_onclick()
                            {
                                var report_unit = $('#sel_unit').val();
                                var report_spec = $('#sel_spec').val();
                                var begin_date  = $('#txt_begin_date').val();
                                var end_date    = $('#txt_end_date').val();
                                
                                var v_url = '<?php echo $this->get_controller_url() . 'type/' . $report_type . '/';?>';

                                v_url += QS + 'print=1';
                                v_url += '&unit=' + report_unit;
                                v_url += '&spec=' + report_spec;
                                v_url += '&begin_date=' + begin_date;
                                v_url += '&end_date=' + end_date;
                                
                                showPopWin(v_url, 1000, 600, null, true);
                            }
                        </script>
                        <?php
                        break;
                    case '3':
                        echo $this->hidden('hdn_period');
                        echo $this->hidden('hdn_year');
                ?>
                        <!--don vi-->
                        <div class="row-fluid">
                            <div class="span2"><b>Đơn vị:</b></div>
                            <div class="span10">
                                <select name="sel_unit" id="sel_unit">
                                    <?php echo $this->generate_select_option($arr_all_unit);?>
                                </select>
                            </div>
                        </div>
                        <!--ky bao cao-->
                        <div class="row-fluid">
                            <div class="span2"><b>Kỳ báo cáo:</b></div>
                            <div class="span10">
                                <div class="span12">
                                    <label style="display: inline-block">
                                        <input type="radio" name="rad_period" id="year_period" value="day" onclick="select_period(this)" checked>
                                        <b>Theo ngày</b>
                                    </label>
                                    &nbsp;&nbsp;
                                    <label style="display: inline-block">
                                        <input type="radio" name="rad_period" id="month_period" value="month" onclick="select_period(this)">
                                        <b>Theo tháng</b>
                                    </label>
                                    &nbsp;&nbsp;
                                    <label style="display: inline-block">
                                        <input type="radio" name="rad_period" id="week_period" value="quarter" onclick="select_period(this)">
                                        <b>Theo quý</b>
                                    </label>
                                    &nbsp;&nbsp;
                                    <label style="display: inline-block">
                                        <input type="radio" name="rad_period" id="week_period" value="year" onclick="select_period(this)">
                                        <b>Theo Năm</b>
                                    </label>
                                </div>
                                <div class="span12" style="margin-left: 0px">
                                    <!--theo ngay-->
                                    <div id="period_day" class="period">
                                        Từ ngày&nbsp;
                                        <input type="textbox" id="txt_begin_date" name="txt_begin_date" class="text valid" value=""/>
                                        <img class="btndate" style="cursor:pointer" id="btnDate" src="<?php echo SITE_ROOT;?>public/images/calendar.gif" onclick="DoCal('txt_begin_date')">
                                        Đến ngày&nbsp;
                                        <input type="textbox" id="txt_end_date" name="txt_end_date" class="text valid" />
                                        <img class="btndate" style="cursor:pointer" id="btnDate" src="<?php echo SITE_ROOT;?>public/images/calendar.gif" onclick="DoCal('txt_end_date')">
                                    </div>
                                    <!--theo thang-->
                                    <div id="period_month" class="period">
                                        Năm&nbsp;
                                        <select name="sel_year" id="sel_year" onchange="sel_year_onchange(this);">
                                            <option value="">--Chọn năm--</option>
                                            <?php
                                                $v_max_year = $arr_year['C_MAX_YEAR'];
                                                $v_min_year = $arr_year['C_MIN_YEAR'];
                                                $v_cur_year = DATE('Y');
                                                $loop = $v_max_year - $v_min_year;
                                                if($loop == 0)
                                                {
                                                    echo "<option value='$v_max_year' selected>$v_max_year</option>";
                                                }
                                                else 
                                                {
                                                    for($i = 0;$i<=$loop;$i++)
                                                    {
                                                        $selected = '';
                                                        if($v_max_year == $v_cur_year)
                                                        {
                                                            $selected = 'selected';
                                                        }
                                                        echo "<option $selected value='$v_max_year'>$v_max_year</option>";
                                                        $v_max_year--;
                                                    }
                                                }
                                            ?>
                                        </select>
                                        Tháng&nbsp;
                                        <select name="sel_month" id="sel_month">
                                            <option value="">--Chọn tháng--</option>
                                            <?php for ($i=1; $i<=12; $i++):?>
                                                <option value="<?php echo $i;?>"><?php echo $i;?></option>
                                            <?php endfor;?>
                                        </select>
                                    </div>
                                    <!--theo quy-->
                                    <?php 
                                        $cur_quater = jwDate::quarterOfYear();
                                    ?>
                                    <div id="period_quarter" class="period">
                                        <label style="display: inline-block">
                                            <input type="radio" name="rad_quarter" id="year_period" value="1" checked>
                                            <b>Quý 1</b>
                                        </label>
                                        &nbsp;&nbsp;
                                        <label style="display: inline-block">
                                            <input type="radio" name="rad_quarter" id="month_period" value="2" >
                                            <b>Quý 2</b>
                                        </label>
                                        &nbsp;&nbsp;
                                        <label style="display: inline-block">
                                            <input type="radio" name="rad_quarter" id="week_period" value="3" >
                                            <b>Quý 3</b>
                                        </label>
                                        &nbsp;&nbsp;
                                        <label style="display: inline-block">
                                            <input type="radio" name="rad_quarter" id="week_period" value="4">
                                            <b>Quý 4</b>
                                        </label>
                                        <label >
                                            Năm
                                            <select name="sel_year1" id="sel_year1" onchange="sel_year_onchange(this);">
                                                <option value="">--Chọn năm--</option>
                                                <?php
                                                    $v_max_year = $arr_year['C_MAX_YEAR'];
                                                    $v_min_year = $arr_year['C_MIN_YEAR'];
                                                    $v_cur_year = DATE('Y');
                                                    $loop = $v_max_year - $v_min_year;
                                                    if($loop == 0)
                                                    {
                                                        echo "<option value='$v_max_year' selected>$v_max_year</option>";
                                                    }
                                                    else 
                                                    {
                                                        for($i = 0;$i<=$loop;$i++)
                                                        {
                                                            $selected = '';
                                                            if($v_max_year == $v_cur_year)
                                                            {
                                                                $selected = 'selected';
                                                            }
                                                            echo "<option $selected value='$v_max_year'>$v_max_year</option>";
                                                            $v_max_year--;
                                                        }
                                                    }
                                                ?>
                                            </select>
                                        </label>
                                    </div>
                                    <!--theo nam-->
                                    <div id="period_year" class="period">
                                        <select name="sel_year2" id="sel_year2" onchange="sel_year_onchange(this);">
                                            <option value="">--Chọn năm--</option>
                                            <?php
                                                $v_max_year = $arr_year['C_MAX_YEAR'];
                                                $v_min_year = $arr_year['C_MIN_YEAR'];
                                                $v_cur_year = DATE('Y');
                                                $loop = $v_max_year - $v_min_year;
                                                if($loop == 0)
                                                {
                                                    echo "<option value='$v_max_year' selected>$v_max_year</option>";
                                                }
                                                else 
                                                {
                                                    for($i = 0;$i<=$loop;$i++)
                                                    {
                                                        $selected = '';
                                                        if($v_max_year == $v_cur_year)
                                                        {
                                                            $selected = 'selected';
                                                        }
                                                        echo "<option $selected value='$v_max_year'>$v_max_year</option>";
                                                        $v_max_year--;
                                                    }
                                                }
                                            ?>
                                        </select>
                                    </div>
                                      <div class="clear" style="height: 10px;"></div>
                                </div>
                            </div>
                        </div>
                        <!--Phòng ban-->
                        <div class="row-fluid">
                            <div class="span2"><b>Phòng ban:</b></div>
                            <div class="span10">
                                <select name="sel_ou" id="sel_ou">
                                    <option value="">--- Tất cả phòng ban ---</option>
                                    <?php foreach($arr_all_group as $arr_group):
                                            $v_group_code = $arr_group['C_GROUP_CODE'];
                                            $v_village_id = $arr_group['FK_VILLAGE_ID'];
                                            $v_name = $arr_group['C_NAME'];
                                    ?>
                                    <option value="<?php echo $v_group_code?>" class="<?php echo $v_village_id?>"><?php echo $v_name?></option>
                                    <?php endforeach;?>
                                </select>
                            </div>
                        </div>
                        <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery.chained.mini.js" type="text/javascript"></script>
                        <script>
                            $("#sel_ou").chained("#sel_unit");
                            $(document).ready(function(){
                                select_period($('[name="rad_period"]').filter('[value="day"]'));
                            });
                            function sel_year_onchange(sel_year)
                            {
                                $('#hdn_year').val($(sel_year).val());
                            }

                            function select_period(obj)
                            {
                                $('.period').each(function(){
                                    $(this).hide();
                                });
                                
                                var value = $(obj).val();
                                $('#period_' + value).show();
                                
                                $('#hdn_period').val(value);
                            }

                            function btn_print_onclick()
                            {
                                var report_unit = $('#sel_unit').val();
                                var report_ou   = $('#sel_ou').val();
                                var period      = $('#hdn_period').val();
                                
                                var v_url = '<?php echo $this->get_controller_url() . 'type/' . $report_type . '/';?>';

                                v_url += QS + 'print=1';
                                v_url += '&unit=' + report_unit;
                                v_url += '&ou=' + report_ou;
                                v_url += '&period=' + period;
                                if(period == 'day')
                                {
                                    var begin_date = $('#txt_begin_date').val();
                                    var end_date   = $('#txt_end_date').val();
                                    
                                    v_url += '&begin_date=' + begin_date;
                                    v_url += '&end_date=' + end_date;
                                }
                                else if(period == 'month')
                                {
                                    var month = $('#sel_month').val();
                                    var year  = $('#sel_year').val();
                                    
                                    v_url += '&month=' + month;
                                    v_url += '&year=' + year;
                                }
                                else if(period == 'quarter')
                                {
                                    var quarter = $('[name="rad_quarter"]:checked').val();
                                    var year  = $('#sel_year1').val();
                                    
                                    v_url += '&quarter=' + quarter;
                                    v_url += '&year=' + year;
                                }
                                else if(period == 'year')
                                {
                                    var year = $('#sel_year2').val();
                                    
                                    v_url += '&year=' + year;
                                }
                                
                                showPopWin(v_url, 1000, 600, null, true);
                            }
                        </script>
                        <center>
                            <!--button in-->
                            <button type="button" name="trash" class="btn" onclick="btn_print_onclick();">
                                <i class="icon-print"></i>
                                In báo cáo
                            </button>
                        </center>
                        <?php
                        break;
                    case '4':
                        ?>
                        <!--don vi-->
                        <div class="row-fluid">
                            <div class="span2"><b>Đơn vị:</b></div>
                            <div class="span10">
                                <select name="sel_unit" id="sel_unit">
                                    <?php echo $this->generate_select_option($arr_all_unit);?>
                                </select>
                            </div>
                        </div>
                        <!--linh vuc-->
                        <div class="row-fluid">
                            <div class="span2"><b>Lĩnh vực:</b></div>
                            <div class="span10">
                                <select name='sel_spec' id='sel_spec'>
                                    <option value=''>--Tất cả lĩnh vực--</option>
                                    <?php echo $this->generate_select_option($arr_all_spec) ?>
                                </select>
                            </div>
                        </div>
                        <!--thoi gian-->
                        <div class="row-fluid">
                            <div class="span2"><b>Trả kết quả từ:</b></div>
                            <div class="span10">
                                <input type="textbox" id="txt_begin_date" name="txt_begin_date" class="text valid" value=""/>
                                <img class="btndate" style="cursor:pointer" id="btnDate" src="<?php echo SITE_ROOT; ?>public/images/calendar.gif" onclick="DoCal('txt_begin_date')">
                                Đến ngày&nbsp;<input type="textbox" id="txt_end_date" name="txt_end_date" class="text valid" />
                                <img class="btndate" style="cursor:pointer" id="btnDate" src="<?php echo SITE_ROOT; ?>public/images/calendar.gif" onclick="DoCal('txt_end_date')">
                            </div>
                        </div>
                        <center>
                            <!--button in-->
                            <button type="button" name="trash" class="btn" onclick="btn_print_onclick();">
                                <i class="icon-print"></i>
                                In báo cáo
                            </button>
                        </center>
                        <script>
                            function btn_print_onclick()
                            {
                                var report_unit = $('#sel_unit').val();
                                var report_spec = $('#sel_spec').val();
                                var begin_date  = $('#txt_begin_date').val();
                                var end_date    = $('#txt_end_date').val();
                                
                                var v_url = '<?php echo $this->get_controller_url() . 'type/' . $report_type . '/';?>';

                                v_url += QS + 'print=1';
                                v_url += '&unit=' + report_unit;
                                v_url += '&spec=' + report_spec;
                                v_url += '&begin_date=' + begin_date;
                                v_url += '&end_date=' + end_date;
                                
                                showPopWin(v_url, 1000, 600, null, true);
                            }
                        </script>
                        <?php
                        break;
                    case '5':
                        ?>
                        <!--don vi-->
                        <div class="row-fluid">
                            <div class="span2"><b>Đơn vị:</b></div>
                            <div class="span10">
                                <select name="sel_unit" id="sel_unit">
                                    <?php echo $this->generate_select_option($arr_all_unit);?>
                                </select>
                            </div>
                        </div>
                        <!--linh vuc-->
                        <div class="row-fluid">
                            <div class="span2"><b>Lĩnh vực:</b></div>
                            <div class="span10">
                                <select name='sel_spec' id='sel_spec'>
                                    <option value=''>--Tất cả lĩnh vực--</option>
                                    <?php echo $this->generate_select_option($arr_all_spec) ?>
                                </select>
                            </div>
                        </div>
                        <!--thoi gian-->
                        <div class="row-fluid">
                            <div class="span2"><b>Tiếp nhận từ:</b></div>
                            <div class="span10">
                                <input type="textbox" id="txt_begin_date" name="txt_begin_date" class="text valid" value=""/>
                                <img class="btndate" style="cursor:pointer" id="btnDate" src="<?php echo SITE_ROOT; ?>public/images/calendar.gif" onclick="DoCal('txt_begin_date')">
                                Đến ngày&nbsp;<input type="textbox" id="txt_end_date" name="txt_end_date" class="text valid" />
                                <img class="btndate" style="cursor:pointer" id="btnDate" src="<?php echo SITE_ROOT; ?>public/images/calendar.gif" onclick="DoCal('txt_end_date')">
                            </div>
                        </div>
                        <center>
                            <!--button in-->
                            <button type="button" name="trash" class="btn" onclick="btn_print_onclick();">
                                <i class="icon-print"></i>
                                In báo cáo
                            </button>
                        </center>
                        <script>
                            function btn_print_onclick()
                            {
                                var report_unit = $('#sel_unit').val();
                                var report_spec = $('#sel_spec').val();
                                var begin_date  = $('#txt_begin_date').val();
                                var end_date    = $('#txt_end_date').val();
                                
                                var v_url = '<?php echo $this->get_controller_url() . 'type/' . $report_type . '/';?>';

                                v_url += QS + 'print=1';
                                v_url += '&unit=' + report_unit;
                                v_url += '&spec=' + report_spec;
                                v_url += '&begin_date=' + begin_date;
                                v_url += '&end_date=' + end_date;
                                
                                showPopWin(v_url, 1000, 600, null, true);
                            }
                        </script>
                        <?php
                        break;
                    case '6':
                        echo $this->hidden('hdn_period');
                        echo $this->hidden('hdn_year');
                ?>
                        <!--don vi-->
                        <div class="row-fluid">
                            <div class="span2"><b>Đơn vị:</b></div>
                            <div class="span10">
                                <select name="sel_unit" id="sel_unit">
                                    <?php echo $this->generate_select_option($arr_all_unit);?>
                                </select>
                            </div>
                        </div>
                        <!--ky bao cao-->
                        <div class="row-fluid">
                            <div class="span2"><b>Kỳ báo cáo:</b></div>
                            <div class="span10">
                                <div class="span12">
                                    <label style="display: inline-block">
                                        <input type="radio" name="rad_period" id="year_period" value="day" onclick="select_period(this)" checked>
                                        <b>Theo ngày</b>
                                    </label>
                                    &nbsp;&nbsp;
                                    <label style="display: inline-block">
                                        <input type="radio" name="rad_period" id="month_period" value="month" onclick="select_period(this)">
                                        <b>Theo tháng</b>
                                    </label>
                                    &nbsp;&nbsp;
                                    <label style="display: inline-block">
                                        <input type="radio" name="rad_period" id="week_period" value="quarter" onclick="select_period(this)">
                                        <b>Theo quý</b>
                                    </label>
                                    &nbsp;&nbsp;
                                    <label style="display: inline-block">
                                        <input type="radio" name="rad_period" id="week_period" value="year" onclick="select_period(this)">
                                        <b>Theo Năm</b>
                                    </label>
                                </div>
                                <div class="span12" style="margin-left: 0px">
                                    <!--theo ngay-->
                                    <div id="period_day" class="period">
                                        Từ ngày&nbsp;
                                        <input type="textbox" id="txt_begin_date" name="txt_begin_date" class="text valid" value=""/>
                                        <img class="btndate" style="cursor:pointer" id="btnDate" src="<?php echo SITE_ROOT;?>public/images/calendar.gif" onclick="DoCal('txt_begin_date')">
                                        Đến ngày&nbsp;
                                        <input type="textbox" id="txt_end_date" name="txt_end_date" class="text valid" />
                                        <img class="btndate" style="cursor:pointer" id="btnDate" src="<?php echo SITE_ROOT;?>public/images/calendar.gif" onclick="DoCal('txt_end_date')">
                                    </div>
                                    <!--theo thang-->
                                    <div id="period_month" class="period">
                                        Năm&nbsp;
                                        <select name="sel_year" id="sel_year" onchange="sel_year_onchange(this);">
                                            <option value="">--Chọn năm--</option>
                                            <?php
                                                $v_max_year = $arr_year['C_MAX_YEAR'];
                                                $v_min_year = $arr_year['C_MIN_YEAR'];
                                                $v_cur_year = DATE('Y');
                                                $loop = $v_max_year - $v_min_year;
                                                if($loop == 0)
                                                {
                                                    echo "<option value='$v_max_year' selected>$v_max_year</option>";
                                                }
                                                else 
                                                {
                                                    for($i = 0;$i<=$loop;$i++)
                                                    {
                                                        $selected = '';
                                                        if($v_max_year == $v_cur_year)
                                                        {
                                                            $selected = 'selected';
                                                        }
                                                        echo "<option $selected value='$v_max_year'>$v_max_year</option>";
                                                        $v_max_year--;
                                                    }
                                                }
                                            ?>
                                        </select>
                                        Tháng&nbsp;
                                        <select name="sel_month" id="sel_month">
                                            <option value="">--Chọn tháng--</option>
                                            <?php for ($i=1; $i<=12; $i++):?>
                                                <option value="<?php echo $i;?>"><?php echo $i;?></option>
                                            <?php endfor;?>
                                        </select>
                                    </div>
                                    <!--theo quy-->
                                    <?php 
                                        $cur_quater = jwDate::quarterOfYear();
                                    ?>
                                    <div id="period_quarter" class="period">
                                        <label style="display: inline-block">
                                            <input type="radio" name="rad_quarter" id="year_period" value="1" checked>
                                            <b>Quý 1</b>
                                        </label>
                                        &nbsp;&nbsp;
                                        <label style="display: inline-block">
                                            <input type="radio" name="rad_quarter" id="month_period" value="2" >
                                            <b>Quý 2</b>
                                        </label>
                                        &nbsp;&nbsp;
                                        <label style="display: inline-block">
                                            <input type="radio" name="rad_quarter" id="week_period" value="3" >
                                            <b>Quý 3</b>
                                        </label>
                                        &nbsp;&nbsp;
                                        <label style="display: inline-block">
                                            <input type="radio" name="rad_quarter" id="week_period" value="4">
                                            <b>Quý 4</b>
                                        </label>
                                        <label >
                                            Năm
                                            <select name="sel_year1" id="sel_year1" onchange="sel_year_onchange(this);">
                                                <option value="">--Chọn năm--</option>
                                                <?php
                                                    $v_max_year = $arr_year['C_MAX_YEAR'];
                                                    $v_min_year = $arr_year['C_MIN_YEAR'];
                                                    $v_cur_year = DATE('Y');
                                                    $loop = $v_max_year - $v_min_year;
                                                    if($loop == 0)
                                                    {
                                                        echo "<option value='$v_max_year' selected>$v_max_year</option>";
                                                    }
                                                    else 
                                                    {
                                                        for($i = 0;$i<=$loop;$i++)
                                                        {
                                                            $selected = '';
                                                            if($v_max_year == $v_cur_year)
                                                            {
                                                                $selected = 'selected';
                                                            }
                                                            echo "<option $selected value='$v_max_year'>$v_max_year</option>";
                                                            $v_max_year--;
                                                        }
                                                    }
                                                ?>
                                            </select>
                                        </label>
                                    </div>
                                    <!--theo nam-->
                                    <div id="period_year" class="period">
                                        <select name="sel_year2" id="sel_year2" onchange="sel_year_onchange(this);">
                                            <option value="">--Chọn năm--</option>
                                            <?php
                                                $v_max_year = $arr_year['C_MAX_YEAR'];
                                                $v_min_year = $arr_year['C_MIN_YEAR'];
                                                $v_cur_year = DATE('Y');
                                                $loop = $v_max_year - $v_min_year;
                                                if($loop == 0)
                                                {
                                                    echo "<option value='$v_max_year' selected>$v_max_year</option>";
                                                }
                                                else 
                                                {
                                                    for($i = 0;$i<=$loop;$i++)
                                                    {
                                                        $selected = '';
                                                        if($v_max_year == $v_cur_year)
                                                        {
                                                            $selected = 'selected';
                                                        }
                                                        echo "<option $selected value='$v_max_year'>$v_max_year</option>";
                                                        $v_max_year--;
                                                    }
                                                }
                                            ?>
                                        </select>
                                    </div>
                                      <div class="clear" style="height: 10px;"></div>
                                </div>
                            </div>
                        </div>
                        <!--Phòng ban-->
                        <div class="row-fluid">
                            <div class="span2"><b>Phòng ban:</b></div>
                            <div class="span10">
                                <select name="sel_ou" id="sel_ou">
                                    <option value="">--- Tất cả phòng ban ---</option>
                                    <?php foreach($arr_all_group as $arr_group):
                                            $v_group_code = $arr_group['C_GROUP_CODE'];
                                            $v_village_id = $arr_group['FK_VILLAGE_ID'];
                                            $v_name = $arr_group['C_NAME'];
                                    ?>
                                    <option value="<?php echo $v_group_code?>" class="<?php echo $v_village_id?>"><?php echo $v_name?></option>
                                    <?php endforeach;?>
                                </select>
                            </div>
                        </div>
                        <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery.chained.mini.js" type="text/javascript"></script>
                        <script>
                            $("#sel_ou").chained("#sel_unit");
                            $(document).ready(function(){
                                select_period($('[name="rad_period"]').filter('[value="day"]'));
                            });
                            function sel_year_onchange(sel_year)
                            {
                                $('#hdn_year').val($(sel_year).val());
                            }

                            function select_period(obj)
                            {
                                $('.period').each(function(){
                                    $(this).hide();
                                });
                                
                                var value = $(obj).val();
                                $('#period_' + value).show();
                                
                                $('#hdn_period').val(value);
                            }

                            function btn_print_onclick()
                            {
                                var report_unit = $('#sel_unit').val();
                                var report_ou   = $('#sel_ou').val();
                                var period      = $('#hdn_period').val();
                                
                                var v_url = '<?php echo $this->get_controller_url() . 'type/' . $report_type . '/';?>';

                                v_url += QS + 'print=1';
                                v_url += '&unit=' + report_unit;
                                v_url += '&ou=' + report_ou;
                                v_url += '&period=' + period;
                                if(period == 'day')
                                {
                                    var begin_date = $('#txt_begin_date').val();
                                    var end_date   = $('#txt_end_date').val();
                                    
                                    v_url += '&begin_date=' + begin_date;
                                    v_url += '&end_date=' + end_date;
                                }
                                else if(period == 'month')
                                {
                                    var month = $('#sel_month').val();
                                    var year  = $('#sel_year').val();
                                    
                                    v_url += '&month=' + month;
                                    v_url += '&year=' + year;
                                }
                                else if(period == 'quarter')
                                {
                                    var quarter = $('[name="rad_quarter"]:checked').val();
                                    var year  = $('#sel_year1').val();
                                    
                                    v_url += '&quarter=' + quarter;
                                    v_url += '&year=' + year;
                                }
                                else if(period == 'year')
                                {
                                    var year = $('#sel_year2').val();
                                    
                                    v_url += '&year=' + year;
                                }
                                
                                showPopWin(v_url, 1000, 600, null, true);
                            }
                        </script>
                        <center>
                            <!--button in-->
                            <button type="button" name="trash" class="btn" onclick="btn_print_onclick();">
                                <i class="icon-print"></i>
                                In báo cáo
                            </button>
                        </center>
                        <?php
                        break;
                    case '7':
                        ?>
                        <!--don vi-->
                        <div class="row-fluid">
                            <div class="span2"><b>Đơn vị:</b></div>
                            <div class="span10">
                                <select name="sel_unit" id="sel_unit">
                                    <?php echo $this->generate_select_option($arr_all_unit);?>
                                </select>
                            </div>
                        </div>
                        <!--linh vuc-->
                        <div class="row-fluid">
                            <div class="span2"><b>Lĩnh vực:</b></div>
                            <div class="span10">
                                <select name='sel_spec' id='sel_spec'>
                                    <option value=''>--Tất cả lĩnh vực--</option>
                                    <?php echo $this->generate_select_option($arr_all_spec) ?>
                                </select>
                            </div>
                        </div>
                        <!--thoi gian-->
                        <div class="row-fluid">
                            <div class="span2"><b>Trả kết quả từ:</b></div>
                            <div class="span10">
                                <input type="textbox" id="txt_begin_date" name="txt_begin_date" class="text valid" value=""/>
                                <img class="btndate" style="cursor:pointer" id="btnDate" src="<?php echo SITE_ROOT; ?>public/images/calendar.gif" onclick="DoCal('txt_begin_date')">
                                Đến ngày&nbsp;<input type="textbox" id="txt_end_date" name="txt_end_date" class="text valid" />
                                <img class="btndate" style="cursor:pointer" id="btnDate" src="<?php echo SITE_ROOT; ?>public/images/calendar.gif" onclick="DoCal('txt_end_date')">
                            </div>
                        </div>
                        <center>
                            <!--button in-->
                            <button type="button" name="trash" class="btn" onclick="btn_print_onclick();">
                                <i class="icon-print"></i>
                                In báo cáo
                            </button>
                        </center>
                        <script>
                            function btn_print_onclick()
                            {
                                var report_unit = $('#sel_unit').val();
                                var report_spec = $('#sel_spec').val();
                                var begin_date  = $('#txt_begin_date').val();
                                var end_date    = $('#txt_end_date').val();
                                
                                var v_url = '<?php echo $this->get_controller_url() . 'type/' . $report_type . '/';?>';

                                v_url += QS + 'print=1';
                                v_url += '&unit=' + report_unit;
                                v_url += '&spec=' + report_spec;
                                v_url += '&begin_date=' + begin_date;
                                v_url += '&end_date=' + end_date;
                                
                                showPopWin(v_url, 1000, 600, null, true);
                            }
                        </script>
                        <?php
                        break;
                    case '8':
                        echo $this->hidden('hdn_period');
                        echo $this->hidden('hdn_year');
                ?>
                        <!--don vi-->
                        <div class="row-fluid">
                            <div class="span2"><b>Đơn vị:</b></div>
                            <div class="span10">
                                <select name="sel_unit" id="sel_unit">
                                    <?php echo $this->generate_select_option($arr_all_unit);?>
                                </select>
                            </div>
                        </div>
                        <!--ky bao cao-->
                        <div class="row-fluid">
                            <div class="span2"><b>Kỳ báo cáo:</b></div>
                            <div class="span10">
                                <div class="span12">
                                    <label style="display: inline-block">
                                        <input type="radio" name="rad_period" id="year_period" value="day" onclick="select_period(this)" checked>
                                        <b>Theo ngày</b>
                                    </label>
                                    &nbsp;&nbsp;
                                    <label style="display: inline-block">
                                        <input type="radio" name="rad_period" id="month_period" value="month" onclick="select_period(this)">
                                        <b>Theo tháng</b>
                                    </label>
                                    &nbsp;&nbsp;
                                    <label style="display: inline-block">
                                        <input type="radio" name="rad_period" id="week_period" value="quarter" onclick="select_period(this)">
                                        <b>Theo quý</b>
                                    </label>
                                    &nbsp;&nbsp;
                                    <label style="display: inline-block">
                                        <input type="radio" name="rad_period" id="week_period" value="year" onclick="select_period(this)">
                                        <b>Theo Năm</b>
                                    </label>
                                </div>
                                <div class="span12" style="margin-left: 0px">
                                    <!--theo ngay-->
                                    <div id="period_day" class="period">
                                        Từ ngày&nbsp;
                                        <input type="textbox" id="txt_begin_date" name="txt_begin_date" class="text valid" value=""/>
                                        <img class="btndate" style="cursor:pointer" id="btnDate" src="<?php echo SITE_ROOT;?>public/images/calendar.gif" onclick="DoCal('txt_begin_date')">
                                        Đến ngày&nbsp;
                                        <input type="textbox" id="txt_end_date" name="txt_end_date" class="text valid" />
                                        <img class="btndate" style="cursor:pointer" id="btnDate" src="<?php echo SITE_ROOT;?>public/images/calendar.gif" onclick="DoCal('txt_end_date')">
                                    </div>
                                    <!--theo thang-->
                                    <div id="period_month" class="period">
                                        Năm&nbsp;
                                        <select name="sel_year" id="sel_year" onchange="sel_year_onchange(this);">
                                            <option value="">--Chọn năm--</option>
                                            <?php
                                                $v_max_year = $arr_year['C_MAX_YEAR'];
                                                $v_min_year = $arr_year['C_MIN_YEAR'];
                                                $v_cur_year = DATE('Y');
                                                $loop = $v_max_year - $v_min_year;
                                                if($loop == 0)
                                                {
                                                    echo "<option value='$v_max_year' selected>$v_max_year</option>";
                                                }
                                                else 
                                                {
                                                    for($i = 0;$i<=$loop;$i++)
                                                    {
                                                        $selected = '';
                                                        if($v_max_year == $v_cur_year)
                                                        {
                                                            $selected = 'selected';
                                                        }
                                                        echo "<option $selected value='$v_max_year'>$v_max_year</option>";
                                                        $v_max_year--;
                                                    }
                                                }
                                            ?>
                                        </select>
                                        Tháng&nbsp;
                                        <select name="sel_month" id="sel_month">
                                            <option value="">--Chọn tháng--</option>
                                            <?php for ($i=1; $i<=12; $i++):?>
                                                <option value="<?php echo $i;?>"><?php echo $i;?></option>
                                            <?php endfor;?>
                                        </select>
                                    </div>
                                    <!--theo quy-->
                                    <?php 
                                        $cur_quater = jwDate::quarterOfYear();
                                    ?>
                                    <div id="period_quarter" class="period">
                                        <label style="display: inline-block">
                                            <input type="radio" name="rad_quarter" id="year_period" value="1" checked>
                                            <b>Quý 1</b>
                                        </label>
                                        &nbsp;&nbsp;
                                        <label style="display: inline-block">
                                            <input type="radio" name="rad_quarter" id="month_period" value="2" >
                                            <b>Quý 2</b>
                                        </label>
                                        &nbsp;&nbsp;
                                        <label style="display: inline-block">
                                            <input type="radio" name="rad_quarter" id="week_period" value="3" >
                                            <b>Quý 3</b>
                                        </label>
                                        &nbsp;&nbsp;
                                        <label style="display: inline-block">
                                            <input type="radio" name="rad_quarter" id="week_period" value="4">
                                            <b>Quý 4</b>
                                        </label>
                                        <label >
                                            Năm
                                            <select name="sel_year1" id="sel_year1" onchange="sel_year_onchange(this);">
                                                <option value="">--Chọn năm--</option>
                                                <?php
                                                    $v_max_year = $arr_year['C_MAX_YEAR'];
                                                    $v_min_year = $arr_year['C_MIN_YEAR'];
                                                    $v_cur_year = DATE('Y');
                                                    $loop = $v_max_year - $v_min_year;
                                                    if($loop == 0)
                                                    {
                                                        echo "<option value='$v_max_year' selected>$v_max_year</option>";
                                                    }
                                                    else 
                                                    {
                                                        for($i = 0;$i<=$loop;$i++)
                                                        {
                                                            $selected = '';
                                                            if($v_max_year == $v_cur_year)
                                                            {
                                                                $selected = 'selected';
                                                            }
                                                            echo "<option $selected value='$v_max_year'>$v_max_year</option>";
                                                            $v_max_year--;
                                                        }
                                                    }
                                                ?>
                                            </select>
                                        </label>
                                    </div>
                                    <!--theo nam-->
                                    <div id="period_year" class="period">
                                        <select name="sel_year2" id="sel_year2" onchange="sel_year_onchange(this);">
                                            <option value="">--Chọn năm--</option>
                                            <?php
                                                $v_max_year = $arr_year['C_MAX_YEAR'];
                                                $v_min_year = $arr_year['C_MIN_YEAR'];
                                                $v_cur_year = DATE('Y');
                                                $loop = $v_max_year - $v_min_year;
                                                if($loop == 0)
                                                {
                                                    echo "<option value='$v_max_year' selected>$v_max_year</option>";
                                                }
                                                else 
                                                {
                                                    for($i = 0;$i<=$loop;$i++)
                                                    {
                                                        $selected = '';
                                                        if($v_max_year == $v_cur_year)
                                                        {
                                                            $selected = 'selected';
                                                        }
                                                        echo "<option $selected value='$v_max_year'>$v_max_year</option>";
                                                        $v_max_year--;
                                                    }
                                                }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="clear" style="height: 15px;"></div>
                                </div>
                            </div>
                        </div>
                        <!--Phòng ban-->
                        <div class="row-fluid">
                            <div class="span2"><b>Phòng ban:</b></div>
                            <div class="span10">
                                <select name="sel_ou" id="sel_ou">
                                    <option value="">--- Tất cả phòng ban ---</option>
                                    <?php foreach($arr_all_group as $arr_group):
                                            $v_group_code = $arr_group['C_GROUP_CODE'];
                                            $v_village_id = $arr_group['FK_VILLAGE_ID'];
                                            $v_name = $arr_group['C_NAME'];
                                    ?>
                                    <option value="<?php echo $v_group_code?>" class="<?php echo $v_village_id?>"><?php echo $v_name?></option>
                                    <?php endforeach;?>
                                </select>
                            </div>
                        </div>
                        <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery.chained.mini.js" type="text/javascript"></script>
                        <script>
                            $("#sel_ou").chained("#sel_unit");
                            $(document).ready(function(){
                                select_period($('[name="rad_period"]').filter('[value="day"]'));
                            });
                            function sel_year_onchange(sel_year)
                            {
                                $('#hdn_year').val($(sel_year).val());
                            }

                            function select_period(obj)
                            {
                                $('.period').each(function(){
                                    $(this).hide();
                                });
                                
                                var value = $(obj).val();
                                $('#period_' + value).show();
                                
                                $('#hdn_period').val(value);
                            }

                            function btn_print_onclick()
                            {
                                var report_unit = $('#sel_unit').val();
                                var report_ou   = $('#sel_ou').val();
                                var period      = $('#hdn_period').val();
                                
                                var v_url = '<?php echo $this->get_controller_url() . 'type/' . $report_type . '/';?>';

                                v_url += QS + 'print=1';
                                v_url += '&unit=' + report_unit;
                                v_url += '&ou=' + report_ou;
                                v_url += '&period=' + period;
                                if(period == 'day')
                                {
                                    var begin_date = $('#txt_begin_date').val();
                                    var end_date   = $('#txt_end_date').val();
                                    
                                    v_url += '&begin_date=' + begin_date;
                                    v_url += '&end_date=' + end_date;
                                }
                                else if(period == 'month')
                                {
                                    var month = $('#sel_month').val();
                                    var year  = $('#sel_year').val();
                                    
                                    v_url += '&month=' + month;
                                    v_url += '&year=' + year;
                                }
                                else if(period == 'quarter')
                                {
                                    var quarter = $('[name="rad_quarter"]:checked').val();
                                    var year  = $('#sel_year1').val();
                                    
                                    v_url += '&quarter=' + quarter;
                                    v_url += '&year=' + year;
                                }
                                else if(period == 'year')
                                {
                                    var year = $('#sel_year2').val();
                                    
                                    v_url += '&year=' + year;
                                }
                                
                                showPopWin(v_url, 1000, 600, null, true);
                            }
                        </script>
                        <center>
                            <!--button in-->
                            <button type="button" name="trash" class="btn" onclick="btn_print_onclick();">
                                <i class="icon-print"></i>
                                In báo cáo
                            </button>
                        </center>
                        <?php
                        break;
                    case '9':
                        ?>
                        <!--don vi-->
                        <div class="row-fluid">
                            <div class="span2"><b>Đơn vị:</b></div>
                            <div class="span10">
                                <select name="sel_unit" id="sel_unit">
                                    <?php echo $this->generate_select_option($arr_all_unit);?>
                                </select>
                            </div>
                        </div>
                        <!--linh vuc-->
                        <div class="row-fluid">
                            <div class="span2"><b>Lĩnh vực:</b></div>
                            <div class="span10">
                                <select name='sel_spec' id='sel_spec'>
                                    <option value=''>--Tất cả lĩnh vực--</option>
                                    <?php echo $this->generate_select_option($arr_all_spec) ?>
                                </select>
                            </div>
                        </div>
                        <!--thoi gian-->
                        <div class="row-fluid">
                            <div class="span2"><b>Trả kết quả từ:</b></div>
                            <div class="span10">
                                <input type="textbox" id="txt_begin_date" name="txt_begin_date" class="text valid" value=""/>
                                <img class="btndate" style="cursor:pointer" id="btnDate" src="<?php echo SITE_ROOT; ?>public/images/calendar.gif" onclick="DoCal('txt_begin_date')">
                                Đến ngày&nbsp;<input type="textbox" id="txt_end_date" name="txt_end_date" class="text valid" />
                                <img class="btndate" style="cursor:pointer" id="btnDate" src="<?php echo SITE_ROOT; ?>public/images/calendar.gif" onclick="DoCal('txt_end_date')">
                            </div>
                        </div>
                        <center>
                            <!--button in-->
                            <button type="button" name="trash" class="btn" onclick="btn_print_onclick();">
                                <i class="icon-print"></i>
                                In báo cáo
                            </button>
                        </center>
                        <script>
                            function btn_print_onclick()
                            {
                                var report_unit = $('#sel_unit').val();
                                var report_spec = $('#sel_spec').val();
                                var begin_date  = $('#txt_begin_date').val();
                                var end_date    = $('#txt_end_date').val();
                                
                                var v_url = '<?php echo $this->get_controller_url() . 'type/' . $report_type . '/';?>';

                                v_url += QS + 'print=1';
                                v_url += '&unit=' + report_unit;
                                v_url += '&spec=' + report_spec;
                                v_url += '&begin_date=' + begin_date;
                                v_url += '&end_date=' + end_date;
                                
                                showPopWin(v_url, 1000, 600, null, true);
                            }
                        </script>
                        <?php
                        break;
                    case '10':
                        ?>
                        <!--don vi-->
                        <div class="row-fluid">
                            <div class="span2"><b>Đơn vị:</b></div>
                            <div class="span10">
                                <select name="sel_unit" id="sel_unit">
                                    <?php echo $this->generate_select_option($arr_all_unit);?>
                                </select>
                            </div>
                        </div>
                        <!--linh vuc-->
                        <div class="row-fluid">
                            <div class="span2"><b>Lĩnh vực:</b></div>
                            <div class="span10">
                                <select name='sel_spec' id='sel_spec'>
                                    <option value=''>--Tất cả lĩnh vực--</option>
                                    <?php echo $this->generate_select_option($arr_all_spec) ?>
                                </select>
                            </div>
                        </div>
                       
                        <!--thoi gian-->
                        <div class="row-fluid">
                            <div class="span2"><b>Ngày thu phí từ:</b></div>
                            <div class="span10">
                                <input type="textbox" id="txt_begin_date" name="txt_begin_date" class="text valid" value=""/>
                                <img class="btndate" style="cursor:pointer" id="btnDate" src="<?php echo SITE_ROOT; ?>public/images/calendar.gif" onclick="DoCal('txt_begin_date')">
                                Đến ngày&nbsp;<input type="textbox" id="txt_end_date" name="txt_end_date" class="text valid" />
                                <img class="btndate" style="cursor:pointer" id="btnDate" src="<?php echo SITE_ROOT; ?>public/images/calendar.gif" onclick="DoCal('txt_end_date')">
                            </div>
                        </div>
                        <center>
                            <!--button in-->
                            <button type="button" name="trash" class="btn" onclick="btn_print_onclick();">
                                <i class="icon-print"></i>
                                In báo cáo
                            </button>
                        </center>
                        <script>
                            function btn_print_onclick()
                            {
                                var report_unit = $('#sel_unit').val();
                                var report_spec = $('#sel_spec').val();
                                var begin_date  = $('#txt_begin_date').val();
                                var end_date    = $('#txt_end_date').val();
                                
                                var v_url = '<?php echo $this->get_controller_url() . 'type/' . $report_type . '/';?>';

                                v_url += QS + 'print=1';
                                v_url += '&unit=' + report_unit;
                                v_url += '&spec=' + report_spec;
                                v_url += '&begin_date=' + begin_date;
                                v_url += '&end_date=' + end_date;
                                
                                showPopWin(v_url, 1000, 600, null, true);
                            }
                        </script>
                        <?php
                        break;
                    case '11':
                        ?>
                        <!--don vi-->
                        <div class="row-fluid">
                            <div class="span2"><b>Đơn vị:</b></div>
                            <div class="span10">
                                <select name="sel_unit" id="sel_unit">
                                    <?php echo $this->generate_select_option($arr_all_unit);?>
                                </select>
                            </div>
                        </div>
                        <!--linh vuc-->
                        <div class="row-fluid">
                            <div class="span2"><b>Lĩnh vực:</b></div>
                            <div class="span10">
                                <select name='sel_spec' id='sel_spec'>
                                    <option value=''>--Tất cả lĩnh vực--</option>
                                    <?php echo $this->generate_select_option($arr_all_spec) ?>
                                </select>
                            </div>
                        </div>
                        <!--TTHC-->
                        <div class="row-fluid">
                            <div class="span2"><b>Tên TTHC:</b></div>
                            <div class="span10">
                                <select name="sel_record_type" id="sel_record_type" style="width: 77%; color: #000000;">
                                    <option value="">--Tất cả loại hồ sơ  --</option>
                                    <?php foreach ($arr_all_record_type_with_spec_code as $reocord_type) : ?>
                                        <?php list($id, $v_spec_code, $v_code, $v_name) = $reocord_type; ?>
                                        <option value="<?php echo $id; ?>" class="<?php echo $v_spec_code; ?>">
                                            <?php 
                                                    $v_name_leftmost    = get_leftmost_words($v_name, 25);
                                                    if(trim($v_name_leftmost) != '' && isset($v_name_leftmost))
                                                    {
                                                        echo $v_code . ' - '. $v_name_leftmost.'...';
                                                    }
                                                    else
                                                    {
                                                         echo $v_code . ' - '.  $v_name;
                                                    }
                                            ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <!--thoi gian-->
                        <div class="row-fluid">
                            <div class="span2"><b>Ngày thu phí từ:</b></div>
                            <div class="span10">
                                <input type="textbox" id="txt_begin_date" name="txt_begin_date" class="text valid" value=""/>
                                <img class="btndate" style="cursor:pointer" id="btnDate" src="<?php echo SITE_ROOT; ?>public/images/calendar.gif" onclick="DoCal('txt_begin_date')">
                                Đến ngày&nbsp;<input type="textbox" id="txt_end_date" name="txt_end_date" class="text valid" />
                                <img class="btndate" style="cursor:pointer" id="btnDate" src="<?php echo SITE_ROOT; ?>public/images/calendar.gif" onclick="DoCal('txt_end_date')">
                            </div>
                        </div>
                        <center>
                            <!--button in-->
                            <button type="button" name="trash" class="btn" onclick="btn_print_onclick();">
                                <i class="icon-print"></i>
                                In báo cáo
                            </button>
                        </center>
                        <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery.chained.mini.js" type="text/javascript"></script>
                        <script>
                            $("#sel_record_type").chained("#sel_spec");
                            function btn_print_onclick()
                            {
                                var report_unit = $('#sel_unit').val();
                                var report_spec = $('#sel_spec').val();
                                var begin_date  = $('#txt_begin_date').val();
                                var end_date    = $('#txt_end_date').val();
                                var record_type = $('#sel_record_type').val();
                                
                                var v_url = '<?php echo $this->get_controller_url() . 'type/' . $report_type . '/';?>';

                                v_url += QS + 'print=1';
                                v_url += '&unit=' + report_unit;
                                v_url += '&spec=' + report_spec;
                                v_url += '&begin_date=' + begin_date;
                                v_url += '&end_date=' + end_date;
                                v_url += '&record_type=' + record_type;
                                
                                showPopWin(v_url, 1000, 600, null, true);
                            }
                        </script>
                        <?php
                        break;
                    case '12':
                        ?>
                        <!--don vi-->
                        <div class="row-fluid">
                            <div class="span2"><b>Đơn vị:</b></div>
                            <div class="span10">
                                <select name="sel_unit" id="sel_unit">
                                    <?php echo $this->generate_select_option($arr_all_unit);?>
                                </select>
                            </div>
                        </div>
                        <!--linh vuc-->
                        <div class="row-fluid">
                            <div class="span2"><b>Lĩnh vực:</b></div>
                            <div class="span10">
                                <select name='sel_spec' id='sel_spec'>
                                    <option value=''>--Tất cả lĩnh vực--</option>
                                    <?php echo $this->generate_select_option($arr_all_spec) ?>
                                </select>
                            </div>
                        </div>
                        <!--TTHC-->
                        <div class="row-fluid">
                            <div class="span2"><b>Tên TTHC:</b></div>
                            <div class="span10">
                                <select name="sel_record_type" id="sel_record_type" style="width: 77%; color: #000000;">
                                    <option value="">--Tất cả loại hồ sơ  --</option>
                                    <?php foreach ($arr_all_record_type_with_spec_code as $reocord_type) : ?>
                                        <?php list($id, $v_spec_code, $v_code, $v_name) = $reocord_type; ?>
                                        <option value="<?php echo $id; ?>" class="<?php echo $v_spec_code; ?>">
                                            <?php 
                                                    $v_name_leftmost    = get_leftmost_words($v_name, 25);
                                                    if(trim($v_name_leftmost) != '' && isset($v_name_leftmost))
                                                    {
                                                        echo $v_code . ' - '. $v_name_leftmost.'...';
                                                    }
                                                    else
                                                    {
                                                         echo $v_code . ' - '.  $v_name;
                                                    }
                                            ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <!--thoi gian-->
                        <div class="row-fluid">
                            <div class="span2"><b>Ngày thu phí từ:</b></div>
                            <div class="span10">
                                <input type="textbox" id="txt_begin_date" name="txt_begin_date" class="text valid" value=""/>
                                <img class="btndate" style="cursor:pointer" id="btnDate" src="<?php echo SITE_ROOT; ?>public/images/calendar.gif" onclick="DoCal('txt_begin_date')">
                                Đến ngày&nbsp;<input type="textbox" id="txt_end_date" name="txt_end_date" class="text valid" />
                                <img class="btndate" style="cursor:pointer" id="btnDate" src="<?php echo SITE_ROOT; ?>public/images/calendar.gif" onclick="DoCal('txt_end_date')">
                            </div>
                        </div>
                        <center>
                            <!--button in-->
                            <button type="button" name="trash" class="btn" onclick="btn_print_onclick();">
                                <i class="icon-print"></i>
                                In báo cáo
                            </button>
                        </center>
                        <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery.chained.mini.js" type="text/javascript"></script>
                        <script>
                            $("#sel_record_type").chained("#sel_spec");
                            function btn_print_onclick()
                            {
                                var report_unit = $('#sel_unit').val();
                                var report_spec = $('#sel_spec').val();
                                var begin_date  = $('#txt_begin_date').val();
                                var end_date    = $('#txt_end_date').val();
                                var record_type = $('#sel_record_type').val();
                                
                                var v_url = '<?php echo $this->get_controller_url() . 'type/' . $report_type . '/';?>';

                                v_url += QS + 'print=1';
                                v_url += '&unit=' + report_unit;
                                v_url += '&spec=' + report_spec;
                                v_url += '&begin_date=' + begin_date;
                                v_url += '&end_date=' + end_date;
                                v_url += '&record_type=' + record_type;
                                
                                showPopWin(v_url, 1000, 600, null, true);
                            }
                        </script>
                        <?php
                        break;
                    default:
                        $QS = check_htacces_file() ? '?' : '&';
                        $v_url = $this->get_controller_url() . 'type/' . $report_type . '/' . $QS . 'pdf=1';
                        _dsp_print_button($v_url);
                }
                ?>
         </div>
</form>
<?php $this->template->display('dsp_footer.php');