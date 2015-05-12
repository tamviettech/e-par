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
    <div class="widget-head blue">
         <h3>
                 <?php echo $repoer_title; ?>
         </h3>
     </div>
      <div class="widget-container" style="min-height: 90px;border: 1px solid #3498DB;">
    <?php echo $this->hidden('controller', $this->get_controller_url()); ?>
     <table class="no-border" width="100%" >
        <tr>
            <td width="10%"><b>Lĩnh vực:</b></td>
            <td>
                <select name="sel_spec" id="sel_spec" style="color: #000000;">
                        <option value="">-- Tất cả lĩnh vực --</option>
                        <?php echo $this->generate_select_option($arr_all_spec,NULL); ?>
                </select>
            </td>
        </tr>
        <tr>
            <td width="10%"><b>Ngày trả từ:</b></td>
            <td>
                <input type="text" id="txt_begin" onclick="DoCal('txt_begin')" value="<?php echo date('d-m-Y') ?>"/>
                <img class="btndate" style="cursor:pointer" src="<?php echo SITE_ROOT; ?>public/images/calendar.gif" onclick="DoCal('txt_begin')">
                &nbsp;
                <label for="txt_end" style="display:inline;"><b>Đến: </b></label>
                <input type="text" id="txt_end" onclick="DoCal('txt_end')" value="<?php echo date('d-m-Y') ?>"/>
                <img class="btndate" style="cursor:pointer" src="<?php echo SITE_ROOT;?>public/images/calendar.gif" onclick="DoCal('txt_end')">
            </td>
        </tr>
     </table>
    <center>
        <!--button in-->
        <button type="button" name="trash" class="btn" onclick="btn_print_onclick();">
            <i class="icon-print"></i>
            In báo cáo
        </button>
    </center>
      </div>
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