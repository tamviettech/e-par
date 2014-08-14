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
<?php if (!defined('SERVER_ROOT')) {exit('No direct script access allowed');}?>

<ul id="ou_treeview" class="filetree treeview">
    <li class="collapsable">
        <div class="hitarea collapsable-hitarea "></div>
        <span class="config">Quản trị tổ chức</span>
        <ul>
            <?php if (isset($VIEW_DATA['arr_ou_tree'])){
                $arr_ou_tree = $VIEW_DATA['arr_ou_tree'];
                $s = sizeof($arr_ou_tree);
                if ($s > 0)
                {
                    $v_root_ou_id   = $arr_ou_tree[0]['PK_OU'];
                    $v_root_ou_name = $arr_ou_tree[0]['C_NAME'];
                    ?>
                    <li class="collapsable"><div class="hitarea collapsable-hitarea "></div>
                        <span class="root-ou">
                            <a href="javascript:void(0)" onclick="get_ajax_ou_list('<?php echo $v_root_ou_id;?>')"><?php echo $v_root_ou_name;?></a>
                        </span>
                        <ul id="ou_<?php echo $v_root_ou_id;?>">
                            <?php
                            for ($i=1; $i < $s; $i++){
                                $v_ou_id            = $arr_ou_tree[$i]['PK_OU'];
                                $v_ou_name          = $arr_ou_tree[$i]['C_NAME'];
                                $v_parent_ou_id     = $arr_ou_tree[$i]['FK_OU'];
                                $v_internal_order   = $arr_ou_tree[$i]['C_INTERNAL_ORDER'];

                                if ($i < $s - 1)
                                {
                                    $v_next_internal_order  = $arr_ou_tree[$i  + 1]['C_INTERNAL_ORDER'];
                                    $v_next_parent_ou_id    = $arr_ou_tree[$i + 1]['FK_OU'];
                                }
                                else
                                {
                                    $v_next_internal_order  = '001';
                                    $v_next_parent_ou_id    = -1;
                                }

                                $v_previous_parent_ou_id    = $arr_ou_tree[$i - 1]['FK_OU'];
                                $v_ou_level                 = strlen($v_internal_order) / 3;
                                $v_next_ou_level            = strlen($v_next_internal_order) / 3;

                                if ($v_ou_level > $v_next_ou_level)
                                {
                                    $v_li_class = 'collapsable';
                                }
                                elseif ($v_ou_level < $v_next_ou_level)
                                {
                                    $v_li_class = 'l';
                                }
                                ?>
                                <?php if ($v_next_parent_ou_id == $v_ou_id): ?>
                                    <li class="collapsable"><div class="hitarea collapsable-hitarea"></div>
                                        <span class="unit last">
                                            <a href="javascript:void(0)" onclick="get_ajax_ou_list('<?php echo $v_ou_id;?>')" id="<?php echo $v_ou_id;?>"><?php echo $v_ou_name;?></a>
                                        </span>
                                        <ul id="ou_<?php echo $v_ou_id;?>">
                                <?php elseif ( ($v_next_parent_ou_id != $v_ou_id) && ($v_next_parent_ou_id == $v_parent_ou_id)): ?>
                                    <li>
                                        <span class="unit">
                                            <a href="javascript:void(0)" onclick="get_ajax_ou_list('<?php echo $v_ou_id;?>')" id="<?php echo $v_ou_id;?>"><?php echo $v_ou_name;?></a>
                                        </span>
                                    </li>
                                <?php else: ?>
                                    <li>
                                        <span class="unit">
                                            <a href="javascript:void(0)" onclick="get_ajax_ou_list('<?php echo $v_ou_id;?>')"><?php echo $v_ou_name;?></a>
                                        </span>
                                    </li>
                                    <?php $v_decrease_indent = $v_ou_level - $v_next_ou_level;?>
                                    <?php while ($v_decrease_indent > 0): ?>
                                        </ul></li>
                                        <?php $v_decrease_indent--;?>
                                    <?php endwhile;?>
                                <?php endif;?>
                                <?php
                            } //end for ?>
                        </ul></li><?php
                } //if ($s > 0)
            }?>
        </ul>
    </li>
</ul>
