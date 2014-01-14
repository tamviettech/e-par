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


$this->template->title = $arr_single_book['c_name'];
$this->template->display('dsp_header.php');
?>

<div id="procedure">
    <h4></h4>
    <form id="frmMain" method="post" action="">
        <?php
        echo $this->hidden('controller', $this->get_controller_url());
        echo $this->hidden('hdn_item_id', $book_id);
        echo $this->hidden('hdn_this_method', 'dsp_single_book');
        echo $this->hidden('hdn_export_method', 'export_book');
        ?>
        <table style="width:80%" class="no-border">
            <colgroup>
                <col width="20%">
                <col width="80%">
            </colgroup>
            <tbody>
                <tr>
                    <tD><b>Tên sổ</b></tD>
                    <td>
                        <select name="sel_books" onchange="sel_books_onchange(this)" style="width:100%">
                            <?php foreach ($arr_all_books as $book): ?>
                                <?php $selected = $book['id'] == $book_id ? 'selected' : '' ?>
                                <option value="<?php echo $book['id'] ?>" <?php echo $selected ?>>
                                    <?php echo $book['c_name'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <tD><b>Lọc theo ngày</b></tD>
                    <td>
                        <label for="txt_begin_date">Từ ngày</label>
                        <input 
                            type="text" id="txt_begin_date" name="txt_begin_date" 
                            onclick="DoCal('txt_begin_date')"
                            value="<?php echo $begin_date ?>"
                            size="18"
                            />
                        <img class="btndate" style="cursor:pointer"
                             src="<?php echo SITE_ROOT ?>public/images/calendar.gif" 
                             onclick="DoCal('txt_begin_date')"/>
                        <label for="txt_end_date">Đến ngày</label>
                        <input 
                            type="text" id="txt_end_date" name="txt_end_date" 
                            onclick="DoCal('txt_end_date')"
                            value="<?php echo $end_date ?>"
                            size="18"
                            />
                        <img class="btndate" style="cursor:pointer" 
                             src="<?php echo SITE_ROOT ?>public/images/calendar.gif" 
                             onclick="DoCal('txt_end_date')"/>
                        <input 
                            type="button" class="solid search" 
                            onclick="this.form.submit()" 
                            value="Lọc"
                            />
                    </td>
                </tr>
            </tbody>
        </table>
        <br/>
        <div id="solid-button">
            <input 
                type="button" class="solid excel" 
                onclick="btn_export_onclick('xls')"
                value="Kết xuất sổ dưới dạng Excel"
                />
            <input 
                type="button" class="solid excel" 
                onclick="btn_export_onclick('cvs')"
                value="Kết xuất sổ dưới dạng CVS"
                />
            <input type="button" class="solid pdf" 
                   value="Kết xuất sổ dưới dạng PDF"
                   onclick="btn_export_onclick('pdf')"
                   />
        </div>
        <?php if ($this->load_abs_xml($this->get_book_config(strtolower($arr_single_book['c_code'])))): ?>
            <?php
            $cols        = $this->dom->xpath("//display_all/list/item[@type != 'primarykey']");
            $total_width = 0;
            foreach ($cols as $col)
            {
                $total_width+= (int) $col->attributes()->size;
            }
            ?>
            <?php if ($total_width > 100): ?>
                <h3 class="page-title">Tổng chiều rộng cột của File XML định nghĩa bảng hiển thị quá 100%. Sổ sau khi in ra có thể bị mất chữ!</h3>
            <?php endif; ?>
            <?php echo $this->render_book($arr_all_record); ?>
        <?php else: ?>
            <h3 class="page-title">File XML định nghĩa bảng hiển thị chưa có hoặc bị lỗi!</h3>
        <?php endif; ?>
        <div class="clear"></div>
        <div>
            <?php echo $this->paging2($arr_all_record) ?>
        </div>
    </form>
</div>

<script>

                            function sel_books_onchange(obj)
                            {
                                v_url = $('#controller').val() + $('#hdn_this_method').val() + '/' + $(obj).val();
                                window.location = v_url;
                            }

                            function btn_export_onclick(file_type)
                            {
                                book_id = $('#hdn_item_id').val();
                                begin_date = $('#txt_begin_date').val();
                                end_date = $('#txt_end_date').val();

                                v_url = $('#controller').val()
                                        + $('#hdn_export_method').val() + '/' + book_id
                                        + '&begin_date=' + begin_date
                                        + '&end_date=' + end_date
                                        + '&file_type=' + file_type;
                                var win = window.open(v_url, '_blank');
                                win.focus();
                            }

</script>

<?php
$this->template->display('dsp_footer.php');






