<?php
defined('DS') or die();
$this->template->title = __('web link');

?>

<script src="<?php echo SITE_ROOT ?>public/js/angular.min.js"></script>
<?php

function buttons($app_name)
{
    ?>
    <div class="button-area">
        <?php if (check_permission('THEM_MOI_WEBLINK',$app_name)): ; ?>
            <input type="button" class="ButtonAdd" onclick="row_onclick(0)" value="<?php echo __('add new') ?>"/>
        <?php endif; ?>
        <?php if (check_permission('XOA_WEBLINK',$app_name)): ?>
            <input type="button" class="ButtonDelete" onclick="btn_delete_onclick()" value="<?php echo __('delete') ?>"/>
        <?php endif; ?>
    </div>
    <?php
}

//end function  
?>


<h2 class="module_title"><?php echo __('web link') ?></h2>
<form name="frmMain" id="frmMain" method="post">
    <input type="hidden"  id="controller" value="<?php echo $this->get_controller_url(); ?>"/>
    <input type="hidden" id="hdn_dsp_single_method" value="dsp_single_weblink"/>
    <?php echo buttons($this->app_name) ?>
    <table class="adminlist" style="width:100%">
        <colgroup>
            <col width="10%">
            <col width="22%">
            <col width="22%">
            <col width="15%">
            <col width="15%">
            <col width="15%">
        </colgroup>
        <tr>
            <th><input type="checkbox" class="chk_all"/></th>
            <th><?php echo __('name') ?></th>
            <th><?php echo __('url') ?></th>
            <th><?php echo __('begin date') ?></th>
            <th><?php echo __('status') ?></th>
            <th><?php echo __('order') ?></th>
        </tr>
        <?php $n = count($arr_all_weblink) ?>
        <?php for ($i = 0; $i < $n; $i++): ?>
            <?php
            $item = $arr_all_weblink[$i];
            $v_prev = isset($arr_all_weblink[$i - 1]) ? $arr_all_weblink[$i - 1]['PK_WEBLINK'] : '';
            $v_next = isset($arr_all_weblink[$i + 1]) ? $arr_all_weblink[$i + 1]['PK_WEBLINK'] : '';
            if (!check_permission('SUA_WEBLINK',$this->app_name))
            {
                $v_next = $v_prev = '';
            }
            $tr_class = 'row' . ($i % 2);
            $img_up = SITE_ROOT . 'apps/public_service/images/up.png';
            $img_down = SITE_ROOT . 'apps/public_service/images/down.png';
            $arr_statuses = array(
                1 => __('active status')
                , 0 => __('inactive status')
            );
            ?>
            <tr class="<?php echo $tr_class ?>">
                <td class="Center">
                    <input type="checkbox" name="chk_item[]" class="chk_item" value="<?php echo $item['PK_WEBLINK'] ?>"/>
                </td>
                <td>
                    <?php if (check_permission('SUA_WEBLINK',$this->app_name)): ?>
                        <a href="javascript:;" onclick="row_onclick(<?php echo $item['PK_WEBLINK'] ?>)">
                            <?php echo $item['C_NAME'] ?>
                        </a>
                    <?php else: ?>
                        <?php echo $item['C_NAME'] ?>
                    <?php endif; ?>
                </td>
                <td>
                    <a target="_blank" href="<?php echo $item['C_URL'] ?>">
                        <?php echo $item['C_URL'] ?>
                    </a>
                </td>
                <td class="Center"><?php echo $item['C_BEGIN_DATE'] ?></td>
                <td class="Center">
                    <?php echo $arr_statuses[$item['C_STATUS']] ?>
                </td>
                <td class="Center">
                    <?php if ($v_prev): ?>
                        <img src="<?php echo $img_up ?>" width="16" height="16" onclick="swap_order('<?php echo $item['PK_WEBLINK'] ?>','<?php echo $v_prev ?>')"/>
                    <?php endif; ?>
                    <?php if ($v_next): ?>
                        <img src="<?php echo $img_down ?>" width="16" height="16" onclick="swap_order('<?php echo $item['PK_WEBLINK'] ?>','<?php echo $v_next ?>')"/>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endfor; ?>
        <?php for ($i; $i < _CONST_DEFAULT_ROWS_PER_PAGE; $i++): ?>
            <tr class="row<?php echo $i % 2 ?>">
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        <?php endfor; ?>
    </table>
    <?php echo buttons($this->app_name) ?>
</form>
<script>
    toggle_checkbox('.chk_all', '.chk_item');
    function swap_order(v_item1, v_item2){
        $.ajax({
            type: 'post'
            ,url: '<?php echo $this->get_controller_url() ?>' + 'swap_order'
            ,data: {item1: v_item1, item2: v_item2}
            ,success: function(){
                  window.location.reload();
            }
        })
    }
    
    window.btn_delete_onclick = function(hdn_id_list){
        var $url = $('#controller').val() + 'delete_weblink';
        if($('.chk_item:checked:not(:disabled)').length == 0)
        {
            alert("<?php echo __('you must choose atleast one object') ?>");
            return;
        }
        if(!confirm("<?php echo __('are you sure to delete all selected object?') ?>"))
        {
            return;
        }
        $.ajax({
            type: 'post',
            url: $url,
            data: $('#frmMain').serialize(),
            success: function(obj){
                window.location.reload();
            }
        });
    }
    
    window.row_onclick = function(id){
        v_url = $('#controller').val() + $('#hdn_dsp_single_method').val() + '/' + id;
        window.location = v_url;
    }
</script>