<?php
if (!defined('SERVER_ROOT')) {
    exit('No direct script access allowed');
}
?>
<?php
//display header
$this->template->title = 'Bản tin nội bộ';
$this->template->display('dsp_header.php');

$arr_single_article     = $VIEW_DATA['arr_single_article'];
?>
<div id="article_detail"><?php
if (isset($arr_single_article['PK_ARTICLE']))
{
    $v_article_id           = $arr_single_article['PK_ARTICLE'];
    $v_title                = $arr_single_article['C_TITLE'];
    $v_short_content        = $arr_single_article['C_SHORT_CONTENT'];
    $v_content              = $arr_single_article['C_CONTENT'];
    $v_attach_file_name      = $arr_single_article['C_ATTACH_FILE_NAME'];
    $v_xml_data             = $arr_single_article['C_XML_DATA'];
    $v_begin_date           = $arr_single_article['C_BEGIN_DATE'];
    $v_status				=$arr_single_article['C_STATUS'];


    $v_create_by           = $arr_single_article['C_CREATE_BY'];

    $v_begin_date = jwDate::yyyymmdd_to_ddmmyyyy($v_begin_date);

    $this->template->title = $v_title;
	if($v_status)
	{
	    echo '<h1>' . $v_title . '</h1>';
	
	    echo $v_short_content;
	}
	else 
	{
		echo 'Tin bài này không đuợc phép hiển thị <br/>';
	}

    $v_permalink = $this->get_controller_url() . 'dsp_single_article/' . $v_article_id . '-' . $v_title;
    ?>
    <?php if ($v_create_by == session::get('login_name') Or $this->check_permission('SUA_TIN_BAI_DO_NGUOI_KHAC_DANG')):?>
        <a href="<?php echo $v_permalink;?>/&action=edit">[Sửa]</a>
    <?php endif;
    echo '<a href="' . $this->get_controller_url(). 'dsp_all_article">[Quay lại]</a>';
}
?>
</div>
<?php $this->template->display('dsp_footer.php');