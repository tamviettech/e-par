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
    
    $v_show_type_code           = $v_show_today
    = $v_show_today_handover    = $v_show_execing 
    = $v_show_in_schedule       = $v_show_over_deadline 
    = $v_show_expried           = $v_show_supplement       
    = $v_show_pausing           = $v_show_waiting_for_return
    = $v_show_returned          = '';
    
    
    $v_record_type_code = $arr_record_type['C_CODE'];
    //Lấy mã vùng truy vấn
    $v_village_id       =  isset($VIEW_DATA['v_village_id'])? $VIEW_DATA['v_village_id'] : 0;
    
    if($v_role == 1)$v_show_type_code = $v_record_type_id."-1-".$v_village_id;
    
    $v_record_type_name                         = $arr_record_type['C_NAME'];
    $v_tooltip                                  = $v_record_type_id . ' - ' . $v_record_type_name;

    $v_count_today_receive_record               = get_array_value($arr_count_today_receive_record, $v_record_type_id);     
    if($v_role == 1) $v_show_today              = $v_record_type_id."-2-".$v_village_id;
    
    $v_count_today_handover_record              = get_array_value($arr_count_today_handover_record, $v_record_type_id);
    if($v_role == 1) $v_show_today_handover     = $v_record_type_id."-3-".$v_village_id;
    
    $v_count_execing_record                     = get_array_value($arr_count_execing_record, $v_record_type_id);
    if($v_role == 1) $v_show_execing            = $v_record_type_id."-4-".$v_village_id;
    
    $v_count_in_schedule_record                 = get_array_value($arr_count_in_schedule_record, $v_record_type_id);
    if($v_role == 1) $v_show_in_schedule        = $v_record_type_id."-5-".$v_village_id;
    
    $v_count_over_deadline_record               = get_array_value($arr_count_over_deadline_record, $v_record_type_id);
    if($v_role == 1) $v_show_over_deadline      = $v_record_type_id."-6-".$v_village_id;
    
    $v_count_expried_record                     = get_array_value($arr_count_expried_record, $v_record_type_id);
    if($v_role == 1) $v_show_expried            = $v_record_type_id."-7-".$v_village_id;
    
    $v_count_supplement_record                  = get_array_value($arr_count_supplement_record, $v_record_type_id);
    if($v_role == 1) $v_show_supplement         = $v_record_type_id."-8-".$v_village_id;
    
    $v_count_pausing_record                     = get_array_value($arr_count_pausing_record, $v_record_type_id);
    if($v_role == 1) $v_show_pausing            = $v_record_type_id."-9-".$v_village_id;
    
    $v_count_waiting_for_return_record          = get_array_value($arr_count_waiting_for_return_record, $v_record_type_id);
    if($v_role == 1) $v_show_waiting_for_return = $v_record_type_id."-10-".$v_village_id;
    
    $v_count_returned_record                    = get_array_value($arr_count_returned_record, $v_record_type_id);
    if($v_role == 1) $v_show_returned           = $v_record_type_id."-11-".$v_village_id;
    
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
            <td  class="code" onmouseover="Tip('<?php echo $v_tooltip ?>')" onmouseout="UnTip()">
                 <?php if($v_role == 1): ?>
                <a href="#" style="color:black" onclick="dsp_list_detail_liveboard('<?php echo $v_show_type_code; ?>');" ><?php echo $v_record_type_code ?></a>
                <?php 
                else:
                    echo $v_record_type_code;
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
                var url = '<?php echo $this->get_controller_url().'dsp_list_detail_liveboard/';?>' + id_status; 
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