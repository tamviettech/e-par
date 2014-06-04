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
defined('DS') or die();
$all_characters        = explode(' ', 'a ă â b c d đ e ê g h i j k l m n o ô ơ p q r s t u ư v x y z');
$v_character           = get_request_var('character');
$this->title = __('tags');
$cur_url               = $this->get_controller_url() . 'dsp_all_tags';
?>
<?php $n                     = count($all_characters); ?>
<h2 class="module_title"><?php echo __('tags') ?></h2>
<h5 style="text-transform: uppercase;">
    <a href="<?php echo $cur_url ?>">[<?php echo __('all') ?>]</a>
    <?php for ($i = 0; $i < $n; $i++): ?>
        <?php
        $item = $all_characters[$i];
        ?>
        <a href="<?php echo $cur_url ?>&character=<?php echo $item ?>"><?php echo $item ?></a>
    <?php endfor; ?>
</h5>
<div style="overflow-y: auto;height: 500px;width:60%">
    <div class="button-area">
        <input type="button" onclick="add_tags()" value="<?php echo __('submit') ?>" class="ButtonAccept"/>
        <input type="button" onclick="window.parent.hidePopWin(false);" value="<?php echo __('close') ?>" class="ButtonCancel"/>
    </div>
    <table class="adminlist" id="main" width="100%">
        <colgroup>
            <col width="10%"/>
            <col width="90%"/>
        </colgroup>
        <tr>
            <th><input type="checkbox" id="chk_all"></input></th>
            <th><?php echo __('name') ?></th>
        </tr>
        <?php $i    = 0; ?>
        <?php foreach ($arr_all_tags as $key => $val): ?>
            <?php if ($v_character == '' or preg_match("/^$v_character/i", $val)): ?>
                <?php $i++; ?>
                <tr class="row<?php echo $key % 2 ?>">
                    <td class="Center">
                        <input 
                            type="checkbox" value="<?php echo $val ?>" 
                            class="chk_item" id="chk_item_<?php echo $key ?>"
                            />
                    </td>
                    <td><label for="chk_item_<?php echo $key ?>"><?php echo $val ?></label></td>
                </tr>
            <?php endif; ?>
        <?php endforeach; ?>
        <?php for ($i; $i < _CONST_DEFAULT_ROWS_PER_PAGE; $i++): ?>
            <tr class="row<?php echo $i % 2 ?>">
                <td></td><td></td>
            </tr>
        <?php endfor; ?>
    </table>
    <div class="button-area">
        <input type="button" onclick="add_tags()" value="<?php echo __('submit') ?>" class="ButtonAccept"/>
        <input type="button" onclick="window.parent.hidePopWin(false);" value="<?php echo __('close') ?>" class="ButtonCancel"/>
    </div>
</div>
<script>
    $(document).ready(function(){
        toggle_checkbox('#chk_all', '.chk_item');
    });
    function add_tags(){
        arr = [];
        $('.chk_item:checked').each(function(){
            arr.push($(this).val());
        });
        returnVal = arr;
        window.parent.hidePopWin(true);
    }
</script>
<?php $this->template->display('dsp_footer_pop_win.php') ?>