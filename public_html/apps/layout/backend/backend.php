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
       
        <script src="<?php echo SITE_ROOT; ?>public/js/advchat/advChatCnf.js" type="text/javascript"></script>
        <script>
            $(document).ready(function(){
                //neu ko phai la man hinh advChat
                if(typeof(processDom) == 'undefined')
                {
                    //ajax count unread mesage
                    var url = '<?php echo SITE_ROOT . build_url('r3/advchat/count_unread')?>';
                    var data = {user_id: advChatCnf.user_id,user_ou: advChatCnf.user_ou};
                    $.ajax({
                        type: 'POST',
                        url: url,
                        data: data,
                        success: function(res)
                        {
                            if(parseInt(res) == 0)
                            {
                                return false;
                            }
                            var html_mes = '<span class="notify-tip">'+res+'</span>';
                            var selector = $('#menu_chat .notify-tip');
                            $('#menu_chat').append(html_mes);
                        }
                    });

                    //dang ky advchat online
                    socket.emit('chat_reg', {id: advChatCnf.user_id, name: advChatCnf.user_name});

                    //cho tin nhan gui ve
                    socket.on('recieve_message', function(data) {
                        var html_mes = '<span class="notify-tip">1</span>';
                        var selector = $('#menu_chat .notify-tip');
                        if($(selector).html() == '' || $(selector).html() == null)
                        {
                            $('#menu_chat').append(html_mes);
                        }
                        else
                        {
                            var no_mes = $(selector).html();
                            no_mes = parseInt(no_mes) + 1;
                            $(selector).html(no_mes);
                        }

                    });
                }
            });
        </script>
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
                       <?php
    $arr_other_role = array(strtolower(_CONST_TRA_CUU_ROLE),strtolower(_CONST_BAO_CAO_ROLE));
?>
<!--include js record-->
<script src="<?php echo SITE_ROOT . 'apps/r3/modules/record/record_views/js_record.js'; ?>"></script>

<div class="left-secondary-nav tab-content">
    <div class="tab-pane active bs-docs-sidebar" id="roles">
