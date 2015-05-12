advChatCnf = {
    advChatServer: 'http://192.168.1.86:8800',
    supportServer: 'http://192.168.1.86:10',
    myVideo : document.getElementById('myVideo'),
    remoteVideo : document.getElementById('remoteVideo'),
    user_id: $('#hdn_global_user_id').val(),
    user_name: $('#hdn_global_user_name').val(),
    user_ou: $('#hdn_global_user_ou').val(),
    supportPrefix: 'TamViet',
    IceServer: {"iceServers": [{"url": "stun:stun.l.google.com:19302"}]},
    sdpConstraints: {
        optional: [],
        mandatory: {
            OfferToReceiveAudio: true,
            OfferToReceiveVideo: true
        }
    },
    streamConstraints:{audio:false,video:false},
    screenCapture: 0,
    constructor: function(){
        //khoi tao su kien onincecandidate
        peer.onicecandidate = function(event) {
            
            var candidate = event.candidate;
            if(!event || !supportSocket || !candidate)
            {
                return;
            }
            var send_user_id = advChatCnf.user_id;
            var send_user_ou = advChatCnf.user_ou;
            
            var recieve_user_id = $('#hdn_recieve_user').val();
            var recieve_user_ou = $('#hdn_recieve_ou').val();

            if(recieve_user_ou == null || recieve_user_ou == '')
            {
                return;
            }
            supportSocket.emit('webrtc_icecandidate',{send_user_id: send_user_id,
                                                send_user_ou: send_user_ou,
                                                recieve_user_id: recieve_user_id,
                                                recieve_user_ou: recieve_user_ou,
                                                candidate: candidate});
        };
        //khoi tao su kien onaddstream
        peer.onaddstream = function(e) { 
            if(advChatCnf.screenCapture == 1)
            {
                var video_capture = document.getElementById('video_capture');
                video_capture.src = webkitURL.createObjectURL(e.stream);
                $('#screenCapture').show();
            }
            else
            {
                advChatCnf.remoteVideo.src = webkitURL.createObjectURL(e.stream);
            }
        };
        peer.oniceconnectionstatechange = function(e)
        {
            if(peer.iceConnectionState == 'disconnected') 
            {
                processDom.hideVideo();
                processDom.sound_call_to(0);
                processDom.showBtnCall();
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
        }
    }
};
var socket        = io.connect(advChatCnf.advChatServer); 
var supportSocket = io.connect(advChatCnf.supportServer); 

navigator.getUserMedia = getUserMedia;
peer = new RTCPeerConnection(advChatCnf.IceServer);