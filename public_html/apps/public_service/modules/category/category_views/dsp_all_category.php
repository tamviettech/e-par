<?php defined('SERVER_ROOT') or die('No direct script'); ?>
<?php
    $html = '<div class="button-area">';
    if (check_permission('THEM_MOI_CHUYEN_MUC',$this->app_name))
    {
        $html .= '<input type="button" class="ButtonAdd" onClick="dsp_single_category(0);" value="Thêm mới"></input>';
    }
    if (check_permission('XOA_CHUYEN_MUC',$this->app_name))
    {
        $html .= '<input type="button" class="ButtonDelete" onClick="delete_multi_category();" value="Xóa"></input>';
    }
    $html .= '</div>';
    echo $html;

?>

<form name="frmMain" id="frmMain" method="post">
    <?php echo $this->hidden('hdn_controller', $this->get_controller_url()) ?>
    <?php echo $this->hidden('hdn_active_tab', 0); ?>
    <?php echo $this->user_token(); ?>
    <div>
        <table width="100%" class="adminlist" cellspacing="0" border="1" >
            <colgroup>
                <col width="10%" />
                <col width="60%" />
                <col width="15%" />
                <col width="15%" />
            </colgroup>
            <tr>
                <th><input type="checkbox" id="chk-all"/></th>
                <th>Chuyên mục</th>
                <th>Thứ tự hiển thị</th>
                <th>Trạng thái</th>
            </tr>
            <?php $n = count($arr_all_category); ?>
            <?php for ($i = 0; $i < $n; $i++): ?>
                <?php
                $item             = $arr_all_category[$i];
                $v_category_id    = $item['PK_CATEGORY'];
                $v_name           = $item['C_NAME'];
                $v_status         = $item['C_STATUS'];
                $v_order          = $item['C_ORDER'];
                $v_internal_order = $item['C_INTERNAL_ORDER'];
                $v_parent         = $item['FK_PARENT'];
                $v_indent         = strlen($item['C_INTERNAL_ORDER']) / 3 - 1;
                $v_indent_text    = '';
                $v_disable        = ($item['C_COUNT_CHILD_CAT'] > 0) ? 'disabled' : '';
                $v_disable        = ($item['C_COUNT_CHILD_ART'] > 0 )? 'disabled' : $v_disable;
                $line_throught    = $v_status == 0 ? 'line-through' : '';

                for ($j = 0; $j < $v_indent; $j++)
                {
                    $v_indent_text .= ' -- ';
                }

                $v_next_item = $v_category_id;
                $v_prev_item = $v_category_id;

                $j = $i - 1;
                while (isset($arr_all_category[$j]))
                {
                    if ($arr_all_category[$j]['FK_PARENT'] == $v_parent)
                    {
                        $v_prev_item = $arr_all_category[$j]['PK_CATEGORY'];
                        break;
                    }
                    else
                    {
                        $j--;
                    }
                }

                $j = $i + 1;
                while (isset($arr_all_category[$j]))
                {
                    if ($arr_all_category[$j]['FK_PARENT'] == $v_parent)
                    {
                        $v_next_item = $arr_all_category[$j]['PK_CATEGORY'];
                        break;
                    }
                    else
                    {
                        $j++;
                    }
                }
                ?>
                <tr class="row<?php echo $i % 2; ?>">
                    <td class="Center">
                        <input 
                            type="checkbox" class="chk-item" name="chk-item[]" 
                            value="<?php echo $v_category_id; ?>" <?php echo $v_disable ?>
                            />
                    </td>
                    <td 
                        class="<?php echo $line_throught ?>"
                        style="cursor:pointer;"
                        <?php if (check_permission('SUA_CHUYEN_MUC',$this->app_name)): ?>
                            onClick="dsp_single_category(<?php echo $v_category_id; ?>);"
                        <?php endif; ?>
                        >
                            <?php echo $v_indent_text . $v_name; ?>
                    </td>
                    <td class="Center">
                        <?php if (check_permission('SUA_CHUYEN_MUC',$this->app_name)): ?>
                            <?php if ($v_prev_item != $v_category_id): ?>
                                <img 
                                    width="16" height="16" src="<?php echo SITE_ROOT; ?>apps/public_service/images/up.png"
                                    onClick="swap_order('<?php echo $v_category_id; ?>','<?php echo $v_prev_item ?>');"
                                    />
                                <?php endif; ?>
                                <?php if ($v_next_item != $v_category_id): ?>
                                <img 
                                    width="16" height="16" src="<?php echo SITE_ROOT; ?>apps/public_service/images/down.png"
                                    onClick="swap_order('<?php echo $v_category_id; ?>','<?php echo $v_next_item ?>');"
                                    />
                                <?php endif; ?>
                            <?php endif; ?>
                    </td>
                    <td class="Center">
                        <?php echo $v_status ? 'Hoạt động' : 'Không hoạt động' ; ?>
                    </td>
                </tr>
            <?php endfor; ?>
        </table>
    </div>
    <?php 
            $html = '<div class="button-area">';
            if (check_permission('THEM_MOI_CHUYEN_MUC',$this->app_name))
            {
                $html .= '<input type="button" class="ButtonAdd" onClick="dsp_single_category(0);" value="Thêm mới"></input>';
            }
            if (check_permission('XOA_CHUYEN_MUC',$this->app_name))
            {
                $html .= '<input type="button" class="ButtonDelete" onClick="delete_multi_category();" value="Xóa"></input>';
            }
            echo $html; ?>
</form>

<script>
    $(document).ready(function(){
        toggle_checkbox('#chk-all', '.chk-item');
    });
    function swap_order(p_item1, p_item2)
    {
        $.ajax({
            url: '<?php echo $this->get_controller_url() . 'swap_category_order'; ?>',
            type: 'post',
            data: {'item1': p_item1, 'item2': p_item2},
            success: function(){ 
                reload_current_tab();
            }
        })
    };
    
    function dsp_single_category(id)
    {
        window.location = "<?php echo $this->get_controller_url() . 'dsp_single_category/' ?>" + id;
    }
    
    function delete_multi_category()
    {
        if($('.chk-item:checked').length == 0)
        {
            alert("<?php echo __('you must choose atleast one object') ?>");
            return;
        }
        
        if(confirm('<?php echo 'Bạn không có quyền xóa đối tượng này' ?>'))
        {
            var $url = "<?php echo $this->get_controller_url() . 'delete_category' ?>";
            $.ajax({
                type: 'post',
                url: $url,
                data: $('#frmMain').serialize(),
                success: function(){
                    reload_current_tab();
                }
            });
        }
    }

</script>