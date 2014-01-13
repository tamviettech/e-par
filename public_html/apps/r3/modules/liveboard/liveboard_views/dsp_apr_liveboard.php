<?php
/**
Copyright (C) 2012 Tam Viet Tech. All rights reserved.

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
$i           = 1;
$arr_scopes  = array(0 => 'Thủ tục cấp xã', 1 => 'Liên thông xã, huyện'
    , 2 => 'Liên thông huyện, xã', 3 => 'Thủ tục cấp huyện');
$old_scope   = -1;
$multi_scope = (strpos($scope, ',') !== FALSE);
?>
<?php foreach ($arr_all_record_type as $v_record_type_id => $arr_record_type): ?>
    <?php
    $v_record_type_code = $arr_record_type['C_CODE'];
    $v_record_type_name = $arr_record_type['C_NAME'];
    $v_tooltip          = $v_record_type_code . ' - ' . $v_record_type_name;

    $v_count_today_receive_record      = get_array_value($arr_count_today_receive_record, $v_record_type_id);
    $v_count_today_handover_record     = get_array_value($arr_count_today_handover_record, $v_record_type_id);
    $v_count_execing_record            = get_array_value($arr_count_execing_record, $v_record_type_id);
    $v_count_in_schedule_record        = get_array_value($arr_count_in_schedule_record, $v_record_type_id);
    $v_count_over_deadline_record      = get_array_value($arr_count_over_deadline_record, $v_record_type_id);
    $v_count_expried_record            = get_array_value($arr_count_expried_record, $v_record_type_id);
    $v_count_supplement_record         = get_array_value($arr_count_supplement_record, $v_record_type_id);
    $v_count_pausing_record            = get_array_value($arr_count_pausing_record, $v_record_type_id);
    $v_count_waiting_for_return_record = get_array_value($arr_count_waiting_for_return_record, $v_record_type_id);
    $v_count_returned_record           = get_array_value($arr_count_returned_record, $v_record_type_id);

    $sum = array_sum(array($v_count_today_receive_record, $v_count_today_handover_record
        , $v_count_execing_record, $v_count_in_schedule_record
        , $v_count_over_deadline_record, $v_count_expried_record
        , $v_count_supplement_record, $v_count_pausing_record
        , $v_count_waiting_for_return_record, $v_count_returned_record));
    ?>
    <?php if ($sum): ?>
        <?php
        $current_scope = $arr_record_type['C_SCOPE'];
        ?>
        <?php if ($multi_scope && ($current_scope != $old_scope)): ?>
            <tr>
                <td colspan="13"><?php echo $arr_scopes[$current_scope] ?></td>
            </tr>
        <?php endif; ?>
        <?php $old_scope = $current_scope ?>
        <tr>
            <td><?php echo $i++ ?></td>
            <td class="code" onmouseover="Tip('<?php echo $v_tooltip ?>')" onmouseout="UnTip()">
                <?php echo $v_record_type_code ?>
            </td>
            <td><?php echo $v_count_today_receive_record ?></td>
            <td><?php echo $v_count_today_handover_record ?></td>
            <td class="e"><?php echo $v_count_execing_record ?> </td>
            <td class="is"><?php echo $v_count_in_schedule_record ?></td>
            <td class="od"><?php echo $v_count_over_deadline_record ?></td>
            <td class="od"><?php echo $v_count_expried_record ?></td>
            <td><?php echo $v_count_supplement_record ?></td>
            <td><?php echo $v_count_pausing_record ?></td>
            <td><?php echo $v_count_waiting_for_return_record ?></td>
            <td class="r left"><?php echo $v_count_returned_record ?></td>
        </tr>
    <?php endif; ?>
<?php endforeach; ?>

<?php

function get_array_value($arr, $key)
{
    return (isset($arr[$key]) ? $arr[$key] : null);
}
?>