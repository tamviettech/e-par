<?php
/**
Copyright (C) 2012 Tam Viet Tech.

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
defined('DS') or die();

$this->template->title = 'Danh sách sổ theo dõi hồ sơ';
$this->template->display('dsp_header.php');

$arr_all_books = $VIEW_DATA['arr_all_books'];
?>


<form id = "frmMain" action = "" method = "post" style = "width:90%; margin:0 auto">
    <?php
    echo $this->hidden('controller', $this->get_controller_url());
    echo $this->hidden('hdn_dsp_single_method', 'dsp_single_book');
    echo $this->hidden('hdn_update_method', 'dsp_update_book');
    echo $this->hidden('hdn_delete_method', 'delete_book');
    ?>
    <h3 class="page-title"><?php echo $this->template->title ?></h3>
    <div>
        <table width="100%" class="adminlist" cellspacing="0" cellpading="0" border="1">
            <colgroup>
                <col width="5%">
                <col width="55%">
                <col width="40%">
            </colgroup>
            <tbody>
                <tr>
                    <th>&nbsp;</th>
                    <th>Tên sổ</th>
                    <th>Hành động</th>
                </tr>
                <?php $index         = 0; ?>
                <?php foreach ($arr_all_books as $book): ?>
                    <?php
                    $class       = 'row' . ($index % 2);
                    $id          = $book['id'];
                    $controller  = $this->get_controller_url();
                    $page_single = $controller . 'dsp_single_book/' . $id;
                    $page_list   = $controller . 'dsp_all_record_type/' . $id;
                    $page_export = $controller . 'export/' . $id;
                    ?>

                    <tr class="<?php echo $class ?>">
                        <td class="center">
                            <?php echo $index + 1 ?>
                        </td>
                        <td><label for="chk_item_<?php echo $index ?>"><?php echo $book["c_name"] ?></label></td>
                        <td class="center">
                            <a href="<?php echo $page_single ?>">Xem</a> |
                            <a href="<?php echo $page_list ?>">Danh sách thủ tục</a>
                        </td>
                    </tr>
                    <?php $index++; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</form>

<?php
$this->template->display('dsp_footer.php');






