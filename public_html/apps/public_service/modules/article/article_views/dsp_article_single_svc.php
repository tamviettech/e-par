<script type="text/javascript" src="<?php echo SITE_ROOT; ?>public/tinymce/script/tiny_mce.js"></script>

<?php
if (!defined('SERVER_ROOT')){exit('No direct script access allowed');}

$this->title = 'Them/Sua bai viet';
$selected_article = $VIEW_DATA['selected_article'];
$arr_all_category = $VIEW_DATA['arr_all_category'];
$arr_category_article = $VIEW_DATA['arr_category_article'];

//Neu khong chon article nao(tuc la them moi), gan gia tri default
$v_pk_article = 0;
$v_title = '';
$v_summary = '';
$v_init_date = @date('d-m-Y');
$v_init_time = @date('H:i');
$v_init_user = 2;
$v_fk_update_user = 2;
$v_update_date = @date('d-m-Y');
$v_update_time = @date('H:i');
$v_begin_date = @date('d-m-Y');
$v_begin_time = date('H:i');
$v_end_date = '1/1/2100';
$v_end_time = '00:00';
$v_status = '';
$v_thumb_img = '';
$v_content = '';
$v_view = '';
$v_tags = '';
$v_slug = '';
$v_init_user_name = '';
$v_update_user_name = '';
$v_today = date('d-m-Y');
$v_now = date('H:i');

//neu chon article thi thong tin da chon vao bien
if ($selected_article !== array())
{
    $v_pk_article = $selected_article['PK_ARTICLE'];
    $v_title = $selected_article['C_TITLE'];
    $v_summary = $selected_article['C_SUMMARY'];
    $v_init_date = @gmdate('d-m-Y', strtotime($selected_article['C_INIT_DATE']));
    $v_init_time = @gmdate('H:i', strtotime($selected_article['C_INIT_DATE']));
    $v_init_user = $selected_article['FK_INIT_USER'];
    $v_fk_update_user = $selected_article['FK_UPDATE_USER'];
    $v_update_date = @gmdate('d-m-Y', strtotime($selected_article['C_UPDATE_DATE']));

    //chuan hoa ngay
    $v_begin_date = @gmdate('d-m-Y', strtotime($selected_article['C_BEGIN_DATE']));
    $v_begin_time = @gmdate('H:i', strtotime($selected_article['C_BEGIN_DATE']));
    $v_end_date = @gmdate('d-m-Y', strtotime($selected_article['C_END_DATE']));
    $v_end_time = @gmdate('H:i', strtotime($selected_article['C_BEGIN_DATE']));
    $v_status = $selected_article['C_STATUS'];
    $v_thumb_img = $selected_article['C_THUMB_IMG'];
    $v_content = $selected_article['C_CONTENT'];
    $v_view = $selected_article['C_VIEW'];
    $v_tags = $selected_article['C_TAGS'];
    $v_slug = $selected_article['C_SLUG'];
    $v_init_user_name = $selected_article['C_INIT_USER_NAME'];
    $v_update_user_name = $selected_article['C_UPDATE_USER_NAME'];
}
?>
<style type="text/css">
    table{
        border-style:none;
    }
    table * td{
        border-style:none;
    }


</style>

