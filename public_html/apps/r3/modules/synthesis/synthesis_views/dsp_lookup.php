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
if (!defined('SERVER_ROOT')) exit('No direct script access allowed');
//filter
$v_record_no         = get_post_var('txt_record_no','');
$v_citizen_name      = get_post_var('txt_citizen_name','');
$v_receive_date_from = get_post_var('txt_receive_date_from','');
$v_receive_date_to   = get_post_var('txt_receive_date_to','');
?>
<form class="form-horizontal" action="" method="POST" name="frmMain" id="frmMain">
    <?php
        echo $this->hidden('controller',$this->get_controller_url());
    ?>
    <!--filter-->
    <div class="content-widgets light-gray">
        <div class="widget-head blue">
                <h3>Tra cứu hồ sơ các đơn vị trực thuộc</h3>
        </div>
        <div class="widget-container">
            <div class="control-group">
                    <label class="control-label">Mã hồ sơ</label>
                    <div class="controls">
                        <input type="text" name="txt_record_no" id="txt_record_no" class="input-xlarge" value="<?php echo $v_record_no?>">
                            Tên công dân
                        <input type="text" name="txt_citizen_name" id="txt_citizen_name" class="input-xlarge" value="<?php echo $v_citizen_name?>">
                    </div>
            </div>
            <div class="control-group">
                    <label class="control-label">Tiếp nhận</label>
                    <div class="controls">
                        <span style="width:80px !important">Từ ngày : </span> 
                <input type="text" name="txt_receive_date_from" id="txt_receive_date_from" class="input-large" value="<?php echo $v_receive_date_from?>">
                <img class="btndate" style="cursor:pointer" id="btnDate" src="<?php echo SITE_ROOT?>public/images/calendar.gif" onclick="DoCal('txt_receive_date_from')">

                <span style="width:80px">Đến ngày: </span>
                <input type="text" name="txt_receive_date_to" id="txt_receive_date_to" class="input-large" value="<?php echo $v_receive_date_to?>">
                <img class="btndate" style="cursor:pointer" id="btnDate" src="<?php echo SITE_ROOT?>public/images/calendar.gif" onclick="DoCal('txt_receive_date_to')">
                    </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary" onclick="this.form.submit();" >
                    <i class="icon-search"></i>
                    Tìm kiếm
                </button>
                <button type="button" class="btn" onclick="btn_reset_onclick();">
                    <i class="icon-remove"></i>Xoá điều khiện lọc
                </button>
            </div>
        </div>
    </div>
    
    <!--result-->
    <div id="result">
        <div class="widget-head blue">
                <h3>Danh sách hồ sơ</h3>
        </div>
        <div class="clear">&nbsp;</div>
        <div id="procedure">
            <?php
            if ($this->load_abs_xml($this->get_xml_config('', 'lookup')))
            {
                echo $this->render_form_display_all_record($arr_all_record, FALSE);
            }
            ?>
        </div>
        <div id="dyntable_length" class="dataTables_length">
            <?php echo $this->paging2($arr_all_record);?>
        </div>
        
        <!-- Context menu -->
        <ul id="myMenu" class="contextMenu">
            <li class="statistics"><a href="#statistics">Xem tiến độ</a></li>
        </ul>
    </div>
</form>

<script>
$(document).ready(function() {
    //Show context on each row
    $(".adminlist tr[role='presentation']").contextMenu({
        menu: 'myMenu'
    }, function(action, el, pos) {
        v_record_id = $(el).attr('data-item_id');
        v_record_type = $(el).attr('data-item-type');
        v_deleted = $(el).attr('data-deleted');
        switch (action) {
            case 'statistics':
                dsp_single_record_statistics(v_record_id);
                break;
        }
    });

    //Quick action
    $('.adminlist tr[role="presentation"] td[role="action"] .quick_action').each(function(index) {
        v_item_id = $(this).attr('data-item_id');
        v_item_type = $(this).attr('data-item-type');
        v_deleted = $(this).attr('data-deleted');

        html = '';

        //Thong tin tien do
        html += '<a href="javascript:void(0)" onclick="dsp_single_record_statistics(\'' + v_item_id + '\');" class="quick_action" >';
        html += '<img src="' + SITE_ROOT + 'public/images/statistics-16x16.png" title="Xem tiến độ" /></a>';
        $(this).html(html);
    });
});

function btn_reset_onclick()
{
    var f = document.frmMain;
    f.txt_record_no.value = '';
    f.txt_citizen_name.value = '';
    f.txt_receive_date_from.value = '';
    f.txt_receive_date_to.value = '';
}    
function dsp_single_record_statistics(record_id, tab)
{
    var url = $("#controller").val() + 'statistics/' + record_id + '&hdn_item_id=' + record_id + '&pop_win=1';
    if (typeof(tab) !== 'undefined')
	{
    	url += '&tab=' + tab;
	}
    
    if (window.parent)
    {
        window.parent.showPopWin(url, 1000, 600, null, true);
    }
    else
    {
        showPopWin(url, 1000, 600, null, true);
    }
}
</script>