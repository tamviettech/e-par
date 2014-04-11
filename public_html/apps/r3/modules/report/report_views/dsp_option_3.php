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
$this->template->title = 'Báo cáo tổng hợp';
$this->template->display('dsp_header.php');


function check_vilage_id()
{
    $vilage_id = replace_bad_char($_SESSION['village_id']);
    if($vilage_id > 0)
    {
        return true;
    }
    else 
    {
        return false;
    }
}
?>
<style>
    table td{padding: 3px;}
</style>
<form id="frmMain" method="post">
    <?php
    echo $this->hidden('controller', $this->get_controller_url());
    echo $this->hidden('hdn_group_level', '');
    ?>
    <table class="no-border" >
        <tr>
            <td><b>Đơn vị:</b></td>
            <td>
                <select name="sel_group" id="sel_group" onchange="sel_group_onchange(this)">
                    <?php if(!check_vilage_id()):?>
                    <option value="">--- Tất cả ---</option>
                    <?php endif;?>
                    <?php foreach($arr_all_group as $group_code => $info):?>
                    <option value="<?php echo $group_code?>" data-level="<?php echo $info['C_LEVEL']?>"><?php echo $info['C_NAME']?></option>
                    <?php endforeach?>
                </select>
            </td>
        </tr>
        <tr>
            <td><b>Thời gian tiếp nhận hồ sơ: </b></td>
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
                <input type="button" name="trash" class="solid print" onclick="btn_print_onclick();" value="In báo cáo">
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
    this.group;
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
    this.year        = $('#sel_year').val();
    this.group       = $('#sel_group').val();
    this.group_level = $('#hdn_group_level').val();
    
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
            + this.begin_month + '&end_month=' + this.end_month + '&group=' + this.group + '&group_level=' + this.group_level;
};
$(document).ready(function() {
    filter.set_type('m');
    $('#rad_m').attr('checked', 'checked');
});

function btn_print_onclick() {
    var query = filter.get_query_string();
    var url = $('#controller').val() + 'option/3' + QS + 'pdf=true' + '&' + query;
    window.showPopWin(url, 1000, 600);
}

function sel_group_onchange(group)
{
    var level = $(group).find(':selected').attr('data-level');
   $('#hdn_group_level').val(level);
}
</script>
<?php
$this->template->display('dsp_footer.php');