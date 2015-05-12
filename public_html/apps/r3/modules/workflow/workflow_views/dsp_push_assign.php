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
defined('SERVER_ROOT') or die();

$this->template->title = 'Sao chép phân công';
$this->template->display('dsp_header_pop_win.php');

$v_search = get_post_var('txt_search');
if (!$v_search)
{
    $v_search = Session::get('workflow/copy/search');
}
?>
<h2 class="page-title">Chọn (các) thủ tục nhận sao chép phân công</h2>
<form id="frmMain" method="post" action="">
    <?php
    echo $this->hidden('controller', $this->get_controller_url());
    echo $this->hidden('hdn_src_record_type_id', $id);
    echo $this->hidden('hdn_push_assign_method', 'do_push_assign');
    echo $this->hidden('hdn_dest_record_type_id_list', '');
    ?>
    <label for="txt_search">Tìm theo tên hoặc mã thủ tục:
        <input type="text" name="txt_search" id="txt_search" value="<?php echo get_post_var('txt_search') ?>" size="50"/>
    </label>
    <br/>
    <table class="adminlist table table-bordered table-striped" style="width:100%">
        <colgroup>
            <col width="5%">
            <col width="5%">
            <col width="15%">
            <col width="75%">
        </colgroup>
        <thead>
            <tr>
                <th>STT</th>
                <th>#</th>
                <th>Mã thủ tục</th>
                <th>Tên thủ tục</th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 1; ?>
            <?php foreach ($arr_spec_record_types as $record_type): ?>
                <tr class="row<?php echo $i % 2 ?>">
                    <td class="center"><?php echo $i++ ?></td>
                    <td class="center">
                        <input type="checkbox" name="chk_record_type" 
                               id="chk_record_type_<?php echo $record_type['PK_RECORD_TYPE']; ?>" 
                               value="<?php echo $record_type['PK_RECORD_TYPE'] ?>" 
                        />
                    </td>
                    <td class="center">
                        <label for="chk_record_type_<?php echo $record_type['PK_RECORD_TYPE']; ?>">
                            <?php echo $record_type['C_CODE'] ?>
                        </label>
                    </td>
                    <td>
                        <label for="chk_record_type_<?php echo $record_type['PK_RECORD_TYPE']; ?>">
                            <?php echo $record_type['C_NAME'] ?>
                        </label>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php echo View::paging2($arr_spec_record_types) ?>
    
    <div class="button-area">
        <button class="btn" onclick="btn_push_workflow_onclick();" name="update" type="button">
            <i class="icon-ok-sign"></i>
            Sao chép
        </button>
        <button type="button" name="cancel" class="btn" onclick="try{window.parent.hidePopWin();}catch(e){window.close();};">
            <i class="icon-remove"></i>
            Đóng cửa sổ     
        </button>
    </div>
    
</form>
<script>
    function btn_push_workflow_onclick() {
        var f = frmMain;
        v_dest = get_all_checked_checkbox(f.chk_record_type,',');
        if (v_dest == '')
        {
            alert('Chưa có thủ tục nào được chọn!');
            return;
        }
        
        if (!confirm('Bạn chắc chắn muốn chép phân công đến các thủ tục đã chọn?')) {
            return;
        }
        
        $("#hdn_dest_record_type_id_list").val(v_dest);
        controller = $('#controller').val();
        v_url = controller + $('#hdn_push_assign_method').val();
        
        $("#frmMain").attr("action", $("#controller").val() + 'do_push_assign'); 
        f.submit();
    }
</script>

<?php
$this->template->display('dsp_footer_pop_win.php');