<html>
    <head>
        <style type="text/css">
            body,div,dl,dt,dd,ul,ol,li,h1,h2,h3,h4,h5,h6,pre,form,fieldset,input,textarea,p,blockquote,th,td{margin:0;padding:0;}
            table{border-collapse:collapse;border-spacing:0;text-align:center;width:100%;}
            fieldset,img{border:0;}
            address,caption,cite,code,dfn,em,strong,th,var{font-style:normal;font-weight:normal;}
            ol,ul{list-style:none;}
            caption,th{text-align:center;font-weight:bold;}
            h1,h2,h3,h4,h5,h6{font-size:100%;font-weight:normal;}
            q:before,q:after{content:'';}
            abbr,acronym{border:0;}
            a:link,
            a:visited,
            a:hover,
            a:active{text-decoration:none;}
            input, textarea{border:1px solid #ccc;padding:4px;border-radius:3px;}
            textarea{width:98%;}
            ol{list-style-type:upper-alpha}
            ol ol{list-style-type:decimal}
            ol ol ol{list-style-type:lower-roman}
            #content p{margin-bottom:15px;margin-top:15px;}
            ol{margin-left:35px;}
            strong{font-weight:bold;}
            em{font-style:italic;}

            #sidebar h2, ul.navigation li, #sidebar h3 #content {
                padding-left: 20px;
                padding-right: 0;
                padding-top: 10px;
            }
            #content.no-grid {
                padding-left: 0 !important;
            }
            #grid-content {
                height: 0;
                overflow: hidden;
            }
            #grid-content li {
                background: none repeat scroll 0 0 #FFFFFF;
                border: 1px solid #EAEAE8;
                border-radius: 6px 6px 6px 6px;
                box-shadow: 0 1px 0.1px rgba(0, 0, 0, 0.1);
                display: block;
                height: 165px;
                margin: 9px;
                padding: 8px;
                width: 124px;
            }
            #grid-content .module {
                width: 124px;
            }
            #grid-content li h2, #grid-content li h2 a, #grid-content li h2 a:visited, .vcard, .vcard a {
                color: #000000;
                font-size: 12px;
                font-weight: bold;
                margin-bottom: 3px;
                margin-top: 5px;
            }
            #grid-content li .module p {
                color: #7F7F7F;
            }
            #grid-content li img {
                border: 0 solid #EAEAE8;
                border-radius: 23px 23px 23px 23px;
                box-shadow: 1px 1px 5px rgba(0, 0, 0, 0.1);
                display: block;
                max-width: 100%;
                padding: 0;
            }
            #grid-content li h3 {
                line-height: 1.1em;
                margin: 0;
                padding: 0;
            }
            #grid-content li p {
                margin: 0;
                padding: 0;
            }
            #grid-content .module {
                height: 48px !important;
                overflow: hidden;
                width: 124px;
            }
            #grid-content li.no-text .module {
                height: 0 !important;
                margin: 0 !important;
                overflow: hidden !important;
                padding: 0 !important;
                visibility: hidden !important;
                width: 0 !important;
            }

            #footer
            {
                text-align: center;
                font-family: arial;
                padding-top: 12px;
            }

            #logout
            {
                text-align: center;
                background: none repeat scroll 0 0 #FFFFFF;
                border: 1px solid #EAEAE8;
                border-radius: 6px 6px 6px 6px;
                box-shadow: 0 1px 0.1px rgba(0, 0, 0, 0.1);
                display: block;
                height: 165px;
                margin: 9px;
                padding: 8px;
                width: 124px;
            }
            #logout:hover
            {
                background-color: #C64934;
            }

            #logout img {
                border: 0 solid #EAEAE8;
                border-radius: 23px 23px 23px 23px;
                box-shadow: 1px 1px 5px rgba(0, 0, 0, 0.1);
                display: block;
                max-width: 100%;
                padding: 0;
            }

        </style>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>Go-Office Văn phòng điện tử</title>
    </head>
    <body>
        <div id="inner" style="border: solid 1px #397E06;">
            <ul id="grid-content" style="position: relative; width: auto; height: 400;">
                <?php if (session::get('is_admin')): ?>
                    <li style="position: absolute; left: 350px; top: 100px;">
                        <a href="<?php echo SITE_ROOT . 'cores/xlist/dsp_all_list'; ?>">
                            <img class="attachment-post-thumbnail wp-post-image" src="<?php echo SITE_ROOT . 'public/images/admin-128.png'; ?>">
                        </a>
                        <div class="module">
                            <h2><a rel="bookmark" href="<?php echo SITE_ROOT . 'cores/xlist/dsp_all_list'; ?>">Quản trị hệ thống</a></h2>
                            <p>&nbsp;</p>
                        </div>
                    </li>
                <?php endif; ?>

                <?php
                $arr_my_application = $VIEW_DATA['arr_my_application'];

                $left = 358;
                $top = 100;
                for ($i = 0; $i < count($arr_my_application); $i++) {
                    $v_code = strtolower($arr_my_application[$i]['C_CODE']);
                    $v_name = $arr_my_application[$i]['C_NAME'];
                    $v_description = $arr_my_application[$i]['C_DESCRIPTION'];
                    $v_default_module = strtolower($arr_my_application[$i]['C_DEFAULT_MODULE']);

                    //echo '<br/><a href="' . SITE_ROOT . $v_code . '/' . $v_default_module . '">'. $v_name . '</a>';
                    $v_href = SITE_ROOT . $v_code . '/' . $v_default_module;
                    $img_src = SITE_ROOT . 'public/images/' . $v_code . '-128.png';

                    $left += 158;
                    if ($left > 960) {
                        $left = 200;
                        $top +=194;
                    }
                    ?>
                    <li style="position: absolute; left: <?php echo $left; ?>px; top: <?php echo $top; ?>px;">
                        <a href="<?php echo $v_href; ?>">
                            <img class="attachment-post-thumbnail wp-post-image" src="<?php echo $img_src; ?>">
                        </a>
                        <div class="module">
                            <h2><a rel="bookmark" href="<?php echo $v_href; ?>"><?php echo $v_name; ?></a></h2>
                            <p>&nbsp;</p>
                        </div>
                    </li>
                    <?php
                }
                ?>
            </ul>

            <center>
            <div id="logout">
                <a href="<?php echo SITE_ROOT; ?>logout.php">
                        <img class="attachment-post-thumbnail wp-post-image" src="<?php echo SITE_ROOT;?>public/images/logout-128.png" />
                    </a>
                    <div class="module">
                        <h2><a rel="bookmark" href="<?php echo SITE_ROOT; ?>logout.php">Đăng thoát</a></h2>
                        <p>&nbsp;</p>
                    </div>
            </div>
            </center>
        </div>

        <div id="footer">
            Văn phòng HĐND-UBND Huyện Lạng Giang - Tỉnh Bắc Giang <br/>
            Go-Office Phần mềm văn phòng điện tử &copy 2012
        </div>
    </boby>
</html>