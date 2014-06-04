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
?>
<!DOCTYPE html>
<html>
    <head>
         <meta charset="utf-8">
        <meta http-equiv="Cache-Control" content="no-cache"/>
        <link rel="shortcut icon" href="<?php echo SITE_ROOT; ?>favicon.ico" />
        <title>Dịch vụ công::<?php echo isset($this->title) ? $this->title : ''; ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <!-- theme resource -->
        <link href="<?php echo SITE_ROOT; ?>public/themes/bootstrap/css/bootstrap.css" rel="stylesheet">
        <link rel="stylesheet" href="<?php echo SITE_ROOT; ?>public/themes/bootstrap/css/font-awesome.css">
        <!--[if IE 7]>
            <link rel="stylesheet" href="<?php echo SITE_ROOT; ?>public/themes/bootstrap/css/font-awesome-ie7.min.css">
        <![endif]-->
        <link href="<?php echo SITE_ROOT; ?>public/themes/bootstrap/css/styles.css" rel="stylesheet">
        
        <!--============j avascript===========-->
        <script src="<?php echo SITE_ROOT; ?>public/themes/bootstrap/js/jquery.js"></script>
        <script src="<?php echo SITE_ROOT; ?>public/themes/bootstrap/js/jquery-ui-1.8.16.custom.min.js"></script>
        
        <!--<script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery-ui.min.js" type="text/javascript"></script>-->
        <script src="<?php echo SITE_ROOT; ?>public/themes/bootstrap/js/bootstrap.js"></script>
