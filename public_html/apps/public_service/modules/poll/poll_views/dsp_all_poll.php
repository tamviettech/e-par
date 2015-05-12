<?php if (!defined('SERVER_ROOT')) {exit('No direct script access allowed');}?>
<?php
    $arr_all_poll = isset($arr_all_poll) ? $arr_all_poll : array();
?>
<h2 class="module_title">Quản lý bình chọn</h2>
<form name="frmMain" id="frmMain" action="" method="POST">
    <?php
    echo $this->hidden('controller',$this->get_controller_url());
    echo $this->hidden('hdn_item_id',0);
    echo $this->hidden('hdn_item_id_list','');

    echo $this->hidden('hdn_dsp_single_method','dsp_single_poll');
    echo $this->hidden('hdn_dsp_all_method','dsp_all_poll');
    echo $this->hidden('hdn_update_method','update_poll');
    echo $this->hidden('hdn_delete_method','delete_poll');
    ?>

<table width="100%" class="adminlist" cellspacing="0" border="1">
    <colgroup>
        <col width="5%" />
        <col width="45%" />
        <col width="10%" />
        <col width="15%" />
        <col width="15%" />
    </colgroup>
    <tr>
        <th><input type="checkbox" name="chk_check_all" onclick="toggle_check_all(this,this.form.chk);"/></th>
        <th>Câu hỏi</th>
        <th>Trạng thái</th>
        <th>Ngày bắt đầu</th>
        <th>Ngày kết thúc</th>
    </tr>
    <?php
    $row = 0;
    $i = 0
    ?>
    <?php
    for ($i = 0; $i < count($arr_all_poll); $i++):
        $v_poll_id     = $arr_all_poll[$i]['PK_POLL'];
        $v_name        = $arr_all_poll[$i]['C_NAME'];
        $v_status      = $arr_all_poll[$i]['C_STATUS'];
        $v_begin_date  = $arr_all_poll[$i]['C_BEGIN_DATE'];
        $v_end_date    = $arr_all_poll[$i]['C_END_DATE'];
        ?>
            
        <tr class="row<?php echo $row; ?>">
            <td class="center">
                <input type="checkbox" name="chk"
                       value="<?php echo $v_poll_id; ?>" 
                       onclick="if (!this.checked) this.form.chk_check_all.checked=false;" 
                       />
            </td>
            <td>
                <a href="javascript:void(0)" onclick="row_onclick(<?php echo $v_poll_id; ?>)"><?php echo $v_name; ?></a>
            </td>
            <td><center><?php echo ($v_status==1)? 'Hiển thị' :'Không hiển thị'; ?></center></td>
            <td><center><?php echo $v_begin_date;?></center></td>
            <td><center><?php echo $v_end_date;?></center></td>   
        </tr>
    <?php
    $row = ($row == 1) ? 0 : 1;
    ?>
<?php endfor; ?>
         <?php $n  = get_request_var('sel_rows_per_page', _CONST_DEFAULT_ROWS_PER_PAGE); ?>
        <?php for ($i; $i < $n; $i++): ?>
            <tr class="row<?php echo $i % 2 ?>">
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        <?php endfor; ?>
</table>
        
<div class="button-area">
        
    <?php if (check_permission('THEM_MOI_CUOC_THAM_DO_Y_KIEN',$this->app_name) == TRUE): ?>
         <input type="button" name="addnew" class="ButtonAdd" value="Thêm mới" onclick="btn_addnew_onclick();">
        
    <?php endif; ?> 

    <?php if (check_permission('XOA_CUOC_THAM_DO_Y_KIEN',$this->app_name) == TRUE): ?>
        <input type="button" name="trash" class="ButtonDelete" value="Xoá" onclick="btn_delete_onclick();">
        
<?php endif; ?>
</div>

</form>