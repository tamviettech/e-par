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
defined('DS') or die;

/* @var $this \View */
?>
<ul>
    <!--Thủ tục cần xử lý-->
    <?php array_walk($arr_all_record_type, 'dsp_record_type') ?>
    <!--Hồ sơ bị trả về-->
    <?php array_walk($arr_all_rollbacked, 'dsp_rollbacked') ?>
</ul>

<?php

/**
 * Thủ tục cần xử lý
 * @param array $record_type
 */
function dsp_record_type($record_type)
{
    $v_type_code = $record_type['record_type_code'];
    $v_type_name = $record_type['record_type_name'];
    $v_count     = $record_type['count_record'];
    ?>
    <li>
        <a href="javascript:void(0)" class="notice-record-type" onclick="set_record_type('<?php echo $v_type_code ?>')">
            <?php echo "- $v_type_code: $v_type_name" ?>&nbsp;
            có <span class="count"><?php echo $v_count ?></span> hồ sơ 
        </a>
    </li>
    <?php
}

/**
 * Hồ sơ bị trả về
 * @param array $record
 */
function dsp_rollbacked($record)
{
    $v_type_code = $record['C_TYPE_CODE'];
    $v_type_name = $record['C_TYPE_NAME'];
    $v_record_no = $record['C_RECORD_NO'];
    $v_id        = $record['PK_RECORD'];
    $v_citizen   = $record['C_CITIZEN_NAME'];

    $xml_processing = simplexml_load_string($record['C_XML_PROCESSING']);
    $dom_step       = xpath($xml_processing, "//step[last()][contains(@code,'CHUYEN_LAI_BUOC_TRUOC')]", XPATH_DOM);
    $datetime       = date_create(strval($dom_step->datetime))->format('d-m-Y H:i');
    ?>
    <li>
        <a href="javascript:void(0)" class="notice-message" onclick="dsp_single_record_statistics('<?php echo $v_id ?>')">- THÔNG BÁO:</a>
        &nbsp;
        <?php echo "{$dom_step->user_name} đã trả lại hồ sơ" ?>
        <a href="javascript:void(0)" class="notice-message" onclick="dsp_single_record_statistics('<?php echo $v_id ?>')"><?php echo $v_record_no ?> (<?php echo $v_citizen ?>)</a>
        <?php echo "vào lúc {$datetime}" ?>
    </li>
    <?php
}
?>