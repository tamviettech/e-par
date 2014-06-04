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
if (!defined('SERVER_ROOT')) {
    exit('No direct script access allowed');
}

$this->template->title = 'All article';
$this->template->display('dsp_admin_header_popup.php');

$arr_all_article = $VIEW_DATA['arr_all_article'];
$arr_all_category = $VIEW_DATA['arr_all_category'];
$count_article = $VIEW_DATA['count_article'];
$v_category_id = isset($_POST['filter_category']) ? $_POST['filter_category'] : -1;
?>

<style type="text/css">
    #div_search{
        float:right;
    }
    .status{
        color:olivedrab;
    }
    body{
        background-color: white;
    }
</style>
<script type="text/javascript">
    $(document).ready(function(){
        //check $_GET de overrider $_POST 
        var json_get = <?php echo isset($_GET) ? json_encode($_GET) : '{}'; ?>;
        
        var arr_filter = <?php echo json_encode($_GET); ?>;
        for(var index in json_get){
            if(index != 'url'){
                $('[name="'+index+'"]').remove();
                var str_hdn = '<input type="hidden" name="'+index+'" value="'+json_get[index]+'"/>';
                $('#frm_filter').append(str_hdn);
            }    
        }
       

       
        //check all
        $('#chk_all_article').change(function(){
            if($('#chk_all_article').attr('checked')){
                $('input:checkbox').attr('checked','checked');
            }else{
                $('input:checkbox').removeAttr('checked');
            }
            return false;
        });
              
        //select filter page size
        var filter_page_size =<?php echo isset($_POST['filter_page_size']) ? $_POST['filter_page_size'] : 20; ?>;
        $('[name="filter_page_size"] option[value=' + filter_page_size + ']').attr('selected',1);
        
    });
    //het document.ready
    
    function return_single(p_article_id)
    {
        q = '#chk_' + p_article_id;
        
        v_title = $(q).attr("data-article_title");
        
        //alert(v_title); return;
        
        var arr_article = [];
        arr_article.push({
            id : p_article_id,
            category : <?php echo  $v_category_id;?>,
            type : 'BAI_VIET',
            title: v_title,
            label : 'Chưa điền nhãn'
        });

        returnVal = arr_article; 
        //window.parent.parent.hidePopWin(true);
        window.top.hidePopWin(true);
    }
    
    function return_arr(){
        var arr_article = [];
        $('#frm_main [name="ckc_article[]"]:checked').each(function(){
            arr_article.push({
                type : 'BAI_VIET',
                id: $(this).val(),
                category : <?php echo  $v_category_id;?>,
                title: $(this).attr('data-article_title'),
                label : 'Chưa điền nhãn'
            });
            
        });
        returnVal = arr_article; 
        window.top.hidePopWin(true);
    }

</script>



<form action="" method="post" id="frm_filter">
    <?php
    echo $this->hidden('controller', $this->get_controller_url());
    ?>
    <div id="div_search">
        <input type="text" name="filter_article_title" size="25" 
               value="<?php if (isset($_POST['filter_article_title'])) echo $_POST['filter_article_title']; ?>"/>
        <input type="submit" value="Tìm tựa đề tin tức"/>
    </div>
    </br></br>

    <select name="filter_status" onChange="this.form.submit();">
