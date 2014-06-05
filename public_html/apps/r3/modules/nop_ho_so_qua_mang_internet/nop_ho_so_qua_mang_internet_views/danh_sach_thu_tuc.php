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
$rows_per_col = 5;
array_unshift($arr_all_spec, '<b>--Tất cả lĩnh vực--</b>');
$v_spec_code  = get_post_var('rad_linh_vuc', 0);
?>
<style>
    table td.top{vertical-align: top}
    .match{color: red; background-color: yellow}
</style>
<div class="panel_color">Chọn thủ tục muốn nộp</div>
<form id="frmMain" method="post">
    <table class="no-border">
        <colgroup>
            <col width="30%">
            <col width="70%">
        </colgroup>
        <tbody>
            <tr class="list-search">
                <td><b>Lĩnh vực:</b></td>
                <td>
                    <?php if ($arr_all_spec): ?>
                        <table>
                            <tr>
                                <?php for ($i = 0; $i < count($arr_all_spec) / $rows_per_col; $i++): ?>
                                    <td class="top">
                                        <?php for ($j = $i * $rows_per_col; $j < $rows_per_col + $i * $rows_per_col; $j++): ?>
                                            <?php if (!$linh_vuc = current($arr_all_spec)) continue; ?>
                                            <?php
                                            $v_name   = $linh_vuc;
                                            $v_value  = key($arr_all_spec);
                                            $v_checked = $v_value == $v_spec_code ? 'checked' : '';
                                            next($arr_all_spec);
                                            ?>
                                            <input 
                                                type="radio" name="rad_linh_vuc" 
                                                id="linh_vuc_<?php echo $v_value ?>"
                                                value="<?php echo $v_value ?>"
                                                <?php echo $v_checked ?>
                                                />
                                            <label for="linh_vuc_<?php echo $v_value ?>">
                                                <?php echo $v_name ?>
                                            </label>
                                            <br/>
                                        <?php endfor; ?>
                                    </td>
                                <?php endfor; ?>
                            </tr>
                        </table>
                    <?php endif; ?>
                </td>
            </tr>
            <tr class="text-search">
                <td>
                    <b>
                        <label for="txt_tu_khoa">Tìm theo Mã hoặc Tên thủ tục: &nbsp;&nbsp;</label>
                    </b>
                </td>
                <td>
                    <input type="text" name="txt_tu_khoa" id="txt_tu_khoa"
                           style="width:320px"
                           value="<?php echo get_post_var('txt_tu_khoa') ?>"
                           />
                    <input type="button" name="btn_filter" value="Tìm kiếm" class="solid search" onclick="this.form.submit()">
                </td>
            </tr>
        </tbody>
    </table>
    <table class="adminlist" style="width: 100%">
        <colgroup>
            <col width="7%">
            <col width="93%">
        </colgroup>
        <thead>
            <tr>
                <th>STT</th>
                <th>Tên thủ tục</th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 0; ?>
            <?php foreach ($arr_all_record_type as $record_type): ?>
                <?php
                if ($v_tu_khoa = get_post_var('txt_tu_khoa'))
                {
                    $record_type['C_NAME'] = str_ireplace($v_tu_khoa, "<span class='match'>$v_tu_khoa</span>", $record_type['C_NAME']);
                }
                ?>
                <tr class="row<?php echo $i % 2 ?>">
                    <td class="center"><?php echo++$i ?></td>
                    <td>
                        <a href="<?php echo SITE_ROOT . 'nop_ho_so/nhap_thong_tin/' . $record_type['PK_RECORD_TYPE'] ?>">
                            <?php echo $record_type['C_NAME'] ?>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php echo $this->paging2($arr_all_record_type); ?>
</form>