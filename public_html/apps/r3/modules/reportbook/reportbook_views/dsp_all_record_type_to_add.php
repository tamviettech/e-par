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

$this->template->title = 'Thêm thủ tục vào sổ';
$this->template->display('dsp_header_pop_win.php');
?>
<h1></h1>
<form id="frmMain" action="" name="frmMain" method = "post" >
    <label for="txt_search_name">
        <b>Mã, hoặc tên loại hồ sơ</b>
    </label>
    <input style="height: 30px;margin-bottom: 0" type="text" id="txt_search_name" value="<?php echo get_post_var('txt_search_name') ?>" name="txt_search_name"/>
    <button type="button" class="btn btn-primary" onclick="this.form.submit();"><i class="icon-search"></i>Lọc</button>
    <div style="height: 5px;width: 100%"></div>
    <table width="100%" class="adminlist" cellspacing="0" cellpading="0" border="1">
        <colgroup>
            <col width="15%">
            <col width="20%">
            <col width="65%">
        </colgroup>
        <thead>
            <tr>
                <th><input type="checkbox" onclick="toggle_check_all(this, this.form.chk);"/></th>
                <th>Mã thủ tục</th>
                <th>Tên thủ tục</th>
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
                </tr>
            <?php endfor; ?>
        </tbody>
    </table>
    <?php echo $this->paging2($arr_all_record_type) ?>
    <div class="button-area">
        <button type="button" class="btn btn-success" onclick="do_attach()"><i class="icon-save"></i>Cập nhật</button>
        <button type="button" class="btn btn-danger" onclick="window.parent.hidePopWin(false)"><i class="icon-remove"></i>Hủy bỏ</button>
    </div>
</form>
<script>
function do_attach()
{
    var data = [];
    $('[name=chk]:checked').each(function(){
       data.push($(this).val()); 
    });
    returnVal = data;
    window.parent.hidePopWin(true);
}
</script>
<?php
$this->template->display('dsp_footer_pop_win.php');