<?php $filter_status = isset($_POST['filter_status']) ? $_POST['filter_status'] : 0; ?>
        <option value="0" <?php if ($filter_status == 0) echo "selected"; ?> >Tất cả bài viết</option>
        <option value="1" <?php if ($filter_status == 1) echo "selected"; ?> >Bài đang đăng</option>
        <option value="-1" <?php if ($filter_status == -1) echo 'selected'; ?> >Bài không đăng</option>
    </select>

    <select name="filter_category" onChange="this.form.submit();">
        <option value="-1">--Tất cả chuyên mục--</option>
        <!--Lấy toàn bộ category cho vào select -->
        <?php
        $n = count($arr_all_category);
        for ($i = 0; $i < $n; $i++):
            ?>
            <option value="<?php echo $arr_all_category[$i]['PK_CMS_CATEGORY']; ?>"
            <?php
            if (isset($_POST['filter_category'])
                    && $_POST['filter_category'] == $arr_all_category[$i]['PK_CMS_CATEGORY']):
                ?>
                        selected
                        <?php endif; ?>
                    >
                        <?php
                        $m = strlen($arr_all_category[$i]['C_INTERNAL_ORDER']) / 3 - 1;
                        for ($j = 0; $j < $m; $j++) {
                            echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                        }
                        echo $arr_all_category[$i]['C_NAME'];
                        ?>
            </option>
        <?php endfor; ?>
    </select>

    <input type="submit" value="Lọc bài viết"/></br>
    Trang:&nbsp;
    <?php //@paging
    ?>
    <select name="filter_page_no" onChange="this.form.submit();">
        <?php $num_rows = $count_article; ?>
        <?php $filter_page_size = isset($_POST['fitler_page_size']) ? $_POST['fitler_page_size'] : 20; ?>
        <?php $n = ceil($num_rows / $filter_page_size); ?>
        <?php $filter_page_no = isset($_POST['fitler_page_no']) ? $_POST['fitler_page_no'] : 1; ?>

            <?php for ($i = 1; $i <= $n; $i++): ?>
            <option value ="<?php echo $i ?>" <?php if ($filter_page_no == $i) echo 'selected'; ?> >
            <?php echo $i ?>
            </option>
<?php endfor; ?>
    </select>
    Số lượng hiển thị:&nbsp;
    <select name="filter_page_size" onChange="this.form.submit();">
        <option value="10">10</option>
        <option value="20">20</option>
        <option value="30">30</option>
        <option value="40">40</option>
    </select>
</form>

<a href="javascript:;" onClick="return_arr()">[Thêm bài đã chọn]</a>

<form action="" method="post" id="frm_main">

    <table class="adminlist" width="100%" cellpadding="0" cellspacing="0">
        <th width="15px"><input type="checkbox" id="chk_all_article"></th>
        <th width="200px">Tiêu đề</th>
        <th width="10%">Ngày đăng</th>
        <th width="10%">Ngày bắt đầu</th>
        <th width="10%">Trạng thái</th>
        <?php if(isset($_POST['filter_category']) && $_POST['filter_category'] != -1): ?>
            <?php
            $n = count($arr_all_article);
            for ($i = 0; $i < $n; $i++):?>
                <?php
                //Gan thong tin article cho cac $v
                $v_pk_article = $arr_all_article[$i]['PK_ARTICLE'];
                $v_title = $arr_all_article[$i]['C_TITLE'];
                $v_status = $arr_all_article[$i]['C_STATUS'];
                $v_init_date = @gmdate('d-m-Y', strtotime($arr_all_article[$i]['C_INIT_DATE']));
                $v_begin_date = @gmdate('d-m-Y', strtotime($arr_all_article[$i]['C_BEGIN_DATE']));
                ?>
                <tr>
                    <td>
                        <input type="checkbox" name="ckc_article[]" id="chk_<?php echo $v_pk_article; ?>"
                               value="<?php echo $v_pk_article; ?>" 
                               data-article_title="<?php echo $v_title; ?>"/>
                    </td>
                    <td>
                        <a 
                            href="javascript:void(0)" 
                            onClick="return_single(<?php echo $v_pk_article; ?>)"
                            >
                            <?php echo $v_title; ?>
                        </a>
                    </td>
                    <td><?php echo $v_init_date; ?></td>
                    <td><?php echo $v_begin_date; ?></td>
                    <td class="status"><?php echo $v_status == 1 ? 'Đăng' : 'Không đăng'; ?></td>
                </tr>
            <?php endfor; ?>
        <?php else: ?>
            <tr><td colspan='5'><center><b>Bạn cần chọn chuyên mục</b></center></td></tr>
        <?php endif;?>
    </table>
</form>

<?php $this->template->display('dsp_admin_footer_popup.php'); ?>