<?php
if (!defined('SERVER_ROOT')) {
    exit('No direct script access allowed');
}
$this->template->title = 'Hướng dẫn thủ tục hành chính';
$this->template->view_data = $arr_all_guidance;
$this->template->template_is_metro = ""; // Thiết lập khóa thanh cuộn right; nếu không phải là template metro value =true else ''
$this->template->display('dsp_header.php');
$arr_all_guidance   =  isset($arr_all_guidance) ? $arr_all_guidance : array();
$v_spacing          =  ( _CONST_SPACING_ITEM > 0) ? _CONST_SPACING_ITEM : 10; // khoảng cách giữa các right item
$v_width_item       =  ( _CONST_WIDTH_ITEM  > 0)  ?  _CONST_WIDTH_ITEM   : 300; // Chiều dài item
$v_height_item      =  (_CONST_HEIHGT_ITEM > 0)   ?  _CONST_HEIHGT_ITEM  : 145; // Chiều cao item
?>
<style>
    <?php 
    $arr_set_color_item = hx_set_bgcolor_item();
    
    foreach ($arr_set_color_item as $key => $value)
    {
        echo "\n.$key{ background-color: $value !important;}\n";
    }
    ?>
</style>
<div id="wrapper" >
    <div id="centerWrapper">
        <div id="tileContainer"  class="">
            <img id="arrowLeft" class="navArrows" src="<?php echo SITE_ROOT; ?>apps/guidance/bootstrap/images/arrowLeft.png" onclick="javascript:$group.goLeft();" alt="left arrow" style="margin-left: 1510px; opacity: 0.5; display: none;">
            <img id="arrowRight" class="navArrows" src="<?php echo SITE_ROOT; ?>apps/guidance/bootstrap/images/arrowRight.png" onclick="javascript:$group.goRight();" alt="right arrow" style="margin-left: 622px; opacity: 0.5; display: inline;">
            <?php $v_count_title_group = 0; ?>
             <?php for ($i = 0; $i < count($arr_all_guidance); $i++):;?>
                <?php 
                    if(($i % 4) == 0)
                    {
                ?>
                    <a href="#" id="groupTitle<?php echo $v_count_title_group; ?>" class="groupTitle" style="margin-left: <?php echo ($v_count_title_group * 775);?>px; margin-top: 0px; display: block; opacity: 1;">
                        <h3>
                            <!--Tiêu đề của nhóm item-->
                        </h3>
                    </a>
                <?php
                        $v_count_title_group += 1;
                    }
                 ?>
            <?php endfor;?>
            <!--##########-->   
            <?php 
                $j             = 0; //Lấy kích thước margin-top, margin-lè cho mỗi item
                 $v_group = 0; // Thiet lap class group cho nhom item
            ?>
            <?php for ($i = 0; $i < count($arr_all_guidance); $i++):;?>
                <?php
                $j  += 1;
                $v_name = isset($arr_all_guidance[$i]['C_NAME']) ? $arr_all_guidance[$i]['C_NAME'] : '';
                $v_id = isset($arr_all_guidance[$i]['PK_LIST']) ? $arr_all_guidance[$i]['PK_LIST'] : 0;
                $v_group = (int)($i / 4);
                if($j == 1 OR $j == 3)
                {
                    $v_margin_left = $v_group * ($v_width_item + $v_spacing + 465);
                }
                else
                {
                    $v_margin_left = ($v_width_item + $v_spacing) + $v_group * ($v_width_item + $v_spacing + 465);
                }
                if((($i + 1) % 4) ==0 )
                {
                   $v_group  += 1;
                }
                ?>
                <a href="<?php echo $this->get_controller_url() . 'dsp_all_list_guidance/' . $v_id; ?>" 
                   class="tile align-center group<?php echo $v_group; ?> <?php echo ' item-'.$i; ?>" 
                   style="  margin-top: <?php echo ($j > 2) ? '200px' : '45px';?>; 
                            margin-left: <?php echo strval($v_margin_left).'px'; ?>; 
                            width:  <?php echo strval($v_width_item).'px';?>; 
                            height: <?php echo strval($v_height_item).'px'; ?>; 
                            background-color: rgb(80, 150, 1); 
                            display: block; 
                            opacity: 1; 
                            background-position: initial initial; 
                            background-repeat: initial initial;" 
                   data-pos="<?php echo (($j > 2) ? '200-' : '45-').strval($v_margin_left).'-'.strval($v_width_item); ?>"        
                   > 
                    <div class="container" style="background:#FFF;" onmouseover="javascript:$(this).css('background', '#00BFFF')" onmouseout="javascript:$(this).css('background', '#FFF')">
                        <h3 style="color:#11528f;font-weight: bold;font-family: arial" onmouseover="javascript:$(this).css('color', '#FFF')" onmouseout="javascript:$(this).css('color', '#11528f')">
                            <!--<img title="" alt="" style="margin-top:0px;margin-left:0px;" src="<?php echo SITE_ROOT; ?>apps/guidance/bootstrap/images/box_download_blue.png" height="60" width="60">-->
                            <?php echo $v_name; ?>
                        </h3>
                    </div>
                </a>
                 <!--end item-->
                 <?php  if($j == 4) {$j = 0;}; ?>
            <?php endfor; ?>
        </div>
        <!--End titleContainer-->
    <!--#######################################################-->
    </div>
</div>
<!--End #wrapper-->
<div id="catchScroll"></div>

<style>
    #content{margin-left:0px;}.sidebar-left{margin-left:0px;}		
</style>
<noscript>
&lt;style&gt;.sidebar&gt;*{position:relative;top:0;margin:5px !important;display:inline-block;}&lt;/style&gt;
</noscript>
<script>
                /* Fix height of sidebar for layout */
                sbDown = 0;
                $("#content, #panelContent").children('.sidebar').children(".tile").each(function() {
                    var thisDown = parseInt($(this).css("margin-top")) + $(this).height();
                    if (thisDown > sbDown) {
                        sbDown = thisDown;
                    }
                });
                $('#contentWrapper, .sidebar').css("min-height", sbDown + 20 + "px");


                /* Responsive sidebar position */
                $.plugin($toColumn, {
                    sidebarAfter: function() {
                        $("#content").children(".sidebar").appendTo("#centerWrapper").css("top", 20).css("margin-left", 0);
                    }
                });
                $.plugin($toSmall, {
                    sidebarBefore: function() {
                        $("#centerWrapper").children(".sidebar").prependTo("#content").css("top", 0).css("margin-left", $(".sidebar").css("width"));
                    }
                });
                $.plugin($toFull, {
                    sidebarBefore: function() {
                        $toSmall.sidebarBefore();
                    }
                });
                $.plugin($beforeSubPageShow, {
                    checkSidebar: function() {
                        switch ($page.layout) {
                            case "column":
                                $toColumn.sidebarAfter();
                                break;
                            case "small":
                                $toSmall.sidebarBefore();
                                break;
                            case "full":
                                $toSmall.sidebarBefore();
                                break;
                        }
                    }
                });
                $events.sidebarShow();
</script>
<?
$this->template->display('dsp_footer.php');
?>
