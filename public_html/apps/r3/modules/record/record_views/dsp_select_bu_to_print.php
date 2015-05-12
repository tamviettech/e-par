<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed');

//display header
$this->template->title = 'Chọn đơn vị tiếp nhận hồ sơ';

$v_pop_win = isset($_REQUEST['pop_win']) ? '_pop_win' : '';
$this->template->display('dsp_header_pop_win.php');

?>
<form id="frmMain" name="frmMain" action="<?php echo $this->get_controller_url().'dsp_print_all_record_for_bu'?>" method="post">
    <?php
        echo $this->hidden('hdn_bu_name','');
    ?>
    <div class="primary-head">
            <h3 class="page-header">Chọn đơn vị tiếp nhận hồ sơ</h3>
    </div>
    <div class="clear" style="height: 10px">&nbsp;</div>
    <!--select don vi ban giao-->
    <div class="Row">
        <div class="left-Col">Đơn vị tiếp nhận hồ sơ</div>
        <div class="right-Col">
            <select name="sel_bu" id="sel_bu">
                <?php foreach($arr_all_bu as $arr_bu):
                        $v_bu_name   = $arr_bu['C_GROUP_NAME'];
                        $v_record_id = $arr_bu['PK_RECORD'];
                        if($v_record_id != '' && $v_record_id != NULL):
                ?>
                <option value="<?php echo $v_record_id?>" data-name="<?php echo $v_bu_name;?>" ><?php echo $v_bu_name;?></option>
                <?php 
                    endif;
                endforeach;?>
            </select>
        </div>
    </div>
    
    <div class="button-area">
         <!--button xet duyet-->
        <button type="button" name="trash" class="btn" onclick="btn_submit();">
            <i class="icon-print"></i>
            In phiếu bàn giao
        </button>
         <!--button dong cua so-->
        <button type="button" name="trash" class="btn" onclick="jacascript:window.parent.hidePopWin();">
            <i class="icon-remove"></i>
            Đóng cửa sổ
        </button>
         
    </div>
</form>
<script>
    function btn_submit()
    {
        $('#hdn_bu_name').val($('#sel_bu option:selected').attr('data-name'));
        $('#frmMain').submit();
    }
</script>
<?php $this->template->display('dsp_footer_pop_win.php');