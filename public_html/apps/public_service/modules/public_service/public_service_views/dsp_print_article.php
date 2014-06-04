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
if (!defined('SERVER_ROOT'))
{
    exit('No direct script access allowed');
}
    
    $title                  = __('print');
    $v_article_sub_title    = isset($arr_single_article['C_SUB_TITLE'])?$arr_single_article['C_SUB_TITLE']:'';
    $v_article_title        = isset($arr_single_article['C_TITLE'])?$arr_single_article['C_TITLE']:'';
    $v_begin_date           = isset($arr_single_article['C_BEGIN_DATE'])?$arr_single_article['C_BEGIN_DATE']:'';
    
    $v_article_sumary       = isset($arr_single_article['C_SUMMARY'])?$arr_single_article['C_SUMMARY']:'';
    $v_article_sumary       = htmlspecialchars_decode($v_article_sumary);
    
    $v_article_cotent       = isset($arr_single_article['C_CONTENT'])?$arr_single_article['C_CONTENT']:'';
    $v_article_cotent       = htmlspecialchars_decode($v_article_cotent);
            
    $v_pen_name             = isset($arr_single_article['C_PEN_NAME'])?$arr_single_article['C_PEN_NAME']:'';
    
    $pattern           = "/\[VIDEO\](.*)\[\/VIDEO\]/i";
    $v_article_cotent = preg_replace($pattern, '', $v_article_cotent,-1);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta http-equiv="Cache-Control" content="no-cache"/>
        <link rel="SHORTCUT ICON" href="favicon.ico">
        <title><?php echo $v_article_title;?></title>
        
        <!-- theme resource -->
        <link href="<?php echo SITE_ROOT; ?>public/themes/bootstrap/css/bootstrap.css" rel="stylesheet">
        <link href="<?php echo SITE_ROOT; ?>public/themes/bootstrap/css/bootstrap-responsive.css" rel="stylesheet">
        <link rel="stylesheet" href="<?php echo SITE_ROOT; ?>public/themes/bootstrap/css/font-awesome.css">
        <!--[if IE 7]>
            <link rel="stylesheet" href="<?php echo SITE_ROOT; ?>public/themes/bootstrap/css/font-awesome-ie7.min.css">
        <![endif]-->
        <link href="<?php echo SITE_ROOT; ?>public/themes/bootstrap/css/styles.css" rel="stylesheet">
        <!--<link href="<?php echo SITE_ROOT; ?>apps/r3/style.css" rel="stylesheet">-->
        <link id="themes" href="#" rel="stylesheet">
        <!--[if IE 7]>
            <link rel="stylesheet" type="text/css" href="<?php echo SITE_ROOT; ?>public/themes/bootstrap/css/ie/ie7.css" />
        <![endif]-->
        <!--[if IE 8]>
            <link rel="stylesheet" type="text/css" href="<?php echo SITE_ROOT; ?>public/themes/bootstrap/css/ie/ie8.css" />
        <![endif]-->
        <!--[if IE 9]>
            <link rel="stylesheet" type="text/css" href="<?php echo SITE_ROOT; ?>public/themes/bootstrap/css/ie/ie9.css" />
        <![endif]-->
        <link href="<?php echo SITE_ROOT; ?>public/themes/bootstrap/css/dosis.css" rel="stylesheet" type="text/css">
        <!--fav and touch icons -->
        <link rel="shortcut icon" href="ico/favicon.ico">
        <link rel="apple-touch-icon-precomposed" sizes="144x144" href="<?php echo SITE_ROOT; ?>public/themes/bootstrap/ico/apple-touch-icon-144-precomposed.png">
        <link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?php echo SITE_ROOT; ?>public/themes/bootstrap/ico/apple-touch-icon-114-precomposed.png">
        <link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?php echo SITE_ROOT; ?>public/themes/bootstrap/ico/apple-touch-icon-72-precomposed.png">
        <link rel="apple-touch-icon-precomposed" href="<?php echo SITE_ROOT; ?>public/themes/bootstrap/ico/apple-touch-icon-57-precomposed.png">
        <!--============j avascript===========-->
        <script src="<?php echo SITE_ROOT; ?>public/themes/bootstrap/js/jquery.js"></script>
        <script src="<?php echo SITE_ROOT; ?>public/themes/bootstrap/js/jquery-ui-1.8.16.custom.min.js"></script>
        <!--<script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery-ui.min.js" type="text/javascript"></script>-->
        <script src="<?php echo SITE_ROOT; ?>public/themes/bootstrap/js/bootstrap.js"></script>
        <script src="<?php echo SITE_ROOT; ?>public/themes/bootstrap/js/accordion.nav.js"></script>
        <script src="<?php echo SITE_ROOT; ?>public/themes/bootstrap/js/custom.js"></script>
        <script src="<?php echo SITE_ROOT; ?>public/themes/bootstrap/js/respond.min.js"></script>
        <script src="<?php echo SITE_ROOT; ?>public/themes/bootstrap/js/ios-orientationchange-fix.js"></script>
        <script src="<?php echo SITE_ROOT; ?>public/themes/bootstrap/js/bootbox.js"></script>
        
        <!--============My resource===========-->
        <link href="<?php echo SITE_ROOT; ?>public/js/jquery/jquery-ui.css" rel="stylesheet" type="text/css"/>
        <!--  Datepicker -->
        <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery.ui.datepicker-vi.js" type="text/javascript"></script>
        <!-- Right-click context menu -->
        <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery.contextMenu.js" type="text/javascript"></script>
        <!-- Upload -->
        <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery.MultiFile.pack.js" type="text/javascript"></script>
        <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery.blockUI.js" type="text/javascript"></script>
        <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery.MetaData.js" type="text/javascript"></script>
        <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery.slimscroll.min.js"></script>
        
        <script type="text/javascript">
            var SITE_ROOT='<?php echo SITE_ROOT; ?>';
            var _CONST_LIST_DELIM = '<?php echo _CONST_LIST_DELIM; ?>';
             <?php $QS = check_htacces_file() ? '?' : '&'; ?>
            var QS = '<?php echo $QS; ?>';
        </script>
        <!--  Modal dialog -->
        <script src="<?php echo SITE_ROOT; ?>public/js/submodal.js" type="text/javascript"></script>
        <link href="<?php echo SITE_ROOT; ?>public/css/subModal.css" rel="stylesheet" type="text/css"/>
        <!-- Tooltip -->
        <script src="<?php echo SITE_ROOT; ?>public/js/overlib_mini.js" type="text/javascript"></script>
        <script src="<?php echo SITE_ROOT; ?>public/js/mylibs.js" type="text/javascript"></script>
        <script src="<?php echo SITE_ROOT; ?>public/js/DynamicFormHelper.js" type="text/javascript"></script>
        <?php if (isset($this->local_js)): ?>
            <script src="<?php echo $this->local_js; ?>" type="text/javascript"></script>
        <?php endif; ?>
        <link rel="stylesheet" href="<?php echo $this->stylesheet_url; ?>" type="text/css" media="screen" />
        <script src="<?php echo FULL_SITE_ROOT . 'public/js/public_service/functions.js'?>"></script>   
        
        <style>
            @media screen {
                #div_content_print {
                    width: 600px;
                    margin: 0 auto;
                }
            }
            @media print
            {
                .img_print {
                    display: none;
                }
                
                #div_content_print {
                    width: 100%;
                }
            }
            
        </style>
    </head>
