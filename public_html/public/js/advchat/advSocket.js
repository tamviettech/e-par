//danh sach nhung nguoi dang online
socket.on('online_list', function(data) {
    
    var list_user_online = data.list_user_online;
    if (list_user_online != '')
    {
        var arr_user_online = list_user_online.split(',');
        $.each(arr_user_online, function(index, value) {
            processDom.online_user(value,advChatCnf.user_ou);
        });
    }
});

//dang ky online
socket.emit('chat_reg', {id: advChatCnf.user_id, name: advChatCnf.user_name});
//hien thi NSD vừa online
socket.on('online_user', function(user_online) {
    var user_id = user_online.user;
    processDom.online_user(user_id,advChatCnf.user_ou);
});
//mat ket noi vs NSD
socket.on('disconnect_user', function(user) {
    var user_id = user.user;
    processDom.offline_user(user_id,advChatCnf.user_ou);
});
//recieve_message
socket.on('recieve_message', function(data) {
    //khai bao bien
    var message = data.mes;
    var user = data.user;
    var user_name = data.user_name;
    processDom.recieve_message(message, user, user_name,advChatCnf.user_ou);
});