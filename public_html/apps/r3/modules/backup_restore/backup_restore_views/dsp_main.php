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
if (!defined('SERVER_ROOT'))
    exit('No direct script access allowed');


//header
$this->template->title = 'Sao lưu khôi phục';
$this->template->display('dsp_header.php');
$arr_all_item               = json_decode($VIEW_DATA['all_dir_folder']);


//mang chua file la.sql
$arr_is_file_backup                       = isset($arr_all_item->file_is_backup) ? (array) ($arr_all_item->file_is_backup) : array();
$arr_is_file_backup                       = array_values($arr_is_file_backup);
$arr_total_is_file_backup[]['TOTAL_RECORD'] = ($arr_all_item->TOTAL_RECORD->TOTAL_RECORD ) ? $arr_all_item->TOTAL_RECORD->TOTAL_RECORD : 0;


//mang chua file # dieu kien loc
//$arr_file                   = isset($arr_all_item->file) ? (array) ($arr_all_item->file) : array();
////mang chua folder
//$arr_folder                 = isset($arr_all_item->folder) ? (array) ($arr_all_item->folder) : array();

$v_current_tab               = isset($_REQUEST['hdn_status_tab']) ? $_REQUEST['hdn_status_tab'] : 0;
?>
<div id="loading" style="display: none;width: 77%; height: 100%; position: absolute; z-index: 99999999; text-align: center; margin: 0px; padding: 0px;">
    <img src="/lang-giang/apps/r3/modules/backup_restore/images/loading_1.gif">
</div>

<form name="frmMain" id="frmMain" method="post">
    <?php
    echo $this->hidden('controller', $this->get_controller_url());
    echo $this->hidden('hdn_item_id', '');
    echo $this->hidden('hdn_item_id_list', '');
    echo $this->hidden('hdn_delete_method', 'dsp_delete_item');

    //restore 
    echo $this->hidden("hdn_restore_method", 'dsp_restore');
    echo $this->hidden('hdn_restore_status', isset($_REQUEST['hdn_restore_status']) ? $_REQUEST['hdn_restore_status'] : 0 );

    //start backup
    echo $this->hidden('hdn_backup_method', 'dsp_backup');
    echo $this->hidden('hdn_backup_status', isset($_REQUEST['hdn_backup_status']) ? $_REQUEST['hdn_backup_status'] : 0 );

    // Trạng thái hiển thị tab
    echo $this->hidden('hdn_status_tab', '0');
    // end hidden file
    ?>
    <div class="row-fluid">
        <div id="all-folder" class="span4">
            <div class="btn-backup-restore" style="margin-bottom: 5px;">
                <button class="btn btn-primary" type="button" onclick="onclick_backup()">Sao lưu</button>
                <button class="btn btn-danger" type="button" onclick="onclick_show_all_item();">Danh sách tập tin</button>
            </div>
            <!--end button-->
            <div class="widget-head orange" style="display:<?php echo ($v_current_tab == 0) ? 'block;' : 'none'; ?>">
                <!--<h3>&nbsp;</h3>-->
            </div>
            <!--end title-->
        </div>  
        <!--End all folder-->
        <table style="display:<?php echo ($v_current_tab == 0) ? 'table;' : 'none'; ?>" class="stat-table table table-stats table-striped table-sortable table-bordered">
            <thead>
                <tr>
                    <th>
                        <input type="checkbox" name="chck_all" id="chk_all" onclick="toggle_check_all(this, this.form.chk);" />
                    </th>
                    <th>
                        Tên tập tin
                    </th>

                    <th>
                        Thời gian tạo
                    </th>
                    <th>
                        Kích thước
                    </th>
                    <th>
                        Thao tác
                    </th>
                </tr>
            </thead>
            <tbody>
                    <?php for ($i = 0; $i < sizeof($arr_is_file_backup); $i++):; ?>
                    <tr>
                        <td style="text-align: center">
                            <input type="checkbox" name="chk" id="chk" onclick="" value="<?php echo $arr_is_file_backup[$i]->name; ?>">
                        </td>
                        <td>
                            <?php echo $arr_is_file_backup[$i]->name; ?>
                        </td>
                        <td>
                             <?php echo $arr_is_file_backup[$i]->date_create; ?>
                        </td>

                        <td>
                             <?php echo $arr_is_file_backup[$i]->size; ?>
                        </td>
                        <td>
                            <a href="javascript::void(0)" onclick="onlcik_restore('<?php echo $arr_is_file_backup[$i]->name; ?>')">Khôi phục</a>&nbsp;|&nbsp;
                            <a href="<?php echo $this->get_controller_url(); ?>download&amp;src=<?php echo md5($arr_is_file_backup[$i]->dir); ?>&amp;name=<?php echo $arr_is_file_backup[$i]->name; ?>" target="_blank">Tải về</a>
                        </td>
                    </tr>
                        <?php endfor; ?>
            </tbody>
        </table>
        <div class="button-area" style="float:right;padding-top: 0;display:<?php echo ($v_current_tab == 0) ? 'block' : 'none'; ?>;">
            <?php echo $this->paging2($arr_total_is_file_backup); ?>
        </div>
        <div class="clear"></div>
        <!--End all file-->
        <div style="display:<?php echo ($v_current_tab == 1) ? 'block' : 'none'; ?>; text-align: center;" >
            <div class="clearfix" ></div>
            <h3 style="border-top:solid 1px #0e90d2;margin-top: 20px;">Chức năng sao lưu dữ liệu</h3>
            <button class="btn btn-primary" type="button" onclick="start_backup()">Bắt đầu</button>
        </div>

        <div class="btn_delete_file" style="float: right;margin-top: 5px; display:<?php echo ($v_current_tab == 0) ? 'table;' : 'none'; ?>">
            <button type="button" class="btn btn-danger" onclick="btn_delete_onclick('chk_item')">
                <i class="icon-remove"></i>
                Xóa tập tin
            </button>
        </div>
        
    </div>
</form>

<?php
$this->template->display('dsp_footer.php');