
function chat_box(partner) {
    this.partner = partner;
    this.visible = true;
    this.messages = [];
    this.typing = '';
    this.old_typing = '';
    this.background = 'blue'; //or orange
    this.version = 0;

    this.blink = false;
    this.interval = null;

    this.old_msg_num = 0;
    this.old_messages = [];

    this.change_visible = function() {
        this.visible = !this.visible;
    };
}


function chat_ctrl($scope, $http, $timeout) {
    $scope.chat_url = SITE_ROOT + 'r3/chat';

    $scope.user_list = [];
    $scope.http_timeout = 3000;
    $scope.show_chat = -1;
    $scope.my_chat_info = {};
    $scope.chat_boxes = [];

    //online list
    $scope.get_online_list = function() {
        $http({
            method: 'GET',
            timeout: $scope.http_timeout,
            cache: false,
            url: SITE_ROOT + 'index.php?url=r3/chat/svc_get_user_list&online=all&t=' + getTime()
        }).
                success(function(data) {
            for (i = 0; i < data.length; i++) {
                data[i]['show_content'] = true;
                if ($scope.user_list.length > 0) {
                    if (data[i].C_LOGIN_NAME != $scope.user_list[i].C_LOGIN_NAME) {
                        $scope.user_list = data;
                    } else {
                        $scope.user_list[i].C_IS_ONLINE = data[i].C_IS_ONLINE;
                        $scope.user_list[i].C_STATUS = data[i].C_STATUS;
                    }
                } else {
                    $scope.user_list = data;
                }

            }
            $scope.refresh_chat_connection();
            $timeout($scope.get_online_list, 2000);
        });
    }
    $scope.get_online_status = function(user) {
        if (user.C_IS_ONLINE == 0) {
            return 'offline';
        } else {
            return user.C_STATUS;
        }
    }
    $scope.show_more_msg = function(chat_box) {
        chat_box.old_msg_num += 10;
        var v_url = SITE_ROOT + 'index.php?url=r3/chat/svc_get_messages&type=all&status=read'
                + '&limit=' + chat_box.old_msg_num
                + '&partner_login=' + chat_box.partner.C_LOGIN_NAME
                + '&t=' + getTime();
        $http({method: 'GET', cache: false, url: v_url, timeout: $scope.http_timeout}).success(function(data) {
            chat_box.old_messages = data;
        });
    }

    //my chat info
    $scope.get_my_chat_info = function() {
        $http({
            method: 'GET',
            cache: false,
            timeout: $scope.http_timeout,
            url: SITE_ROOT + 'index.php?url=r3/chat/svc_my_chat_info' + '&t=' + getTime()
        }).
                success(function(data) {
            $scope.my_chat_info = data;
        });
        $timeout($scope.get_my_chat_info, 30000);
    }
    $scope.get_online_list();
    $scope.get_my_chat_info();

    $scope.get_show_status = function() {
        return $scope.show_chat == 1 ? true : false;
    }
    $scope.is_me = function(sender) {
        if ($scope.my_chat_info.C_LOGIN_NAME == sender)
            return 'is_me';
        else
            return false;
    }

    $scope.change_show_status = function() {
        $scope.show_chat = -$scope.show_chat;
    }
    $scope.change_ou_show_status = function(ou) {
        ou.show_content = ou.show_content == true ? false : true;
    }
    $scope.begin_chat = function(v_partner) {
        if (!v_partner) {
            return;
        }
        box_index = $scope.get_box_index_by_login(v_partner.C_LOGIN_NAME);
        if (box_index == -1) {
            $scope.chat_boxes.push(new chat_box(v_partner));
            var box_index = $scope.chat_boxes.length - 1;
            $scope.get_message($scope.chat_boxes[box_index]);
            $.ajax({
                type: 'get',
                cache: false,
                timeout: $scope.http_timeout,
                url: SITE_ROOT,
                data: {url: 'r3/chat/svc_add_connection', partner: v_partner.C_LOGIN_NAME}
            });
        }
        $timeout(function() {
            $('.chat_box_' + box_index).find('[type=text]').focus();
        });
    }

    $scope.refresh_chat_connection = function() {
        $http({
            method: 'GET',
            cache: false,
            timeout: $scope.http_timeout,
            url: SITE_ROOT + 'index.php?url=r3/chat/svc_my_chat_conn&t=' + getTime()}).
                success(function(json_data) {
            var i;
            for (i = 0; i < json_data.length; i++) {
                if (json_data[i].C_POSITION == 1)
                    var partner_login = json_data[i].C_2ND_USER;
                else
                    var partner_login = json_data[i].C_1ST_USER;
                box_index = $scope.get_box_index_by_login(partner_login);
                if (box_index == -1) {
                    $scope.begin_chat($scope.get_user_by_login(partner_login));
                    var box_index = $scope.chat_boxes.length - 1;
                    $scope.chat_boxes[box_index].version = json_data[i].C_REFRESH_VERSION;
                } else {
                    var chat_box = $scope.chat_boxes[$scope.get_box_index_by_login(partner_login)];
                    if ($scope.chat_boxes[box_index].version < parseInt(json_data[i].C_REFRESH_VERSION)) {
                        $scope.get_message($scope.chat_boxes[box_index]);
                        $scope.chat_boxes[box_index].version = parseInt(json_data[i].C_REFRESH_VERSION);
                    }
                }
            }

            count_chat_box = $scope.chat_boxes.length;
            var to_delete = [];
            for (i = 0; i < count_chat_box; i++) {
                br = false;
                var partner = $scope.chat_boxes[i].partner;
                var still_opened = false;
                for (j = 0; j < json_data.length && br == false; j++) {
                    if (json_data[j].C_POSITION == 1)
                        var partner_login = json_data[j].C_2ND_USER;
                    else
                        var partner_login = json_data[j].C_1ST_USER;

                    if (partner_login == partner.C_LOGIN_NAME)
                        still_opened = true;
                } //for j
                if (!still_opened) {
                    to_delete.push(partner.C_LOGIN_NAME);
                    br = true;
                }
            } //for i
            for (i = 0; i < to_delete.length; i++) {
                index = $scope.get_box_index_by_login(to_delete[i]);
                $scope.close_chat(index);
            }
        });
    }
    $scope.get_user_by_login = function(partner_login) {
        for (i = 0; i < $scope.user_list.length; i++) {
            var ou = $scope.user_list[i];
            for (j = 0; j < ou.users.length; j++)
            {
                user = ou.users[j];
                if (user.C_LOGIN_NAME == partner_login)
                {
                    return user;
                }
            }
        }
        return -1;
    };
    $scope.get_message = function(chat_box) {
        $http({
            method: 'GET',
            timeout: $scope.http_timeout,
            url: SITE_ROOT + 'index.php?url=r3/chat/svc_get_sess_message&partner_login=' + chat_box.partner.C_LOGIN_NAME + '&t=' + getTime()
        }).
                success(function(data) {
            chat_box.messages = data;
            $timeout(function() {
                chat_index = $scope.get_box_index_by_login(chat_box.partner.C_LOGIN_NAME);
                $('.chat_msg:eq(' + chat_index + ')').slimScroll({
                    height: '250px',
                    railVisible: true,
                    alwaysVisible: true
                }).slimScroll({scrollTo: '99999px'});
            });
        });
    }
    $scope.close_chat = function(index) {
        var partner = $scope.chat_boxes[index].partner;
        $.ajax({
            type: 'get',
            timeout: $scope.http_timeout,
            cache: false,
            url: SITE_ROOT,
            data: {url: 'r3/chat/svc_remove_connection',
                partner: partner.C_LOGIN_NAME}
        });
        $scope.chat_boxes.splice(index, 1);
    };
    $scope.send_message = function(chat_box) {
        if (!chat_box.typing) {
            return;
        }
        $.ajax({type: 'post'
                    , url: SITE_ROOT + 'index.php?url=r3/chat/send_message'
                    , cache: false
                    , timeout: $scope.http_timeout
                    , data: {message: chat_box.typing.toString(), receiver: chat_box.partner.C_LOGIN_NAME}
            , dataType: 'json'
                    , success: function(data) {
                if (!data)
                    return;
                if (typeof data != 'object')
                    return;
                if (typeof data.length == 'undefined')
                    return;
                for (i = 0; i < data.length; i++) {
                    chat_box.messages = data;
                    $timeout(function() {
                        chat_index = $scope.get_box_index_by_login(chat_box.partner.C_LOGIN_NAME);
                        $('.chat_msg:eq(' + chat_index + ')').slimScroll({
                            height: '250px',
                            railVisible: true,
                            alwaysVisible: true
                        }).slimScroll({scrollTo: '99999px'});
                    });
                }
            }
        });
        chat_box.typing = '';
    }


    $scope.get_box_index_by_login = function(partner_login) {
        var n = $scope.chat_boxes.length;
        for (i = 0; i < n; i++) {
            if ($scope.chat_boxes[i].partner.C_LOGIN_NAME == partner_login) {
                return i;
            }
        }
        return -1;
    }
    $scope.get_box_right_pos = function(index) {
        return $('.chatbox').width() + 10 + 260 * index;
    }
    $scope.event_typing = function(chat_box) {

    }
}