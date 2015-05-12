<?php
if (!defined('SERVER_ROOT'))
{
    exit('No direct script access allowed');
}
//header
//$this->template->title = get_system_config_value('unit_name');

//$this->template->display('dsp_header.php');
@session::init();
if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
{
    $ip = $_SERVER['HTTP_CLIENT_IP'];
}
elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
{
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
}
else
{
    $ip = $_SERVER['REMOTE_ADDR'];
}
?>
<div>
    <table cellpadding="0" cellspacing="0" width="100%" class="TableContent">
        <tr>
            <!--thong tin nguoi dung-->
            <td valign="top" class="LeftContent">
                <table width="100%" cellpadding="0" cellspacing="0" class="TableLoginInfo">
                    <tr>
                        <td class="Header_default" colspan="2">
                            &nbsp;<span><?php echo __('login information'); ?></span>
                        </td>
                    </tr>
                    <tr><td style="height:9px"></td></tr>
                    <tr>
                        <td class="LoginInfoLeftContent"></td>
                        <td class="LoginInfoRightContent">
                            <table width="100%" cellpadding="0" cellspacing="0" class="TableLoginInfoContent">
                                <tr>
                                    <td class="LoginInfo">
                                        <span><?php echo __('user'); ?></span>:
                                        <br>
                                        <span class="UserName"><?php echo session::get('user_name') ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="LoginInfo">
                                        <span><?php echo __('position'); ?></span>:
                                        <span><?php echo session::get('user_job_title'); ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="LoginInfo">
                                        <span><?php echo __('unit'); ?></span>:
                                        <span><?php echo session::get('ou_name'); ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="LoginInfo">
                                        <span><?php echo __('login times'); ?></span>:
                                        <span><?php echo session::get('time_to_join'); ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="LoginInfo">
                                        <span><?php echo __('ip address'); ?></span>:
                                        <span><?php echo $ip; ?></span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
            <td valign="top" class="RightContent">
                <!--div quan tri noi dung-->
                <div id="dashboard_content">
                    <table id="ctl00_ContentPlaceHolderContent_AdminLeftMenu_dtlMenu" class="TableLeftMenuOverview" cellspacing="0" align="Left" border="0" style="border-width:0px;width:180px;border-collapse:collapse;">
                        <tr>
                             <?php if ( check_permission('XEM_DANH_SACH_CHUYEN_MUC',$this->app_name) 
                                    ): ?>
                                <td class="LeftMenuOverview">
                                    <div class="Content">
                                        <div id="ctl00_ContentPlaceHolderContent_AdminLeftMenu_dtlMenu_ctl00_divRoleImage">
                                            <img alt="" onclick="javascript:window.location='<?php echo $this->get_controller_url('category'); ?>'" id="imgSubRole" style="cursor:pointer;" width="99" height="84" src="<?php echo SITE_ROOT . "public/images/CategoryLogo.gif" ?>">
                                        </div>
                                        <div class="Item">
                                            <a href="<?php echo $this->get_controller_url('category'); ?>">
                                                <span><?php echo __('category'); ?></span>
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            <?php endif; ?>
                           <?php if ( check_permission('XEM_DANH_SACH_TIN_BAI',$this->app_name) 
                                     ): ?>
                                <td class="LeftMenuOverview">
                                    <div class="Content">
                                        <div id="ctl00_ContentPlaceHolderContent_AdminLeftMenu_dtlMenu_ctl01_divRoleImage">
                                            <img alt="" onclick="javascript:window.location='<?php echo $this->get_controller_url('article'); ?>'" id="imgSubRole" style="cursor:pointer;" width="99" height="84" src="<?php echo SITE_ROOT . "public/images/ArticleLogo.gif" ?>">
                                        </div>
                                        <div class="Item">
                                            <a href="<?php echo $this->get_controller_url('article'); ?>">
                                                <span><?php echo __('article') ?></span>
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            <?php endif; ?>
                             <?php if ( check_permission('XEM_DANH_SACH_NOI_BAT',$this->app_name) 
                                ): ?>
                                <td class="LeftMenuOverview">
                                    <div class="Content">
                                        <div id="ctl00_ContentPlaceHolderContent_AdminLeftMenu_dtlMenu_ctl03_divRoleImage">
                                            <img alt="" onclick="javascript:window.location='<?php echo $this->get_controller_url('sticky'); ?>'" id="imgSubRole" style="cursor:pointer;" width="99" height="84" src="<?php echo SITE_ROOT . "public/images/Stickylogo.gif" ?>">
                                        </div>
                                        <div class="Item">
                                            <a href="<?php echo $this->get_controller_url('sticky'); ?>">
                                                <span><?php echo __('sticky'); ?></span>
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            <?php endif; ?>
                             <?php if ( check_permission('XEM_DANH_SACH_MEDIA',$this->app_name) 
                                    ): ?>
                                <td class="LeftMenuOverview">
                                    <div class="Content">
                                        <div id="ctl00_ContentPlaceHolderContent_AdminLeftMenu_dtlMenu_ctl09_divRoleImage">
                                            <img alt="" onclick="javascript:window.location='<?php echo $this->get_controller_url('advmedia'); ?>'" id="imgSubRole" style="cursor:pointer;" width="99" height="84" src="<?php echo SITE_ROOT . "public/images/MediaLogo.gif" ?>">
                                        </div>
                                        <div class="Item">
                                            <a href="<?php echo $this->get_controller_url('advmedia'); ?>">
                                                <span><?php echo __('media'); ?></span>
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            <?php endif; ?>
                            <?php if (check_permission('XEM_DANH_SACH_WEBLINK', $this->app_name) > 0): ?>
                                <td class="LeftMenuOverview">
                                    <div class="Content">
                                        <div id="ctl00_ContentPlaceHolderContent_AdminLeftMenu_dtlMenu_ctl09_divRoleImage">
                                            <img alt="" onclick="javascript:window.location='<?php echo $this->get_controller_url('weblink'); ?>'" id="imgSubRole" style="cursor:pointer;" width="99" height="84" src="<?php echo SITE_ROOT . "public/images/weblink.png" ?>">
                                        </div>
                                        <div class="Item">
                                            <a href="<?php echo $this->get_controller_url('weblink'); ?>">
                                                <span><?php echo __('weblink'); ?></span>
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            <?php endif; ?>
                        </tr>
                        <tr>
                            <tr>
                                <?php if (check_permission('XEM_DANH_SACH_USER','cores') > 0): ?>
                                    <td class="LeftMenuOverview">
                                        <div class="Content">
                                            <div id="ctl00_ContentPlaceHolderContent_AdminLeftMenu_dtlMenu_ctl08_divRoleImage">
                                                <img  alt="" 
                                                      onclick="javascript:window.open('<?php echo $this->get_controller_url('user','cores'); ?>','_blank')" 
                                                      id="imgSubRole" style="cursor:pointer;" width="99" height="84" 
                                                      src="<?php echo SITE_ROOT . "public/images/UserLogo.gif" ?>">
                                            </div>
                                            <div class="Item">
                                                <a  target="_blank" href="<?php echo $this->get_controller_url('user','cores'); ?>">
                                                    <span><?php echo __('user'); ?></span>
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                <?php endif; ?>
                                <?php if (check_permission('XEM_DANH_SACH_LISTTYPE', 'cores') > 0): ?>
                                    <td class="LeftMenuOverview">
                                        <div class="Content">
                                            <div id="ctl00_ContentPlaceHolderContent_AdminLeftMenu_dtlMenu_ctl09_divRoleImage">
                                                <img alt="" 
                                                     onclick="javascript:window.open('<?php echo $this->get_controller_url('xlist/dsp_all_listtype','cores'); ?>','_blank')" 
                                                     id="imgSubRole" 
                                                     style="cursor:pointer;" 
                                                     width="99" height="84" 
                                                     src="<?php echo SITE_ROOT . "public/images/ListStyleLogo.jpg" ?>">
                                            </div>
                                            <div class="Item">
                                                <a  target="_blank" href="<?php echo $this->get_controller_url('xlist/dsp_all_listtype','cores'); ?>">
                                                    <span><?php echo __('list type'); ?></span>
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                <?php endif; ?>
                                <?php if (check_permission('XEM_DANH_SACH_LIST', 'cores') > 0): ?>
                                    <td class="LeftMenuOverview">
                                        <div class="Content">
                                            <div id="ctl00_ContentPlaceHolderContent_AdminLeftMenu_dtlMenu_ctl09_divRoleImage">
                                                <img alt="" target="_blank" onclick="javascript:window.open('<?php echo $this->get_controller_url('xlist/dsp_all_list','cores'); ?>','_blank')" id="imgSubRole" style="cursor:pointer;" width="99" height="84" src="<?php echo SITE_ROOT . "public/images/ListLogo.png" ?>">
                                            </div>
                                            <div class="Item">
                                                <a  target="_blank" href="<?php echo $this->get_controller_url('xlist/dsp_all_list','cores'); ?>">
                                                    <span><?php echo __('list'); ?></span>
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                <?php endif; ?>
                                      <?php if (  check_permission('XEM_DANH_SACH_CUOC_THAM_DO_Y_KIEN',$this->app_name)
                                                 ): ?>
                                    <td class="LeftMenuOverview">
                                        <div class="Content">
                                            <div id="ctl00_ContentPlaceHolderContent_AdminLeftMenu_dtlMenu_ctl09_divRoleImage">
                                                <img alt="" target="_blank" onclick="javascript:window.open('<?php echo $this->get_controller_url('poll','public_service'); ?>','_blank')" id="imgSubRole" style="cursor:pointer;" width="99" height="84" src="<?php echo SITE_ROOT . "public/images/PollLogo.gif" ?>">
                                            </div>
                                            <div class="Item">
                                                <a  target="_blank" href="<?php echo $this->get_controller_url('poll','public_service'); ?>">
                                                    <span><?php echo __('poll'); ?></span>
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                <?php endif; ?>
                                   
                            </tr>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </td>
        </tr>
    </table>
</div>
<script>
    $(document).ready(function(){
        if($('#hdn_menu_select').attr('value')=='div_menu_content')
        {
            $('#dashboard_interactive').remove();
            $('#dashboard_system').remove();
            $('#dashboard_office').remove();
        }
        else if($('#hdn_menu_select').attr('value')=='div_menu_system')
        {
            $('#dashboard_content').remove();
            $('#dashboard_interactive').remove();
            $('#dashboard_office').remove();
        }
        else if($('#hdn_menu_select').attr('value')=='div_menu_interactive')
        {
            $('#dashboard_content').remove();
            $('#dashboard_system').remove();
            $('#dashboard_office').remove();
        }
        else if($('#hdn_menu_select').attr('value')=='div_office_manager')
        {
            $('#dashboard_content').remove();
            $('#dashboard_interactive').remove();    
            $('#dashboard_system').remove();
        }
    });    
</script>
