<?php
if (!defined('SERVER_ROOT'))
    exit('No direct script access allowed');
count($arr_result) > 0 or die();
$this->template->title = 'Kết quả đánh giá';
//$v_pop_win = isset($_REQUEST['pop_win']) ? '_pop_win' : '';
$this->template->display('dsp_header_pop_win.php');
?>
<div class="widget-head blue">
    <h3>Kết quả đánh giá</h3>
</div>
<div class="filter">
    <form name="frmMain" method="post" id="frmMain" action="<?php echo $this->get_controller_url(); ?>dsp_single_report/<?php echo $user_id; ?>">
        <?php
            echo hidden('user_id', $user_id);
        ?>
        <select name="day" style="width: 25%; margin: 10px 3px">
            <option value="">Ngày</option>
            <?php 
                $arr_day = range(1,31);
                foreach($arr_day as $v)
                {
                    $selected = ($c_day == $v) ? 'selected="selected"' : '';
                    echo "<option $selected value=\"$v\">$v</option>";
                }
            ?>
        </select>
        <select name="month" style="width: 25%; margin: 10px 3px">
            <option value="">Tháng</option>
            <?php 
                $arr_day = range(1,12);
                foreach($arr_day as $v)
                {
                    $selected = ($c_month == $v) ? 'selected="selected"' : '';
                    echo "<option $selected value=\"$v\">$v</option>";
                }
            ?>
        </select>
        <select name="year" style="width: 25%; margin: 10px 3px">
            <option value="">Năm</option>
            <?php
                foreach($arr_year as $v)
                {
                    $selected = ($c_year == $v['C_YEAR']) ? 'selected="selected"' : '';
                    echo "<option $selected value=\"{$v['C_YEAR']}\">{$v['C_YEAR']}</option>";
                }
            ?>
        </select>
        <button class="btn btn-file" name="btn_filter" onclick="btn_filter_onclick();" type="button">
            <i class="icon-search"></i>
            Lọc
        </button>
        <?php if(count($arr_result) == 1): ?>
        <span class="note">Chưa có kết quả</span>
        <?php else: ?>
        <div class="result">
            <?php 
                $arr_total = end($arr_result);
                foreach($arr_result as $row):
                    if(!array_key_exists('TOTAL', $row)):
            ?>
                    <div>
                        <label><?php echo $row['C_NAME']; ?></label>
                        <span class="ass-parent-status">
                            <?php 
                                $width = $row['C_VOTE'] * 300/$arr_total['TOTAL'];
                            ?>
                            <span class="ass-child-status" style="width: <?php echo $width; ?>px;"></span>
                        </span>
                        <?php echo $row['C_VOTE']; ?> lượt
                    </div>
            <?php
                    endif;
                endforeach;
            ?> 
            <div class="clearfix"></div>
            <hr />
            <span style="margin: 10px;">Tổng số lượt đánh giá: <?php echo $arr_total['TOTAL']; ?></span> 
        </div>
        <?php endif; ?>
    </form>
    

    
</div>
<script type="text/javascript">
    function btn_filter_onclick() {
        $('#frmMain').submit();
    }
</script>
<?php
$this->template->display('dsp_footer_pop_win.php');