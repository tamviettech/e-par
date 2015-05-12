<?php if (!defined('SERVER_ROOT')) {exit('No direct script access allowed');}
//header
$this->template->title = 'Bản tin nội bộ';
$this->template->display('dsp_header.php');

$arr_all_article    = $VIEW_DATA['arr_all_article'];
$v_filter           = $VIEW_DATA['txt_filter'];

?>
<form name="frmMain" id="frmMain" action="#" method="POST">
    <?php
    echo $this->hidden('controller',$this->get_controller_url());
    echo $this->hidden('hdn_item_id','');
    echo $this->hidden('hdn_item_id_list','');

    echo $this->hidden('hdn_dsp_single_method','dsp_single_article');
    echo $this->hidden('hdn_dsp_all_method', 'dsp_all_article');
    echo $this->hidden('hdn_update_method','update_article');
    echo $this->hidden('hdn_delete_method','delete_article');
    ?>

    <!-- filter -->
    <div id="div_filter">
        <input type="text" name="txt_filter"
            value="<?php echo $v_filter;?>"
            class="inputbox" size="30" autofocus="autofocus"
            onkeypress="txt_filter_onkeypress(this.form.btn_filter,event);"
        />
        <input type="button" class="filter_button" onclick="btn_filter_onclick();"
                name="btn_filter" value="<?php echo _LANG_FILTER_BUTTON;?>"
        />
    </div>
    <div id="artile_list"   >
        <ul>
            <?php for ($i=0; $i < sizeof($arr_all_article); $i++):
                $v_article_id = $arr_all_article[$i]['PK_ARTICLE'];
                $v_title = $arr_all_article[$i]['C_TITLE'];
                $v_attach_file_name = $arr_all_article[$i]['C_ATTACH_FILE_NAME'];
                $v_short_content = $arr_all_article[$i]['C_SHORT_CONTENT'];
                $v_slug = $arr_all_article[$i]['C_SLUG'];

                $v_begin_date = jwDate::yyyymmdd_to_ddmmyyyy($arr_all_article[$i]['C_BEGIN_DATE']);

                $v_create_by = $arr_all_article[$i]['C_CREATE_BY'];



                $v_class = ($i==0) ? ' class="first_artilce"' : '';

                $v_permalink = $this->get_controller_url() . 'dsp_single_article/' . $v_article_id . '-' . $v_title;
                $v_link_to_attach = $arr_all_article[$i]['C_LINK_TO_ATTACH'];

                ?>
                <li>[<?php echo $v_begin_date;?>]
                    <?php if ($v_link_to_attach > 0): ?>
                        <img src="<?php echo SITE_ROOT . 'public/images/' . array_pop(explode('.', $v_attach_file_name)) . '-icon.png';?>" width="16px" height="16px" />
                        <a href="<?php echo SITE_ROOT . 'uploads/news/' . $v_attach_file_name;?>" target="_blank"><?php echo $v_title;?></a>
                    <?php else: ?>
                        <a href="<?php echo $v_permalink;?>"><?php echo $v_title;?></a>
                    <?php endif;?>
                    <?php if ($v_create_by == session::get('login_name') Or $this->check_permission('XOA_TIN_BAI_DO_NGUOI_KHAC_DANG')):?>
                        <a href="javascript:void(0)" onclick="quick_delete_item(<?php echo $v_article_id;?>)">[Xoá tin]</a>
                    <?php endif; ?>
                    <?php if ($v_create_by == session::get('login_name') Or $this->check_permission('SUA_TIN_BAI_DO_NGUOI_KHAC_DANG')):?>
                        <a href="<?php echo $v_permalink;?>/&action=edit">[Sửa]</a>
                    <?php endif; ?>
                </li>
            <?php endfor; ?>
        </ul>
    </div>

    <?php if ($this->check_permission('THEM_MOI_TIN_BAI')): ?>
        <div class="button-area">
            <input type="button" name="addnew" class="button" value="<?php echo _LANG_ADDNEW_BUTTON;?>" onclick="btn_addnew_article_onclick();"/>
        </div>
    <?php endif; ?>
</form>
<script>
    function btn_addnew_article_onclick()
    {
        v_url = '<?php echo $this->get_controller_url();?>dsp_single_article/0/&action=edit';
        window.location=v_url;
    }
</script>
<?php $this->template->display('dsp_footer.php');