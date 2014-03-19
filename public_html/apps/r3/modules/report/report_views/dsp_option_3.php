<?php
defined('DS') or die;
/* @var $this \View */
$this->template->title = 'Báo cáo tổng hợp';
$this->template->display('dsp_header.php');
?>
<style>
    table td{padding: 3px;}
</style>
<form id="frmMain" method="post">
    <?php
    echo $this->hidden('controller', $this->get_controller_url());
    ?>
    <table class="no-border" >
        <tr>
            <td><b>Chọn tiêu chí báo cáo</b></td>
            <td>
                <?php
                $arr_rads              = array('m'     => 'Tháng', 'Q'     => 'Quý', 'Y'     => 'Năm'
                    , '1to6'  => '6 tháng đầu năm', '7to12' => '6 tháng cuối năm');
                ?>
                <?php foreach ($arr_rads as $k => $v): ?>
                    <label class="checkbox inline">
                        <input 
                            type="radio" name="rad_type"
                            id="rad_<?php echo $k ?>" value="<?php echo $k ?>"
                            onclick="filter.set_type('<?php echo $k ?>')"
                            />
                            <?php echo $v; ?>
                    </label>
                <?php endforeach; ?>
            </td>
        </tr>
        <tr>
            <td><b>Chọn năm</b></td>
            <td>
                <select name="sel_year" id="sel_year">
                    <?php for ($i = date('Y'); $i >= 2012; $i--): ?>
                        <option value="<?php echo $i ?>"><?php echo $i ?></option>
                    <?php endfor; ?>
                </select>
                &nbsp;
                <select name="sel_month" id="sel_month">
                    <?php for ($i = 1; $i <= 12; $i++): $selected = date('m') == $i ? 'selected' : '' ?>
                        <option value="<?php echo $i ?>" <?php echo $selected ?>><?php echo $i ?></option>
                    <?php endfor; ?>
                </select>
            </td>
        </tr>
        <tr id="tr_quarter">
            <td><b>Chọn quý</b></td>
            <td>
                <?php
                $current_month = (int) date('m');
                switch (true)
                {
                    case (in_array($current_month, range(1, 3))):
                        $quarter = 1;
                        break;
                    case (in_array($current_month, range(4, 6))):
                        $quarter = 2;
                        break;
                    case (in_array($current_month, range(7, 9))):
                        $quarter = 3;
                        break;
                    default:
                        $quarter = 4;
                } //switch
                $arr_quarters = array(1 => 'Quý I', 2 => 'Quý II', 3 => 'Quý III', 4 => 'Quý IV');
                ?>
                <?php foreach ($arr_quarters as $k => $v): $checked = $quarter == $k ? 'checked' : '' ?>
                    <label class="checkbox inline">
                        <input type="radio" name="rad_quarter" <?php echo $checked ?> value="<?php echo $k ?>"/>
                        <?php echo $v ?>
                    </label>
                <?php endforeach; ?>
            </td>
        </tr>
        <tr>
            <td></td>
            <td class="">
                <!--button in-->
                <button type="button" name="trash" class="btn btn-info" onclick="btn_print_onclick();">
                    <i class="icon-print"></i>
                    In báo cáo
                </button>
            </td>
        </tr>
    </table>
</form>




<script type="text/javascript">
                                function filter() {
                                    this.type = 'm';
                                    this.begin_month;
                                    this.end_month;
                                    this.year;
                                }
                                filter.set_type = function(type) {
                                    this.type = type;
                                    switch (type) {
                                        case 'm':
                                            $('#sel_month').show();
                                            $('#tr_quarter').hide();
                                            break;
                                        case 'Q':
                                            $('#sel_month').hide();
                                            $('#tr_quarter').show();
                                            break;
                                        case 'Y':
                                            $('#sel_month').hide();
                                            $('#tr_quarter').hide();
                                            break;
                                        case '1to6':
                                            $('#sel_month').hide();
                                            $('#tr_quarter').hide();
                                            break;
                                        case '7to12':
                                            $('#sel_month').hide();
                                            $('#tr_quarter').hide();
                                            break;
                                    } //switch
                                };
                                filter.get_query_string = function() {
                                    this.year = $('#sel_year').val();
                                    switch (this.type) {
                                        case 'm':
                                            this.begin_month = $('#sel_month').val();
                                            this.end_month = $('#sel_month').val();
                                            break;
                                        case 'Q':
                                            $('#sel_month').hide();
                                            $('#tr_quarter').show();
                                            switch ($('[name="rad_quarter"]:checked').val()) {
                                                case '1':
                                                    this.begin_month = 1;
                                                    break;
                                                case '2':
                                                    this.begin_month = 4;
                                                    break;
                                                case '3':
                                                    this.begin_month = 7;
                                                    break;
                                                default:
                                                    this.begin_month = 10;
                                                    break;
                                            } //switch
                                            this.end_month = this.begin_month + 2;
                                            break;
                                        case 'Y':
                                            this.begin_month = 1;
                                            this.end_month = 12;
                                            break;
                                        case '1to6':
                                            this.begin_month = 1;
                                            this.end_month = 6;
                                            break;
                                        case '7to12':
                                            this.begin_month = 7;
                                            this.end_month = 12;
                                            break;
                                    } //switch
                                    return 'type=' + this.type + '&year=' + this.year + '&begin_month='
                                            + this.begin_month + '&end_month=' + this.end_month;
                                };
                                $(document).ready(function() {
                                    filter.set_type('m');
                                    $('#rad_m').attr('checked', 'checked');
                                });

                                function btn_print_onclick() {
                                    query = filter.get_query_string();
                                    url = $('#controller').val() + 'option/3' + QS + 'pdf=true' + '&' + query;
                                    window.showPopWin(url, 1000, 600);
                                }
</script>




<?php
$this->template->display('dsp_footer.php');