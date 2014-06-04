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
defined('SERVER_ROOT') or die('no direct access'); 
?>

<form name="frmMain" id="frmMain" method="post">
    <?php 
        $html = '<div class="button-area">';
        if (check_permission('THEM_MOI_CHUYEN_MUC_NOI_BAT',$this->app_name))
        {
            $html .= '<input type="button" class="ButtonAdd" onClick="dsp_modal();" value="Thêm mới"></input>';
        }
        if (check_permission('XOA_CHUYEN_MUC_NOI_BAT',$this->app_name))
        {
            $html .= '<input type="button" class="ButtonDelete" onClick="delete_multi_category();" value="Xóa"></input>';
        }
        $html .= '</div>';
        echo $html;
    ?>
    <?php echo $this->hidden('hdn_controller', $this->get_controller_url()) ?>
    <?php echo $this->hidden('hdn_active_tab', 1); ?>
    <?php echo $this->user_token(); ?>
    <div>
        <table width="100%" class="adminlist" cellspacing="0" border="1" >
            <colgroup>
                <col width="10%" />
                <col width="75%" />
                <col width="15%" />
            </colgroup>
            <tr>
                <th>
                    <input type="checkbox" id="chk-all"/>
                </th>
                <th>Chuyên mục</th>
                <th>Thú tự hiển thị</th>
            </tr>
            <?php
            $arr_all_featured = isset($arr_all_featured) ? $arr_all_featured :array();
            $n = count($arr_all_featured);
            ?>
            <?php for ($i = 0; $i < $n; $i++): ?>
                <?php
                $item        = $arr_all_featured[$i];
                $v_id        = $item['PK_HOMEPAGE_CATEGORY'];
                $v_name      = $item['C_NAME'];
                $v_disable   = $item['C_STATUS'] == 0 ? 'line-through' : '';
                $v_cat_id    = $item['PK_CATEGORY'];
                $v_prev_item = isset($arr_all_featured[$i - 1]) ? $arr_all_featured[$i - 1]['PK_HOMEPAGE_CATEGORY'] : false;
                $v_next_item = isset($arr_all_featured[$i + 1]) ? $arr_all_featured[$i + 1]['PK_HOMEPAGE_CATEGORY'] : false;
                $v_website_id= intval(Session::get('session_website_id'));
                ?>
                <tr class="row<?php echo $i % 2 ?>">
                    <td class="Center">
                        <input
                            type="checkbox" name="chk-item[]" class="chk-item"
                            value="<?php echo $v_id; ?>"
                            data-cat-id="<?php echo $v_cat_id ?>"
                            data-website-id="<?php echo $v_website_id ?>"
                            id="item-<?php echo $v_id ?>"
                            />
                    </td>
                    <td>
                        <label class="<?php echo $v_disable ?>" for="item-<?php echo $v_id ?>"><?php echo $v_name; ?></label>
                    </td>
                    <td class="Center">
                        <?php if (check_permission('SUA_CHUYEN_MUC_NOI_BAT',$this->app_name)): ?>
                            <?php if ($v_prev_item): ?>
                                <img 
                                    height="16" width="16"
                                    src="<?php echo SITE_ROOT ?>apps/public_service/images/up.png"
                                    onClick="swap_order(<?php echo $v_id ?>,<?php echo $v_prev_item ?>);"
                                    />
                                <?php endif; ?>
                                <?php if ($v_next_item): ?>
                                <img 
                                    height="16" width="16"
                                    src="<?php echo SITE_ROOT ?>apps/public_service/images/down.png"
                                    onClick="swap_order(<?php echo $v_id ?>,<?php echo $v_next_item ?>);"
                                    />
                                <?php endif; ?>
                            <?php endif; ?>
                    </td>
                </tr>
            <?php endfor; ?>
        </table>
    </div>
    
    <?php 
        $html = '<div class="button-area">';
        if (check_permission('THEM_MOI_TIN_BAI',$this->app_name))
        {
            $html .= '<input type="button" class="ButtonAdd" onClick="row_onclick(0);" value="Thêm mới"></input>';
        }
        if (check_permission('XOA_TIN_BAI',$this->app_name))
        {
            $html .= '<input type="button" class="ButtonDelete" onClick="btn_delete_onclick(\'hdn_id_list\');" value="Xóa"></input>';
        }

        $html .= '</div>';

        echo $html;
    ?>
</form>
<script>
    toggle_checkbox('#chk-all', '.chk-item');
    
    function dsp_modal()
    {
        var url = "<?php echo $this->get_controller_url() ?>dsp_all_category_svc/" 
            + <?php echo intval(Session::get('session_website_id')) ?>;
        
        var v_inserted = '0';
        
        $('.chk-item').each(function(){
            v_inserted += ', ' + $(this).attr('data-cat-id');
        });
        window.showPopWin(url, 800, 600, function(obj){
            if(obj.length == 0)
                return;
            $.ajax({
                type: 'post',
                url: '<?php echo $this->get_controller_url() ?>insert_featured_category/',
                data: {
                    'category': obj 
                    , 'inserted-category': v_inserted
                    , 'goback': "<?php echo $this->get_controller_url() . 'dsp_all_featured/' ?>"
                },
                success: function(){
                    $('#tabs').tabs('load', 1);
                }
            });
        });
    }
    
    function delete_multi_category()
    {
        if($('.chk-item:checked').length == 0)
        {
            alert("<?php echo __('you must choose atleast one object') ?>");
            return;
        }
        
        if(! confirm("<?php echo __('are you sure to delete all selected object?') ?>"))
        {
            return;
        }
        $.ajax({
            type: 'post',
            url: '<?php echo $this->get_controller_url() . 'delete_featured_category/'; ?>',
            data: $('#frmMain').serialize(),
            success: function(){
                reload_current_tab();
            }
        });
    }
    
    function swap_order($item1, $item2)
    {
        $.ajax({
            type: 'post',
            url: '<?php echo $this->get_controller_url() . 'swap_featured_order/' ?>',
            data: {'item1': $item1, 'item2': $item2},
            success: function()
            {
                reload_current_tab();
            }
        });
    }
</script>