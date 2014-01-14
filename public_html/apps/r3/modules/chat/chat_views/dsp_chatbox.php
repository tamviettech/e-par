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
defined('DS') or die('direct access');
?>

<div class="chatbox">
    <h4 class="chat_header">
        <div ng-click="change_show_status()" style="width: 80%;float:left;">Chat</div>
        <div style="width: 20%;float:left;text-align:right">      
            <img src="<?php echo SITE_ROOT ?>public/chat_module/images/minus-white.png" 
                 width="12" height="12" 
                 ng-click="change_show_status()"/>
        </div>
    </h4>
    <div id="chat_body_list" class="chat_body" ng-show="get_show_status()" >
        <div class="chat_tools">
            <input style="width:100%;border:#c0c0c0 1px solid;height: 20px;" type="text" ng-model="chat_search_user" placeholder="Tìm theo tên"/>
        </div>
        <hr/>
        <div class="chat_list">
            <div ng-repeat="ou in user_list" ng-show="ou.users.length">
                <div ng-show="!chat_search_user">
                    <label ng-show="!ou.show_content" ng-click="change_ou_show_status(ou)">
                        <image 
                            src="<?php echo SITE_ROOT . 'public/chat_module/images/plus.png' ?>"
                            width="12" height="12"
                            />
                    </label>
                    <label ng-show="ou.show_content" ng-click="change_ou_show_status(ou)">
                        <image 
                            src="<?php echo SITE_ROOT . 'public/chat_module/images/minus.png' ?>"
                            width="12" height="12"
                            />
                    </label>
                    <label class="chat_ou">{{ou.C_NAME}}</label>
                </div>
                <div class="single_user" ng-repeat="user in ou.users | filter:chat_search_user" ng-show="ou.show_content">
                    <a href="javascript:;" ng-click="begin_chat(user)">
                        <span class="online_status {{get_online_status(user)}}" />
                        {{user.C_NAME}}
                    </a>
                </div>
            </div>
            <h1></h1>
            <h1></h1>
            <h1></h1>
            <h1></h1>

        </div>

    </div>
</div>


<div 
    class="chat_box" ng-class="'chat_box_'+$index"
    ng-repeat="box in chat_boxes" style="right:{{get_box_right_pos($index)}}px"
    >
    <h4 class="chat_header" ng-class='box.background'>
        <div style="float:left;width: 75%;overflow:hidden;">
            <label style="width:100%;overflow:hidden;height:30px;display:block;">
                <span class="online_status {{get_online_status(box.partner)}}" />
                {{box.partner.C_NAME}}
            </label>
        </div>
        <div style="float:left;width: 25%;overflow:hidden;height:30px;line-height:30px;text-align:right;">
            <img 
                src="<?php echo SITE_ROOT . 'public/chat_module/images/minus-white.png' ?>"
                width="12" height="12"
                ng-click="box.change_visible()"
                />
            &nbsp;
            <img 
                src="<?php echo SITE_ROOT . 'public/chat_module/images/close-white.png' ?>"
                width="12" height="12"
                ng-click="close_chat($index)"
                />
        </div>
    </h4>
    <div class="chat_tab_body" ng-show="box.visible">
        <div class="chat_msg">
            <p class="chat_more_msg">
                <span ng-click="show_more_msg(box)" ng-show="!box.old_msg_num">
                    ++ Hiển thị tin nhắn gần đây
                </span>
                <span ng-click="show_more_msg(box)" ng-show="box.old_msg_num">
                    ++ Hiển thị thêm tin nhắn
                </span>
            </p>
            <p ng-show="box.old_msg_num"><hr/></p>
            <p class="chat_row" ng-repeat="message in box.old_messages">
                <label class="chat_sender {{is_me(message.C_SENDER)}}">{{message.C_SENDER}}:</label>
                <span ng-bind-html-unsafe="message.C_MESSAGE"></span>
                <label class="chat_msg_time">{{message.C_TIME}}</label>
            </p>
            <p class="chat_more_msg" ng-click="box.old_messages = []; box.old_msg_num = 0;" ng-show="box.old_msg_num">
                -- Ẩn tin nhắn cũ
            </p>
            <p ng-show="box.old_msg_num"><hr/></p>
            <p class="chat_row" ng-repeat="message in box.messages">
                <label class="chat_sender {{is_me(message.C_SENDER)}}">{{message.C_SENDER}}:</label>
                <span ng-bind-html-unsafe="message.C_MESSAGE"></span>
                <label class="chat_msg_time">{{message.C_TIME}}</label>
            </p>
            <h2></h2>
        </div>
        <div class="chat_input">
            <form style="width:100%;overflow:hidden;" ng-submit="send_message(box)">
                <input 
                    type="text" ng-model="box.typing" 
                    placeholder="Nhấn phím Enter để gửi"
                    ng-change="event_typing(box)"
                    style="width:93%;padding:5px;3px;border: 1px solid #ccc"
                    />
            </form>
        </div>
    </div>
    <script>
    </script>
</div>

<script>
    $('.chat_list').slimScroll({
        height: '400px',
        railVisible: true,
        alwaysVisible: true
    });
</script>