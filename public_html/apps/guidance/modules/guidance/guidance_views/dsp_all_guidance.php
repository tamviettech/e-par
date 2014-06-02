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
if (!defined('SERVER_ROOT')) {
    exit('No direct script access allowed');
}

$this->template->title      = 'Hướng dẫn thủ tục hành chính';
$arr_all_guidance   =  isset($arr_all_guidance) ? $arr_all_guidance : array();
$this->template->view_data = $arr_all_guidance;

$this->template->is_metro   = true;
$this->template->display('dsp_header.php');
$v_spacing          =  ( _CONST_SPACING_ITEM > 0) ? _CONST_SPACING_ITEM : 10; // khoảng cách giữa các right item
$v_width_item       =  ( _CONST_WIDTH_ITEM  > 0)  ?  _CONST_WIDTH_ITEM   : 300; // Chiều dài item
$v_height_item      =  (_CONST_HEIHGT_ITEM > 0)   ?  _CONST_HEIHGT_ITEM  : 145; // Chiều cao item
$arr_bg_color       =  hx_set_bgcolor_item();
$arr_bg_color       = is_array($arr_bg_color) ? $arr_bg_color : array();

?>

<div id="wrapper" >
    <div id="centerWrapper">
        <div id="tileContainer"  class="">
            <img id="arrowLeft" class="navArrows" src="<?php echo SITE_ROOT; ?>apps/guidance/bootstrap/images/arrowLeft.png" onclick="javascript:$group.goLeft();" alt="left arrow" style="margin-left: 1510px; opacity: 0.5; display: none;">
            <img id="arrowRight" class="navArrows" src="<?php echo SITE_ROOT; ?>apps/guidance/bootstrap/images/arrowRight.png" onclick="javascript:$group.goRight();" alt="right arrow" style="margin-left: 622px; opacity: 0.5; display: inline;">
            <!--##########-->   
            <?php $v_group = 0; ?>
            <?php for ($i = 0; $i < count($arr_all_guidance); $i++):; ?>
                <?php
                if (($i % 4) == 0) {
                    $v_group = (int) ($i / 4);
                    ?>
                    <a href="#&amp;group<?php echo $v_group; ?>" id="groupTitle<?php echo $v_group; ?>" 
                       class="groupTitle" style="margin-left: <?php echo ($v_group * 775); ?>px; margin-top: 0px; opacity: 1; display: block;" 
                       onclick="javascript:$group.goTo(<?php echo $v_group; ?>);">
                    </a>
                    <!--End group-->
                <?php } ?>
                    
            <?php endfor; ?>

            
            <?php
            $j = 0; //Lấy kích thước margin-top, margin-lè cho mỗi item
            $v_group = 0; // Thiet lap class group cho nhom item
            ?>
            <?php for ($i = 0; $i < count($arr_all_guidance); $i++):; ?>
                <?php
                $j += 1;
                $v_name = isset($arr_all_guidance[$i]['C_NAME']) ? $arr_all_guidance[$i]['C_NAME'] : '';
                $v_id = isset($arr_all_guidance[$i]['PK_LIST']) ? $arr_all_guidance[$i]['PK_LIST'] : 0;
                $v_code     = isset($arr_all_guidance[$i]['C_CODE']) ? $arr_all_guidance[$i]['C_CODE'] : '';

                $v_curr_group = (int) ($i / 4);
                if ($j == 1 OR $j == 3) {
                    $v_margin_left = $v_curr_group * ($v_width_item + $v_spacing + 465);
                } else {
                    $v_margin_left = ($v_width_item + $v_spacing) + $v_curr_group * ($v_width_item + $v_spacing + 465);
                }



                if (($i  % 4) == 0) 
                {
                    $v_group = $i / 4;
                }
                ?>
                <a href="<?php echo $this->get_controller_url() . 'dsp_all_list_guidance/' . $v_id; ?>" 
                   class="tile align-center group<?php echo $v_group; ?> <?php echo ' item-' . $i; ?>" 
                   style="  margin-top: <?php echo ($j > 2) ? '200px' : '45px'; ?>; 
                   margin-left: <?php echo strval($v_margin_left) . 'px'; ?>; 
                   width:  <?php echo strval($v_width_item) . 'px'; ?>; 
                   height: <?php echo strval($v_height_item) . 'px'; ?>; 
                   background-color: <?php echo $arr_bg_color['item-'.$i]; ?>; 
                   display: block; 
                   opacity: 1; 
                   background-position: initial initial; 
                   background-repeat: initial initial;" 
                   data-pos="<?php echo (($j > 2) ? '200-' : '45-') . strval($v_margin_left) . '-' . strval($v_width_item); ?>"        
                   > 
                     <?php 
                        $arr_logo_item      = is_array(hx_set_metro_logo_item()) ? hx_set_metro_logo_item() : array();
                        $v_url_logo_image   = '';
                    
                        if(isset($arr_logo_item[$v_code])  && trim($arr_logo_item[$v_code]) !='')
                        {
                            $v_file_path_logo   = $this->get_url_image_directory . trim($arr_logo_item[$v_code]);
                            if(file_exists($v_file_path_logo))
                            {
                                $v_url_logo_image = $this->get_url_image . trim($arr_logo_item[$v_code]);
                            }
                        }
                    ?>
                     <?php if($v_url_logo_image == ''): ?>
                    <div class="container" style="background:#FFF;" onmouseover="javascript:$(this).css('background', '#00BFFF')" onmouseout="javascript:$(this).css('background', '#FFF')">
                        <h3 style="color:#11528f;font-weight: bold;font-family: arial" onmouseover="javascript:$(this).css('color', '#FFF')" onmouseout="javascript:$(this).css('color', '#11528f')">
                             <?php echo $v_name; ?>
                        </h3>
                    </div>
                    <?php  else: ?> 
                        <img src="<?php echo $v_url_logo_image; ?>"  alt="<?php echo $v_name; ?>" style="width: 100%;height: 100%">
                    <?php endif;?>
                </a>
                <!--end item-->
    <?php  if ($j == 4) {$j = 0;};  ?>
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
