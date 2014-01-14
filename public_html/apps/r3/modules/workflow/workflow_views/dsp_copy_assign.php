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
<h2 class="page-title">Chọn thủ tục làm mẫu để sao chép</h2>
<form id="frmMain" method="post">
    <?php
    echo $this->hidden('controller', $this->get_controller_url());
    echo $this->hidden('hdn_item_id', $id);
    echo $this->hidden('hdn_copy_assign_method', 'copy_assign');
    ?>
    <label for="txt_search">Tìm theo tên hoặc mã thủ tục:</label>
    <input type="text" name="txt_search" id="txt_search" value="<?php echo get_post_var('txt_search') ?>" size="50"/>
    <br/>
    <table class="adminlist" style='width:100%'>
        <colgroup>
            <col width="5%">
            <col width="15%">
            <col width="80%">
        </colgroup>
        <thead>
            <tr>
                <th>STT</th>
                <th>Mã thủ tục</th>
                <th>Tên thủ tục</th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 1; ?>
            <?php foreach ($arr_spec_record_types as $record_type): ?>
                <tr class="row<?php echo $i % 2 ?>">
                    <td class="center"><?php echo $i++ ?></td>
                    <td class="center"><?php echo $record_type['C_CODE'] ?></td>
                    <td>
                        <a href="javascript:;" id="item_<?php echo $record_type['PK_RECORD_TYPE'] ?>" 
                           data-code="<?php echo $record_type['C_CODE'] ?>" 
                           onclick="copy_assign(<?php echo $record_type['PK_RECORD_TYPE'] ?>)"
                           >
                               <?php echo $record_type['C_NAME'] ?>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php echo View::paging2($arr_spec_record_types) ?>
</form>
<script>
                           function copy_assign(v_src) {
                               code = $('#item_' + v_src).attr('data-code');
                               if (!confirm('Bạn chắc chắn muốn sao chép phân công từ thủ tục ' + code + ' ?')) {
                                   return;
                               }
                               v_dest = $('#hdn_item_id').val();
                               controller = $('#controller').val();
                               v_url = controller + $('#hdn_copy_assign_method').val();
                               $.ajax({
                                   type: 'post',
                                   url: v_url,
                                   data: {src: v_src, dest: v_dest},
                                   success: function(msg) {
                                       if (msg)
                                           alert(msg);
                                       returnVal = 0;
                                       window.parent.hidePopWin(true);
                                   }
                               });
                           }
</script>

<?php
$this->template->display('dsp_footer_pop_win.php');