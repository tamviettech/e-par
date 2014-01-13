<?php
/**
Copyright (C) 2012 Tam Viet Tech. All rights reserved.

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

<?php
if (!defined('SERVER_ROOT'))
{
    exit('No direct script access allowed');
}
//header
$this->template->title = 'Lịch làm việc';
$this->template->display('dsp_header.php');
?>
<form name="frmMain" id="frmMain" action="#" method="POST">
    <?php
    echo $this->hidden('controller', $this->get_controller_url());

    echo $this->hidden('hdn_update_method', 'update_calendar');

    echo $this->hidden('hdn_date_off', '');
    echo $this->hidden('hdn_date_work', '');
    ?>
    <!-- Toolbar -->
    <h2 class="module_title">Thiết lập ngày nghỉ/ngày làm việc trong năm</h2>
    <!-- /Toolbar -->

    <!-- filter -->
    <div id="div_filter">
        <label>Năm:</label>
        <select name="sel_year" onchange="this.form.submit()">
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
    </div>
    <div class="calendar">
        Ngày nghỉ trong năm <?php echo $v_year_filter; ?><br/>
        <select name="sel_date_off" id="off" size="12" multiple="multiple" style="width:100px;">
            <?php echo $this->generate_select_option($arr_all_date_off, ''); ?>
        </select>
    </div>
    <div class="calendar switch_date">
        <?php if (Session::get('is_admin') == 1): ?>
            <input type="button" name="off2work" value="Chuyển sang ngày làm việc >>" /><br/>
            <input type="button" name="work2off" value="<< Chuyển sang ngày nghỉ    " />
        <?php endif; ?>
    </div>
    <div class="calendar">
        Ngày làm việc trong năm <?php echo $v_year_filter; ?><br />
        <select name="sel_date_working" id ="work"size="12" multiple="multiple" style="width:100px;">
            <?php echo $this->generate_select_option($arr_all_date_working, ''); ?>
        </select>
    </div>
    <div style="clear:both"></div>
    <div class="button-area">
        <?php if (Session::get('is_admin') == 1): ?>
            <input type="button" name="btn_update_calendar" class="button" value="Cập nhật" onclick="btn_update_calendar_onclick()"/>
        <?php endif; ?>
    </div>
</form>
<script type="text/javascript">
            $(function() {
                $(".switch_date input[type='button']").click(function() {
                    var arr = $(this).attr("name").split("2");
                    var from = arr[0];
                    var to = arr[1];
                    $("#" + from + " option:selected").each(function() {
                        $("#" + to).append($(this).clone());
                        $(this).remove();
                    });
                });
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
<?php
$this->template->display('dsp_footer.php');