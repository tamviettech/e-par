
<?php 

       $this->title                        = isset($arr_single_article['C_TITLE']) ? $arr_single_article['C_TITLE'] : '';
       $this->keywords                     = isset($arr_single_article['C_KEYWORDS']) ? $arr_single_article['C_KEYWORDS'] : '';
       $this->summary                      = isset($arr_single_article['C_SUMMARY']) ? remove_html_tag($arr_single_article['C_SUMMARY']) : '';

       //du lieu content
       $v_article_sub_title                = isset($arr_single_article['C_SUB_TITLE']) ? $arr_single_article['C_SUB_TITLE'] : '';
       $v_article_title                    = isset($arr_single_article['C_TITLE']) ? $arr_single_article['C_TITLE'] : '';
       $v_begin_date                       = isset($arr_single_article['C_BEGIN_DATE']) ? $arr_single_article['C_BEGIN_DATE'] : '';

       $v_article_sumary                   = isset($arr_single_article['C_SUMMARY']) ? $arr_single_article['C_SUMMARY'] : '';
       $v_article_sumary                   = htmlspecialchars_decode($v_article_sumary);

       $v_article_content                  = isset($arr_single_article['C_CONTENT']) ? $arr_single_article['C_CONTENT'] : '';
       $v_article_content                  = htmlspecialchars_decode($v_article_content);

       $v_xml_attach                       = isset($arr_single_article['C_XML_ATTACH']) ? $arr_single_article['C_XML_ATTACH'] : '';
       $v_xml_other_news                   = isset($arr_single_article['C_XML_OTHER_NEWS']) ? $arr_single_article['C_XML_OTHER_NEWS'] : '';
       $v_category_slug                    = isset($arr_single_article['C_SLUG_CAT']) ? $arr_single_article['C_SLUG_CAT'] : '';
       $v_pen_name                         = isset($arr_single_article['C_PEN_NAME']) ? $arr_single_article['C_PEN_NAME'] : '';
       $article_slug                       = isset($arr_single_article['C_SLUG_ARTICLE']) ? $arr_single_article['C_SLUG_ARTICLE'] : '';
       $v_media_file_name                  = isset($arr_single_article['C_FILE_NAME']) ? $arr_single_article['C_FILE_NAME'] : '';
       $v_article_tags                     = isset($arr_single_article['C_TAGS']) ? $arr_single_article['C_TAGS'] : '';
       //vote
       $rating_result                      = isset($arr_single_article['C_CACHED_RATING']) ? $arr_single_article['C_CACHED_RATING'] : 0;
       $rating_count                       = isset($arr_single_article['C_CACHED_RATING_COUNT']) ? $arr_single_article['C_CACHED_RATING_COUNT'] : 0;
       $v_attach_file_name                 = isset($arr_single_article['C_FILE_NAME']) ? $arr_single_article['C_FILE_NAME'] : 0;
       $website_id        = get_request_var('website_id', 0);
       $category_id       = get_request_var('category_id', 0);
       $article_id        = get_request_var('article_id', 0);

   ?>

<div id="single-article" class="main-navigation span12">
    <div class="span8" id="box-detail-arcticle">
        <div class="title">
            <h3><?php echo $v_article_title; ?></h3>
             <span class="begin-date">
                 <img src="<?php echo CONST_SITE_THEME_ROOT.'images/DateIcon.gif'?>"   style="border:0px;">
                &nbsp;
                <?php echo $v_begin_date; ?>
            </span>
        </div>
        
        <!--end title-->
        <div class="div_article_summary" align="justify">
            <?php echo $v_article_sumary; ?>
        </div>
        <div class="div_article_content" align="justify"> 
            <?php error_reporting(E_ALL) ?>
            <?php $pattern = "/\[VIDEO\](.*)\[\/VIDEO\]/i"; ?>
            <?php echo preg_replace_callback($pattern, 'replace_video', $v_article_content, -1, $count) ?>
        </div>
        <?php if (trim($v_attach_file_name) != ''): ?>
            <div class="div_file_attach">
                <label style="float: left"> Tập tin đính kèm: &nbsp;</label>
                 <?php
                    $arr_attach_file_name  = explode(',', $v_attach_file_name);
                    $dir_root              = CONST_SERVER_UPLOADS_ROOT;
                    foreach ($arr_attach_file_name as $file)
                    {
                        $file_path   = $dir_root.$file;
                        if(is_file($file_path))
                        {
                            $v_file_name = basename($file_path);
                            $type_file   = end(explode('.', $v_file_name));
                            //lay anh icon hien thi file dinh kem
                            $arr_icon_attacment = json_decode(CONTS_ICON_FILE_GUIDANCE,TRUE);                            
                            
                            $v_url_icon_image_attach = SITE_ROOT.'public/images/attach.gif';
                            if(key_exists($type_file, $arr_icon_attacment))
                            {
                                $v_url_icon_image_attach = CONST_SITE_THEME_ROOT.$arr_icon_attacment[$type_file];
                            }               
                           $v_url = $this->get_controller_url().'attach_dowload&file='.$file;
                           echo  "<a target='_blank' style='width:15px;overflow:hidden;display:block;float:left;margin-right:8px' href= '$v_url' title='$v_file_name'> 
                                        <img width='100%' src='$v_url_icon_image_attach'/>
                                  </a>";
                        }
                    }
                ?>
                    
            </div>
        <?php endif; ?>
        <div class="clear" style="height: 8px;"></div>
        
        <div class="div_pen_name" style="float:left">
            <?php echo $v_pen_name; ?>
        </div>
        
        <!--tags-->
        <!--Chưa sử dụng-->
        <?php if ($v_article_tags != ''): ?>
