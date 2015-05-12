<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed');

$v_today = $VIEW_DATA['today'];
$arr_all_am_event = $VIEW_DATA['arr_all_am_event'];
$arr_all_pm_event = $VIEW_DATA['arr_all_pm_event'];

$rindex = 0;
?>

<table id="tbl-event" border="1" width="100%">
    <colgroup>
        <col width="13%" />
        <col width="10%" />
        <col width="32%" />
        <col width="30%" />
        <col width="15%" />
    </colgroup>
	<tr>
		<th>Thứ</th>
		<th>Buổi</th>
		<th>Lịch làm việc</th>
		<th>Lãnh đạo chủ trì/tham dự</th>
		<th>Địa điểm</th>
	</tr>
	<tr>
		<td rowspan="1000"><b>Hôm nay,<br/> <?php echo jwDate::vn_day_of_week();?>,<br>ngày <?php echo Date('d/m/Y')?></b> 
			<br />
		</td>
		<td rowspan="<?php echo count($arr_all_am_event);?>" style="background-color: #FFF;text-align:center; font-weight:bold;">Sáng</td>
		<?php if (count($arr_all_am_event) < 1): ?>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	<?php else:?>
	<?php for ($i=0; $i<count($arr_all_am_event); $i++): ?>
	    <?php if ($i>0):?>
	        <tr class="row<?php echo ($rindex % 2);?>">
		<?php endif;?>
		<td><?php echo $arr_all_am_event[$i]['begin_hour'];?>h<?php echo (intval($arr_all_am_event[$i]['begin_minute']) <10) ? '0'.$arr_all_am_event[$i]['begin_minute']: $arr_all_am_event[$i]['begin_minute'];?>:
			<?php echo $arr_all_am_event[$i]['subject'];?>
		</td>
		<td><?php $dom_event_user = simplexml_load_string($arr_all_am_event[$i]['event_user']);?>
			<?php $rows = $dom_event_user->xpath('//row');?> <?php foreach ($rows as $row): ?>
			<?php echo $row->attributes()->user_name?>; <?php endforeach;?>
		</td>
		<td><?php echo $arr_all_am_event[$i]['location']?></td>
	</tr>
	<?php $rindex++?>
	<?php endfor;?>
	<?php endif;?>
	<tr class="row<?php echo ($rindex % 2);?>">
		<td rowspan="<?php echo count($arr_all_pm_event);?>"
			style="background-color: #FFF;text-align:center; font-weight:bold;">Chiều</td>
		<?php if (count($arr_all_pm_event) < 1): ?>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	<?php else:?>
	<?php for ($i=0; $i<count($arr_all_pm_event); $i++): ?>
	<?php if ($i>0):?>
	<tr class="row<?php echo ($rindex % 2);?>">
		<?php endif;?>
		<td><?php echo $arr_all_pm_event[$i]['begin_hour'];?>h<?php echo (intval($arr_all_pm_event[$i]['begin_minute']) < 10) ? '0'.$arr_all_pm_event[$i]['begin_minute'] : $arr_all_pm_event[$i]['begin_minute'];?>:
			<?php echo $arr_all_pm_event[$i]['subject'];?>
		</td>
		<td><?php $dom_event_user = simplexml_load_string($arr_all_pm_event[$i]['event_user']);?>
			<?php $rows = $dom_event_user->xpath('//row');?> <?php foreach ($rows as $row): ?>
			<?php echo $row->attributes()->user_name?>; <?php endforeach;?>
		</td>
		<td><?php echo $arr_all_pm_event[$i]['location']?></td>
	</tr>
	<?php $rindex++?>
	<?php endfor;?>
	<?php endif;?>
</table>
