//dang ky online len tam viet
supportSocket.emit('support_reg', {id: advChatCnf.user_id, name: advChatCnf.user_name, ou: advChatCnf.user_ou});
//lay list online support
supportSocket.on('online_list', function(data) {
    for(var user_ou in data)
    {
        for(var user_id in data[user_ou])
        {
            if(typeof user_id == 'undefined' || user_ou == 'undefined' || (user_id == advChatCnf.user_id && user_ou == advChatCnf.user_ou))
            {
                continue;
            }
            
            var user_name = data[user_ou][user_id];
            processDom.online_user(user_id,user_ou,user_name);
        }
    }
});
supportSocket.on('online_user',function(data){
   var user_id   =  data.user;
   var user_ou   =  data.user_ou;
   var user_name =  data.user_name;
   
   processDom.online_user(user_id,user_ou,user_name);
});
//nhan tin nhan tu suppor
supportSocket.on('recieve_message', function(data) {
    //khai bao bien
    var message   = data.mes;
    var user      = data.user;
    var user_name = data.user_name;
    var user_ou   = data.user_ou;
    if(typeof(data) != 'undefined')
    {
        var url = SITE_ROOT + 'r3/advchat/do_insert_mes';
        $.ajax({
                type: 'POST',
                url: url,
                data: {
                        mes: data.mes,
                        status:1,
                        recieve_user: advChatCnf.user_id,
                        recieve_user_ou: advChatCnf.user_ou,
                        send_user:data.user,
                        send_user_ou:data.user_ou,
                        user_name:data.user_name
                       },
                success: function(result)
                {
                    if(parseInt(result) == 0)
                    {
                        processDom.recieve_message(message, user, user_name, user_ou);
                    }
                }
        });
    }           
    
});

//disconnect user
supportSocket.on('disconnect_user',function(data){
    var user_id = data.user_id;
    var user_ou = data.user_ou;
    processDom.offline_user(user_id,user_ou);
});


/**
 * Socket to rtcVideo
 */
//nhan webrtc_offer tu may 2
supportSocket.on('webrtc_offer',function(data)
{
    advChatCnf.constructor();
    var send_user_id    = data.send_user_id;
    var send_user_ou    = data.send_user_ou;
    var screen_capture  = (typeof data.screen_capture != 'undefined')?data.screen_capture:0;
    
    //Huong - Hien thi thong bao co nguoi goi den va am thanh
    var send_user_name  = data.send_user_name;
    processDom.has_call(send_user_id, send_user_ou);    
    processDom.sound_call_to(1);

     if(confirm('Bạn muốn nhận cuộc gọi !!!'))
    {
        var remote_offerSDP = data.offerSDP;
        
        // Hien thi dang goi
        processDom.hideBtnCall(send_user_id, send_user_ou);
        
        processDom.select_chat_user(send_user_id,send_user_ou);
        
        
        peer.setRemoteDescription(new RTCSessionDescription(remote_offerSDP));//luu thong tin offer cuar may truyen de
        
        if(screen_capture == 1)
        {
            advChatCnf.screenCapture = screen_capture;
            peer.createAnswer(function(offerSDP) {
                        peer.setLocalDescription(offerSDP);
                        data.offerSDP = offerSDP;
                        supportSocket.emit('webrtc_answer',data);
                    }, onfailure, {optional: [],mandatory: {}});
        }
        else
        {
            navigator.getUserMedia(advChatCnf.streamConstraints,function(stream){
            peer.addStream(stream);
            advChatCnf.myVideo.src = window.URL.createObjectURL(stream);
            window.stream = stream;
            
            peer.createAnswer(function(offerSDP) {
                        peer.setLocalDescription(offerSDP);
                        data.offerSDP = offerSDP;
                        supportSocket.emit('webrtc_answer',data);
                    }, onfailure, advChatCnf.sdpConstraints);
            },OnMediaError);
            // hien ten nguoi dam thoai den va di
            var myname = $('#div_chat #user_status_' + send_user_id + '_'+send_user_ou + ' a').text(); 
            $('#remote-name').html(myname);        
            $('#my-name').html(advChatCnf.user_name);
             //Hien thi box video va dung am thanh thong bao
            processDom.showVideo();  
            processDom.sound_call_to(0);
        }
    }
    else
    {
        endCall(send_user_id,send_user_ou);
        return;
    }
});

//nhan cau tra loi
supportSocket.on('webrtc_answer',function(data){
    var user_id = data.recieve_user_id;
    var user_ou = data.recieve_user_ou;
    
    //Huong- xoa thong bao cho goi  den, dung am thanh thong bao
    var send_user_id = data.send_user_id;
    var send_user_ou = data.send_user_ou;
    $('#box-name-message a[user_id-user_ou="'+ send_user_id +'_'+ send_user_ou +'"]').remove();
    processDom.hideBtnCall(user_id, user_ou);
    processDom.sound_call_to(0);
    
    if(advChatCnf.screenCapture != 1)
    {
        processDom.showVideo();
    }
    
    
    var offerSDP = data.offerSDP;
    peer.setRemoteDescription(new RTCSessionDescription({sdp: offerSDP.sdp, type: offerSDP.type}));//luu thong tin offer cuar may truyen de
});