<!--            <div id="div_tags">
                <h2 class="h2Acticle"></span></h2>
                <?php
                $arr_tags = explode(',', $v_article_tags);
                ?>
                <?php echo __('tag: '); ?>
                <?php foreach ($arr_tags as $row_tags): ?>
                    <a href="<?php echo build_url_tags($row_tags) ?>"><?php echo $row_tags; ?></a>
                <?php endforeach; ?>
            </div>-->
        <?php endif; ?>
        <!--End tags-->

        <!--button single article-->
        <div class="div-buton-single-article">
             
            <a href="javascript:void(0)" onclick="print_onclick();">
                    <img src="<?php echo CONST_SITE_THEME_ROOT . "images/Print.gif"; ?>">
                    <?php echo __('print') ?>
                </a>
        </div>
        <hr/>
        <?php if($arr_all_connection_article): ?>
        <div id="connection">
            <h4 class="title" >Các tin liên quan</h4>
            <ul>
            <?php
            for ($i = 0; $i <count($arr_all_connection_article); $i ++):
                $v_art_title = $arr_all_connection_article[$i]['C_TITLE'];
                $v_art_id    = $arr_all_connection_article[$i]['PK_ARTICLE'];
                $v_art_slug  = $arr_all_connection_article[$i]['C_SLUG_ARTICLE'];
                $v_cat_slug  = $arr_all_connection_article[$i]['C_SLUG_CAT'];
                $v_cat_id    = $arr_all_connection_article[$i]['C_CAT_ID'];
                $v_url       = build_url_article($v_cat_slug, $v_art_slug, $v_cat_id, $v_art_id);
            ?>
            <li><a href="<?php echo $v_url; ?>"><?php echo $v_art_title; ?></a></li>
            
            <?php endfor; ?>
            <?php if(isset($arr_all_article_khac) && sizeof($arr_all_article_khac) >0):?>
            <li style="background: none; font-size: 1.1em;font-weight: bold; margin-left: -10px; margin-top: 10px">Các tin khác</li>
            <?php
            for ($i = 0; $i <count($arr_all_article_khac); $i ++):
                $v_art_title = $arr_all_article_khac[$i]['C_TITLE'];
                $v_art_id    = $arr_all_article_khac[$i]['PK_ARTICLE'];
                $v_art_slug  = $arr_all_article_khac[$i]['C_SLUG_ARTICLE'];
                $v_cat_slug  = $arr_all_article_khac[$i]['C_SLUG_CAT'];
                $v_cat_id    = $arr_all_article_khac[$i]['C_CAT_ID'];
                $v_url       = build_url_article($v_cat_slug, $v_art_slug, $v_cat_id, $v_art_id);
            ?>
            <li><a href="<?php echo $v_url; ?>"><?php echo $v_art_title; ?></a></li>
            <?php endfor; ?>
            <?php endif ;?>
            </ul>
        </div>
    <?php endif; ?>
    </div>
    
    
    <!--End single article-->
    <?php include_once 'dsp_right_sidebar.php';?>
   
</div><!--End #single-article-->


<script>
    function print_onclick()
    {
        str="<?php echo build_url_print($v_category_slug, $article_slug, $category_id, $article_id) ?>";
        window.open(str,"",'scrollbars=1,width=700,height=600');
    }
</script>