<!DOCTYPE html>
<html lang="vi" class="win chrome webkit">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">		 

        <meta name="viewport" content="width=device-width,initial-scale=1.00, minimum-scale=1.00">    
        <meta name="viewport" content="width=320, target-densitydpi=150, initial-scale=0.999 , user-scalable=no">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600" rel="stylesheet" type="text/css">

        <link rel="stylesheet" type="text/css" href="<?php echo SITE_ROOT; ?>apps/guidance/bootstrap/css/layout.css">
         
        <link rel="stylesheet" type="text/css" href="<?php echo SITE_ROOT; ?>apps/guidance/bootstrap/css/nav.css">
        <link rel="stylesheet" type="text/css" href="<?php echo SITE_ROOT; ?>apps/guidance/bootstrap/css/tiles.css">
        <link rel="stylesheet" type="text/css" href="<?php echo SITE_ROOT; ?>apps/guidance/bootstrap/css/theme.css">
        <!--<link rel="stylesheet" type="text/css" href="<?php echo SITE_ROOT; ?>apps/guidance/bootstrap/css/theme-mob.css">-->
        <!--<link rel="stylesheet" type="text/css" href="<?php echo SITE_ROOT; ?>apps/guidance/bootstrap/css/mobile.css">-->
        <link rel="stylesheet" type="text/css" href="<?php echo SITE_ROOT; ?>apps/guidance/bootstrap/css/plugin.css">
        <link rel="stylesheet" type="text/css" href="<?php echo SITE_ROOT; ?>apps/guidance/bootstrap/css/plugin(1).css">
        <link rel="stylesheet" type="text/css" href="<?php echo SITE_ROOT; ?>apps/guidance/bootstrap/css/plugin(2).css">
        <link rel="stylesheet" type="text/css" href="<?php echo SITE_ROOT; ?>apps/guidance/bootstrap/css/plugin(3).css">
        <link rel="stylesheet" type="text/css" href="<?php echo SITE_ROOT; ?>apps/guidance/bootstrap/css/plugin(4).css">
        <link rel="stylesheet" type="text/css" href="<?php echo SITE_ROOT; ?>apps/guidance/bootstrap/css/plugin(5).css">
        <link rel="stylesheet" type="text/css" href="<?php echo SITE_ROOT; ?>apps/guidance/bootstrap/css/plugin(6).css">
        <link rel="stylesheet" type="text/css" href="<?php echo SITE_ROOT; ?>apps/guidance/bootstrap/css/plugin(7).css">
        <link rel="stylesheet" type="text/css" href="<?php echo SITE_ROOT; ?>apps/guidance/bootstrap/css/plugin(8).css">
        <link rel="stylesheet" type="text/css" href="<?php echo SITE_ROOT; ?>apps/guidance/bootstrap/css/plugin(9).css">
        <link rel="stylesheet" type="text/css" href="<?php echo SITE_ROOT; ?>apps/guidance/bootstrap/css/plugin(10).css">
        <link rel="stylesheet" type="text/css" href="<?php echo SITE_ROOT; ?>apps/guidance/bootstrap/css/plugin(11).css">
        <link rel="stylesheet" type="text/css" href="<?php echo SITE_ROOT; ?>apps/guidance/bootstrap/css/plugin(12).css">
        <link rel="stylesheet" type="text/css" href="<?php echo SITE_ROOT; ?>apps/guidance/bootstrap/css/plugin(13).css">
        <style>
            html{background-color:#0F6D32;}
            #bgImage { position:fixed; top:0; left:0; z-index:-4; min-width:115%;min-height:100%;
                       -webkit-transition:margin-left 450ms linear;
                       -moz-transition:margin-left 450ms linear;
                       -o-transition:margin-left 450ms;
                       -ms-transition:margin-left 450ms;
                       transition:margin-left 450ms;
            }
            .tile{
                -webkit-transition-property: box-shadow, margin-left,  margin-top;
                -webkit-transition-duration: 0.25s, 0.5s, 0.5s;
                -moztransition-property: box-shadow, margin-left,  margin-top;
                -moz-transition-duration: 0.25s, 0.5s, 0.5s;
                -o-transition-property: box-shadow, margin-left,  margin-top;
                -o-transition-duration: 0.25s, 0.5s, 0.5s;
                -ms-transition-property: box-shadow, margin-left,  margin-top;
                -ms-transition-duration: 0.25s, 0.5s, 0.5s;
                transition-property: box-shadow, margin-left,  margin-top;
                transition-duration: 0.25s, 0.5s, 0.5s;
            }
        </style>
        <script type="text/javascript" async="" src="<?php echo SITE_ROOT; ?>apps/guidance/bootstrap/js/ga.js">
        </script>
        <script type="text/javascript">
            var _gaq = _gaq || [];
            _gaq.push(['_setAccount', 'UA-30159978-1']);
            _gaq.push(['_trackPageview']);
            (function() {
                var ga = document.createElement('script');
                ga.type = 'text/javascript';
                ga.async = true;
                ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
                var s = document.getElementsByTagName('script')[0];
                s.parentNode.insertBefore(ga, s);
            })();
        </script> 
        <!--[if IE]>
    <script src="js/html5.js"></script>
     <![endif]-->
        <!--[if lt IE 9]>
        <script type="text/javascript" language="javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
        <script src="js/html5.js"></script>
            <![endif]-->
        <!--[if gte IE 9]><!-->
        <script src="<?php echo SITE_ROOT; ?>apps/guidance/bootstrap/js/jquery.min.js"></script>
        <script src="<?php echo SITE_ROOT; ?>apps/guidance/bootstrap/js/html5.js"></script>
        <!--[endif]---->
        <!--[if !IE]>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
            <![endif]-->

        <script type="text/javascript">window.jQuery || document.write('<\/script><script type="text/javascript" src="<?php echo SITE_ROOT; ?>apps/guidance/bootstrap/js/jquery1102.js"><\/script>')</script>
        <script type="text/javascript" src="<?php echo SITE_ROOT; ?>apps/guidance/bootstrap/js/plugins.js"></script>
        <?php 
            
            $j               =  0;
            $v_group_item    = '';
            $v_spacingFull   = '';
            
            $arr_group_item  =  array('"nhom1"');
            $arr_spacingFull = array(5);
            $arr_all_guidance= isset($this->view_data)? $this->view_data : array();

            for ($i = 0; $i < count($arr_all_guidance); $i++):
                if((($i + 1) % 4) == 0)
                {
                    $j += 1;
                    $arr_group_item[$j]  = '"nhom'.((($i + 1) / 4) + 1).'"';
                    $arr_spacingFull[$j] = 5;
                }
            endfor;
            
            $v_group_item = implode(', ', $arr_group_item);
            $v_spacingFull = implode(', ', $arr_spacingFull);

        ?>
        <script>
            scale =   <?php echo (_CONST_HEIHGT_ITEM >0 )   ?  _CONST_HEIHGT_ITEM  : 145; ?>;
            spacing = <?php echo ( _CONST_SPACING_ITEM >0) ? _CONST_SPACING_ITEM : 10; ?>;
            theme = 'theme_default';
            $group.titles = [<?php echo $v_group_item; ?>];
            $group.spacingFull = [<?php echo $v_spacingFull; ?>];
            $group.inactive.opacity = "1";
            $group.inactive.clickable = "1";
            $group.showEffect = 0;
            $group.direction = "horizontal";

            mouseScroll = "1";

            siteTitle = '<?php echo isset($this->title)? $this->title : ''; ?>';
            siteTitleHome = 'Guidance';
            showSpeed = 400;
            hideSpeed = 300;
            scrollSpeed = 550;

            device = "desktop";
            scrollHeader = "1";
            disableGroupScrollingWhenVerticalScroll = "<?php echo $this->template_is_metro;?>";

            /*For background image*/
            bgMaxScroll = "130";
            bgScrollSpeed = "450";

            /*For responsive */
            autoRearrangeTiles = "1";
            autoResizeTiles = "1";
            rearrangeTreshhold = 3;

            /*Locale */
            lang = "vi";
            l_pageNotFound = "";
            l_pageNotFoundDesc = "";
            l_menu = "Menu";
            l_goToFullSiteRedirect = "";
            panelDim = '0.6';
            hidePanelOnClick = '1';
            panelGroupScrolling = '';
        </script>
        <script type="text/javascript" src="<?php echo SITE_ROOT; ?>apps/guidance/bootstrap/js/plugin.js"> </script>
        <script type="text/javascript" src="<?php echo SITE_ROOT; ?>apps/guidance/bootstrap/js/plugin(14).js"></script>
        <script type="text/javascript" src="<?php echo SITE_ROOT; ?>apps/guidance/bootstrap/js/desktop.js"></script>
        <script type="text/javascript" src="<?php echo SITE_ROOT; ?>apps/guidance/bootstrap/js/plugins/panels/mobile.js"></script>
        
        <script type="text/javascript" src="<?php echo SITE_ROOT; ?>apps/guidance/bootstrap/js/plugin(15).js"></script>
        <script type="text/javascript" src="<?php echo SITE_ROOT; ?>apps/guidance/bootstrap/js/plugin(16).js"></script>
        <script type="text/javascript" src="<?php echo SITE_ROOT; ?>apps/guidance/bootstrap/js/plugin(17).js"></script>
        <script type="text/javascript" src="<?php echo SITE_ROOT; ?>apps/guidance/bootstrap/js/plugin(18).js"></script>
        <script type="text/javascript" src="<?php echo SITE_ROOT; ?>apps/guidance/bootstrap/js/plugin(19).js"></script>
        <script type="text/javascript" src="<?php echo SITE_ROOT; ?>apps/guidance/bootstrap/js/plugin(20).js"></script>
        <script type="text/javascript" src="<?php echo SITE_ROOT; ?>apps/guidance/bootstrap/js/plugin(21).js"></script>
        <script type="text/javascript" src="<?php echo SITE_ROOT; ?>apps/guidance/bootstrap/js/plugin(22).js"></script>
        <script type="text/javascript" src="<?php echo SITE_ROOT; ?>apps/guidance/bootstrap/js/plugin(23).js"></script>
        <script type="text/javascript" src="<?php echo SITE_ROOT; ?>apps/guidance/bootstrap/js/plugin(24).js"></script>
        <script type="text/javascript" src="<?php echo SITE_ROOT; ?>apps/guidance/bootstrap/js/plugin(25).js"></script>
        <script type="text/javascript" src="<?php echo SITE_ROOT; ?>apps/guidance/bootstrap/js/functions.js"></script>
        <script type="text/javascript" src="<?php echo SITE_ROOT; ?>apps/guidance/bootstrap/js/main.js"></script>
        <script type="text/javascript" src="<?php echo SITE_ROOT; ?>apps/guidance/bootstrap/js/jquery.media.js"></script>
        <script type="text/javascript" src="<?php echo SITE_ROOT; ?>apps/guidance/bootstrap/js/jquery.metadata.js"></script>
        <script type="text/javascript" src="<?php echo SITE_ROOT; ?>apps/guidance/bootstrap/js/jquery.simpleWeather-2.1.2.min.js"> </script>
        <style>
            #catchScroll{
                background:rgb(30,30,30);
                -ms-filter: 'progid:DXImageTransform.Microsoft.Alpha(Opacity=00)';
                filter: alpha(opacity=00);
                -moz-opacity: 0;
                -khtml-opacity: 0;
                opacity:0;
            }
        </style>    
        <noscript>
        &lt;style&gt;#tileContainer{display:block}&lt;/style&gt;
        </noscript>
        <link rel="stylesheet" href="<?php echo $this->stylesheet_url; ?>" type="text/css" media="screen" />
          <!--[if IE]>
            <link rel="stylesheet" href="<?php echo SITE_ROOT; ?>apps/guidance/ie.css" type="text/css" media="screen" />
         <![endif]-->
        
    </head>
    <body class="full desktop" style="">
        <!--<img src="<?php echo SITE_ROOT; ?>apps/guidance/bootstrap/images/metro_green.jpg" alt="background-image" id="bgImage" style="margin-left: -67.66666666666667px;">-->
        <!--End #header-->
            <!--start #header-->
            <header>
                <div id="headerWrapper">
                    <div id="banner">
                        <img src="<?php echo SITE_ROOT . 'apps/guidance/' ?>images/banner.jpg" height="auto" width="100%" />
                    </div>
                    <div class="curr_time">
                      <label>&nbsp; <?php echo jwDate::vn_day_of_week() . ',&nbsp;' . date('d/m/Y'); ?></label>
                    </div>
                </div>

            </header>
            <!--End #header-->