<body>
<div id="div_content_print">
    <div id="banner" class="banner">
        <img src="<?php echo FULL_SITE_ROOT . 'public/images/layout/public_service/banner.jpg' ?>" height="50px" width="100%" />
    </div>
    <div  style="border-bottom: 1px solid #000000;margin-top: 10px;width: 100%;" ></div>
    <div class="div_article">
        <div style="overflow: hidden;margin-top: 10px;padding: 5px 5px 5px 0px;width: 100%;" class="img_print">
            <a href="javascript:window.print()" style="float: left">
                <img src="<?php echo CONST_SITE_THEME_ROOT."images/icon_print.png";?>">
                <?php echo __('In trang') ?>
            </a>
        </div>
        <div class="clear"></div>
        <div style="text-align: center" class="div_sub_title">
            <?php echo $v_article_sub_title;?>
        </div>
        <div class="div_article_title">
            <h2><?php echo $v_article_title;?></h2>
        </div>
        <div class="div_article_begin_date">
            (<?php echo $v_begin_date;?>)
        </div>
        <div class="div_article_summary">
            <?php echo $v_article_sumary;?>
        </div>
         <div class="div_article_content">
            <?php echo $v_article_cotent;?>
        </div>
        <div class="clear" style="height: 8px;"></div>
        <div class="div_pen_name" style="float:right">
            <?php echo $v_pen_name;?>
        </div>
    </div>
    <div style="margin-bottom: 20px;border-top: 1px solid #CCCCCC;margin-top: 10px;padding: 5px 5px 5px 0px;width: 100%;" class="img_print">
         
         <a href="javascript:window.print()" style="float: right">
               <img src="<?php echo CONST_SITE_THEME_ROOT."images/icon_print.png";?>">
               <?php echo __('In trang')?>
         </a>
    </div>
</div>
</body>
</html>