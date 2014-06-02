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
defined('DS') or die();

$this->template->title = isset($arr_single_book['c_name']) ? $arr_single_book['c_name'] : '';
$this->template->display('dsp_header.php');
//Dánh sách vùng
$arr_all_scope = is_array($VIEW_DATA['arr_all_scope']) ? $VIEW_DATA['arr_all_scope'] :array();
$v_curent_url =  $_GET['url'];
$v_book_id     = end(explode('/', $v_curent_url));  

?>

<div id="procedure">
    <h4></h4>
    <form id="frmMain" method="post" action="">
        <?php
        echo $this->hidden('controller', $this->get_controller_url());
        echo $this->hidden('hdn_this_method', 'dsp_single_book');
        echo $this->hidden('hdn_item_id', $v_book_id);
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
                        <select name="sel_books" onchange="sel_books_onchange(this)" style="height: 30px;width:100%">
                            <option value="0">-------------- Lựa chọn sổ theo dõi --------------</option>
                            
                            <?php foreach ($arr_all_books as $book): ?>
                                <?php $selected = $book['id'] == $v_book_id ? 'selected' : '' ?>
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
                        <table>
                            <tr>
                                <td>
                                    <label style="float: left;margin-right: 10px; line-height: 30px"   for="txt_begin_date">Từ ngày</label>
                                    <input style="margin-bottom: 0;  height: 30px;" 
                                           type="text" id="txt_begin_date" name="txt_begin_date" 
                                           onclick="DoCal('txt_begin_date')"
                                           value="<?php echo $begin_date ?>"
                                           size="18"
                                           />
                                    <img class="btndate" style="cursor:pointer"
                                         src="<?php echo SITE_ROOT ?>public/images/calendar.gif" 
                                         onclick="DoCal('txt_begin_date')"/>
                                             
                                </td>
                                <td>
                                    <label style="float: left;margin-right: 10px; line-height: 30px"   for="txt_end_date">Đến ngày</label>
                                    <input style="margin-bottom: 0;  height: 30px;" 
                                           type="text" id="txt_end_date" name="txt_end_date" 
                                           onclick="DoCal('txt_end_date')"
                                           value="<?php echo $end_date ?>"
                                           size="18"
                                           />
                                    <img class="btndate" style="cursor:pointer" 
                                         src="<?php echo SITE_ROOT ?>public/images/calendar.gif" 
                                         onclick="DoCal('txt_end_date')"/>
                                             
                                </td>
                            </tr>
                        </table>
                                
                                
                    </td>
                </tr>
                <tr>
                    <td><b>Lọc theo đơn vị tiếp nhận</b></td>
                    <td>
                        <div style="height: 5px;width: 100%"></div>
                        <select name="sel_scope" id="sel_scope" style="margin-bottom: 0;height: 30px;" >
                            <option value="-1">Tất cả đơn vị tiếp nhận</option>
                            <?php
                                $$v_is_village    = FALSE;
                                $$v_is_village    = (bool) Session::get('la_can_bo_cap_xa');
                                if(!$$v_is_village)
                                {
                                    $v_selected_village = '';   
                                    if(isset($_REQUEST['sel_scope']) &&  $_REQUEST['sel_scope'] == 0)
                                    {
                                        $v_selected_village = 'selected';
                                    }
                                    echo '<option '. $v_selected_village .'  value="0">UBND Huyện Lạng Giang</option>';
                                }
                            ?>
                            <?php for($i =0; $i<count($arr_all_scope); $i ++):;?>
                            <?php
                                $v_scope_id   = isset($arr_all_scope[$i]['PK_OU']) ? $arr_all_scope[$i]['PK_OU'] : '';
                                $v_scope_name = isset($arr_all_scope[$i]['C_NAME']) ? $arr_all_scope[$i]['C_NAME'] : '';
                                $v_selected = ($_REQUEST['sel_scope'] == $arr_all_scope[$i]['PK_OU']) ? 'selected' : '';
                            ?>
                            <option <?php echo $v_selected; ?> value="<?php echo $v_scope_id; ?>"><?php echo $v_scope_name;?></option>
                            <?php endfor;?>
                        </select>
                        
                         <div style="width: 100%;text-align: center; margin-top: 5px;">
                            <button data-toggle="dropdown" value="Lọc" class="btn btn-primary dropdown-toggle" onclick="this.form.submit()" >Lọc
                            <span class="icon-search"></span>
                            </button>
                             <button data-toggle="dropdown" value="Lọc" class="btn btn-default" onclick="window.location.href = '<?php echo SITE_ROOT;?>r3/reportbook/';" >
                                 <i class="icon-undo"></i>
                                 Quay lại
                            </button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        <br/>
        <?php if(sizeof($arr_all_record) > 0): ?>
        <div id="solid-button" style="margin-bottom: 5px;">
            <button class="btn btn-info" type="button" 
                    class="solid excel" onclick="btn_export_onclick('xls')">
                <i class="icon-print"></i> 
                Kết xuất sổ dưới dạng Excel
            </button>
            
           <button class="btn btn-info" 
                    type="button" class="solid excel" 
                    onclick="btn_export_onclick('cvs')">
                 <i class="icon-print"></i>  
               Kết xuất sổ dưới dạng CVS
            </button>
           
            
            <button class="btn btn-info" 
                    type="button" class="solid pdf" 
                    onclick="btn_export_onclick('pdf')">
                 <i class="icon-print"></i>  
               Kết xuất sổ dưới dạng PDF
            </button>
        </div>
        <?php
             $arr_single_book['c_code'] = isset($arr_single_book['c_code']) ? $arr_single_book['c_code'] : '';
             if ($this->load_abs_xml($this->get_book_config(strtolower($arr_single_book['c_code'])))): ?>
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
        <?php else:?>
             <h3 class="page-title">Không tìm thấy hồ hơ nào phù hợp với tiêu chí đã chọn</h3>
        <?php endif;?>
    </form>
</div>

<script>

                            function sel_books_onchange(obj)
                            {
                                v_url = $('#controller').val() + $('#hdn_this_method').val() + '/' + $(obj).val();
                                document.forms.frmMain.submit();
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