<script type="text/javascript">
    function remove_thumb(){
        $('#frm_main [name=hdn_thumb_img]').remove();
        var html_data = '<div style="width:200px; height:150px; border-style: dashed; margin:0 auto; text-align: center; border-color: #c0c0c0; cursor:pointer;" onClick="choose_thumb(\'insert\');">'
            +'<a href="javascript:;">Chọn ảnh đại diện</a>'
            +'</div>';
        $('#thumbnail_container').html(html_data);
    }
    
    function choose_thumb(){
        window.showPopWin('<?php echo SITE_ROOT; ?>admin/media/dsp_service/image', 800, 500, function(returnVal){
            var img_url = returnVal[0]['media_file_url'];
            $('#frm_main [name=hdn_thumb_img]').remove();
            
            $('#frm_main').prepend('<input type="hidden" name="hdn_thumb_img" value="'+img_url+'"/>');
            $('#thumbnail_container').html(
            '<a href="javascript:;" onClick="remove_thumb();">Xoá ảnh</a></br>'+
                '<center><a href="javascript:;" onClick="choose_thumb();">' +
                '<img src="'+img_url+'" style="width: 200px; height: 150px;"/>' +
                '</a></center>'
        );
        });
    }
    
    function go_back(){
        $('#frm_main').attr('action','<?php echo SITE_ROOT; ?>admin/article');
        $('#frm_main').submit();
    }
    function update_article()
    {
        $('#frm_main').attr('action','<?php echo SITE_ROOT; ?>admin/article/update_article_svc');
        $('#frm_main').submit();
        
        var arr_article = [];
        arr_article.push({
            title: $('[name="txt_title"]').val()
        });
        returnVal = arr_article; 
        window.top.hidePopWin(true);
    }
</script>

