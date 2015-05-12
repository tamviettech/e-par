<?php
defined('DS') or die;

/* @var $this \View */
?>
    <!--Thủ tục cần xử lý-->
    <?php array_walk($arr_all_record_type, 'dsp_record_type') ?>
    <!--Hồ sơ bị trả về-->
    <?php array_walk($arr_all_rollbacked, 'dsp_rollbacked') ?>
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
    <tr class="item">
        <td style="text-align: center">
            <a href="javascript:void(0)" class="notice-record-type" onclick="set_record_type('<?php echo $v_type_code ?>')">
                <?php echo $v_type_code?>
            </a>
        </td>
        <td style="text-align: left">
            <a href="javascript:void(0)" class="notice-record-type" onclick="set_record_type('<?php echo $v_type_code ?>')">
                <?php echo get_leftmost_words($v_type_name,20) ?>
            </a>
        </td>
        <td style="text-align: center">
            <a href="javascript:void(0)" class="notice-record-type" onclick="set_record_type('<?php echo $v_type_code ?>')">
                <?php echo $v_count ?>
            </a>
        </td>
    </tr>
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
    <tr class="item">
        <td>
            <a href="javascript:void(0)" class="notice-message" onclick="dsp_single_record_statistics('<?php echo $v_id ?>')">THÔNG BÁO:</a>
        </td>
        <td>
            <?php echo "{$dom_step->user_name} đã trả lại hồ sơ" ?>
            <a href="javascript:void(0)" class="notice-message" onclick="dsp_single_record_statistics('<?php echo $v_id ?>')"><?php echo $v_record_no ?> (<?php echo $v_citizen ?>)</a>
            <?php echo "vào lúc {$datetime}" ?>
        </td>
        <td></td>
    </tr>
    <?php
}

?>