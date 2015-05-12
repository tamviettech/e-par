<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed');

$pop_win = get_request_var('pop_win','');
$pop_win = ($pop_win == '1')?'_pop_win':'';

$this->template->title = 'Quản lý tài liệu';
$this->template->display('dsp_header'.$pop_win.'.php');


$v_record_type_code = get_post_var('sel_record_type','');
?>
<form id="frmMain" name="frmMain" method="POST" action="">
    <?php
        echo $this->hidden('controller',$this->get_controller_url());
        echo $this->hidden('hdn_update_method','update_mapping');
        echo $this->hidden('XmlData','');
    ?>
    <div class="fillter">
        <div class="Row">
            <div class="left-Col">Tên thủ tục</div>
            <div class="right-Col">
                <select name="sel_record_type" id="sel_record_type" style="width:75%; color:#000000;" onchange="btn_fillter_onclick()">
                    <option value="">-- Chọn loại hồ sơ --</option>
                    <?php $v_la_ho_so_lien_thong = FALSE;?>
                    <?php foreach ($arr_all_record_type as $record_type):
                            $code = $record_type['C_RECORD_TYPE_CODE'];
                            $name = $record_type['C_NAME'];
                            $str_selected = ($code == strval($v_record_type_code)) ? ' selected':'';
                    ?>
                        <option value="<?php echo $code;?>" <?php echo $str_selected?>><?php echo $code.' - '.$name;?></option>
                    <?php endforeach;?>
                </select>
            </div>
        </div>
    </div>
    <div class="clear" style="height: 10px"></div>
    <div id="solid-button">
        <!--button update-->
        <button type="button" name="trash" class="btn btn-primary" onclick="btn_update_onclick();" accesskey="2">
            <i class="icon-save"></i>
            <?php echo __('update'); ?>
        </button>
    </div>
    <div class="clear" style="height: 10px"></div>
    <table class="adminlist table table-bordered table-striped">
        <tr>
            <th width="5%" >STT</th>
            <th width="15%">Mã thủ tục</th>
            <th width="*">Tên thủ tục</th>
            <th width="15%">Mã ánh xạ</th>
        </tr>
        <?php
            $i= 1;
            foreach($arr_all_mapping as $mapping):
                $code = $mapping['C_RECORD_TYPE_CODE'];
                $name = $mapping['C_NAME'];
                $v_mapping_code = isset($mapping['C_CODE'])?$mapping['C_CODE']:'';
        ?>
            <tr>
                <td><?php echo $i?></td>
                <td><?php echo $code?></td>
                <td><?php echo $name?></td>
                <td>
                    <input type="text" style="width: 100%" value="<?php echo $v_mapping_code?>" id="txt_<?php echo $code?>" name="txt_<?php echo $code?>"/>
                </td>
            </tr>
        <?php 
            $i++;
        endforeach;?>
    </table>
    <div class="button-area">
        <!--button update-->
        <button type="button" name="trash" class="btn btn-primary" onclick="btn_update_onclick();">
            <i class="icon-save"></i>
            <?php echo __('update'); ?> (Alt+2)"
        </button>
    </div>
</form>
<script>
    function btn_fillter_onclick()
    {
        $('#frmMain').submit();
    }
</script>
<?php $this->template->display('dsp_footer'.$pop_win.'.php');