<form id="frm_main" action="" method="post">
    <!--Thong tin ve bai viet-->
    <?= $this->hidden('hdn_thumb_img',$v_thumb_img); ?>
    <input type="hidden" name="hdn_article_id"      value="<?php echo $v_pk_article; ?>"/>
    <input type="hidden" name="hdn_init_user"    value="<?php echo $v_init_user; ?>"/>
    <input type="hidden" name="hdn_update_user"  value='<?php echo $v_fk_update_user; ?>'/>
    <input type="hidden" name="hdn_update_date"   value="<?php echo $v_today; ?>"/>
    <input type="hidden" name="hdn_update_time"   value="<?php echo $v_now; ?>"/>
    <input type="hidden" name="hdn_init_date"     value="<?php echo $v_init_date; ?>"/>
    <input type="hidden" name="hdn_init_time"     value="<?php echo $v_init_time; ?>"/>
    <!--Thong tin ve filter-->
    <input type="hidden" name="hdn_filter_status" 
           value="<?php echo isset($_POST['hdn_filter_status']) ? $_POST['filter_status'] : 0; ?>"/>
    <input type="hidden" name="hdn_filter_category" 
           value="<?php echo isset($_POST['hdn_filter_category']) ? $_POST['filter_category'] : 0; ?>"/>
    <input type="hidden" name="hdn_filter_article_title" 
           value="<?php echo isset($_POST['hdn_filter_article_title']) ? $_POST['filter_article_title'] : ''; ?>"/>      

    <div class="grid_17">
        <table>
            <tr>
                <td><input type="button" value="Lưu bài viết" onclick="update_article()"/></td>
                <td><input type="button" value="Quay lại" onclick="go_back()"/></td>
            </tr>

            <tr>
                <td>Tiêu đề:</td>
                <td>
                    <input type="text" name="txt_title" size="70" value="<?php echo $v_title; ?>"/> 
                </td>
            </tr>

            <tr>
                <td>Slug:</td>
                <td>
                    <input type="text" name='txt_slug' size="70" value="<?php echo $v_slug; ?>"/> 
                </td>
            </tr>
            <tr>
                <td colspan="2">Tóm tắt:</td>
            </tr>
            <tr>
                <td colspan="2"><textarea name="txt_summary" cols="54" rows="5"><?php echo $v_summary; ?></textarea></td>
            </tr>
            <tr>
                <td colspan="2">Nội dung:</td>
            </tr>
            <tr>
                <td colspan="2"><textarea name="txt_content" rows="30"><?php echo $v_content; ?></textarea>

                </td>
            </tr>
            <tr>
                <td><input type="submit" value="Lưu bài viết"/></td>
                <td><input type="button" value="Quay lại" onclick="go_back()"/></td>
            </tr>

        </table>
    </div> <!-- grid_main -->
    <div class="grid_6">        
        <div class="right_header">
            Thuộc chuyên mục
        </div>
        <div class="item_container" style="display:block">
            <?php
            $n = count($arr_all_category);
            for ($i = 0; $i < $n; $i++):
                ?>
                <?php
                $cat_name = $arr_all_category[$i]['C_NAME'];
                $cat_id = $arr_all_category[$i]['PK_CMS_CATEGORY'];
                $space = '';
                $m = strlen($arr_all_category[$i]['C_INTERNAL_ORDER']) / 3 - 1;
                for ($j = 0; $j < $m; $j++)
                    $space.="&nbsp;&nbsp;&nbsp;&nbsp;";
                ?> 
                <?php echo $space; ?>
                <input type="checkbox" name="chk_category[]" value="<?php echo $cat_id; ?>"
                       <?php if (in_array($cat_id, $arr_category_article)) echo 'checked'; ?> />
                <?php echo $cat_name; ?></br>
                </input>
            <?php endfor; ?>
        </div>
        <div class="right_header" id="thumbnail_drop_down">
            Ảnh đại diện [v]
        </div>
        <div class="item_container" id="thumbnail_container">
            <?php if ($v_thumb_img == ''): ?>
                <div 
                    style="
                        width:200px; 
                        height:150px; 
                        border-style: dashed; 
                        margin:0 auto; 
                        text-align: center; 
                        border-color: #c0c0c0;
                        cursor: pointer;"
                    onClick="choose_thumb('insert');"
                    >
                    <a href="javascript:;">Chọn ảnh đại diện</a>
                </div>
            <?php else: ?>
                <a href="javascript:;" onClick="remove_thumb();">Xoá ảnh</a></br>
                <center>
                    <a href="javascript:;" onClick="choose_thumb();">
                        <img src="<?php echo $v_thumb_img; ?>" style="height: 150px; width: 200px;"/>
                    </a>
                </center>
            <?php endif; ?>
        </div>
        <div class="right_header" id="option_drop_down">
            Tuỳ chọn [v]
        </div>
        <div class="item_container" id="option_container" style="display:block">
            <p>
                <b>Tags:</b></br>
                <input type="text" name="txt_tags" size="35" value="<?php echo $v_tags; ?>"/>
            </p>
            <p>
                <b>Ngày bắt đầu:</b></br>
                <input type="text" name="txt_begin_date" id="txt_begin_date" value="<?php echo $v_begin_date; ?>" size="10"/>
                <img src="<?php echo SITE_ROOT; ?>public/images/calendar.png" onclick="DoCal('txt_begin_date')"/>
                </br>
                <b>Vào lúc:</b></br>
                <input type="text" name="txt_begin_time" value="<?php echo $v_begin_time; ?>" size="10"/>
            </p>
            <p>
                <b>Ngày kết thúc:</b></br>
                <input type="text" name="txt_end_date" id="txt_end_date" value="<?php echo $v_end_date; ?>" size="10"/>
                <img src="<?php echo SITE_ROOT; ?>public/images/calendar.png" onclick="DoCal('txt_end_date')"/>
                </br>
                <b>Vào lúc:</b></br>
                <input type="text" name="txt_end_time" value="<?php echo $v_end_time; ?>" size="10"/>
            </p>
            </br>
        </div>
        <div class="right_header" id='info_drop_down'>
            Thông tin bổ sung [v]
        </div>
        <div class="item_container" id="info_container">
            <p>
                <b>Người đăng:</b></br>
                <?php echo $v_init_user_name; ?>
            </p>
            <p>
                <b>Ngày đăng:</b></br>
                <?php echo $v_init_date; ?>
                &nbsp;
                Vào lúc:&nbsp;
                <?php echo $v_init_time; ?>
            </p>
            <p>
                <b>Người cập nhật cuối:</b></br>
                <?php echo $v_update_user_name; ?>
            </p>
            <p>
                <b>Ngày cập nhật cuối:</b></br>
                <?php echo $v_update_date; ?> &nbsp;
                Vào lúc:&nbsp;
                <?php echo $v_update_time; ?>
            </p>
            <p>
                <b>Lượt xem:</b>
                <?php echo $v_view; ?>
            </p>
        </div>

    </div> <!-- div ben fai-->

</form>
