<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=0; user-scalable=0;"  />
    <title>Đánh giá cán bộ</title>
    <link rel="stylesheet" type="text/css" href="<?php echo FULL_SITE_ROOT; ?>apps/r3/cadre.css" />
    <script type="text/javascript" src="<?php echo FULL_SITE_ROOT;?>public/js/jquery/jquery-1.8.2.js"></script>
    <!--[if IE]>
        <style type="text/css">
        image
        {
            border: 0;
        }
        #header,#wrapper
        {
            overflow:hidden;
            width:900px;
            margin:10px auto 0 auto;
        }
        #list
        {   
            overflow:hidden;
        }
        #list ul
        {
            padding-left:100px
        }
        #list ul li
        {
            width:160px
        }
        #list ul li a
        {
            padding:5px ;
            display:block;
            font-size:1.4em;
        }
        </style>
    <![endif]-->
    <script type="text/javascript">
        $(document).ready(function() {
            var today = $('#today').val();
            var user_id = $('#user_id').val();
            $('.update_vote').click(function() {
                var fk_criterial = $(this).parent().attr('id');
                $.ajax({
                    type: 'POST',
                    url: '<?php echo FULL_SITE_ROOT; ?>r3/cadre_evaluation/update_vote',
                    data: {user_id:""+user_id+"",today:""+today+"",fk_criterial:""+fk_criterial+""},
                    dataType: 'json',
                    success: function(data) {
                        if(typeof(data) != 'undefined' && parseInt(data) >0) 
                        {
                            alert('Đánh giá thành công!');
                            $('a strong','#' + fk_criterial).text(data);
                        }
                        else
                        {
                            alert('Bạn để đánh giá lần tiếp, Xin vui lòng đợ sau 30 giây');
                        }
                    }
                });
            });
        });
    </script>
</head>
<body>
    <div id="wrapper">
        <div id="header">
            <?php
            foreach ($VIEW_DATA['arr_single_staff'] as $item) 
            {
                $v_staff_name = isset($item['C_NAME'])? $item['C_NAME'] :'';
                $v_login_name = isset($item['C_LOGIN_NAME'])? $item['C_LOGIN_NAME'] :'';
            }
              
            ?>
            <div class="avatar-img">
                <?php 
                           $v_dir_img_staff_logo = CONST_DIRECT_R3_IMAGES . $v_login_name . '.png';
                            
                            //Anh logo mac dinh
                            $v_url_img_staff_logo = CONST_URL_R3_IMAGES . 'logo_default.jpg';

                            if (file_exists($v_dir_img_staff_logo)) {
                                $v_url_img_staff_logo = CONST_URL_R3_IMAGES . $v_login_name . '.png';
                            }
                ?>
                    <img src="<?php echo $v_url_img_staff_logo; ?>" width="100%" height="auto" />
                    <h3><?php echo $v_staff_name; ?></h3>
                </div>
            <h1 style="margin-top: 50px">Chọn biểu tượng bên dưới để đánh giá cán bộ </h1>
            <h1 style="font-family: Time New Roman;" >*&nbsp;*&nbsp;*</h1>
            
     
        </div><!-- end #header -->
        <div id="content" class="check-vote">
            <div id="list">
                <ul>
                    <?php
                        $arr_ressult_vote = isset($arr_ressult_vote) ? $arr_ressult_vote : array();
                        if(is_array($VIEW_DATA['CRITERIAL'])):
                            foreach($VIEW_DATA['CRITERIAL'] as $row):
                            
                    ?>
                        <li id="<?php echo $row['PK_LIST']; ?>">
                            <img src="<?php echo FULL_SITE_ROOT; ?>public/images/<?php echo $row['IMAGE_LINK']; ?>" class="update_vote" />
                            <a href="javascript:void(0)" class="update_vote"><?php echo $row['C_NAME']; ?>
                                <span >Số bình chọn hôm nay<br/><strong><?php echo isset($row['C_VOTE']) ? (((int)$row['C_VOTE'] >0)? $row['C_VOTE']: 0) :0; ?></strong></span>
                            </a>                            
                        </li>
                    <?php
                            endforeach;
                        endif;
                    ?>
                </ul>
            </div>     
            <?php 
                $v_user_id = isset($_REQUEST['id']) ? replace_bad_char($_REQUEST['id']) : '';
                $today = date('d-m-Y');
                echo hidden('user_id', $v_user_id);
                echo hidden('today', $today);
            ?>   
            <div class="box-button">
                <!--button back-->
                <button class="back" onclick="go_back();"> Quay lại
                </button>
            </div>
            <script>
                /**
                 * go_back
                 */
                function go_back() 
                {
                 window.location.href ="<?php echo FULL_SITE_ROOT.'r3/cadre_evaluation/'?>";   
                }
            </script>
            <!--end button back-->
        </div><!-- end #content -->
            <script>
                $(document).ready(function(){
                    load_size();
                     $(window).resize(
                     function()
                     {
                         $('#content.check-vote').removeAttr('style');
                        load_size();
                     });
                    
                    
                });
                function load_size()
                {
                    
                    width_content =  $('#content').outerWidth() - 20;// margin:  -lef:10  -right:10
                    
                    window_width  = $(window).width();
                    item_outwidth = $('#list li').last().outerWidth() + 20 || 0;
                    $('#content.check-vote').width(item_outwidth * 4);
                    $('#header').width(item_outwidth * 4 );
                    if(window_width < item_outwidth * 4)
                    {
                        $('#header').width(item_outwidth * 2);
                        $('#content.check-vote').width(item_outwidth * 2);
                    }
                    
                    if(window_width < item_outwidth * 2)
                    {
                        $('#header').width(item_outwidth );
                        $('#content.check-vote').width(item_outwidth);
                    }
                    
                     
                    
                }
            </script>
        <div id="footer">
            
        </div><!-- end #footer -->
    </div><!-- end #wrapper -->
</body>
</html>