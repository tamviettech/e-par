<?php
defined('SERVER_ROOT') or die();
$this->template->title = 'Đánh giá của công dân về bộ phận một cửa';
$this->template->display('dsp_header.php');
?>
<div class="page-title"><?php echo $this->template->title ?></div>
<form method="post" id="frmMain" name="frmMain">
    <?php
    echo $this->hidden('controller', $this->get_controller_url());
    echo $this->hidden('hdn_dsp_single_method', 'dsp_single_slot');
    echo $this->hidden('hdn_delete_method', 'delete_slot');
    echo $this->hidden('hdn_item_id_list', '');
    ?>
    <div id="solid-button">
        <input class="solid add" type="button" value="Thêm bàn tiếp nhận" onclick="item_onclick(0);"></input>
        <input class="solid delete" type="button" onclick="btn_delete_onclick();" value="Xoá bàn tiếp nhận" ></input>
    </div>

    <table class="adminlist" style="width: 100%">
        <colgroup>

        </colgroup>
        <thead>
            <tr>
                <th><input type="checkbox" id="chk_all" onclick="toggle_check_all(this, this.form.chk)"/></th>
                <th>Bàn tiếp nhận</th>
                <th>Tên cán bộ</th>
                <th>Số hài lòng</th>
                <th>Số phàn nàn</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php for ($i = 0; $i < count($arr_all_slots); $i++): ?>
                <?php
                $slot = $arr_all_slots[$i];
                ?>
                <tr class="row<?php echo $i % 2 ?>">
                    <td class="center">
                        <input type="checkbox" name="chk" value="<?php echo $slot['PK_ONEGATE_SLOT'] ?>"/>
                    </td>
                    <td class="center"><?php echo $slot['C_SLOT'] ?></td>
                    <td class="center"><?php echo $slot['C_NAME'] ?></td>
                    <td class="center"><?php echo $slot['C_COUNT_PLEASED'] ?></td>
                    <td class="center"><?php echo $slot['C_COUNT_COMPLAINED'] ?></td>
                    <td class="center">
                        <a href="javascript:;" onclick="item_onclick(<?php echo $slot['PK_ONEGATE_SLOT'] ?>)">Sửa</a> |
                        <a href="javascript:;">Thống kê</a> |
                        <a href="javascript:;" onclick="btn_delete_single_onclick(<?php echo $slot['PK_ONEGATE_SLOT'] ?>)">Xoá</a>
                    </td>
                </tr>
            <?php endfor; ?>
            <?php if ($i == 0): ?>
                <tr class="row<?php echo $i++ % 2 ?>">
                    <td colspan="6" class="center">
                        Hiện tại chưa có bàn tiếp nhận nào
                    </td>
                </tr>
            <?php endif; ?>
            <?php while ($i < _CONST_DEFAULT_ROWS_PER_PAGE): ?>
                <tr class="row<?php echo $i++ % 2 ?>">
                    <?php echo str_repeat('<td></td>', 6) ?>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</form>
<script>
            function item_onclick(id)
            {
                url = $('#controller').val() + $('#hdn_dsp_single_method').val() + '/' + id;
                showPopWin(url, 400, 300, function(data) {
                    window.location.reload();
                });
            }

            function btn_delete_single_onclick(id) {
                $('input[type=checkbox][value='+id+']').attr('checked', '1');
                btn_delete_onclick();
            }
</script>
<?php
$this->template->display('dsp_footer.php');
