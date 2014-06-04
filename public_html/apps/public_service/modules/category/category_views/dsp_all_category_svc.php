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
defined('DS') or die('no direct access');
$enable_all = get_request_var('enable_all') ? true : false;
$v_single_pick = get_request_var('single_pick','0');
?>

<?php

function show_button()
{
    $html      = '<div class="button-area">';
    $html .= '<input type="button" class="ButtonAccept" onClick="return_func();" value="' . __('select') . '"/>';
    $html .= '<input type="button" class="ButtonCancel" onClick="window.parent.hidePopWin(false);" value="' . __('cancel') . '"/>';
    $html .= '</div>';
    echo $html;
}
?>
<h2 class="module_title">Chuyên mục</h2>

<?php show_button(); ?>
<form name="frmMain" id="frmMain" method="post">
    <div style="height:300px;overflow: scroll;overflow-x: hidden;">
        <input type="button" class="ButtonDelete" onClick="not_select_category_onclick();" value="<?php echo __('not select category')?>"/>
        <table width="100%" class="adminlist" cellspacing="0" border="1" >
            <colgroup>
                <col width="10%" />
                <col width="90%" />
            </colgroup>
            <tr>
                <?php if($v_single_pick != '0'):?>
                <th>&nbsp;</th>
                <?php else:?>
                <th><input type="checkbox" id="chk-all"/></th>
                <?php endif;?>
                <th><?php echo __('category'); ?></th>
            </tr>
            <?php if (count($arr_all_category) == 0): ?>
                <tr>
                    <td class="Center" colspan="2">
                        <b><?php echo __('there are no record') ?></b>
                    </td>
                </tr>
            <?php endif; ?>
                
            <?php $n = count($arr_all_category); ?>
            <?php for ($i = 0; $i < $n; $i++): ?>
                <?php
                $item      = $arr_all_category[$i];
                $v_id      = $item['PK_CATEGORY'];
                $v_name    = $item['C_NAME'];
                $v_slug    = $item['C_SLUG'];
                $v_disable = ($item['C_STATUS'] == 0 && $enable_all == false) ? 'disabled' : '';
                $v_level   = strlen($item['C_INTERNAL_ORDER']) / 3 - 1;
                $v_indent  = '';

                for ($j = 0; $j < $v_level; $j++)
                {
                    $v_indent .= ' -- ';
                }
                ?>
                <tr class="row<?php echo $i % 2; ?>">
                    <td class="Center">
                        <input
                            type="checkbox" name="chk-item[]" class="chk-item"
                            value="<?php echo $v_id ?>"
                            id="item_<?php echo $v_id ?>"
                            data-name="<?php echo $v_name ?>"
                            data-slug="<?php echo $v_slug ?>"
                            <?php echo $v_disable; ?>
                            />
                    </td>
                    <td>
                        <label for="item_<?php echo $v_id ?>">
                            <?php echo $v_indent . $v_name ?>
                        </label>
                    </td>
                </tr>
            <?php endfor; ?>
        </table>
    </div>
</form>
<?php show_button() ?>

<script>
    toggle_checkbox('#chk-all', '.chk-item');
    function reload_page(website_id)
    {
        var url = "<?php echo $this->get_controller_url(); ?>dsp_all_category_svc/" + website_id;
        window.location = url;
        return;
    }
    
    function return_func()
    {
        var a = [];
        $('.chk-item:checked').each(function(){
            var v_id = $(this).val();
            var v_name = $(this).attr('data-name');
            var v_slug = $(this).attr('data-slug');
            
            a.push({id: v_id, name: v_name, slug: v_slug});
        });
        returnVal = a;
        window.parent.hidePopWin(true);
    }
    
    function not_select_category_onclick(chk_not_select)    
    {
        $('.chk-item:checked').removeAttr('checked');
    }
    
    //single pick onclick
    <?php if($v_single_pick != '0'):?>
        $('[name="chk-item[]"]').click(function (){
            cur_chk = $(this);
            $('[name="chk-item[]"]:checked').each(function (){
                 if($(this).val() != $(cur_chk).val())
                 {

                     $(this).removeAttr('checked');
                 }
            });
         });
    <?php endif;?>
</script>