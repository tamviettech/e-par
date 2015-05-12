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
            <!--chosen-->
        <link rel="stylesheet" type="text/css" href="<?php echo SITE_ROOT ?>public/chosen/chosen.min.css"/>
        <script src="<?php echo SITE_ROOT ?>public/chosen/chosen.jquery.min.js"></script>
        
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
       
        <script src="<?php echo SITE_ROOT; ?>public/js/advchat/adapter.js" type="text/javascript"></script>
        <?php if(CHAT_MODULE != 0):?>
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
                            var html_mes = '<span class="notify-tip" style="line-height: 15px;">'+res+'</span>';
                            var selector = $('li[data-module="advchat"] .notify-tip');
                            $('li[data-module="advchat"]').prepend(html_mes);
                        }
                    });

                    //dang ky advchat online
                    socket.emit('chat_reg', {id: advChatCnf.user_id, name: advChatCnf.user_name});
                    
                    //cho tin nhan gui ve
                    socket.on('recieve_message', function(data) {
                        update_message();
                    });
                    //support
                    //dang ky online len tam viet
                    supportSocket.emit('support_reg', {id: advChatCnf.user_id, name: advChatCnf.user_name, ou: advChatCnf.user_ou});
                    //nhan tin nhan tu support
                    supportSocket.on('recieve_message', function(data) {
                        if(typeof(data) != 'undefined')
                        {
                            var url = '<?php echo SITE_ROOT . build_url('r3/advchat/do_insert_mes')?>';
                            $.ajax({
                                    type: 'POST',
                                    url: url,
                                    data: {
                                            mes: data.mes,
                                            status:1,
                                            recieve_user: advChatCnf.user_id,
                                            recieve_user_ou: advChatCnf.user_ou,
                                            send_user:data.user,
                                            send_user_ou:data.user_ou,
                                            user_name:data.user_name
                                           },
                                    success: function(result)
                                    {
                                        if(parseInt(result) == 0)
                                        {
                                            update_message();
                                        }
                                    }
                            });
                        }                        
                    });
                }
            });
            function update_message()
            {
                var html_mes = '<span class="notify-tip" style="line-height: 15px;">1</span>';
                var selector = $('li[data-module="advchat" .notify-tip');
                if($(selector).html() == '' || $(selector).html() == null)
                {
                    $('li[data-module="advchat').prepend(html_mes);
                }
                else
                {
                    var no_mes = $(selector).html();
                    no_mes = parseInt(no_mes) + 1;
                    $(selector).html(no_mes);
                }
            }
        </script>
        <?php endif;?>
        <?php
                // Fix css chrome version 39.0.2171.95
                $chrome = $_SERVER['HTTP_USER_AGENT'];
                preg_match( "#Chrome/(.+?)\s#", $chrome,  $browser);

                if(strpos( $browser[0], 'Chrome') !== false && $browser[1]  == '39.0.2171.95')
                {
                    echo '  <link rel="stylesheet" href="'.SITE_ROOT.'apps/'.$this->app_name.'/fix_css_chrome.css">';
                }


        ?>
    </head>
    <body>
        <div class="layout">
            <DIV id=overDiv style="Z-INDEX: 200; VISIBILITY: hidden; POSITION: absolute"></DIV>
            <div style="width: 100%;">
                <div style="background: #da3610;width: 220px;float:left;height: 50px;">
                    <div style="float: left;padding-left: 10px;padding-top: 2px;">
                                <img src="<?php echo SITE_ROOT . 'public/logoQuocHuy.png' ?>" style="width: 45px;height: 45px;"/>
                    </div>
                    <center style="color: white;font-weight: bold;padding-top: 5px;font-size: 13px;font-family: Tohama">
                        <?php
                        $dom_unit = simplexml_load_file(SERVER_ROOT . 'public/xml/xml_unit_info.xml');
                        if (Session::get('la_can_bo_cap_xa'))
                            $unit_fullname = Session::get('ou_name');
                        else
                            $unit_fullname = mb_strtoupper(xpath($dom_unit, '//full_name', XPATH_STRING), 'UTF-8');
                        echo $unit_fullname;
                        ?>
                    </center>
                </div>
                <div class="main-wrapper" style="padding: 0;">
                    <?php #include (SERVER_ROOT . 'dsp_top_primary_nav.php');?>
                    <div class="navbar navbar-inverse top-nav">
                        <div class="navbar-inner">
                            <div class="container">
                                <span class="home-link">
                                    <a href="<?php echo SITE_ROOT;?>" class="icon-home"></a>
                                </span>
                                <div class="nav-collapse">
                                    <style>
                                        .navbar .nav > li > a
                                        {
                                            padding: 10px 10px 10px;
                                        }
                                    </style>
                                    <?php View::build_app_menu( dirname(__FILE__) . '/r3_menu.xml.php', $this->app_name);?>
                                </div>
                                <div class="btn-toolbar pull-right notification-nav">
                                    <?php View::build_user_profile_menu( dirname(__FILE__) . '/r3_menu.xml.php', $this->app_name);?>
                                </div>
                                <script>
                                    $(document).ready(function(){
                                        count_notify_module();
                                        setInterval(count_notify_module, <?php echo _CONST_GET_NEW_RECORD_NOTICE_INTERVAL; ?>);
                                    });
                                    function count_notify_module()
                                    {
                                        //app record
                                        var v_url = '<?php echo SITE_ROOT; ?>' + 'r3/record/count_processing_record_by_role/';
                                        jQuery.ajax({
                                            cache: false,
                                            url: v_url,
                                            dataType: 'json',
                                            success: function(data) {
                                                var count = data.count;
                                                var html = '<span class="notify-tip activity-num" style="line-height: 15px;">'+count+'</span>';
                                                $('li[data-module="record"]').find('span').remove(); 
                                                $('li[data-module="record"]').prepend(html);
                                            }
                                        });
                                    }
                                </script>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="clear"></div>
            <?php if ($this->show_left_side_bar == TRUE): ?>
                <div class="leftbar leftbar-close clearfix">
                    <div style="background: #da3610;width: 220px;float:left;height: 50px;z-index:200;left:0px;top:0px;position:fixed;">
                        <div style="float: left;padding-left: 10px;padding-top: 2px;">
                                <img src="<?php echo SITE_ROOT . 'public/logoQuocHuy.png' ?>" style="width: 45px;height: 45px;"/>
                        </div>
                        <center style="color: white;font-weight: bold;padding-top: 5px;font-size: 13px;font-family: Tohama">
                            <?php
                            $dom_unit = simplexml_load_file(SERVER_ROOT . 'public/xml/xml_unit_info.xml');
                            if (Session::get('la_can_bo_cap_xa'))
                                $unit_fullname = Session::get('ou_name');
                            else
                                $unit_fullname = mb_strtoupper(xpath($dom_unit, '//full_name', XPATH_STRING), 'UTF-8');
                            echo $unit_fullname;
                            ?>
                        </center>
                    </div>
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
                                            strtolower(_CONST_TRA_CUU_LIEN_THONG_ROLE));
                    if(in_array(strtolower($this->active_role), $arr_show_menu) && !empty($this->activity_filter)):
                    ?>
                    <!--menu--> 
                    <div class="row-fluid">
                        <div class="span12">
                            <div class="switch-board gray">
                                <ul class="clearfix switch-item">
                                
                                    <?php 
                                    $arr_style = array(
                                        0 => array('icon'=>'icon-asterisk','color'=>'blue'),
                                        1 => array('icon'=>'icon-star-empty','color'=>'blue'),
                                        2 => array('icon'=>'icon-edit','color'=>'blue'),
                                        10=> array('icon'=>'icon-pause','color'=>'blue'),
                                        3 => array('icon'=>'icon-ban-circle','color'=>'blue'),
                                        4 => array('icon'=>'icon-tasks','color'=>'blue'),
                                        5 => array('icon'=>'icon-pushpin','color'=>'blue'),
                                        6 => array('icon'=>'icon-time','color'=>'blue'),
                                        7 => array('icon'=>'icon-certificate','color'=>'blue'),
                                        8 => array('icon'=>'icon-time','color'=>'brown'),
                                        9 => array('icon'=>'icon-warning-sign','color'=>'magenta'),
                                        11 => array('icon'=>'icon-check','color'=>'blue '),
                                        12 => array('icon'=>'icon-check','color'=>'blue'),
                                        13 => array('icon'=>'icon-warning-sign','color'=>'blue'),
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
                                                if(get_request_var('tt') == $key)
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
                                    
                                </ul>
                            </div>
                        </div>
                    </div>
                    <?php endif;
       