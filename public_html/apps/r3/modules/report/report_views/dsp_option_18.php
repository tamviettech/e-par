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
    <label for="txt_begin"><b>Ngày trả từ: </b></label>
    <input type="text" id="txt_begin" onclick="DoCal('txt_begin')" value="<?php echo date('d-m-Y') ?>"/>
    <img class="btndate" style="cursor:pointer" src="<?php echo SITE_ROOT;?>public/images/calendar.gif" onclick="DoCal('txt_begin')">
    &nbsp;
    <label for="txt_end"><b>Đến: </b></label>
    <input type="text" id="txt_end" onclick="DoCal('txt_end')" value="<?php echo date('d-m-Y') ?>"/>
    <img class="btndate" style="cursor:pointer" src="<?php echo SITE_ROOT;?>public/images/calendar.gif" onclick="DoCal('txt_end')">
    <br/>
    <!--button in-->
    <button type="button" name="trash" class="btn btn-info" onclick="btn_print_onclick();">
        <i class="icon-print"></i>
        In báo cáo
    </button>
</form>

<script type="text/javascript">
        function btn_print_onclick() {
            url = $('#controller').val() + 'type/18/' + QS + 'pdf=true'
                    + '&begin=' + $('#txt_begin').val()
                    + '&end=' + $('#txt_end').val();
            window.showPopWin(url, 1000, 600);
        }
</script>




<?php
$this->template->display('dsp_footer.php');