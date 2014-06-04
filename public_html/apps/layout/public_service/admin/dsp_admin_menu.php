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
<!--quan tri noi dung-->
<div id="menu_content">
    <table id="ctl00_ctl00_ContentPlaceHolderContent_AdminLeftMenu_dtlMenu" class="TableLeftMenu" cellspacing="0" align="Left" border="0" style="border-width:0px;width: 100%;border-collapse:collapse;">
        <?php if ( check_permission('XEM_DANH_SACH_CHUYEN_MUC',$this->app_name) 
                ): ?>
            <tr>
                <td class="<?php echo ($v_menu_active == 'category') ? 'LeftMenuActive' : 'LeftMenu';?>" data-name="category">
                    <div class="Content_menu">
                        <div class="Item">
                            <a href="<?php echo get_admin_controller_url('category'); ?>">
                                <span>Chuyên mục</span>
                            </a>
                        </div>
                    </div>
                </td>
            </tr>
        <?php endif; ?>
        <?php if ( check_permission('XEM_DANH_SACH_TIN_BAI',$this->app_name) 
                 ): ?>
            <tr>
                <td class="<?php echo ($v_menu_active == 'article') ? 'LeftMenuActive' : 'LeftMenu';?>" data-name="article">
                    <div class="Content_menu">

                        <div class="Item">
                            <a href="<?php echo get_admin_controller_url('article'); ?>">
                                <span>Tin bài</span>
                            </a>
                        </div>
                    </div>
                </td>
            </tr>
        <?php endif; ?>
                 <?php if ( check_permission('XEM_DANH_SACH_NOI_BAT',$this->app_name)
                ): ?>
            <tr>
                <td class="<?php echo ($v_menu_active == 'sticky') ? 'LeftMenuActive' : 'LeftMenu';?>" data-name="sticky">
                    <div class="Content_menu">
                        <div class="Item">
                            <a href="<?php echo get_admin_controller_url('sticky'); ?>">
                                <span>Tin bài nổi bật</span>
                            </a>
                        </div>
                    </div>
                </td>
            </tr>
        <?php endif; ?>
         <?php if ( check_permission('XEM_DANH_SACH_MEDIA',$this->app_name) 
                ): ?>
            <tr>
                <td class="<?php echo ($v_menu_active == 'advmedia') ? 'LeftMenuActive' : 'LeftMenu';?>" data-name="advmedia">
                    <div class="Content_menu">
                        <div class="Item">
                            <a href="<?php echo get_admin_controller_url('advmedia'); ?>">
                                <span>Media</span>
                            </a>
                        </div>
                    </div>
                </td>
            </tr>
        <?php endif; ?>
            
            <!--poll-->
        <?php if (  check_permission('XEM_DANH_SACH_CUOC_THAM_DO_Y_KIEN',$this->app_name)
                 ): ?>
       <tr>
           <td class="<?php echo ($v_menu_active == 'poll') ? 'LeftMenuActive' : 'LeftMenu';?>" data-name="poll">
               <div class="Content_menu">
                   <div class="Item">
                       <a href="<?php echo get_admin_controller_url('poll'); ?>">
                           <span>Thăm dò ý kiến</span>
                       </a>
                   </div>
               </div>
           </td>
       </tr>
   <?php endif; ?>
       <!--end poll-->
        
            <!--Weblink-->
        <?php if (  check_permission('XEM_DANH_SACH_WEBLINK',$this->app_name)
                 ): ?>
       <tr>
           <td class="<?php echo ($v_menu_active == 'weblink') ? 'LeftMenuActive' : 'LeftMenu';?>" data-name="weblink">
               <div class="Content_menu">
                   <div class="Item">
                       <a href="<?php echo get_admin_controller_url('weblink'); ?>">
                           <span>Link liên kết</span>
                       </a>
                   </div>
               </div>
           </td>
       </tr>
   <?php endif; ?>
       <!--end weblink-->
    </table>
</div>
<script>
    $(document).ready(function() 
    {
        $('#menu_content').css({'display':'block'});
    });
</script>