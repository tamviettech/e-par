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
    <label for="txt_begin"><b>Từ ngày: </b></label>
    <input type="text" id="txt_begin" onclick="DoCal('txt_begin')" value="<?php echo date('d-m-Y') ?>"/>
    <img class="btndate" style="cursor:pointer" src="/lang-giang/public/images/calendar.gif" onclick="DoCal('txt_begin')">
    &nbsp;
    <label for="txt_end"><b>Đến ngày: </b></label>
    <input type="text" id="txt_end" onclick="DoCal('txt_end')" value="<?php echo date('d-m-Y') ?>"/>
    <img class="btndate" style="cursor:pointer" src="/lang-giang/public/images/calendar.gif" onclick="DoCal('txt_end')">
    <br/>
    <input type="button" name="print" class="solid print" value="In báo cáo" onclick="btn_print_onclick();"/>
</form>

<script type="text/javascript">
        function btn_print_onclick() {
            url = $('#controller').val() + 'option/18' + QS + 'pdf=true'
                    + '&begin=' + $('#txt_begin').val()
                    + '&end=' + $('#txt_end').val();
            window.showPopWin(url, 1000, 600);
        }
</script>




<?php
$this->template->display('dsp_footer.php');