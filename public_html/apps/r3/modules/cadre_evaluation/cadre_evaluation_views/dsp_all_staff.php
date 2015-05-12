<?php
/*
 * Hien thi danh sach tat cac cac nhan vien duoc danh gia.
 */
$arr_all_staff = isset($arr_all_staff) ? $arr_all_staff : array();
?>
<!DOCTYPE html>
<html>
    <head>

        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=0; user-scalable=0;" />
        <title>Đánh giá cán bộ</title>
        <link rel="stylesheet" type="text/css" href="<?php echo FULL_SITE_ROOT; ?>apps/r3/cadre.css" />
        <script type="text/javascript" src="<?php echo FULL_SITE_ROOT; ?>public/js/jquery/jquery-1.8.2.js"></script>
        <!--[if IE]>
        <style type="text/css">
          image
            {
                border: 0;
            }
        #content
        {
            width:100%;
            margin:0 20px;
            overflow:hiddenite;
        }
        #content ul li.single-staff
        {
            width:150px;
            height:220px;
            background:white
        }
        #content ul li.single-staff image
        {
            height:150px
        }
        </style>
    <![endif]-->
    </head>
    <body>
        <div id="wrapper">
            <div id="header">
                <h1 style="font-family: Time New Roman;">Đánh giá cán bộ</h1>
                <h1 style="font-family: Time New Roman;" >*&nbsp;*&nbsp;*</h1>
                <h2 style="font-family: Time New Roman;" >Lựa chọn cán bộ</h2>
            </div><!-- end #header -->
            <div id="content">
                <div id="list" class="staff">
                    <ul>
                        <?php foreach ($arr_all_staff as $arr_single_staff):; ?>
                            <?php
                            $v_staff_id = ($arr_single_staff['PK_USER']) ? $arr_single_staff['PK_USER'] : '';
                            $v_staff_name = ($arr_single_staff['C_NAME']) ? $arr_single_staff['C_NAME'] : '';
                            $v_staff_user_name = ($arr_single_staff['C_LOGIN_NAME']) ? $arr_single_staff['C_LOGIN_NAME'] : '';

                            $v_dir_img_staff_logo = CONST_DIRECT_R3_IMAGES . $v_staff_user_name . '.png';

                            //Anh logo mac dinh
                            $v_url_img_staff_logo = CONST_URL_R3_IMAGES . 'logo_default.jpg';

                            if (file_exists($v_dir_img_staff_logo)) {
                                $v_url_img_staff_logo = CONST_URL_R3_IMAGES . $v_staff_user_name . '.png';
                            }
                            ?>
                            <li class="single-staff">
                                <a href="<?php echo $this->get_controller_url() . 'dsp_check_vote?id=' . $v_staff_id; ?>" data-id="<?php echo $v_staff_id; ?>" >
                                    <img src="<?php echo $v_url_img_staff_logo; ?>" width="100%" height="100%" />
                                    <span><?php echo $v_staff_name; ?></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>              
            </div><!-- end #content -->
            <div id="footer">
            </div><!-- end #footer -->
            <script>
                $(document).ready(function(){
                    load_size();
                     $(window).resize(
                     function()
                     {
                         $('#content').removeAttr('style');
                        load_size();
                     });
                    
                    
                });
                function load_size()
                {
                    
                    width_content =  $('#content').outerWidth() - 20;// margin:  -lef:10  -right:10
                    
                    window_width  = $(window).width();
                    item_outwidth = $('#list li').last().outerWidth() + 20 || 0;
                    
                    if(width_content < item_outwidth * 5)
                    {
                        $('#content').width(item_outwidth * 4);
                    }
                    
                    if(width_content < item_outwidth * 4)
                    {
                        $('#content').width(item_outwidth * 3);
                    }
                    
                    if(window_width < item_outwidth * 3)
                    {
                        $('#content').width(item_outwidth * 2);
                    }
                    
                    if(window_width < item_outwidth * 2)
                    {
                        $('#content').width(item_outwidth);
                    }
                    
                     
                    
                }
            </script>
        </div><!-- end #wrapper -->
    </body>
</html>

