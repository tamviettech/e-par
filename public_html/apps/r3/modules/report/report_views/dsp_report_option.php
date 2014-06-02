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
<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed');
/* @var $this \View */
//header
$this->template->title = 'In báo cáo';

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
<?php
    echo $this->hidden('hdn_group_level','');
    function _dsp_print_button($url)
    {
        echo '<center>
                <button type="button" name="trash" class="btn btn-info" onclick="showPopWin(\'' . $url . '\', 1000, 600, null, true);">
                    <i class="icon-print"></i>
                    In báo cáo
                </button>
              </center>';
    }
    
    switch (strtolower($report_type))
    {
        case '6':
            echo $this->hidden('hdn_period');
            echo $this->hidden('hdn_year');
            ?>
<?php /*            <label>
                <strong>Đơn vị:</strong>
                &nbsp;&nbsp;&nbsp;
                <select name="sel_groupid="sel_group">
                    <?php if(!check_vilage_id()):?>
                    <option value="">--- Tất cả ---</option>
                    <?php endif;?>
                    <?php foreach($arr_all_group as $group_code => $group_name):?>
                    <option value="<?php echo $group_code?>"><?php echo $group_name?></option>
                    <?php endforeach?>
                </select>
            </label>*/?>
            <label><strong>Kỳ báo cáo:</strong></label>
            <label class="checkbox inline">
                <input type="radio" name="rad_period" id="year_period" value="year" onclick="select_period(this)" checked>
                Theo năm
            </label>
            <label class="checkbox inline">
                <input type="radio" name="rad_period" id="month_period" value="month" onclick="select_period(this)">
                Theo tháng
            </label>
            <label class="checkbox inline">
                <input type="radio" name="rad_period" id="week_period" value="week" onclick="select_period(this)">
                Theo tuần
            </label>

            <div id="div_year_period" style="display:none;">
                Năm
                <select name="sel_year" onchange="sel_year_onchange(this);">
                    <option value="">--Chọn năm--</option>
                    <?php
                        $v_max_year = $arr_year['C_MAX_YEAR'];
                        $v_min_year = $arr_year['C_MIN_YEAR'];
                        $loop = $v_max_year - $v_min_year;
                        if($loop == 0)
                        {
                            echo "<option value='$v_max_year'>$v_max_year</option>";
                        }
                        else 
                        {
                            for($i = 0;$i<=$loop;$i++)
                            {
                                echo "<option value='$v_max_year'>$v_max_year</option>";
                                $v_max_year--;
                            }
                        }
                    ?>
                </select>
            </div>

            <div id="div_month_period" style="display:none;">
                Năm
                <select name="sel_year2" onchange="sel_year_onchange(this);">
                    <option value="">--Chọn năm--</option>
                    <?php
                        $v_max_year = $arr_year['C_MAX_YEAR'];
                        $v_min_year = $arr_year['C_MIN_YEAR'];
                        $loop = $v_max_year - $v_min_year;
                        if($loop == 0)
                        {
                            echo "<option value='$v_max_year'>$v_max_year</option>";
                        }
                        else 
                        {
                            for($i = 0;$i<=$loop;$i++)
                            {
                                echo "<option value='$v_max_year'>$v_max_year</option>";
                                $v_max_year--;
                            }
                        }
                    ?>
                </select>

                Tháng
                <select name="sel_month" id="sel_month">
                    <option value="">--Chọn tháng--</option>
                    <?php for ($i=1; $i<=12; $i++):?>
                        <option value="<?php echo $i;?>"><?php echo $i;?></option>
                    <?php endfor;?>
                </select>
            </div>

            <div id="div_week_period" style="display:none;">
                Từ ngày <input type="textbox" id="txt_begin_date" name="txt_begin_date" class="text valid" value=""/>
                        <img class="btndate" style="cursor:pointer" id="btnDate" src="<?php echo SITE_ROOT;?>public/images/calendar.gif" onclick="DoCal('txt_begin_date')">
                Đến ngày <input type="textbox" id="txt_end_date" name="txt_end_date" class="text valid" />
                        <img class="btndate" style="cursor:pointer" id="btnDate" src="<?php echo SITE_ROOT;?>public/images/calendar.gif" onclick="DoCal('txt_end_date')">
            </div>

            <script>
                $(document).ready(function(){
                    $('#hdn_period').val('year');
                    $("#div_year_period").show();
                });
                function sel_year_onchange(sel_year)
                {
                    $('#hdn_year').val($(sel_year).val());
                }
                
                function select_period(obj)
                {
                    id = $(obj).attr('id');
                    $("#hdn_period").val($(obj).val());

                    $("#div_year_period").hide();
                    $("#div_month_period").hide();
                    $("#div_week_period").hide();

                    var v_div = '#div_' + id;
                    $(v_div).show();
                }

                function btn_print_onclick()
                {
                    var v_url = '<?php echo $this->get_controller_url() . 'type/' . $report_type . '/';?>';

                    v_url += QS + 'pdf=1';
                    v_url += '&period=' + $("#hdn_period").val();
                    v_url += '&year=' + $("#hdn_year").val();
                    v_url += '&month=' + $("#sel_month").val();
                    v_url += '&begin_date=' + $("#txt_begin_date").val();
                    v_url += '&end_date=' + $("#txt_end_date").val();
//                    v_url += '&group=' + $("#sel_group").val();

                    showPopWin(v_url, 1000, 600, null, true);
                }
            </script>
            <center>
                <!--button in-->
                <button type="button" name="trash" class="btn btn-info" onclick="btn_print_onclick();">
                    <i class="icon-print"></i>
                    In báo cáo
                </button>
            </center>
            <?php
            break;

        case '15':
           ?>
            <div id="div_week_period">
                <?php /*
                <label>
                    <strong>Đơn vị:</strong>
                    &nbsp;&nbsp;&nbsp;
                    <select name="sel_group" id="sel_group">
                        <?php if(!check_vilage_id()):?>
                        <option value="">--- Tất cả ---</option>
                        <?php endif;?>
                        <?php foreach($arr_all_group as $group_code => $group_name):?>
                        <option value="<?php echo $group_code?>"><?php echo $group_name?></option>
                        <?php endforeach?>
                    </select>
                </label>
                 */?>
                Lĩnh vực
                <select name='sel_spec' id='sel_spec'>
                    <option value=''>--Tất cả lĩnh vực--</option>
                    <?php echo $this->generate_select_option($arr_all_spec) ?>
                </select>
                <br/>
                Từ ngày <input type="textbox" id="txt_begin_date" name="txt_begin_date" class="text valid" value=""/>
                        <img class="btndate" style="cursor:pointer" id="btnDate" src="<?php echo SITE_ROOT;?>public/images/calendar.gif" onclick="DoCal('txt_begin_date')">
                Đến ngày <input type="textbox" id="txt_end_date" name="txt_end_date" class="text valid" />
                        <img class="btndate" style="cursor:pointer" id="btnDate" src="<?php echo SITE_ROOT;?>public/images/calendar.gif" onclick="DoCal('txt_end_date')">
            </div>
            <div class="clear" style="height: 10px">&nbsp;</div>
            <center>
                <!--button in -->
                <button type="button" name="trash" class="btn btn-info" onclick="btn_print_onclick();">
                    <i class="icon-print"></i>
                    In báo cáo
                </button>
            </center>
            <script>
                function btn_print_onclick(report_type)
                {
                    if(typeof(report_type) == 'undefined')
                    {
                        var v_url = '<?php echo $this->get_controller_url() . 'type/' . $report_type . '/';?>';
                    }
                    else
                    {
                        var v_url = '<?php echo $this->get_controller_url() . 'type/';?>' + report_type + '/' ;
                    }
                    v_url += QS + 'pdf=1';
                    v_url += '&begin_date=' + $("#txt_begin_date").val();
                    v_url += '&end_date=' + $("#txt_end_date").val();
                    v_url += '&spec=' + $("#sel_spec").val();
//                    v_url += '&group=' + $("#sel_group").val();
                    showPopWin(v_url, 1000, 600, null, true);
                }
            </script>
            <?php
            break;
        case '7':
            ?>
            <div id="div_week_period">
                Lĩnh vực
                <select name='sel_spec' id='sel_spec'>
                    <option value=''>--Tất cả lĩnh vực--</option>
                    <?php echo $this->generate_select_option($arr_all_spec) ?>
                </select>
                <br/>
                Từ ngày <input type="textbox" id="txt_begin_date" name="txt_begin_date" class="text valid" value=""/>
                        <img class="btndate" style="cursor:pointer" id="btnDate" src="<?php echo SITE_ROOT;?>public/images/calendar.gif" onclick="DoCal('txt_begin_date')">
                Đến ngày <input type="textbox" id="txt_end_date" name="txt_end_date" class="text valid" />
                        <img class="btndate" style="cursor:pointer" id="btnDate" src="<?php echo SITE_ROOT;?>public/images/calendar.gif" onclick="DoCal('txt_end_date')">
            </div>
            <div class="clear" style="height: 10px">&nbsp;</div>
            <center>
                <!--button in phi le phi-->
                <button type="button" name="trash" class="btn btn-info" onclick="btn_print_onclick();">
                    <i class="icon-print"></i>
                    In báo cáo
                </button>
            </center>
            <script>
                function btn_print_onclick()
                {
                    var v_url = '<?php echo $this->get_controller_url() . 'type/' . $report_type . '/';?>';
                    v_url += QS + 'pdf=1';
                    v_url += '&begin_date=' + $("#txt_begin_date").val();
                    v_url += '&end_date=' + $("#txt_end_date").val();
                    v_url += '&spec=' + $("#sel_spec").val();
                    showPopWin(v_url, 1000, 600, null, true);
                }
            </script>
            <?php
            break;
        case '7b':
            ?>
            <div id="div_week_period">
                Lĩnh vực
                <select name='sel_spec' id='sel_spec'>
                    <option value=''>--Tất cả lĩnh vực--</option>
                    <?php echo $this->generate_select_option($arr_all_spec) ?>
                </select>
                <br/>
                Loai hồ sơ
                <select name="sel_record_type" id="sel_record_type" style="width: 77%; color: #000000;">
                    <option value="">--Tất cả loại hồ sơ  --</option>
                    <?php foreach ($arr_all_record_type_with_spec_code as $reocord_type) : ?>
                        <?php list($id, $v_spec_code, $v_code, $v_name) = $reocord_type; ?>
                        <option value="<?php echo $id; ?>" class="<?php echo $v_spec_code; ?>"><?php echo $v_code . ' - ' . $v_name; ?></option>
                    <?php endforeach; ?>
                </select>
                <br/>
                Từ ngày <input type="textbox" id="txt_begin_date" name="txt_begin_date" class="text valid" value=""/>
                        <img class="btndate" style="cursor:pointer" id="btnDate" src="<?php echo SITE_ROOT;?>public/images/calendar.gif" onclick="DoCal('txt_begin_date')">
                Đến ngày <input type="textbox" id="txt_end_date" name="txt_end_date" class="text valid" />
                        <img class="btndate" style="cursor:pointer" id="btnDate" src="<?php echo SITE_ROOT;?>public/images/calendar.gif" onclick="DoCal('txt_end_date')">
            </div>
            <div class="clear" style="height: 10px">&nbsp;</div>
            <center>
                <!--button in phi le phi-->
                <button type="button" name="trash" class="btn btn-info" onclick="btn_print_onclick();">
                    <i class="icon-print"></i>
                    In báo cáo
                </button>
            </center>
            <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery.chained.mini.js" type="text/javascript"></script>
            <script>
                $("#sel_record_type").chained("#sel_spec");
                function btn_print_onclick()
                {
                    var v_url = '<?php echo $this->get_controller_url() . 'type/' . $report_type . '/';?>';
                    
                    v_url += QS + 'pdf=1';
                    v_url += '&begin_date=' + $("#txt_begin_date").val();
                    v_url += '&end_date=' + $("#txt_end_date").val();
                    v_url += '&spec=' + $("#sel_spec").val();
                    v_url += '&record_type=' + $("#sel_record_type").val();
                    showPopWin(v_url, 1000, 600, null, true);
                }
            </script>
            <?php
            break;
        case '16':
            ?>
            <table style="width: 100%;" class="none-border-table">
                <tr>
                    <td width="20%">Đơn vị</td>
                    <td colspan="3">
                        <select name="sel_group" id="sel_group" onchange="sel_group_onchange(this)">
                            <?php if(!check_vilage_id()):?>
                            <option value="">--- Tất cả ---</option>
                            <?php endif;?>
                            <?php foreach($arr_all_group as $group_code => $info):?>
                            <option value="<?php echo $group_code?>" data-level="<?php echo $info['C_LEVEL']?>"><?php echo $info['C_NAME']?></option>
                            <?php endforeach?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td width="20%">Lĩnh vực</td>
                    <td colspan="3">
                        <select name="sel_spec" id="sel_spec" style="width: 77%; color: #000000;">
                            <option value="">-- Tất cả lĩnh vực --</option>
                            <?php echo $this->generate_select_option($arr_all_spec,NULL); ?>
                    </select>
                    </td>
                </tr>
                <tr>
                    <td width="20%">Loai hồ sơ</td>
                    <td colspan="3">
                        <select name="sel_record_type" id="sel_record_type" style="width: 77%; color: #000000;">
                            <option value="">--Tất cả loại hồ sơ  --</option>
                            <?php foreach ($arr_all_record_type_with_spec_code as $reocord_type) :?>
                                <?php list($id, $v_spec_code, $v_code, $v_name) = $reocord_type;?>
                                <option value="<?php echo $id;?>" class="<?php echo $v_spec_code;?>"><?php echo $v_code . ' - ' . $v_name;?></option>
                            <?php endforeach;?>
                    </select>
                    </td>
                </tr>
                <tr>
                    <td width="20%">Từ ngày</td>
                    <td>
                        <input type="textbox" id="txt_begin_date" name="txt_begin_date" class="text valid" value=""/>
                        <img class="btndate" style="cursor:pointer" id="btnDate" src="<?php echo SITE_ROOT;?>public/images/calendar.gif" onclick="DoCal('txt_begin_date')">
                    </select>
                    </td>
                    <td width="20%">Đến ngày</td>
                    <td>
                        <input type="textbox" id="txt_end_date" name="txt_end_date" class="text valid" />
                        <img class="btndate" style="cursor:pointer" id="btnDate" src="<?php echo SITE_ROOT;?>public/images/calendar.gif" onclick="DoCal('txt_end_date')">
                    </select>
                    </td>
                </tr>
            </table>
            <center>
                <!--button in-->
                <button type="button" name="trash" class="btn btn-info" onclick="btn_print_onclick();">
                    <i class="icon-print"></i>
                    In báo cáo
                </button>
            </center>

            <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery.chained.mini.js" type="text/javascript"></script>
            <script>
                $("#sel_record_type").chained("#sel_spec");
                function btn_print_onclick()
                {
                    var v_url = '<?php echo $this->get_controller_url() . 'type/' . $report_type . '/';?>';
                    v_url += QS + 'pdf=1';
                    v_url += '&spec_code=' + $("#sel_spec").val();
                    v_url += '&record_type=' + $("#sel_record_type").val();
                    v_url += '&begin_date=' + $("#txt_begin_date").val();
                    v_url += '&end_date=' + $("#txt_end_date").val();
                    v_url += '&group=' + $("#sel_group").val();
                    v_url += '&group_level=' + $("#hdn_group_level").val();

                    showPopWin(v_url, 1000, 600, null, true);
                }
            </script>
            <?php
            break;
            
        case '7c':
            ?>
            <table style="width: 100%;" class="none-border-table">
                <tr>
                    <td width="20%">Lĩnh vực</td>
                    <td colspan="3">
                    <select name="sel_spec" id="sel_spec" style="width: 77%; color: #000000;">
                            <option value="">-- Tất cả lĩnh vực --</option>
                            <?php echo $this->generate_select_option($arr_all_spec,NULL); ?>
                    </select>
                    </td>
                </tr>
                <tr>
                    <td width="20%">Từ ngày</td>
                    <td>
                        <input type="textbox" id="txt_begin_date" name="txt_begin_date" class="text valid" value=""/>
                        <img class="btndate" style="cursor:pointer" id="btnDate" src="<?php echo SITE_ROOT;?>public/images/calendar.gif" onclick="DoCal('txt_begin_date')">
                    </select>
                    </td>
                    <td width="20%">Đến ngày</td>
                    <td>
                        <input type="textbox" id="txt_end_date" name="txt_end_date" class="text valid" />
                        <img class="btndate" style="cursor:pointer" id="btnDate" src="<?php echo SITE_ROOT;?>public/images/calendar.gif" onclick="DoCal('txt_end_date')">
                    </select>
                    </td>
                </tr>
            </table>
            <center>
                <input type="button" name="print" class="btn btn-info" value="In báo cáo" onclick="btn_print_onclick();" />
            </center>
            <script>
                function btn_print_onclick()
                {
                    v_url = '<?php echo $this->get_controller_url() . 'type/' . $report_type . '/';?>';
                    v_url += QS + 'pdf=1';
                    v_url += '&spec_code=' + $("#sel_spec").val();
                    v_url += '&begin_date=' + $("#txt_begin_date").val();
                    v_url += '&end_date=' + $("#txt_end_date").val();

                    showPopWin(v_url, 1000, 600, null, true);
                }
            </script>
            <?php 
            break;

//        case '3':
        case '12':
            ?>
            <label>
                <strong>Đơn vị:</strong>
                &nbsp;&nbsp;&nbsp;
                <select name="sel_group" id="sel_group" onchange="sel_group_onchange(this)">
                    <?php if(!check_vilage_id()):?>
                    <option value="">--- Tất cả ---</option>
                    <?php endif;?>
                    <?php foreach($arr_all_group as $group_code => $info):?>
                    <option value="<?php echo $group_code?>" data-level="<?php echo $info['C_LEVEL']?>"><?php echo $info['C_NAME']?></option>
                    <?php endforeach?>
                </select>
            </label>
            <label>
                <strong>Lĩnh vực:</strong>
                &nbsp;&nbsp;&nbsp;
                <select name="sel_spec" id="sel_spec">
                    <option value="">--- Tất cả ---</option>
                    <?php foreach($arr_all_spec as $spec_code => $spec_name):?>
                    <option value="<?php echo $spec_code?>"><?php echo $spec_name?></option>
                    <?php endforeach?>
                </select>
            </label>
            <center>
                <!--button in-->
                <button type="button" name="trash" class="btn btn-info" onclick="btn_print_onclick();">
                    <i class="icon-print"></i>
                    In báo cáo
                </button>
            </center>
            <script>
                function btn_print_onclick()
                {
                    var v_url = '<?php echo $this->get_controller_url() . 'type/' . $report_type ;?>';
                    v_url += QS + 'pdf=1';
                    v_url += '&spec_code=' + $("#sel_spec").val();
                    v_url += '&group=' + $("#sel_group").val();
                    v_url += '&group_level=' + $("#hdn_group_level").val();
                    
                    showPopWin(v_url, 1000, 600, null, true);
                }
            </script>
            <?php
            break;
        case '13':
            ?>
            <label>
                <strong>Đơn vị:</strong>
                &nbsp;&nbsp;&nbsp;
                <select name="sel_group" id="sel_group" onchange="sel_group_onchange(this)">
                    <?php if(!check_vilage_id()):?>
                    <option value="">--- Tất cả ---</option>
                    <?php endif;?>
                    <?php foreach($arr_all_group as $group_code => $info):?>
                    <option value="<?php echo $group_code?>" data-level="<?php echo $info['C_LEVEL']?>"><?php echo $info['C_NAME']?></option>
                    <?php endforeach?>
                </select>
            </label>
            <label>
                <strong>Lĩnh vực:</strong>
                &nbsp;&nbsp;&nbsp;
                <select name="sel_spec" id="sel_spec">
                    <option value="">--- Tất cả ---</option>
                    <?php foreach($arr_all_spec as $spec_code => $spec_name):?>
                    <option value="<?php echo $spec_code?>"><?php echo $spec_name?></option>
                    <?php endforeach?>
                </select>
            </label>
            <center>
                <!--button in-->
                <button type="button" name="trash" class="btn btn-info" onclick="btn_print_onclick();">
                    <i class="icon-print"></i>
                    In báo cáo
                </button>
            </center>
            <script>
                function btn_print_onclick()
                {
                    var v_url = '<?php echo $this->get_controller_url() . 'type/' . $report_type ;?>';
                    v_url += QS + 'pdf=1';
                    v_url += '&spec_code=' + $("#sel_spec").val();
                    v_url += '&group=' + $("#sel_group").val();
                    v_url += '&group_level=' + $("#hdn_group_level").val();
                    showPopWin(v_url, 1000, 600, null, true);
                }
            </script>
            <?php
            break;
        case '14':
        	?>
            <?php /*
            <label>
                <strong>Đơn vị:</strong>
                &nbsp;&nbsp;&nbsp;
                <select name="sel_group" id="sel_group">
                    <?php if(!check_vilage_id()):?>
                    <option value="">--- Tất cả ---</option>
                    <?php endif;?>
                    <?php foreach($arr_all_group as $group_code => $group_name):?>
                    <option value="<?php echo $group_code?>"><?php echo $group_name?></option>
                    <?php endforeach?>
                </select>
            </label>
             */?>
            <label>
                <strong>Lĩnh vực:</strong>
                &nbsp;&nbsp;&nbsp;
                <select name="sel_spec" id="sel_spec">
                    <option value="">--- Tất cả ---</option>
                    <?php foreach($arr_all_spec as $spec_code => $spec_name):?>
                    <option value="<?php echo $spec_code?>"><?php echo $spec_name?></option>
                    <?php endforeach?>
                </select>
            </label>
            <center>
                <!--button in-->
                <button type="button" name="trash" class="btn btn-info" onclick="btn_print_onclick();">
                    <i class="icon-print"></i>
                    In báo cáo
                </button>
            </center>
            <script>
                function btn_print_onclick()
                {
                    var v_url = '<?php echo $this->get_controller_url() . 'type/' . $report_type ;?>';
                    v_url += QS + 'pdf=1';
                    v_url += '&spec_code=' + $("#sel_spec").val();
//                    v_url += '&group=' + $("#sel_group").val();
                    
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
</form>
<!--script dung chung cho don vi-->
<script>
    $(document).ready(function(){
        if(typeof $('#sel_group').val() != 'undefined')
        {
            sel_group_onchange($('#sel_group'));
        }
    });
    function sel_group_onchange(group)
    {
        var level = $(group).find(':selected').attr('data-level');
       $('#hdn_group_level').val(level);
    }
</script>
<?php $this->template->display('dsp_footer.php');