//nhan icecandidate 
supportSocket.on('webrtc_icecandidate',function(data){
    var candidate = data.candidate;
    if(typeof candidate != 'undefined')
    {
         peer.addIceCandidate(new RTCIceCandidate(candidate));
    }
});

/**
 * su dung cho video
 */
//call video
function call(user_id,user_ou)
{
    advChatCnf.constructor();
    // Huong - chan goi nguoi offline
    var selector          = '#div_chat #user_status_' + user_id + '_' + user_ou + ' a';
    var v_status_offline  = $(selector).find('[class="user-online"]').attr('class');
    
    if(typeof(v_status_offline) == 'undefined')
    {
        alert('Xin lỗi! Bạn chỉ có thể goi video cho những người đang online.');
        return;
    }        
    if( processDom.have_a_conversation_video() == '1')
    {
        alert('Đang tồn tại một cuộc trò chuyện khác! Bạn cần hủy cuộc gọi trước đó trước khi thực hiện một cuộc gọi mới');
        return;
    }
    //Dong cuoc goi truoc
    processDom.showBtnCall();   
    if(confirm('Bạn muốn kết nối bằng Video'))
    {
        //đánh dấu người call video
        processDom.select_chat_user(user_id,user_ou);
        processDom.hideBtnCall(user_id,user_ou);
       
        var send_user_id = advChatCnf.user_id;
        var send_user_ou = advChatCnf.user_ou;
        
        var recieve_user_id = user_id;
        var recieve_user_ou = user_ou;
        
        console.log(advChatCnf.streamConstraints);
        navigator.getUserMedia(advChatCnf.streamConstraints,function(stream){
            advChatCnf.myVideo.src = window.URL.createObjectURL(stream);
            
            peer.addStream(stream);
            window.stream = stream;
            
            peer.createOffer(function(offerSDP) {
                    peer.setLocalDescription(offerSDP);
                    supportSocket.emit('webrtc_offer',{
                                                send_user_id: send_user_id,
                                                send_user_ou: send_user_ou,
                                                recieve_user_id: recieve_user_id,
                                                recieve_user_ou: recieve_user_ou,
                                                offerSDP: offerSDP});
                }, onfailure, advChatCnf.sdpConstraints);
        },OnMediaError);
        
        var send_user_name = advChatCnf.user_name;
        //Huong dang dam thoai
        processDom.sound_call_to(1);
        //Huong- Hien thi co nguoi goi den tren titel
        var html_mes    = '<span class="btnCallTo"></span>';
        var html        = '<div class="show-call">'
                        +' <a href="javascript:void(0);" user_id-user_ou="'+send_user_id +'_'+ send_user_ou +'">'+ send_user_name + '</a>'
                        + html_mes +'</div>';        
        $('#box-title-call').append(html);
        
        // hien ten nguoi dam thoai den va di
        var myname = $('#div_chat #user_status_' + user_id + '_'+user_ou + ' a').text(); 
        $('#remote-name').html(myname);        
        $('#my-name').html(advChatCnf.user_name);   
    }
}
//hiển thị lỗi
function onfailure()
{
    console.log('false')
}
//hien thi loi khi addstream
function OnMediaError(error) {
    if(error.name != '' || typeof(error.name) == 'undefined')
    {
        $('#box-title-call').empty();
        //Huong- Dung am thanh thong bao
        $('#remote-name').empty();        
        $('#my-name').empty();
        processDom.sound_call_to(0);
        processDom.showBtnCall();
        processDom.hideVideo();// an div hien video
        //
        alert('Bạn cần cho phép sử dụng camera');
        
        window.stream.stop();
        peer.close();

        delete peer;
        peer = new RTCPeerConnection(advChatCnf.IceServer);
    }
}
//dung cuoi goi
function endCall(user_id,user_ou)
{
    //Huong 
    $('#box-title-call').empty();
    $('#remote-name').empty();        
    $('#my-name').empty();
    
    processDom.hideVideo();
    processDom.sound_call_to(0);
    processDom.showBtnCall(user_id,user_ou);
    advChatCnf.remoteVideo.src = '';
    advChatCnf.myVideo.src = '';
    advChatCnf.screenCapture = 0;
     
    if(typeof window.stream != 'undefined')
    {
        window.stream.stop();
    }
    $('#screenCapture').hide();
    peer.close();
    
    delete peer;
    peer = new RTCPeerConnection(advChatCnf.IceServer);
}
/**
 * kiem tra thiet bi 
 * @param {type} sourceInfos
 * @returns {undefined}
 */
function gotSources(sourceInfos)
{
    for(var i = 0; i < sourceInfos.length ;i++)
    {
        //kiem tra thiet bi audio
        if(sourceInfos[i].kind === 'audio')
        {
            advChatCnf.sdpConstraints.mandatory.OfferToReceiveAudio = true;
            advChatCnf.streamConstraints.audio = true;
        }
        
        //kiem tra thiet bi video
        if(sourceInfos[i].kind === 'video')
        {
            advChatCnf.sdpConstraints.mandatory.OfferToReceiveVideo = true;
            advChatCnf.streamConstraints.video = true;
        }
    }
}