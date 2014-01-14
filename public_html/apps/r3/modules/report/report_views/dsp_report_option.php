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
?>
<form name="frmMain" id="frmMain" action="" method="POST">
<?php
    function _dsp_print_button($url)
    {
        echo '<center><input type="button" name="print" class="solid print" value="In báo cáo" onclick="showPopWin(\'' . $url . '\', 1000, 600, null, true);" /></center>';
    }
    
    switch (strtolower($report_type))
    {
        case '6':
            echo $this->hidden('hdn_period');
            echo $this->hidden('hdn_year');
            ?>
            <label><strong>Kỳ báo cáo:</strong></label>
            <input type="radio" name="rad_period" id="year_period" value="year" onclick="select_period(this)"><label for="year_period">Theo năm</label>
            <input type="radio" name="rad_period" id="month_period" value="month" onclick="select_period(this)"><label for="month_period">Theo tháng</label>
            <input type="radio" name="rad_period" id="week_period" value="week" onclick="select_period(this)"><label for="week_period">Theo tuần</label>

            <div id="div_year_period" style="display:none;">
                Năm
                <select name="sel_year" onchange="this.form.hdn_year.value=this.value">
                    <option value="">--Chọn năm--</option>
                    <option value="2010">2010</option>
                    <option value="2011">2011</option>
                    <option value="2012">2012</option>
                    <option value="2013">2013</option>
                </select>
            </div>

            <div id="div_month_period" style="display:none;">
                Năm
                <select name="sel_year2" onchange="this.form.hdn_year.value=this.value">
                    <option value="">--Chọn năm--</option>
                    <option value="2010">2010</option>
                    <option value="2011">2011</option>
                    <option value="2012">2012</option>
                    <option value="2013">2013</option>
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
                function select_period(obj)
                {
                    id = $(obj).attr('id');
                    $("#hdn_period").val($(obj).val());

                    $("#div_year_period").hide();
                    $("#div_month_period").hide();
                    $("#div_week_period").hide();

                    v_div = '#div_' + id;
                    $(v_div).show();
                }

                function btn_print_onclick()
                {
                    v_url = '<?php echo $this->get_controller_url() . 'type/' . $report_type . '/';?>';

                    v_url += QS + 'pdf=1';
                    v_url += '&period=' + $("#hdn_period").val();
                    v_url += '&year=' + $("#hdn_year").val();
                    v_url += '&month=' + $("#sel_month").val();
                    v_url += '&begin_date=' + $("#txt_begin_date").val();
                    v_url += '&end_date=' + $("#txt_end_date").val();

                    showPopWin(v_url, 1000, 600, null, true);
                }
            </script>
            <center>
                <input type="button" name="print" class="solid print" value="In báo cáo" onclick="btn_print_onclick();" />
            </center>
            <?php
            break;

        case '15':
        case '7':
        case '7b':
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
            <center>
                <input type="button" name="print" class="solid print" value="In báo cáo" onclick="btn_print_onclick();" />
            </center>
            <script>
                function btn_print_onclick()
                {
                    v_url = '<?php echo $this->get_controller_url() . 'type/' . $report_type . '/';?>';
                    v_url += QS + 'pdf=1';
                    v_url += '&begin_date=' + $("#txt_begin_date").val();
                    v_url += '&end_date=' + $("#txt_end_date").val();
                    v_url += '&spec=' + $("#sel_spec").val();
                    showPopWin(v_url, 1000, 600, null, true);
                }
            </script>
            <?php
            break;

        case '16':
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
                <input type="button" name="print" class="solid print" value="In báo cáo" onclick="btn_print_onclick();" />
            </center>

            <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery.chained.mini.js" type="text/javascript"></script>
            <script>
                $("#sel_record_type").chained("#sel_spec");
                function btn_print_onclick()
                {
                    v_url = '<?php echo $this->get_controller_url() . 'type/' . $report_type . '/';?>';
                    v_url += QS + 'pdf=1';
                    v_url += '&spec_code=' + $("#sel_spec").val();
                    v_url += '&record_type=' + $("#sel_record_type").val();
                    v_url += '&begin_date=' + $("#txt_begin_date").val();
                    v_url += '&end_date=' + $("#txt_end_date").val();

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
                <input type="button" name="print" class="solid print" value="In báo cáo" onclick="btn_print_onclick();" />
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

        case '3':
        case '12':
        case '13':
        case '14':
        	$QS = check_htacces_file() ? '?' : '&';
            $v_url = $this->get_controller_url() . 'type/' . $report_type . '/' . $QS . 'pdf=1';
            _dsp_print_button($v_url);
            break;

        default:
            ?>
            <iframe src="<?php echo SITE_ROOT?>r3/liveboard" style="width:100%;height:500px; border:0; overflow: scroll;"></iframe>
            <a href="<?php echo SITE_ROOT?>r3/liveboard" target="blank"><label>Xem bảng tổng hợp đầy đủ</label></a>
            <?php 
            break;
    }
    ?>
</form>
<?php $this->template->display('dsp_footer.php');