<!--        <script src="<?php echo SITE_ROOT; ?>public/themes/bootstrap/js/accordion.nav.js"></script>
        <script src="<?php echo SITE_ROOT; ?>public/themes/bootstrap/js/custom.js"></script>
        <script src="<?php echo SITE_ROOT; ?>public/themes/bootstrap/js/respond.min.js"></script>
        <script src="<?php echo SITE_ROOT; ?>public/themes/bootstrap/js/ios-orientationchange-fix.js"></script>
        <script src="<?php echo SITE_ROOT; ?>public/themes/bootstrap/js/bootbox.js"></script>-->

        <!--============My resource===========-->
        <link href="<?php echo SITE_ROOT; ?>public/js/jquery/jquery-ui.css" rel="stylesheet" type="text/css"/>
        <link href="<?php echo SITE_ROOT; ?>public/js/jquery/jquery-ui-1.8.18.css" rel="stylesheet" type="text/css"/>
        
        <!--  Datepicker -->
        <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery.ui.datepicker-vi.js" type="text/javascript"></script>
        <!-- Upload -->
        <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery.MultiFile.pack.js" type="text/javascript"></script>
        <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery.blockUI.js" type="text/javascript"></script>
        <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery.MetaData.js" type="text/javascript"></script>
        
        
        <!--combobox-->
        <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquerycombobox/js/jquery.combobox.js" type="text/javascript"></script>
        <link href="<?php echo SITE_ROOT; ?>public/js/jquery/jquerycombobox/css/style.css" rel="stylesheet" type="text/css">
        
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
        
        <!--Grild-css-->
        <link rel="stylesheet" href="<?php echo SITE_ROOT; ?>public/css/reset.css" type="text/css" media="screen" />
        <link rel="stylesheet" href="<?php echo SITE_ROOT; ?>public/css/text.css" type="text/css" media="screen" />
        <link rel="stylesheet" href="<?php echo SITE_ROOT; ?>public/css/1008_24_0_0.css" type="text/css" media="screen" />  
        <link rel="stylesheet" href="<?php echo SITE_ROOT; ?>apps/layout/public_service/admin/report.css" type="text/css" media="screen" />
        <link rel="stylesheet" href="<?php echo SITE_ROOT; ?>apps/layout/public_service/admin/style.css" type="text/css" media="screen" />        
        <link rel="stylesheet" href="<?php echo SITE_ROOT; ?>apps/layout/public_service/admin/style_mobile.css" type="text/css" media="screen" />
        <!--[if IE]>
            <link rel="stylesheet" href="<?php echo SITE_ROOT; ?>apps/layout/public_service/admin/style_ie.css" type="text/css" media="screen" />
        <![endif]-->
        <script src="<?php echo FULL_SITE_ROOT . 'public/js/public_service/functions.js'?>"></script>
    </head>
    <body>
        <?php
            @Session::init();
            $arr_url = explode('/', $_GET['url']);
            $v_menu_active = $arr_url[1];
            if ($v_menu_active == 'xlist')
            {
                $v_menu_active = $arr_url[2];
                $arr_menu_active = explode('_', $v_menu_active);
                $v_last = count($arr_menu_active) - 1;
                $v_menu_active = $arr_menu_active[$v_last];
            }
       
        $arr_count_article     = $this->arr_count_article;
        echo view::hidden('hdn_menu_select', $v_menu_active);
        $v_user_login_name = session::get('user_name');
        ?>
        <DIV id=overDiv style="Z-INDEX: 10000; VISIBILITY: hidden; POSITION: absolute"></DIV>
        <div class="container_24">
            <div id="header_admin">
                <div>
                    <div class="Banner">
                        <table style="" cellpadding="0" cellspacing="0" class="TableBannerMenu none-border-table" align="right" border="0">
                            <tr>
                                <td class="BannerMenu">
                                    
                                    <a  href="javascript:void(0)" onclick="change_password_onclick();" style="cursor:pointer;">
                                        <span id="ctl00_lblTopMenuAccount" class="BannerMenu"><i class="icon-lock"></i> &nbsp;Đổi mật khẩu</span></a>
                                </td>
                                <td class="BannerMenuLast">
                                    <img src="<?php echo SITE_ROOT . "apps/layout/public_service/admin/images/icon_bannermenu_exit.gif"; ?>" alt="" style="vertical-align: middle">
                                    <a href="<?php echo SITE_ROOT . "logout.php?b=". $this->app_name .'/'. $this->module_name ?>">
                                        <span id="ctl00_lblTopMenuExit" class="BannerMenu">Đăng xuất(<span class="LoginName"><?php echo $v_user_login_name; ?></span>)</span></a>
                                </td>
                            </tr>
                            <tr valign="top">
                                <td class="CounterStatistic" colspan="3">
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="Aticle_info">
                    <label>Thông tin:  - Tổng số bài viết: (<?php echo $arr_count_article['count_editor']; ?>)</label>
                </div>
                <div id="header_admin_bot">
                    
                    <div class="TopMenuAdmin">
                        <div style="float: left;width: 100%;">
                            <div style="width: 10px;height: 40px;float: left;"></div>
                            <div class="TopMenuActive" name="div_menu_content" id="div_menu_content" >
                                <a href="<?php echo FULL_SITE_ROOT.'public_service/dashboard';?>">
                                    <span>Quản trị nội dung</span>
                                </a>
                            </div>
                        </div>
                        <div class="clear"></div>
                        <div style="border-top:solid 1px #438cb4;"></div>
                    </div>
                </div>

            </div> <!--header admin-->
            <div class="clear"></div>
            <div class="grid_24" style="background-color: #EBEBEB;">
                <?php if ($this->dsp_side_bar == true): ?>
                    <div class="grid_4" style="min-height: 1px;">
                        <?php include 'dsp_admin_menu.php'; ?>
                    </div>
                    <div class="grid_20" style="background-color: white;">
                        <div style="padding-left: 4px;">

                        <?php else: ?>
                            <div class="grid_24" style="background-color: white">
                                <div>
                                <?php endif; ?>

                                <?php

                                function get_admin_controller_url($module)
                                {
                                    if (file_exists(SERVER_ROOT . '.htaccess'))
                                    {
                                        return SITE_ROOT . 'public_service/' . $module . '/';
                                    }
                                    return SITE_ROOT . 'index.php?url=public_service/' . $module . '/';
                                }
                                ?>
<script>
    var SITE_ROOT = "<?php echo SITE_ROOT; ?>";
    $(document).ready(function(){
        var div_select = '#'+$('#hdn_menu_select').val();
        //alert($('#hdn_menu_select').attr('value'));
        $(div_select).attr('class','TopMenuActive');
    });
    function change_password_onclick()
    {
        showPopWin('/lang-giang/cores/user/dsp_change_password' , 500,400, null);
    }
    
</script>

   <div id="main-apps" class="row-fluid ">
                <?php echo $content ?>
    </div>



<?php
$arr_url       = explode('/', $_GET['url']);
$v_menu_active = isset($arr_url[1]) ? $arr_url[1] : '';
?>
<?php if ($v_menu_active != ''): ?>
    <?php if ($this->dsp_side_bar): ?>
        </div>
        </div>
    <?php else: ?>
        </div>
    <?php endif; ?>
<?php endif; ?>
</div>
<!-- end .grid_16 -->

<div class="clear">&nbsp;</div>

  <div class="copyright">&COPY;Cổng thông tin điện tử</div>
</div>
<!-- class="container_24" -->
</body>
</html>
