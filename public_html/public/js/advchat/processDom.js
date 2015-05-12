 processDom = {
    //hien thi nguoi dang online
    online_user: function(user_id,user_ou,user_name) {
        var selector = '#div_chat #user_status_' + user_id + '_' + user_ou;
        //kiem tra neu ko phai tam viet
        if (user_ou != advChatCnf.user_ou)
        {
            var check_exist = $(selector).html();
            if (check_exist == null || check_exist == '' || check_exist == 'undefined')
            {
                this.create_new_tab(user_id, user_ou, user_name);
            }
        }
        $(selector).find('a span[class="user-offline"]').attr('class', 'user-online');
    },
    //tao tab moi neu NSD cho co
    create_new_tab: function(user_id, user_ou, user_name)
    {       
        //tao them tab
        var html = '';
        var selector_ul = '#chat-tab';
        var select_div_chat = '#div_chat_content';
        var selector_box_name_msg =  '#box-name-message';
        //tao li
        html += '<li id="user_status_'+user_id+'_'+user_ou+'" >';
        html += '<a href="#user_'+user_id+'_'+user_ou+'" data-user_id ="'+user_id+'" data-user_ou="'+user_ou+'">';
        html += '<span class="user-offline"></span>';
        html += '<i class="icon-user"></i>'
        html += user_ou + ': ' + user_name;
        html += '<button title="Gọi" class="btnCall"  onclick="call('+user_id+',\''+user_ou+'\')">'
        html += '</button>';
        html += '<button title="Dừng cuộc gọi" style="display: none;" class="btnEndCall" onclick="endCall('+user_id+',\''+user_ou+'\')">'
        html += '</button>'
        html += '</a>';
        html += '</li>';
        if(user_ou != advChatCnf.user_ou)
        {
            $((html)).insertAfter($(selector_ul + ' #TamViet_user'));
            $('#TamViet_user').show();
        }
        else
        {
            $(selector_ul).append(html);
        }
        //tao div content
        html  = '';
        html += '<div class="tab-pane " id="user_'+user_id+'_'+user_ou+'">';
//        html += '<div class="single-user-name"><b>'+user_ou + ': ' + user_name+'</b>';
        html += '</div></div>';
        $(select_div_chat).append(html);
        
        //gan event cho tab moi
        $(selector_ul).find(' #user_status_'+ user_id +'_'+ user_ou + ' a').click(function(e){
            e.preventDefault();
            var user_id = $(this).attr('data-user_id');
            var user_ou = $(this).attr('data-user_ou');
            $(this).tab('show');
            $(this).find('[class="_51jx"]').remove();
            $('.btnEndCall',$(this)).filter(function(index){
                    var v_display = $(this).css('display') ;
                    if(v_display == 'block' && advChatCnf.screenCapture != 1)
                    {
                        processDom.showVideo();
                    }
                    else
                    {   
                        //processDom.hideVideo();
                    }
                 }
             );
//          dinh ten nguoi chat vao tieu de
            var html = '<div class="'+user_id +'_'+ user_ou +'"><a href="javascript:void(0);" user_id-user_ou="'+user_id +'_'+ user_ou +'" onclick="processDom.select_tab_message(\''+ user_id +'\',\''+ user_ou +'\');">'
                    + user_name 
                    +'</a>'
                    + '<span class="close-user-chat" onclick="processDom.onclick_close_chat_tab(\''+ user_id +'\',\''+ user_ou + '\');" ><i class="icon-remove"></i></span>'
                    + '</div>';
            var count_selector = $('a[user_id-user_ou="'+user_id +'_'+ user_ou +'"]','#box-name-message').length;
            if(count_selector == 0)
            {
                $(selector_box_name_msg).append(html);
            }
//                Loai bo thong bao tin den chua xem
            $('._51jx',$(selector_box_name_msg + ' a[user_id-user_ou="'+ user_id +'_'+ user_ou +'"]')).remove();
             // them class hien thi nguoi dang chat
            $(selector_box_name_msg + ' div').filter('.active').removeClass('active')
            $(selector_box_name_msg + ' div').filter('.'+ user_id +'_'+ user_ou).addClass('active');
            processDom.fix_height_div_chat();
            processDom.select_chat_user(user_id,user_ou);
        });
    },
    //hien thi nguoi dang online
    offline_user: function(user_id,user_ou)
    {
            processDom.showBtnCall(user_id, user_ou);
            var selector = '#div_chat #user_status_' + user_id + '_' + user_ou;
            $(selector).find('a span[class="user-online"]').attr('class', 'user-offline');
    },
    //send message
    send_message: function()
    {
        //du lieu chung
        var message = $('#txt_message').val();
        var recieve_user = $('#hdn_recieve_user').val();
        var recieve_user_ou = $('#hdn_recieve_ou').val();

        var send_user    = advChatCnf.user_id;
        var send_user_ou = advChatCnf.user_ou;
        var send_user_name = advChatCnf.user_name;

        if (recieve_user == '' || recieve_user == null || typeof(recieve_user) == 'undefined')
        {
            alert('Bạn cần chọn người trò chuyện !!!');            
            return false;
        }
         //insert to database
        var url = $('#controller').val() + $('#hdn_insert_method').val();
        var data = {
                    mes: message,
                    recieve_user: recieve_user,
                    send_user: send_user,
                    send_user_ou: send_user_ou,
                    user_name: send_user_name,
                    recieve_user_ou: recieve_user_ou
                    };
        processDom.ajax_post_to_server(url,data);
        // Huong - chan nguoi offline
         var selector          = '#div_chat #user_status_' + recieve_user + '_' + recieve_user_ou + ' a';
         var v_status_offline  = $('.user-offline',$(selector)).length;
         if(v_status_offline > 0)
         {
             var v_ou = $(selector).attr('data-user_ou');
             if(v_ou != advChatCnf.user_ou)
             {
                alert('Xin lỗi bạn không thể gửi tin nhắn này khi người nhận offline!');
                return;
             }
             //Nếu là người khác server thì stop:
             
             processDom.recieve_message(message, recieve_user, send_user_name, send_user_ou);
             $('#txt_message').val('');
             return;
         }
         $('#txt_message').val('');
        //send message
        if (recieve_user_ou != advChatCnf.user_ou)
        {
            supportSocket.emit('send_message', {mes: message,
                recieve_user: recieve_user,
                send_user: send_user,
                send_user_ou: send_user_ou,
                user_name: send_user_name,
                recieve_user_ou: recieve_user_ou});
        }
        else
        {
            socket.emit('send_message', {mes: message, recieve_user: recieve_user, send_user: send_user,send_user_ou:send_user_ou, user_name: send_user_name});
        }
    },
    //chon nguoi noi chuyen
    select_chat_user: function(user_id,user_ou)
    {
        var selector = '#div_chat #user_' + user_id + '_' + user_ou;
        //scroll bar bottom
        var height = $(selector).height() - $('#div_chat_content').height();
        if (height > 0)
        {
            $('#div_chat_content').scrollTop(height);
        }
        $('#hdn_recieve_user').val(user_id);
        $('#hdn_recieve_ou').val(user_ou);
        $('.chat-input #txt_message').focus();

        processDom.change_mes_to_readed(user_id,user_ou,advChatCnf.user_id,advChatCnf.user_ou);
    },
    /**
     * nhan tin nhan
     * @param {type} message
     * @param {type} user
     * @param {type} user_name
     * @param {type} user_ou
     */
    recieve_message: function(message,user,user_name,user_ou) {
        var html = '';        
        //tao html
        html = '<div class="conversation">';
        html += '<a href="#" class="pull-left media-thumb"><img src="'+SITE_ROOT+'public/images/item-pic.png" width="34" height="34" alt="user"></a>';
        html += '<div class="conversation-body ">';
        html += '<h4 class="conversation-heading">' + user_name + ':</h4>';
        html += '<p>' + message + '</p>';
        html += '</div>';
        html += '</div>';
        
        //khai bao selector
        var selector_tab_content = '#div_chat #user_' + user + '_' + user_ou;
        var selector_li = '#div_chat #user_status_' + user + '_' + user_ou;
        var selector_recent = '#recent_user';
        var selector_box_name_msg =  '#box-name-message';

        //hien thi chat
        $(selector_tab_content).append(html);
        
        //scroll bar bottom
        var height = $(selector_tab_content).height() - $('#div_chat_content').height();
        if (height > 0)
        {
            $('#div_chat_content').scrollTop(height);
        }
        
        //them vao list recent
        $(selector_li).detach().insertAfter(selector_recent);        
        $(selector_recent).show();
        //hien thi tab neu chua co tab nao the hien
        var cur_chat_user = $('#hdn_recieve_user').val();
        if (cur_chat_user == '')
        {
            $(selector_tab_content).addClass('active');

            $(selector_li).addClass('active');
            var html = '<div class="'+user +'_'+ user_ou +' active">'
                            + '<a href="javascript:void(0);" user_id-user_ou="'+user +'_'+ user_ou +'" onclick="processDom.select_tab_message(\''+ user +'\',\''+ user_ou +'\');">'
                            + user_name 
                            +'</a>'
                            + '<span class="close-user-chat" onclick="processDom.onclick_close_chat_tab(\''+ user +'\',\''+ user_ou + '\');" ><i class="icon-remove"></i></span> '
                            + '</div>';
            var count_selector = $('a[user_id-user_ou="'+user +'_'+ user_ou +'"]',selector_box_name_msg).length;
            if(count_selector == 0)
            {
                $(selector_box_name_msg).append(html);
            }
             //change message to readed
             
            $('._51jx','#box-name-message '+ '.' + user + '_' + user_ou).remove();
            $('._51jx','#div_chat #user_status_'+ user + '_' + user_ou).remove();
            processDom.select_chat_user(user,user_ou);
            return;
        }        
        //kiem tra neu nguoi send ko phai la nguoi dag chat
        if ($(selector_li).attr('class') != 'active')
        {
            var no_mes                = $(selector_li).find('[class="_51jx"]').html();
            
            if (no_mes == '' || no_mes == null)
            {
                var html_mes = '<span class="_51jx">1</span>';
                $(selector_li).find('a').append(html_mes);
                $(selector_box_name_msg).find('a[user_id-user_ou="'+ user +'_'+ user_ou +'"]').append(html_mes);
                
                //Huong - hien thong bao tren thanh title top
                var selector_li_clone   = $(selector_li).clone(true);
                var data_user_id        = $(selector_li).find('a').attr('data-user_id');
                var data_user_ou        = $(selector_li).find('a').attr('data-user_ou');
                var data_user_name      = $(selector_li).find('a').text();
                
                var html = '<div class="'+user +'_'+ user_ou +'">'
                            + '<a href="javascript:void(0);" user_id-user_ou="'+user +'_'+ user_ou +'" onclick="processDom.select_tab_message(\''+ user +'\',\''+ user_ou +'\');">'
                            + user_name + html_mes
                            +'</a>'
                            + '<span class="close-user-chat" onclick="processDom.onclick_close_chat_tab(\''+ user +'\',\''+ user_ou + '\');" ><i class="icon-remove"></i></span> '
                            + '</div>';
                var count_selector = $('a[user_id-user_ou="'+user +'_'+ user_ou +'"]',selector_box_name_msg).length;
                if(count_selector == 0)
                {
                    $(selector_box_name_msg).append(html);
                }
                //get sound message                
                processDom.sound_message(1);
            }
            else
            {
                $('a[user_id-user_ou="'+ user +'_'+ user_ou +'"]',selector_box_name_msg).find('._51jx').remove();
                no_mes = parseInt(no_mes) + 1;
                $(selector_li).find('[class="_51jx"]').html(no_mes);                
                 //Huong                
                $(selector_box_name_msg).find('a[user_id-user_ou="'+ user +'_'+ user_ou +'"]').append($('<span class="_51jx">'+ no_mes +'</span>'));
            }
        }
        else //neu la nguoi dang chat chuyen tin nhan thanh da doc
        {                            
            processDom.change_mes_to_readed(user,user_ou,advChatCnf.user_id,advChatCnf.user_ou);
        }
    },
    //Hien thi do
    hideBtnCall: function(user_id,user_ou){
        
        var selector_call    = '#div_chat #user_status_' + user_id + '_'+user_ou+' .btnCall';
        var selector_endcall = '#div_chat #user_status_' + user_id + '_'+user_ou+'  .btnEndCall';
        var selector_call_to = '#div_chat #user_status_' + user_id + '_'+user_ou+'  .btnCallTo';
        $('#box-title-call').empty();
        $(selector_call_to).hide();
        $(selector_call).hide();
        $(selector_endcall).show();
        $('.btnCall').attr('disabled','disabled');
        
    },
    // hien thi xanh
    showBtnCall: function(user_id,user_ou){    
        var selector_call    = '#div_chat #user_status_' + user_id + '_'+user_ou+' .btnCall';
        var selector_endcall = '#div_chat #user_status_' + user_id + '_'+user_ou+' .btnEndCall';
        var selector_call_to = '#div_chat #user_status_' + user_id + '_'+user_ou+'  .btnCallTo';
        $('#box-title-call').empty();
        if(typeof(user_id) =="undefined" || typeof(user_ou) == 'undefined')
        {
            $('.btnCall').show();
            $('.btnEndCall').hide();
            $('.btnCallTo').hide();
        }
        else
        {
            $(selector_call_to).hide();
            $(selector_call).show();
            $(selector_endcall).hide();
        }
        $('.btnCall').removeAttr('disabled');
    },
    /**
     * change message to readed
     * @param {type} send_user_id
     * @param {type} send_user_ou
     * @param {type} recieve_user_id
     * @param {type} recieve_user_ou
     */
    change_mes_to_readed: function(send_user_id,send_user_ou,recieve_user_id,recieve_user_ou)
    {
        var url = $('#controller').val() + $('#hdn_readed_method').val();
        var data = {
                    recieve_user: recieve_user_id,
                    send_user: send_user_id,
                    send_user_ou: send_user_ou,
                    recieve_user_ou: recieve_user_ou
                    };
        
        processDom.ajax_post_to_server(url,data);
    },
    /**
     * post to server
     * @param {type} url
     * @param {type} data
     * @returns {Boolean}
     */
    ajax_post_to_server: function(url,data)
    {
        console.log(url);
        //kiem tra dieu kien url
        if(url == '' || url == null || typeof url == 'undefined')
        {
            return false;
        }
        //kiem tra dieu kien data
        if(typeof data != 'object' || typeof data == 'undefined')
        {
            return false;
        }
        
        $.ajax({
            type: "POST",
            url: url,
            data: data
        });
    },
            
    //Huong - Hien thi box goi video
    showVideo:function()
    {
        $('#div_chat #div_chat_content').css({width:'65%','float':'left'}); 
        $('#div_chat .show-video').css({width:'27%',display:'block'});
        
        var width_content_chat = $('#div_chat_content').width();
        $('#div_chat .chat-input textarea').width(width_content_chat + 40);
        $('#div_chat .chat-input textarea').css('border-right','solid 5px #ededed');
        
    },
            
    //Huong - Hien thi box goi video
    hideVideo:function()
    {
        $('#div_chat #div_chat_content').removeAttr('style');
        $('#div_chat .show-video').css({width:'0%',display:'none'});
        
        $('#div_chat .chat-input textarea').css('width','100%');
        $('#div_chat .chat-input textarea').css('border-right','0');
    },
            
    //Huong - show tab khi click vào thong bao trên tiêu đề tuong ung
    select_tab_message: function(user_id,user_ou)
    {   
        var v_active = $('#box-name-message a[user_id-user_ou="'+ user_id +'_'+ user_ou +'"]').hasClass('active');
        var selector_box_name_msg = '#box-name-message';
        if(v_active != true)
        {
            var selector_ul = '#chat-tab';
           //gan event cho tab moi
           $('li',$('#chat-tab')).removeAttr('class');
           $(selector_ul).find(' #user_status_'+ user_id +'_'+ user_ou +' a').trigger('click');

           $('._51jx',$(selector_box_name_msg + ' .'+ user_id +'_'+ user_ou)).remove();
           $('._51jx',$('#div_chat #user_status_'+ user_id +'_'+ user_ou )).remove();
           $('#txt_message').focus();
           
           // them class hien thi nguoi dang chat
            $(selector_box_name_msg + ' div').filter('.active').removeClass('active')
            $(selector_box_name_msg + ' div').filter('.'+ user_id +'_'+ user_ou).addClass('active');
        }
    },
    onclick_close_chat_tab: function(user_id,user_ou)
    {
        var selector_box_name_msg = '#box-name-message';
        //kiem tra tab colose is .active
        var v_count_active = $(selector_box_name_msg).find('.'+ user_id +'_'+ user_ou).filter('.active').length;        
        $(selector_box_name_msg).find('.'+ user_id +'_'+ user_ou).remove();

        if(v_count_active >0)
        {
            var user_id_user_ou = $(selector_box_name_msg).eq(0).find('a').attr('user_id-user_ou');
            
            if(typeof(user_id_user_ou) != 'undefined')
            {
                $('#chat-tab li#user_status_'+user_id_user_ou +' a').trigger('click');
            }
            else
            {
                $('#chat-tab li').removeClass();
                $('#hdn_recieve_user').val('');
                $('#hdn_recieve_ou').val('');
                
                $('.tab-pane ',$('#div_chat #div_chat_content')).removeClass('active');
            }
        }
       processDom.fix_height_div_chat();
              
    },
    sound_call_to:function(status) // am bao cuoc goi video
    {
        if(status == 1)
        {
            //goi
            document.getElementById('message_sound_viedeo').play();
        }
        else
        {
            document.getElementById('message_sound_viedeo').pause();
        }
    },
    sound_message: function(status) //am tin nhan
    {
        if(status == 1)
        {
            //goi
            document.getElementById('message_sound').play();
        }
        else
        {
            document.getElementById('message_sound').pause();
        }
    },
    /**
     * Hx- Kiem tra dang ton tai mot cuoc dam thoai video
     * @return '1' neu ton tai
     */
    have_a_conversation_video:function()
    {

        $('.btnEndCall','#div_chat').filter(
            function(index)
            {
               var v_display = $(this).css('display') ;
               if(v_display == 'block')
               {
                   return '1';
               }
            }
        );
        v_count          = $('[class="btnCallTo"]','#box-name-message').length;
        if(v_count > 0)
        {
            return '1';
        }
    },
    //fix height #chat_tab
    fix_height_div_chat: function()
    {
        var height_box_chat        = $('#box-chat').outerHeight() || 0;
        $('#chat-tab').height(height_box_chat);
    },
    //hien thi button trang thai dang co cuoc goi den
    has_call:function(user_id,user_ou)
    {
        var selector_call    = '#div_chat #user_status_' + user_id + '_'+user_ou+' .btnCall';
        var selector_endcall = '#div_chat #user_status_' + user_id + '_'+user_ou+' .btnEndCall';
        var selector_call_to = '#div_chat #user_status_' + user_id + '_'+user_ou+' .btnCallTo';

        $(selector_call).hide();
        $(selector_endcall).hide();
        $(selector_call_to).show();
    }


};

function onclick_show_message(args, user_id, user_ou)
{
    var curr_user_status = $('#chat-tab').find('[id="user_status_' + user_id + '"]');
    var curr_user_ou = $('#chat-tab').find('[id="user_status_' + user_id + '"]').find('a').attr('data-user_ou');
    // Xac dinh phan tu nhap click
    if (curr_user_ou == user_ou)
    {
        $('#chat-tab').find('[id="user_status_' + user_id + '"]').find('a').trigger('click');
        $(args).remove();
    }
}
