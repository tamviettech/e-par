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
if (!defined('SERVER_ROOT'))
{
    exit('No direct script access allowed');
}
defined('_CONST_TRA_CUU_LIEN_THONG_ROLE') or define('_CONST_TRA_CUU_LIEN_THONG_ROLE', '');
?>
<!DOCTYPE html>
<html lang="vi" dir="ltr" ng-app>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta http-equiv="Cache-Control" content="no-cache"/>
        <link rel="shortcut icon" href="<?php echo SITE_ROOT;?>favicon.ico" />
        <title><?php echo session::get('user_name'); ?>::<?php echo $this->eprint($this->title); ?></title>
        <link rel="stylesheet" href="<?php echo SITE_ROOT; ?>public/css/reset.css" type="text/css" media="screen" />
        <link rel="stylesheet" href="<?php echo SITE_ROOT; ?>public/css/text.css" type="text/css" media="screen" />
        <link rel="stylesheet" href="<?php echo SITE_ROOT; ?>public/css/1008_24_1_1.css" type="text/css" media="screen" />
        <link rel="stylesheet" href="<?php echo $this->stylesheet_url; ?>" type="text/css" media="screen" />
        <link rel="stylesheet" href="<?php echo SITE_ROOT; ?>public/chat_module/chat.css" type="text/css" media="screen" />

        <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery.min.js" type="text/javascript"></script>
        <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery-ui.min.js" type="text/javascript"></script>
        <link href="<?php echo SITE_ROOT; ?>public/js/jquery/jquery-ui.css" rel="stylesheet" type="text/css"/>
        <!--  Datepicker -->
        <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery.ui.datepicker-vi.js" type="text/javascript"></script>

        <!-- Right-click context menu -->
        <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery.contextMenu.js" type="text/javascript"></script>
        <!-- Upload -->
        <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery.MultiFile.pack.js" type="text/javascript"></script>
        <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery.blockUI.js" type="text/javascript"></script>
        <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery.MetaData.js" type="text/javascript"></script>
        <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery.slimscroll.min.js" type="text/javascript"></script>

        <?php $QS = check_htacces_file() ? '?' : '&';?>
        <script type="text/javascript">
            var SITE_ROOT = '<?php echo SITE_ROOT; ?>';
            var _CONST_LIST_DELIM = '<?php echo _CONST_LIST_DELIM; ?>';
            var QS = '<?php echo $QS;?>';
        </script>
        <script>
            $(document).ready(function(){
                //chống tràn menu
                ul = $('#roles > ul');
                child_width = 0;
                $(ul).find('> li:visible').each(function(){
                    if($(this).attr('id') == 'more_roles')
                        return;
                    if(child_width == 0){
                        child_width += $('#more_roles').outerWidth();
                        //child_width += 100;
                    }  
                    child_width += $(this).outerWidth();
                    if(child_width >= $(ul).width()){
                        var el = $(this).clone();
                        el.appendTo($('#more_roles > ul')).removeClass('active_role');
                        $(this).remove();
                        $('#more_roles').show();
                    }
                });
                $('#more_roles').mouseover(function(){
                    $('#more_roles > ul').show().css({top: 38, right: 0});
                }).mouseout(function(){
                    $('#more_roles > ul').hide();
                });
            });
        </script>
        <!--  Modal dialog -->
        <script src="<?php echo SITE_ROOT; ?>public/js/submodal.js" type="text/javascript"></script>
        <link href="<?php echo SITE_ROOT; ?>public/css/subModal.css" rel="stylesheet" type="text/css"/>

        <script src="<?php echo SITE_ROOT; ?>public/js/qm.js" type="text/javascript"></script>
        <link href="<?php echo SITE_ROOT; ?>public/css/qm.css" rel="stylesheet" type="text/css"/>

        <!-- Tooltip -->
        <script src="<?php echo SITE_ROOT; ?>public/js/overlib_mini.js" type="text/javascript"></script>

        <script src="<?php echo SITE_ROOT; ?>public/js/mylibs.js" type="text/javascript"></script>
        <script src="<?php echo SITE_ROOT; ?>public/js/DynamicFormHelper.js" type="text/javascript"></script>

        <script>
            function check_session_token() {          
                $.ajax({
                    type: 'get',
                    url: SITE_ROOT + 'cores/login/svc_check_session_token/<?php echo get_session_token() ?>',
                    cache: false,
                    dataType: 'json',
                    success: function(is_valid_token) {
                        if (!is_valid_token) {
                            window.location.reload();
                        }
                        setTimeout(check_session_token, 20000);
                    }
                });
            }

            $(document).ready(function() {
                setTimeout(function() {
                    var head = document.getElementsByTagName('head')[0];
                    var script = document.createElement('script');
                    script.type = 'text/javascript';
                    script.src = SITE_ROOT + 'public/js/angular.min.js';
                    head.appendChild(script);
                }, 1);
                check_session_token();
            });

        </script>
        <?php if (CHAT_MODULE > 0): ?>
            <script src="<?php echo SITE_ROOT ?>public/chat_module/chat_module.js"></script>
        <?php endif; ?>

        <?php if (isset($this->local_js)): ?>
            <script src="<?php echo $this->local_js; ?>" type="text/javascript"></script>
        <?php endif; ?>
    </head>
    <body>
        <DIV id=overDiv style="Z-INDEX: 10000; VISIBILITY: hidden; POSITION: absolute"></DIV>

        <div class="container_24" id="main" <?php echo defined('CHAT_MODULE') && CHAT_MODULE ? 'ng-controller="chat_ctrl"' : '' ?>> 
            <?php if (defined('CHAT_MODULE') && CHAT_MODULE): ?>
                <ng-include src="chat_url"></ng-include>
            <?php endif; ?>

            <div class="grid_24" id="banner">
                <label id="unit_full_name"><?php echo get_xml_value(simplexml_load_file(SERVER_ROOT . 'public/xml/xml_unit_info.xml'), '//full_name') ?></label>
            </div>
            <div class="grid_24 top-nav-box" id="header">
                <div id="user_info">
                        <ul id="menu-bar">
                            <li><?php echo VIEW::nav_home(); ?></li>
                            <?php if (check_permission('QUAN_TRI_DANH_MUC_LOAI_HO_SO', 'R3') OR check_permission('QUAN_TRI_QUY_TRINH_XU_LY_HO_SO', 'R3') OR check_permission('QUAN_TRI_LUAT_CAN_LOC_HO_SO', 'R3') OR Session::get('is_admin')): ?>
                                <li><a href="javascript:void(0)"><img src="<?php echo SITE_ROOT; ?>public/images/admin-32x32.png" border="0" width="28px" height="28px"/>Quản trị</a>
                                    <ul>
                                        <?php if (Session::get('is_admin')): ?>
                                            <li><a href="<?php echo SITE_ROOT . build_url('cores/xlist'); ?>"><img src="<?php echo SITE_ROOT; ?>public/images/item_configuration-16x16.png" border="0" /> &nbsp;Quản trị loại danh mục</a></li>
                                            <li><a href="<?php echo SITE_ROOT . build_url('cores/xlist/dsp_all_list'); ?>"><img src="<?php echo SITE_ROOT; ?>public/images/item_configuration-16x16.png" border="0" /> &nbsp;Quản trị đối tượng danh mục</a></li>
                                            <li><a href="<?php echo SITE_ROOT . build_url('cores/user'); ?>"><img src="<?php echo SITE_ROOT; ?>public/images/item_configuration-16x16.png" border="0" /> &nbsp;Quản trị NSD</a></li>
                                            <li><a href="<?php echo SITE_ROOT . build_url('cores/calendar'); ?>"><img src="<?php echo SITE_ROOT; ?>public/images/item_configuration-16x16.png" border="0" /> &nbsp;Quản trị ngày làm việc/ngày nghỉ</a></li>
                                            <li><a href="<?php echo SITE_ROOT . build_url('cores/application'); ?>"><img src="<?php echo SITE_ROOT; ?>public/images/item_configuration-16x16.png" border="0" /> &nbsp;Quản trị ứng dụng</a></li>
                                        <?php endif; ?>
                                        <?php if (check_permission('QUAN_TRI_DANH_MUC_LOAI_HO_SO', 'R3')): ?>
                                            <li><a href="<?php echo SITE_ROOT . build_url('r3/record_type'); ?>"><img src="<?php echo SITE_ROOT; ?>public/images/item_configuration-16x16.png" border="0" /> &nbsp;Quản trị danh mục loại hồ sơ</a></li>
                                        <?php endif; ?>
                                        <?php if (check_permission('QUAN_TRI_QUY_TRINH_XU_LY_HO_SO', 'R3')): ?>
                                            <li><a href="<?php echo SITE_ROOT . build_url('r3/workflow'); ?>"><img src="<?php echo SITE_ROOT; ?>public/images/item_configuration-16x16.png" border="0" /> &nbsp;Quản trị quy trình xử lý hồ sơ</a></li>
                                        <?php endif; ?>
                                        <?php if (check_permission('QUAN_TRI_LUAT_CAN_LOC_HO_SO', 'R3')): ?>
                                            <li><a href="<?php echo SITE_ROOT . build_url('r3/blacklist'); ?>"><img src="<?php echo SITE_ROOT; ?>public/images/item_configuration-16x16.png" border="0" /> &nbsp;Quản trị quy luật cản lọc hồ sơ</a></li>
                                        <?php endif; ?>
                                        <?php if (check_permission('THEO_DOI_NGUOI_DUNG', 'R3')): ?>
                                            <li><a href="<?php echo SITE_ROOT . build_url('r3/logistic'); ?>"><img src="<?php echo SITE_ROOT; ?>public/images/item_configuration-16x16.png" border="0" /> &nbsp;Theo dõi hoạt động người dùng</a></li>
                                        <?php endif; ?>
                                    </ul>
                                </li>
                            <?php endif; ?>
                        </ul>
                </div>    
                <div id="date"><?php echo jwDate::vn_day_of_week() . ', ' . date("d/m/Y"); ?>
                    <?php if (Session::get('login_name') !== NULL): ?>
                        <img src="<?php echo SITE_ROOT; ?>public/images/users.png" />
                        <label><?php echo Session::get('user_name'); ?> - <?php echo Session::get('user_job_title'); ?></label>
                        <label>(<a href="<?php echo SITE_ROOT . build_url('r3/mapping'); ?>">Bảng ánh xạ thủ tục</a>)</label>
                        <?php if (session::get('auth_by') != 'AD'): ?>
                            <?php $v_change_password_url = SITE_ROOT . build_url('cores/user/dsp_change_password'); ?>
                            <label>(<a href="javascript:void(0)" onclick="showPopWin('<?php echo $v_change_password_url; ?>', 500, 400, null);">Đổi mật khẩu</a>)</label>
                        <?php endif; ?>
                        <label>(<a href="<?php echo SITE_ROOT . 'logout.php'; ?>">Đăng thoát</a>)</label>
                    <?php endif; ?>
                </div>
            </div>
            <div class="grid_24">
                <div id="roles">
                    <ul>
                        <?php if (isset($this->arr_roles)): ?>
                            <?php foreach ($this->arr_roles as $key => $val): ?>
                                <?php if (check_permission($key, 'r3')): ?>
                                    <?php $v_class       = (strtolower($this->active_role) == strtolower($key)) ? ' class="active_role"' : ''; ?>
                                    <li <?php echo $v_class; ?> data-role="<?php echo $key; ?>" data-menu="1">
                                        <a href="<?php echo $this->controller_url . 'ho_so/' . strtolower($key); ?>"><?php echo $val; ?>
                                            <?php
                                            $arr_not_count = array(
                                                _CONST_TRA_CUU_ROLE
                                                , _CONST_TRA_CUU_LIEN_THONG_ROLE
                                                , _CONST_BAO_CAO_ROLE
                                                , _CONST_Y_KIEN_LANH_DAO_ROLE
                                                , _CONST_TRA_CUU_LIEN_THONG_ROLE
                                                , _CONST_TRA_CUU_TAI_XA_ROLE
                                                );
                                            ?>
                                            <?php if (!in_array(strtoupper($key), $arr_not_count)): ?>
                                                <span class="count">(0)</span>
                                            <?php endif; ?>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                            <li id="more_roles" style="display:none;">
                                <a href="javascript:;" title="Hiện thêm đối tượng"><?php echo htmlentities('>>') ?></a>
                                <ul style="display:none"></ul>
                            </li>
                            <script>
            function get_role_notice() {
                get_notice(SITE_ROOT+"<?php echo build_url('r3/notice/main') . '/' . $this->active_role; ?>");
            }
            $(function() {
        <?php if (!in_array(strtoupper($this->active_role), $arr_not_count)): ?>
                    get_role_notice();
                    setInterval(get_role_notice, <?php echo _CONST_GET_NEW_RECORD_NOTICE_INTERVAL; ?>);
        <?php endif; ?>

                count_processing_record_per_role();
                setInterval(count_processing_record_per_role, <?php echo _CONST_GET_NEW_RECORD_NOTICE_INTERVAL; ?>);
            });

            function count_processing_record_per_role()
            {
                q = 'li[data-menu="1"]';
                $(q).each(function(index) {
                    var v_role = $(this).attr('data-role');
                    if (v_role.toUpperCase() != '<?php echo _CONST_TRA_CUU_ROLE; ?>' && v_role.toUpperCase() != '<?php echo _CONST_BAO_CAO_ROLE; ?>' && v_role.toUpperCase() != '<?php echo _CONST_Y_KIEN_LANH_DAO_ROLE; ?>')
                    {
                        var v_url = '<?php echo $this->controller_url; ?>' + 'count_processing_record_by_role/' + v_role + '/' + QS + 't=' + getTime();
                        $.ajax({
                            cache: false,
                            url: v_url,
                            dataType: 'json',
                            success: function(data) {
                                count = data.count;
                                role = data.role;
                                rq = 'li[data-role="' + role + '"] span[class="count"]';
                                $(rq).html(' (' + count + ')');
                            }
                        });
                    }
                });
            }

                            </script>
                        <?php endif; ?>
                    </ul>
                </div><!--roles-->
            </div>
            <?php if ($this->show_left_side_bar): ?>
                <div class="clear">&nbsp;</div>
                <div class="container_24" id="wrapper" >
                    <div class="grid_5">
                        <div class="edit-box" id="left_side_bar">
                            <div style="width: 96%; padding: 4px;">
                                <?php if (isset($this->activity_filter)): ?>
                                    <div class="menuLeft" id="menuLeft">
                                        <ul>
                                            <?php if (in_array(strtoupper($this->active_role), array(_CONST_TRA_CUU_LIEN_THONG_ROLE, _CONST_TRA_CUU_TAI_XA_ROLE))): ?>
                                                <div class="menuLeft-left">
                                                    <li class="menuLeft-item">
                                                        <select id="sel_village" onchange="sel_tra_cuu_xa_onchange(this.value)" style="max-width:145px;">
                                                            <option value="-1">
                                                                --Tất cả xã--
                                                            </option>
                                                            <?php $v_village_id = (int) get_request_var('village') ?>
                                                            <?php foreach ($this->arr_villages as $village): ?>
                                                                <?php $v_selected = ($v_village_id == $village['PK_OU']) ? 'selected' : ''; ?>
                                                                <option value="<?php echo $village['PK_OU'] ?>" <?php echo $v_selected ?>>
                                                                    <?php echo $village['C_NAME'] ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                        <script>
                                                            function sel_tra_cuu_xa_onchange(value)
                                                            {
                                                                controller = '<?php echo $this->controller_url ?>';
                                                                active_role = '<?php echo $this->active_role ?>';
                                                                tt = '<?php echo (int) get_request_var('tt') ?>';
                                                                village_id = value;
                                                                url = controller + 'ho_so/' + active_role + '/' + QS + 'tt=' + tt + '&village=' + village_id;
                                                                window.location.href = url;
                                                            }
                                                        </script>
                                                    </li>
                                                </div>
                                            <?php endif; ?>
                                            <?php foreach ($this->activity_filter as $key => $val): ?>
                                                <div class="menuLeft-left">
                                                    <?php $v_village_id = (int) get_request_var('village') ?>
                                                    <li class="menuLeft-item<?php echo ($_GET['tt'] == $key) ? ' menuLeft-item-mark' : ''; ?>">
                                                        <?php
                                                        $url          = $this->controller_url . 'ho_so/' . $this->active_role . '/' . $QS . 'tt=' . $key;
                                                        if ($v_village_id)
                                                        {
                                                            $url .= '&village=' . $v_village_id;
                                                        }
                                                        ?>
                                                        <a href="<?php echo $url ?>">
                                                            <?php echo $val ?>
                                                        </a>
                                                        (<span class="activity-num" data-activity="<?php echo $key; ?>"></span>)
                                                    </li>
                                                </div>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
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
                                                    $('#menuLeft .activity-num').each(function(index) {
                                                        v_activity = $(this).attr('data-activity');
                                                        $(this).html(json_data[v_activity]);
                                                    });
                                                }
                                            });
                                        });
                                    </script>
                                <?php endif; ?>

                                <!-- Menu Bao cao -->
                                <?php if (isset($this->arr_all_report_type)): ?>
                                    <div id="report-menu" class="menuLeft">
                                        <ul class="report-menu">
                                            <?php foreach ($this->arr_all_report_type as $v_code => $v_name): ?>
                                                <li <?php echo (strval($v_code) == strval($this->current_report_type)) ? ' class="current"' : ''; ?>>
                                                    <a href="<?php echo $this->controller_url; ?>option/<?php echo $v_code; ?>"><?php echo $v_name; ?></a>
                                                </li>
                                            <?php endforeach; ?>
                                            <?php
                                            $is_admin       = Session::get('is_admin');
                                            $has_permission = check_permission('QUAN_TRI_SO_THEO_DOI_HO_SO', 'R3');
                                            ?>
                                            <?php if ($is_admin OR $has_permission): ?>
                                                <li>  
                                                    <a href="<?php echo $this->reportbook_url; ?>">Sổ theo dõi hồ sơ</a>
                                                </li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="grid_19" id="content_right" >
                    <?php else: ?>
                        <div class="grid_24" id="content_right">
                        <?php endif; ?>