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
defined('SERVER_ROOT') or die;

/* @var $this \View */
$this->template->title = 'Đổi cán bộ';
$this->template->display('dsp_header_pop_win.php');
?>
<form id="frmMain" name="frmMain" method="post" action="">
    <?php
    echo $this->hidden('controller', $this->get_controller_url());
    echo $this->hidden('hdn_update_method', 'switch_user');
    echo $this->hidden('hdn_src', $user);
    echo $this->hidden('hdn_dest', '');
    echo $this->hidden('hdn_task', $task);
    ?>
    <label for="txt_search"><b>Tìm theo tên:</b></label>
    <input type="text" id="txt_search" name="txt_search" value="<?php echo $keywords ?>" size="50"/>
    <table class="adminlist table table-bordered table-striped" style="width:100%">
        <colgroup>
            <col width="10%">
            <col width="45%">
            <col width="45%">
        </colgroup>
        <tr>
            <th>STT</th>
            <th>Tên cán bộ</th>
            <th>Chức vụ</th>
        </tr>
        <?php for ($i = 0; $i < count($arr_all_user); $i++): $user = $arr_all_user[$i] ?>
            <tr class="row<?php echo $i % 2 ?>">
                <td style="text-align:center"><?php echo $i + 1 ?></td>
                <td>
                    <a href="javascript:;" onclick="switch_user('<?php echo $user['C_LOGIN_NAME'] ?>')">
                        <?php echo $user['C_NAME'] ?>
                    </a>
                </td>
                <td><?php echo $user['C_JOB_TITLE'] ?></td>
            </tr>
        <?php endfor; ?>
    </table>
    <?php echo $this->paging2($arr_all_user) ?>
</form>

<script>
                        function switch_user(dest) {
                            v_url = $('#controller').val() + $('#hdn_update_method').val()
                            $('#hdn_dest').val(dest);
                            $('#frmMain').attr('action', v_url).submit();
                        }
</script>

<?php
$this->template->display('dsp_footer_pop_win.php');
