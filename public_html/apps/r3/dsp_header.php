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
<?php if (!defined('SERVER_ROOT')) {exit('No direct script access allowed');}?>

<?php if (! isset($this->show_left_side_bar)): ?>
    <?php $this->show_left_side_bar = FALSE;?>
<?php endif; ?>
<!DOCTYPE HTML>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="Cache-Control" content="no-cache"/>
        <link rel="shortcut icon" href="<?php echo SITE_ROOT;?>favicon.ico" />
        <title><?php echo session::get('user_name');?>::<?php echo $this->title; ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        
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
        <script type="text/javascript">
            var SITE_ROOT='<?php echo SITE_ROOT; ?>';
            var _CONST_LIST_DELIM = '<?php echo _CONST_LIST_DELIM; ?>';
            <?php $QS = check_htacces_file() ? '?' : '&';?>
            var QS = '<?php echo $QS;?>';
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
        <link rel="stylesheet" href="<?php echo $this->stylesheet_url;?>" type="text/css" media="screen" />
        
        <!--socket io client-->
        <script src="<?php echo SITE_ROOT; ?>public/js/socketio/socket.io.js" type="text/javascript"></script>
        <script src="<?php echo SITE_ROOT; ?>public/js/socketio/m.s.io-c.compiled.js" type="text/javascript"></script>
        
         <!--connect to chat-->
        <?php
            $v_user_id   = session::get('user_id');
            $v_user_name = session::get('user_name');
            $v_user_ou   = CONST_MY_OU_NAME;
        ?>
        <input type="hidden" name="hdn_global_user_id" id="hdn_global_user_id" value="<?php echo $v_user_id?>"/>
        <input type="hidden" name="hdn_global_user_name" id="hdn_global_user_name" value="<?php echo $v_user_name?>"/>
        <input type="hidden" name="hdn_global_user_ou" id="hdn_global_user_ou" value="<?php echo $v_user_ou?>"/>
       
    </head>
    <body>
        <div class="layout">
            <DIV id=overDiv style="Z-INDEX: 10000; VISIBILITY: hidden; POSITION: absolute"></DIV>
            <div style="width: 100%;">
                <div style="background: #da3610;width: 220px;float:left;height: 50px;">
                    <center>
                        <img src="<?php echo SITE_ROOT.'public/logoQuocHuy.png'?>" style="width: 45px;height: 45px;"/>
                    </center>
                </div>
                <div class="main-wrapper" style="padding: 0;">
                    <?php include (SERVER_ROOT . 'dsp_top_primary_nav.php');?>
                </div>
            </div>
            <div class="clear"></div>
            <?php if ($this->show_left_side_bar == TRUE): ?>
                <div class="leftbar leftbar-close clearfix">
                    <div class="left-nav clearfix">
                        <?php include(dirname(__FILE__) . DS . 'dsp_left_primary_nav.php')?>
                    </div>
                </div><!-- .leftbar leftbar-close -->
                 <div class="main-wrapper">
            <?php else: ?>
                <div class="main-wrapper" style="margin-left: 0px !important;">
            <?php endif;?>
                    
                    <?php
                        //array role k hien thi tại menu
                        $arr_show_menu = array(strtolower(_CONST_TRA_CUU_ROLE),
                                                strtolower(_CONST_TRA_CUU_TAI_XA_ROLE),
                                                strtolower(_CONST_TRA_CUU_LIEN_THONG_ROLE),
                                                strtolower(_CONST_BAO_CAO_ROLE));
                        if(in_array(strtolower($this->active_role), $arr_show_menu)):
                    ?>
                    <!--menu--> 
                    <div class="row-fluid">
                        <div class="span12">
                            <div class="switch-board gray">
                                <ul class="clearfix switch-item">
                                    <?php if (isset($this->activity_filter)): ?>
                                    <?php 
                                    $arr_style = array(
                                                        0 =>array('icon'=>'icon-user','color'=>'brown'),
                                                        1 =>array('icon'=>'icon-cogs','color'=>'blue'),
                                                        2 =>array('icon'=>'icon-lightbulb','color'=>'green'),
                                                        10=>array('icon'=>'icon-bar-chart','color'=>'green'),
                                                        3 =>array('icon'=>'icon-shopping-cart','color'=>'brown'),
                                                        4 =>array('icon'=>'icon-time','color'=>'blue'),
                                                        5 =>array('icon'=>'icon-file-alt','color'=>'blue'),
                                                        6 =>array('icon'=>'icon-copy','color'=>'green'),
                                                        7 =>array('icon'=>'icon-file-alt','color'=>'brown'),
                                                        8 =>array('icon'=>'icon-shopping-cart','color'=>'brown'),
                                                        9 =>array('icon'=>'icon-bar-chart','color'=>'magenta'),
                                                        11 =>array('icon'=>'icon-lightbulb','color'=>'orange'),
                                                    ); 
                                ?>
                                <?php foreach($this->activity_filter as $key => $val):
                                        $url          = $this->controller_url . 'ho_so/' . $this->active_role . '/' . $QS . 'tt=' . $key;
                                        $v_icon = $arr_style[$key]['icon'];
                                        $v_color = $arr_style[$key]['color'];
                                ?>
                                <li>
                                    <span class="count notify-tip activity-num" data-activity="<?php echo $key; ?>">0</span>
                                    <a href="<?php echo $url?>" class="<?php echo $v_color?>">
                                        <i class="<?php echo $v_icon;?>"></i>
                                        <span><?php echo $val ?></span>
                                    </a>
                                    <?php
                                        if(isset($_GET['tt']) && $_GET['tt'] == $key)
                                        {
                                            $html = '<div class="active-menu">';
                                            $html .= '&nbsp;</div>';
                                            echo $html;
                                        }   
                                    ?>
                                </li>
                                <?php endforeach;?>
                                <script>
                                        $(document).ready(function() {
                                            var v_url = '<?php echo $this->controller_url; ?>count_record_by_activity';
                                            var village_id = <?php echo (int) Session::get('village_id') ?>;
                                            if (!village_id)
                                            {
                                                village_id = <?php echo (int) get_request_var('village') ?>;
                                            }
                                            if (!village_id)
                                            {
                                                if ($('#sel_village').length)
                                                    village_id = -1;
                                            }

                                            v_url += '&village=' + village_id;
                                            $.ajax({
                                                cache: false,
                                                url: v_url,
                                                dataType: 'json',
                                                success: function(json_data) {
                                                    $('.activity-num').each(function(index) {
                                                        v_activity = $(this).attr('data-activity');
                                                        $(this).html(json_data[v_activity]);
                                                    });
                                                }
                                            });
                                        });
                                </script>
                             
                                    <?php elseif(isset($this->arr_all_report_type)):?>
                                        <?php foreach ($this->arr_all_report_type as $v_code => $v_name): 
                                                $url = SITE_ROOT . 'r3/report/option/' . $v_code;
                                                $v_icon = 'icon-copy';
                                                $v_color = 'blue';
                                        ?>
                                            <li>
                                                <a href="<?php echo $url?>" class="<?php echo $v_color?>">
                                                    <i class="<?php echo $v_icon;?>"></i>
                                                    <span><?php echo $v_name; ?></span>
                                                </a>
                                                <?php
                                                    if(strval($v_code) == strval($this->current_report_type))
                                                    {
                                                        $html = '<div class="active-menu">';
                                                        $html .= '&nbsp;</div>';
                                                        echo $html;
                                                    }   
                                                ?>
                                            </li>
                                        <?php endforeach;?>
                                            <?php if(check_permission('QUAN_TRI_SO_THEO_DOI_HO_SO', 'r3')):?>
                                             <li>
                                                <a href="<?php echo SITE_ROOT?>r3/reportbook"  class="<?php echo 'blue'?>">
                                                    <i class="icon-copy"></i>
                                                    <span>Sổ theo dõi hồ sơ</span>
                                                </a>
                                                <?php
                                                    if(isset($this->menu_reportbook) && strval($this->menu_reportbook == 'reportbook'))
                                                    {
                                                        $html = '<div class="active-menu">';
                                                        $html .= '&nbsp;</div>';
                                                        echo $html;
                                                    }   
                                                ?>
                                            </li>
                                            <?php endif; ?>
                                    <?php endif;?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <?php endif;?>
                    <!--end-->
            
            

                    