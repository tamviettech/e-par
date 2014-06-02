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
<?php
defined('DS') or die;
/* @var $this \View */
$this->template->title = 'Báo cáo hồ sơ trả kết quả quá hạn';
$this->template->display('dsp_header.php');

?>
<style>
    table td{padding: 3px;}
</style>
<form id="frmMain" method="post">
    <?php echo $this->hidden('controller', $this->get_controller_url()); ?>
    <label style="display:inline;"><b>Lĩnh vực</b></label>
    <select name="sel_spec" id="sel_spec" style="color: #000000;">
            <option value="">-- Tất cả lĩnh vực --</option>
            <?php echo $this->generate_select_option($arr_all_spec,NULL); ?>
    </select>
    <br>
    <label for="txt_begin" style="display:inline;"><b>Ngày trả từ: </b></label>
    <input type="text" id="txt_begin" onclick="DoCal('txt_begin')" value="<?php echo date('d-m-Y') ?>"/>
    <img class="btndate" style="cursor:pointer" src="<?php echo SITE_ROOT;?>public/images/calendar.gif" onclick="DoCal('txt_begin')">
    &nbsp;
    <label for="txt_end" style="display:inline;"><b>Đến: </b></label>
    <input type="text" id="txt_end" onclick="DoCal('txt_end')" value="<?php echo date('d-m-Y') ?>"/>
    <img class="btndate" style="cursor:pointer" src="<?php echo SITE_ROOT;?>public/images/calendar.gif" onclick="DoCal('txt_end')">
    <br/>
    <center>
        <!--button in-->
        <button type="button" name="trash" class="btn btn-info" onclick="btn_print_onclick();">
            <i class="icon-print"></i>
            In báo cáo
        </button>
    </center>
</form>

<script type="text/javascript">
        function btn_print_onclick() {
            var url = $('#controller').val() + 'type/18/' + QS + 'pdf=true'
                    + '&begin=' + $('#txt_begin').val()
                    + '&end=' + $('#txt_end').val()
                    + '&spec=' + $('#sel_spec').val();
            window.showPopWin(url, 1000, 600);
        }
</script>
<?php
$this->template->display('dsp_footer.php');