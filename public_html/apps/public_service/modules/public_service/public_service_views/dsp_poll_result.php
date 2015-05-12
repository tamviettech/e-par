<?php
defined('DS') or die('no direct access');
$data['title'] = __('poll result');

$total_vote = 0;
foreach ($arr_all_opt as $item)
{
    $total_vote += $item['C_VOTE'];
}
?>

<div class="poll-result-content">
    <div class="PollResultTitle"><span id="lblPollResultTitle">Kết quả bình chọn</span></div>
    <div class="Question"><?php echo $arr_single_poll['C_NAME'] ?></div>
    <table width="100%" class="poll-result">
        <colgroup>
            <col width="35%"></col>
            <col width="55%"></col>
            <col width=10%"></col>
        </colgroup>
        <?php $n = count($arr_all_opt) ?>
        <?php for ($i = 0; $i < $n; $i++): ?>
            <?php
            $item              = $arr_all_opt[$i];
            if($total_vote != 0)
            {
                $v_percent         = round($item['C_VOTE'] * 100 / $total_vote, 1);
            }
            else
            {
                $v_percent = 0;
            }
            
            $v_max_div_percent = 80;
            $v_div_percent     = round(($v_percent / 100) * $v_max_div_percent);
            ?>
            <tr >
                <td><?php echo $item['C_ANSWER'] ?></td>
                <td style="overflow: visible;">
                    <div class="vote-percent" style="width:<?php echo $v_div_percent . '%'; ?>">&nbsp;</div>
                    <div style="float:left;">&nbsp;<?php echo $v_percent ?>%</div>
                </td>
                <td><?php echo $item['C_VOTE'] ?></td>
            </tr>
            <?php if($i <($n-1)):?>
            <tr><td colspan="3" height="5px" style="border-bottom: solid 1px #A5A5A7;"></td></tr>
            <?php endif;?>
        <?php endfor; ?>
    </table>
</div>
<div class="clear" style="height: 10px;"></div>
<div class="button-poll-area">
    <a class="CloseButton" onCLick="close_poll_result();" href="javascript:void(0)" >
       <span id="lblDongcuaso">Đóng cửa sổ</span>
    </a>
</div>
<script>
    function close_poll_result()
    {
        window.parent.close_widget_poll_model();
    }
</script>
