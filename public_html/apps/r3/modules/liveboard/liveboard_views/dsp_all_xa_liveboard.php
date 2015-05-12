<?php
defined('DS') or die();
//Số thứ tự hiển thị danh sách
$i           = 1;
$arr_scopes  = array(0 => 'Thủ tục cấp xã', 1 => 'Liên thông xã, huyện'
    , 2 => 'Liên thông huyện, xã', 3 => 'Thủ tục cấp huyện');
$scope = 3;

$arr_all_xa = isset($VIEW_DATA['arr_all_village']) ? $VIEW_DATA['arr_all_village'] : array();


?>
<?php foreach ($arr_all_xa as $arr_single_village): ?>
    <?php
    // check quyen
    session::init();
    $login_name = session::get('login_name');

    if ($login_name == NULL)
    {
        $v_role = 0;
    }
    else
    {
        $v_role = 1;
    }
    // hiện tại chưa cần check
    $v_role = 0;
    
    $v_show_type_code           = $v_show_today
    = $v_show_today_handover    = $v_show_execing 
    = $v_show_in_schedule       = $v_show_over_deadline 
    = $v_show_expried           = $v_show_supplement       
    = $v_show_pausing           = $v_show_waiting_for_return
    = $v_show_returned          = '';
    
   
    //Lấy mã vùng truy vấn
    $v_village_id   = isset($arr_single_village['PK_OU']) ? $arr_single_village['PK_OU']: 0;
    $v_village_name = isset($arr_single_village['C_NAME']) ? $arr_single_village['C_NAME']: '';
    if($v_role == 1)$v_show_type_code = $v_village_id."-1-".$arr_single_village['PK_OU'];
    //tolltip
    $v_tooltip                                  = $v_village_id . ' - ' . $v_village_name;

    
    
    $v_count_today_receive_record               = get_array_value($arr_count_today_receive_record, $v_village_id);     
    if($v_role == 1) $v_show_today              = $v_village_id."-2-";
    
    $v_count_today_handover_record              = get_array_value($arr_count_today_handover_record, $v_village_id);
    if($v_role == 1) $v_show_today_handover     = $v_village_id."-3-";
    
    $v_count_execing_record                     = get_array_value($arr_count_execing_record, $v_village_id);
    if($v_role == 1) $v_show_execing            = $v_village_id."-4-";
    
    $v_count_in_schedule_record                 = get_array_value($arr_count_in_schedule_record, $v_village_id);
    if($v_role == 1) $v_show_in_schedule        = $v_village_id."-5-";
    
    $v_count_over_deadline_record               = get_array_value($arr_count_over_deadline_record, $v_village_id);
    if($v_role == 1) $v_show_over_deadline      = $v_village_id."-6-";
    
    $v_count_expried_record                     = get_array_value($arr_count_expried_record, $v_village_id);
    if($v_role == 1) $v_show_expried            = $v_village_id."-7-";
    
    $v_count_supplement_record                  = get_array_value($arr_count_supplement_record, $v_village_id);
    if($v_role == 1) $v_show_supplement         = $v_village_id."-8-";
    
    $v_count_pausing_record                     = get_array_value($arr_count_pausing_record, $v_village_id);
    if($v_role == 1) $v_show_pausing            = $v_village_id."-9-";
    
    $v_count_waiting_for_return_record          = get_array_value($arr_count_waiting_for_return_record, $v_village_id);
    if($v_role == 1) $v_show_waiting_for_return = $v_village_id."-10-";
    
    $v_count_returned_record                    = get_array_value($arr_count_returned_record, $v_village_id);
    if($v_role == 1) $v_show_returned           = $v_village_id."-11-";
    
    $sum = array_sum(array($v_count_today_receive_record, $v_count_today_handover_record
        , $v_count_execing_record, $v_count_in_schedule_record
        , $v_count_over_deadline_record, $v_count_expried_record
        , $v_count_supplement_record, $v_count_pausing_record
        , $v_count_waiting_for_return_record, $v_count_returned_record));
    

    ?>
    <?php if ($sum): ?>
        <?php
        $current_scope = 3;
        ?>        
        <?php $old_scope = $current_scope ?>
        <tr>
            <td><?php echo $i++ ?></td>
            <td  class="code" onmouseover="Tip('<?php echo $v_tooltip ?>')" onmouseout="UnTip()">
                 <?php if($v_role == 1): ?>
                <a href="#" style="color:black" onclick="dsp_list_detail_liveboard('<?php echo $v_show_type_code; ?>');" ><?php echo $v_village_name ?></a>
                <?php 
                else:
                    echo $v_village_name;
                endif;
                ?>
            </td>
            <td >
                <?php if($v_role == 1): ?>
                <a href="#" style="color:black" onclick="dsp_list_detail_liveboard('<?php echo $v_show_today ?>');"> <?php echo $v_count_today_receive_record;?></a>
                <?php 
                else:
                    echo $v_count_today_receive_record;
                endif;
                ?>
            </td>
            <td >
                 <?php if($v_role == 1): ?>
                <a href="#" style="color:black" onclick="dsp_list_detail_liveboard('<?php echo $v_show_today_handover ?>');" > <?php echo $v_count_today_handover_record ?></a>
            <?php 
                else:
                    echo $v_count_today_handover_record;
                endif;
                ?>
            </td>
            <td class="e">
                 <?php if($v_role == 1): ?>
                <a href="#" style="color:black" onclick="dsp_list_detail_liveboard('<?php echo $v_show_execing ?>');" > <?php echo $v_count_execing_record ?> </a>
                <?php 
                else:
                    echo $v_count_execing_record;
                endif;
                ?>
            </td>
            <td class="is">
                 <?php if($v_role == 1): ?>
                <a href="#" style="color:black" onclick="dsp_list_detail_liveboard('<?php echo $v_show_in_schedule ?>');" > <?php echo $v_count_in_schedule_record ?></a>
            <?php 
                else:
                    echo $v_count_in_schedule_record;
                endif;
                ?>
            </td>
            <td class="od">
                 <?php if($v_role == 1): ?>
                <a href="#" style="color:red" onclick="dsp_list_detail_liveboard('<?php echo $v_show_over_deadline ?>');" > <?php echo $v_count_over_deadline_record ?></a>
              <?php 
                else:
                    echo $v_count_over_deadline_record;
                endif;
                ?>
            </td>
            <td class="od">
                 <?php if($v_role == 1): ?>
                <a href="#" style="color:red" onclick="dsp_list_detail_liveboard('<?php echo $v_show_expried ?>');" > <?php echo $v_count_expried_record ?></a>
                <?php 
                else:
                    echo $v_count_expried_record;
                endif;
                ?>
            </td>
            <td >
                <?php if($v_role == 1): ?>
                <a href="#" style="color:black" onclick="dsp_list_detail_liveboard('<?php echo $v_show_supplement ?>');"> <?php echo $v_count_supplement_record ?></a>
                <?php 
                else:
                    echo $v_count_expried_record;
                endif;
                ?>
            </td>
            <td >
                <?php if($v_role == 1): ?>
                <a href="#" style="color:black" onclick="dsp_list_detail_liveboard('<?php echo $v_show_pausing ?>');" > <?php echo $v_count_pausing_record ?></a></td>
            <?php 
                else:
                    echo $v_count_pausing_record;
                endif;
                ?>
            <td >
                <?php if($v_role == 1): ?>
                <a href="#" style="color:black" onclick="dsp_list_detail_liveboard('<?php echo $v_show_waiting_for_return ?>');" > <?php echo $v_count_waiting_for_return_record ?></a>
                <?php 
                else:
                    echo $v_count_waiting_for_return_record;
                endif;
                ?>
            </td>
            <td  class="r left">
                <?php if($v_role == 1): ?>
                <a href="#" style="color:black" onclick="dsp_list_detail_liveboard('<?php echo $v_show_returned ?>');"> <?php echo $v_count_returned_record ?></a>
            <?php 
                else:
                    echo $v_count_returned_record;
                endif;
                ?>
            </td>
        </tr>
    <?php endif; ?>
<?php endforeach; ?>
    <script>
        /**
         * Hiene thi danh sach chon theo tieu chi
         */
        function dsp_list_detail_liveboard(id_status) 
        {
            if(id_status != '' && id_status != null )
            {
                var url = '<?php echo $this->get_controller_url().'dsp_list_detail_village_liveboard/';?>' + id_status; 
                showPopWin(url, 800, 600, null, true);
            }
        }
    </script>
<?php

function get_array_value($arr, $key)
{
    return (isset($arr[$key]) ? $arr[$key] : null);
}
?>