<!--        <h4 class="side-head">Hồ sơ</h4>-->
        <ul class="accordion-nav bs-docs-sidenav affix" style="width: 220px;">
            <?php /*
            <!--giai quyet thu tuc hanh chính-->
            <?php
            $class_active = (!in_array(strtolower($this->active_role), $arr_other_role))?'active':'';
            ?>
            <li class="<?php echo $class_active;?>">
                <a href="<?php echo SITE_ROOT . build_url('r3/record/') ; ?>">
                    <i class=" icon-list-alt"></i>
                    Giải quyết TTHC
                </a>
            </li>
            <!--tra cuu-->
            <?php 
            //kiem tra quyen
            if (check_permission(_CONST_TRA_CUU_ROLE, 'r3')):
                $class_active = (strtolower($this->active_role) == strtolower(_CONST_TRA_CUU_ROLE))?'active':'';
            ?>
            <li class="<?php echo $class_active;?>">
               
                <a href="<?php echo SITE_ROOT . build_url('r3/record/ho_so/tra_cuu'); ?>">
                    <i class=" icon-list-alt"></i>
                    Tra cứu
                </a>
            </li>
            <?php endif;?>
            <!--Bao cao-->
            <?php 
            //kiem tra quyen
             if (check_permission(_CONST_BAO_CAO_ROLE, 'r3')):
                $class_active = (strtolower($this->active_role) == strtolower(_CONST_BAO_CAO_ROLE))?'active':'';
            ?>
            <li class="<?php echo $class_active;?>">
                <a href="<?php echo SITE_ROOT . build_url('r3/record/ho_so/bao_cao'); ?>">
                    <i class=" icon-list-alt"></i>
                    Báo cáo
                </a>
            </li>
            <?php endif;?>
            <li>
                <a href="<?php echo SITE_ROOT . build_url('r3/record/ho_so/theo_doi'); ?>">
                    <i class=" icon-list-alt"></i>
                    Theo dõi
                </a>
            </li>*/?>
            
            <?php if (isset($this->arr_roles)): ?>
                <?php foreach ($this->arr_roles as $key => $val): ?>
                    <?php if (check_permission($key, 'r3')): ?>
                        <?php $v_class       = (strtolower($this->active_role) == strtolower($key) && ( !isset($_GET['url']) OR $_GET['url'] != "r3/record/liveboard") ) ? ' class="active_role active"' : ''; ?>
                        <li <?php echo $v_class; ?> data-role="<?php echo $key; ?>" data-menu="1" style="width: 100%;;">
                            <a href="<?php echo $this->controller_url . 'ho_so/' . strtolower($key); ?>">
                                <i class="icon-list-alt"></i>
                                <?php echo $val; ?>
                                <?php
                                $arr_not_count = array(
                                    _CONST_TRA_CUU_ROLE
                                    , _CONST_TRA_CUU_LIEN_THONG_ROLE
                                    , _CONST_BAO_CAO_ROLE
                                    , _CONST_Y_KIEN_LANH_DAO_ROLE
                                    , _CONST_TRA_CUU_LIEN_THONG_ROLE
                                    , _CONST_TRA_CUU_TAI_XA_ROLE
                                    );
                                ?>
                                
                                <?php if (!in_array(strtoupper($key), $arr_not_count)): ?>
                                    <i style="font-style: normal" class="count">(0)</i>
                                <?php endif; ?>
                            </a>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
                        <?php $v_class    = (isset($_GET['url']) && $_GET['url'] == "r3/record/liveboard") ? ' class="active_role active"' : ''; ?>
                        <!--Theo dõi trực tuyến-->
                        <li <?php echo $v_class; ?> style="width: 100%;;" >
                             <a href="<?php echo $this->controller_url.'liveboard';?>"><i class="icon-list-alt"></i> Theo dõi trực tuyến</a>
                        </li>
                        <?php $v_class    = (isset($_GET['url']) && $_GET['url'] == "r3/advchat") ? ' class="active_role active"' : ''; ?>
                        <!--Trò chuyện-->
                        <li <?php echo $v_class; ?> style="width: 100%;;" >
                             <a id="menu_chat" href="<?php echo SITE_ROOT . build_url('r3/advchat');?>">
                                 <i class="icon-list-alt"></i> 
                                 Chat - Hỗ trợ
                             </a>
                        </li>
                <script>
                    function get_role_notice() {
                        get_notice(SITE_ROOT+"<?php echo build_url('r3/notice/main') . '/' . $this->active_role; ?>");
                    }
                    
                    jQuery(document).ready(function() {
                        <?php if (!in_array(strtoupper($this->active_role), $arr_not_count)): ?>
                                get_role_notice();
                                setInterval(get_role_notice, <?php echo _CONST_GET_NEW_RECORD_NOTICE_INTERVAL; ?>);
                        <?php endif; ?>
                        count_processing_record_per_role();
                        setInterval(count_processing_record_per_role, <?php echo _CONST_GET_NEW_RECORD_NOTICE_INTERVAL; ?>);
                    });

                    function count_processing_record_per_role()
                    {
                        q = 'li[data-menu="1"]';
                        jQuery(q).each(function(index) {
                            var v_role = $(this).attr('data-role');
                            if (v_role.toUpperCase() != '<?php echo _CONST_TRA_CUU_ROLE; ?>' && v_role.toUpperCase() != '<?php echo _CONST_BAO_CAO_ROLE; ?>' && v_role.toUpperCase() != '<?php echo _CONST_Y_KIEN_LANH_DAO_ROLE; ?>')
                            {
                                var v_url = '<?php echo $this->controller_url; ?>' + 'count_processing_record_by_role/' + v_role + '/' + QS + 't=' + getTime();
                                jQuery.ajax({
                                    cache: false,
                                    url: v_url,
                                    dataType: 'json',
                                    success: function(data) {
                                        count = data.count;
                                        role = data.role;
                                        rq = 'li[data-role="' + role + '"] i[class="count"]';
                                        count = '('+ count +')';
                                        jQuery(rq).html(count);
                                    }
                                });
                            }
                        });
                    }
                </script>
            <?php endif; ?>
               
        </ul>
    </div> <!-- /#role -->
</div>
                    </div>
                </div><!-- .leftbar leftbar-close -->
                 <div class="main-wrapper">
            <?php else: ?>
                <div class="main-wrapper" style="margin-left: 0px !important;">
            <?php endif;?>
                    
                    <?php
                        //array role k hien thi tại menu
                        $arr_show_menu = array(strtolower(_CONST_TRA_CUU_ROLE),strtolower(_CONST_BAO_CAO_ROLE));
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
                                    <?php endif;?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <?php endif;?>
                    <!--end-->
                    <?php echo $content?>
            

                <div class="footer">
                    R3 - Phần mềm hỗ trợ giải quyết thủ tục hành chính theo cơ chế một cửa
                </div>
            </div><!-- .main-wrapper -->
        </div><!-- .layout -->
    </body>
</html>