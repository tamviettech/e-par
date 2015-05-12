<?php
defined('DS') or die;
/* @var $this \View */
$this->template->title = 'BÁO CÁO CHI TIẾT QUÁ HẠN CỦA CÁC BƯỚC SỬ LÝ HỒ SƠ';
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
<style>
    table td{padding: 3px;}
</style>
<form id="frmMain" method="post">
    <?php 
        echo $this->hidden('controller', $this->get_controller_url()); 
        echo $this->hidden('hdn_group_level','');
    ?>    
     <div class="widget-head blue">
         <h3>
                <?php
                    if(trim($repoer_title) == '')
                        echo $this->template->title;
                    else
                        echo $repoer_title;
                ?>
            </h3>
        </div>
    <div class="widget-container" style="min-height: 90px;border: 1px solid #3498DB;">
     <table class="no-border" width="100%" >
            <tr>
                <td width="10%"><b>Đơn vị:</b></td>
                <td>
                     <select name="sel_group" id="sel_group" onchange="sel_group_onchange(this)">
                        <?php if(!check_vilage_id()):?>
                            <option value="" data-level="-1" >--- Tất cả ---</option>
                        <?php endif;?>
                        <?php foreach($arr_all_village as $arr_single_village):?>
                            <option value="<?php echo $arr_single_village['C_CODE']?>" data-level="<?php echo $arr_single_village['C_SPEC_CODE']?>"><?php echo $arr_single_village['C_NAME']?></option>
                        <?php endforeach?>
                    </select>
                </td>
            </tr>
            <tr>
                <td width="10%"><b>Lĩnh vực:</b></td>
                <td>
                  
                    <select name="sel_spec" id="sel_spec" style="color: #000000;">
                        <option value="">-- Tất cả lĩnh vực --</option>
                        <?php $active_sel_spec = isset($_REQUEST['sel_spec']) ? $_REQUEST['sel_spec'] : NULL ; ?>
                        <?php echo $this->generate_select_option($arr_all_spec, $active_sel_spec); ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td width="10%"><b>Loai hồ sơ: </b></td>
                <td>
                    <select name="sel_record_type" id="sel_record_type" style="width: 77%; color: #000000;">
                        <option value="">--Tất cả loại hồ sơ  --</option>
                        <?php foreach ($arr_all_record_type_with_spec_code as $reocord_type) : ?>
                            <?php list($id, $v_spec_code, $v_code, $v_name) = $reocord_type; ?>
                            <option value="<?php echo $id; ?>" class="<?php echo $v_spec_code; ?>"><?php echo $v_code . ' - ' . $v_name; ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td width="10%"><b>Tiếp nhận từ: </b></td>
                <td>
                   <input type="text" id="txt_begin" onclick="DoCal('txt_begin')" value="<?php echo isset($_REQUEST['txt_begin']) ? $_REQUEST['txt_begin'] : date('d-m-Y') ?>"/>
                    <img class="btndate" style="cursor:pointer" src="<?php echo SITE_ROOT; ?>public/images/calendar.gif" onclick="DoCal('txt_begin')">
                    &nbsp;
                    <label for="txt_end" style="display:inline;"><b>Đến: </b></label>
                    <input type="text" id="txt_end" onclick="DoCal('txt_end')" value="<?php echo isset($_REQUEST['txt_end']) ? $_REQUEST['txt_end'] : date('d-m-Y') ?>"/>
                    <img class="btndate" style="cursor:pointer" src="<?php echo SITE_ROOT; ?>public/images/calendar.gif" onclick="DoCal('txt_end')">
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

<script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery.chained.mini.js" type="text/javascript"></script>
<script>
    $("#sel_record_type").chained("#sel_spec");
</script>
<script type="text/javascript">
    function btn_print_onclick() {
        var url = $('#controller').val() + 'type/19/' + QS + 'pdf=true'
            + '&txt_begin=' + $('#txt_begin').val()
            + '&txt_end=' + $('#txt_end').val()
            + '&sel_spec=' + $('#sel_spec').val()
            + '&sel_record_type='+ $('#sel_record_type').val()
            + '&sel_group=' + $('#sel_group').val()
            + '&group_level=' + $('#hdn_group_level').val();
        window.showPopWin(url, 1000, 600);
    }
function sel_group_onchange(group)
{
    var level = $(group).find(':selected').attr('data-level');
    $('#hdn_group_level').val(level);
}
</script>
<?php
$this->template->display('dsp_footer.php');