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
<!DOCTYPE html>
<html lang="vi" class="win chrome webkit">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">	
        <meta name="viewport" content="width=device-width,initial-scale=1.00, minimum-scale=1.00">    
        <meta name="viewport" content="width=320, target-densitydpi=150, initial-scale=0.999 , user-scalable=no">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <?php if ($this->is_metro): ?>
        <link rel="stylesheet" type="text/css" href="<?php echo SITE_ROOT; ?>apps/guidance/bootstrap/css/layout.css">
        <link rel="stylesheet" type="text/css" href="<?php echo SITE_ROOT; ?>apps/guidance/bootstrap/css/nav.css">
        <link rel="stylesheet" type="text/css" href="<?php echo SITE_ROOT; ?>apps/guidance/bootstrap/css/tiles.css">
        <link rel="stylesheet" type="text/css" href="<?php echo SITE_ROOT; ?>apps/guidance/bootstrap/css/theme.css">
        <title></title>
        <style>
            html{background-color:#053B95;}
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
            $arr_tile_group = array();
            for ($i = 0; $i < count($this->view_data); $i++) {
                if (($i % 4) == 0) {
                    $arr_tile_group[] = '"group' . ((int) ($i / 4)+1) . '"';
                }
            }
        ?>
        <script>
            scale =   <?php echo (_CONST_HEIHGT_ITEM >0 )   ?  _CONST_HEIHGT_ITEM  : 145; ?>;
            spacing = <?php echo ( _CONST_SPACING_ITEM >0) ? _CONST_SPACING_ITEM : 10; ?>;
            theme = 'theme_default';
            $group.titles = [<?php echo (sizeof($arr_tile_group)) ? implode(', ', $arr_tile_group) : ''; ?>];
            $group.spacingFull = [5];
            $group.inactive.opacity = "1";
            $group.inactive.clickable = "1";
            $group.showEffect = 0;
            $group.direction = "horizontal";

            mouseScroll = "1";

            siteTitle = 'Yên Bái';
            siteTitleHome = '<?php echo isset($this->title)? $this->title : ''; ?>';
            showSpeed = 400;
            hideSpeed = 300;
            scrollSpeed = 550;

            device = "desktop";
            scrollHeader = "1";
            disableGroupScrollingWhenVerticalScroll = "";

            /*For background image*/
            bgMaxScroll = "130";//"130";
            bgScrollSpeed = "450";//"450";

            /*For responsive */
            autoRearrangeTiles = "1";
            autoResizeTiles = "1";
            rearrangeTreshhold = 3;

            /*Locale */
            l_pageNotFound = "";
            l_pageNotFoundDesc = "";
            l_goToFullSiteRedirect = "";
            panelDim = '0.6';
            hidePanelOnClick = '1';
            panelGroupScrolling = '';
        </script>
        </script><script type="text/javascript" src="<?php echo SITE_ROOT; ?>apps/guidance/bootstrap/js/functions.js"> </script>
        <script type="text/javascript" src="<?php echo SITE_ROOT; ?>apps/guidance/bootstrap/js/main.js"></script>
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
        <style>#tileContainer{display:block}</style>
        </noscript>
        <?php endif; ?>
         <!--[if IE]>
            <link rel="stylesheet" href="<?php echo $this->get_apps_url; ?>ie.css" type="text/css" media="screen"  />
            <?php if(! $this->is_metro):?>
                <style>
                    #headerWrapper
                    {
                        position:  inherit !important;
                    }
                    #headerWrapper h2
                    {
                        color:white;
                        font-size: 1.3em;
                        margin:10px
                    }
                </style>
            <?php else:?>
            <style>
                    #headerWrapper
                    {
                        position:  fixed !important;
                    }
                    #headerWrapper h2
                    {
                        color:white;
                        font-size: 1.3em;
                        margin:10px
                    }
                </style>
            <?php endif;?>
        <![endif]-->
        <link rel="stylesheet" href="<?php echo $this->stylesheet_url; ?>" type="text/css" media="screen" />
    </head>
    <body class="full desktop" style="">
        <?php if ($this->is_metro): ?>
            <img src="<?php echo SITE_ROOT; ?>apps/guidance/images/bg_metro.jpg"  alt="background-image" id="bgImage" style="margin-left: -67.66666666666667px;height: 100%">
       <?php else:?>
            <style>
                body
                {
                    background: #053B95 url(<?php echo SITE_ROOT; ?>apps/guidance/images/bg_metro.jpg);
                }
                
            </style>
        <?php endif;?>
        <header>
            <div id="headerWrapper">
                <div class="quochuy">
                    <img src="<?php echo SITE_ROOT . 'apps/guidance/' ?>images/quochuy.png" height="100px" width="100px" />    
                </div>
                <h1 class="title">ỦY BAN NHÂN DÂN TP YÊN BÁI</h1>
                <div class="list-head">
                    <h3 class="title-type">
                        <?php echo isset($this->v_name_linh_vuc) ? '<span>Lĩnh vực: </span>' .$this->v_name_linh_vuc : ''; ?></h3>
                </div>  
            </div>
        </header>
        <!--End #header-->