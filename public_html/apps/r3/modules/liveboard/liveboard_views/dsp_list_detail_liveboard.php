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

//la hien tong hop hop theo xa
$is_single_all_village =$VIEW_DATA['all_xa'];
if($is_single_all_village == 'all_xa')
{
    $arr_village    = $VIEW_DATA['arr_village'];
    $v_village_name = isset($arr_village['C_NAME']) ? $arr_village['C_NAME'] : '';
    $v_village_id   = isset($arr_village['PK_OU']) ? $arr_village['PK_OU'] : '';
    $v_type_record_name     = $this->template->title  = $v_village_name;
}
else 
{
    // Hien thi theo loai thu tuc
    $arr_all_record = $arr_all_record_type = array();
    $v_record_type_code     = $VIEW_DATA['type_record_code'];
    //header
    $v_type_record_name     = $this->template->title  = $VIEW_DATA['type_record_name'];
}
$arr_all_record         = $VIEW_DATA['arr_all_record'];
$v_title                = $VIEW_DATA['record_status'];
$this->template->display('dsp_header_pop_win.php');

?>
<div class="container-fluid">
    <form name="frmMain" id="frmMain" action="" method="POST" class="form-horizontal">
        <?php
            echo $this->hidden('controller',$this->get_controller_url());
            echo $this->hidden('hdn_item_id','0');

        ?>
        <!--header-->
        <div class="list-detail-head" >
            <h5 style="background: #3498DB;padding-left: 10px; color: white; padding: 5px 0">
                Loại thủ tục: <span style="font-weight: normal">
                    <?php echo ($is_single_all_village == 'all_xa') ? $v_village_name : $v_type_record_name;?></span>
            </h5>
            <h5 style="padding-left: 10px;">
                Trạng thái: <span style="font-weight: normal;color: red">"<?php echo $v_title ?></span>
            </h5>
        </div>
        <div class="clear" style="height: 10px;"></div>
        
        <!--table show list-->
        <div id="procedure">
               <?php
                if ($this->load_abs_xml($this->get_xml_config($v_record_type_code, 'lookup')))
                {
                    echo $this->render_form_display_all_record($arr_all_record, FALSE);
                }
                ?>
        </div>
        <!--paging-->
        <div>
            <?php
                $v_page = isset($_POST['sel_goto_page']) ? ($_POST['sel_goto_page']) : 1;
                $v_rows_per_page = isset($_POST['sel_rows_per_page']) ? ($_POST['sel_rows_per_page']) : _CONST_DEFAULT_ROWS_PER_PAGE;
                $total_record = (isset($arr_all_record[0]['TOTAL_RECORD'])) ? $arr_all_record[0]['TOTAL_RECORD'] : $v_rows_per_page;
                
                echo $this->paging($v_page, $v_rows_per_page, $total_record);
            ?>
        </div>

    </form>
    <!--Button close window--> 
    <div style="float:right;margin-right: 10px;margin-bottom: 5px; " >
    <button type="button" name="trash" class="btn btn-danger" onclick="try{window.parent.hidePopWin();}catch(e){window.close();};">
        <i class="icon-remove"></i>
        <?php echo __('close window'); ?>
    </button>
    </div>
    <!--ContextMenu-->
    <ul id="myMenu" class="contextMenu" style="top: 385px; left: 483px; display: none;">
        <li class="statistics"><a href="#statistics">Xem tiến độ</a></li>
    </ul>
</div>
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
                case 'rollback':
                    dsp_rollback(v_record_id, v_record_type);
                    break;
                case 'delete':
                    if (v_deleted == '0' || v_deleted == 0) {
                        delete_record(v_record_id);
                    } else {
                        undelete_record(v_record_id);
                    }
                    break;
            }
        });

        //Quick action

            $('.adminlist tr[role="presentation"] td[role="action"] .quick_action').each(function(index) {
                v_item_id = $(this).attr('data-item_id');
                html = '';
                //Thong tin tien do
                html += '<a href="javascript:void(0)" onclick="dsp_single_record_statistics(\'' + v_item_id + '\');" class="quick_action" >';
                html += '<img src="' + SITE_ROOT + 'public/images/statistics-16x16.png" title="Xem tiến độ" /></a>';
                
                $(this).html(html);
            });
    });

    function dsp_single_record_statistics(record_id, tab)
    {
        var url = $("#controller").val() + 'dsp_single_record_statistics/' + record_id + '&hdn_item_id=' + record_id + '&pop_win=1' ;
        if (typeof(tab) !== 'undefined')
            {
            url += '&tab=' + tab;
            }
            showPopWin(url, 700, 500);
    }

</script>
<?php
$this->template->display('dsp_footer_pop_win.php');