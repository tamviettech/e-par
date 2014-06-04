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
defined('DS') or die('no direct script');

$this->title = __('article');
$v_disable_category    = get_request_var('disable_category') ? 'disabled' : '';

?>
<h2 class="module_title"><?php echo __('article') ?></h2>
<form name="frm_filter" id="frm_filter" action="" method="post">
    <?php echo $this->hidden('hdn_category', get_request_var('hdn_category')); ?>
    <div style="text-align: right">
        <?php echo __('title') ?>
        <input type="text" name="txt_title" id="txt_title" value="<?php echo get_post_var('txt_title') ?>" size="50"/>
        <input type="submit" class="ButtonSearch" value="<?php echo __('filter'); ?>"/>
    </div>
    <div>
        
        <select name="sel_category" id="sel_category" onchange="sel_onchange();" <?php echo $v_disable_category ?>>
            <option value="0"> -- <?php echo __('choose category') ?> -- </option>
            <?php foreach ($arr_all_category as $item): ?>
                <?php
                $v_level  = strlen($item['C_INTERNAL_ORDER']) / 3 - 1;
                $v_indent = '';
                for ($i = 0; $i < $v_level; $i++)
                {
                    $v_indent .= ' -- ';
                }
                ?>
                <option value="<?php echo $item['PK_CATEGORY'] ?>" data-slug="<?php echo $item['C_SLUG'] ?>">
                    <?php echo $v_indent . $item['C_NAME'] ?>
                </option>
            <?php endforeach; ?>
            <script>$('#sel_category').val(<?php echo get_request_var('hdn_category') ?>);</script>
        </select>
    </div>
    <table class="adminlist" width="100%" cellspacing="0" border="1">
        <colgroup>
            <col width="10%"/>
            <col width="90%"/>
        </colgroup>
        <tr>
            <th>
                <input type="checkbox" name="chk_all" id="chk_all" />
            </th>
            <th><?php echo __('title') ?></th>
        </tr>
        <?php
        $n = count($arr_all_article);
        ?>
        <?php if ($n == 0): ?>
            <tr>
                <td colspan="2" class="Center">
                    <b><?php echo __('there are no record'); ?></b>
                </td>
            </tr>
        <?php endif; ?>
        <?php for ($i = 0; $i < $n; $i++): ?>
            <?php
            $item = $arr_all_article[$i];
            //kiem tra dieu kien tin bai
            if (
                    $item['CK_BEGIN_DATE'] >= 0
                    && $item['CK_END_DATE'] >= 0
                    && $item['C_CAT_STATUS'] == 1
            )
            {
                $v_disable = '';
            }
            else //khong du dieu kien hien ra
            {
                $v_disable = 'line-through';
            }
            ?>
            <tr class="row<?php echo $i % 2 ?>">
                <td class="Center">
                    <input 
                        type="checkbox" class="chk_item" name="chk_item[]" id="item_<?php echo $i ?>"
                        value="<?php echo $item['PK_ARTICLE']; ?>"
                        data-title="<?php echo $item['C_TITLE'] ?>"
                        data-category="<?php echo $item['PK_CATEGORY'] ?>"
                        data-slug="<?php echo $item['C_SLUG'] ?>"
                        data-date="<?php echo $item['C_BEGIN_DATE'] ?>"
                        data-disabled="<?php echo $v_disable ?>"
                        />
                </td>
                <td class="<?php echo $v_disable ?>">
                    <label for="item_<?php echo $i ?>" width="100%">
                        <?php echo $item['C_TITLE']; ?>
                    </label>
                </td>
            </tr>
        <?php endfor; ?>
    </table>
    <?php echo $this->paging2($arr_all_article); ?>
    <div class="button-area">
        <input type="button" class="ButtonAccept" id="btn_update" onClick="return_json();" value="<?php echo __('select'); ?>"/>
        <input type="button" class="ButtonCancel" onClick="window.parent.hidePopWin(false);" value="<?php echo __('goback') ?>"/>
    </div>
</form>

<script>
    
     $(document).ready(function(){
        toggle_checkbox('#chk_all', '.chk_item');
    });
    
    function sel_onchange()
    {
        $('#hdn_category').val($('#sel_category').val());
        //alert($('#hdn_category').val());
        $('#frm_filter').submit();
    }
    
//    toggle_checkbox('#chk_all', '[name="chk_item[]"]');
    
    function return_json()
    {
        var $json = [];
        $('.chk_item:checked:not(:disabled)').each(function(){
            $json.push({
                article_id: $(this).val(),
                article_title: $(this).attr('data-title'),
                article_category_id: $(this).attr('data-category'),
                article_category_slug: $('#sel_category option:checked').attr('data-slug'),
                article_slug: $(this).attr('data-slug'),
                article_date: $(this).attr('data-date'),
                article_disabled: $(this).attr('data-disabled')
            });
        });
        
        returnVal = $json;
<?php if (isset($_GET['parent_iframe_path'])): ?>
            window.top.hidePopWin(true, '<?php echo $_GET['parent_iframe_path']; ?>');
<?php else: ?>
            window.top.hidePopWin(true);
<?php endif; ?>
    }
</script>
