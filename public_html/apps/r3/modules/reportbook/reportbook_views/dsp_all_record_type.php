<?php
/**
Copyright (C) 2012 Tam Viet Tech. All rights reserved.

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

$this->template->title = $arr_single_book['c_name'];
$this->template->display('dsp_header.php');
?>
<form id="frmMain" action="" name="frmMain" method = "post" style = "width:90%; margin:0 auto">
    <?php
    echo $this->hidden('controller', $this->get_controller_url());
    echo $this->hidden('hdn_dsp_add_method', 'dsp_all_record_type_to_add');
    echo $this->hidden('hdn_add_method', 'insert_record_type_to_book');
    echo $this->hidden('hdn_delete_method', 'delete_record_type_from_book');
    echo $this->hidden('hdn_item_id_list', '');
    echo $this->hidden('hdn_go_back', 'dsp_all_record_type');
    echo $this->hidden('hdn_item_id', $book_id);
    echo $this->hidden('hdn_item_code', $arr_single_book['c_code']);
    ?>
    <h3 class="page-title"><?php echo $this->template->title; ?></h3>
    <div id="solid-button">
        <input type="button" class="solid add" value="Thêm thủ tục vào sổ" onclick="show_all_record_type()"/>
        <input type="button" class="solid delete" value="Loại thủ tục khỏi sổ" onclick="btn_delete_onclick()"/>
    </div>
    <table width="100%" class="adminlist" cellspacing="0" cellpading="0" border="1">
        <colgroup>
            <col width="15%">
            <col width="20%">
            <col width="50%">
            <col width="15%">
        </colgroup>
        <thead>
            <tr>
                <th><input type="checkbox" onclick="toggle_check_all(this, this.form.chk);"/></th>
                <th>Mã thủ tục</th>
                <th>Tên thủ tục</th>
                <th>Loại khỏi sổ</th>
            </tr>
        </thead>
        <tbody>
            <?php for ($i = 0; $i < count($arr_all_record_type); $i++): ?>
                <?php
                $datarow = $arr_all_record_type[$i];
                $id      = $datarow['PK_RECORD_TYPE'];
                $html_id = "chk_$id";
                $name    = $datarow['C_NAME'];
                $code    = $datarow['C_CODE'];
                ?>
                <tr class="row<?php echo $i % 2 ?>">
                    <td class="center">
                        <input 
                            type="checkbox" name="chk" 
                            value="<?php echo $id ?>"
                            id="<?php echo $html_id ?>"
                            />
                    </td>
                    <td>
                        <label for="<?php echo $html_id ?>">
                            <?php echo $code ?>
                        </label>
                    </td>
                    <td>
                        <label for="<?php echo $html_id ?>">
                            <?php echo $name ?>
                        </label>
                    </td>
                    <td>
                        <a href="javascript:;" onclick="delete_single(<?php echo $id ?>)">
                            Loại thủ tục này khỏi sổ
                        </a>
                    </td>
                </tr>
            <?php endfor; ?>
        </tbody>
    </table>
</form>
<script>
            function show_all_record_type() {
                v_url = $('#controller').val() + $('#hdn_dsp_add_method').val() + '/' + $('#hdn_item_id').val();
                window.showPopWin(v_url, 800, 600, function(returnVal) {
                    if (!returnVal) {
                        return;
                    }
                    $.ajax({
                        type: 'post',
                        url: $('#controller').val() + $('#hdn_add_method').val() + '/' + $('#hdn_item_id').val(),
                        data: {book_id: $('#hdn_item_id').val(), arr_record_type: returnVal},
                        success: function() {
                            window.location.reload();
                        }
                    });
                });
            }

            function delete_single(item_id) 
            {
                $('#chk_' + item_id).attr('checked', 'checked');
                btn_delete_onclick();
            }

</script>
<?php
$this->template->display('dsp_footer.php');