<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed');

$this->template->active_role  = '';
//header
$this->template->title = 'Trò chuyện';
$this->template->display('dsp_header.php');
$v_user_ou = CONST_MY_OU_NAME;
?>

<?php 
    echo $this->hidden('hdn_recieve_user','');
    echo $this->hidden('hdn_recieve_ou','');
    
    echo $this->hidden('controller',$this->get_controller_url());
    echo $this->hidden('hdn_insert_method','do_insert_mes');
    echo $this->hidden('hdn_readed_method','do_readed');
?>

<script>
    var SITE_ROOT = '<?php echo SITE_ROOT?>';
</script>
<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            <div class="content-widgets white"> 
                <div id="title-message-chat" class="widget-head light-blue">
                    <h3 class="icon-chat"><i class="icon-comments-alt"></i> Chat</h3>                    
                </div>
                <div class="widget-container">
                    <div id="div_chat" class="tab-widget tabbable tabs-left chat-widget">
                        <!--danh sach nguoi dung-->
                        <ul class="nav nav-tabs" id="chat-tab" style="width: auto;height: 442px;overflow-y: scroll;">                            
                            <span style="width: 100%;display: none" id="recent_user"><br><b>Chat với bạn gần đây</b><br></span>
                            <span style="width: 100%; display: none" id="TamViet_user"><b>Hỗ trợ kỹ thuật</b><br></span>
                            <!--tin nhan tu sever khac chua doc-->
                            <?php 
                                $arr_all_msg_not_in_server = (isset($arr_all_msg_not_in_server) OR !empty($arr_all_msg_not_in_server))? $arr_all_msg_not_in_server : array();
                                
                                foreach ($arr_all_msg_not_in_server as $arr_single_msg_not_in_server):
                                    
                                $v_user_id      = $arr_single_msg_not_in_server['C_SENDER'];
                                $v_user_ou_send = $arr_single_msg_not_in_server['C_SEND_USER_OU'];
                                $v_user_name    = $arr_single_msg_not_in_server['C_NAME_USER_SEND'];
                                $xml_message    = $arr_single_msg_not_in_server['C_XML_MESSAGE'];
                                
                                $dom            = simplexml_load_string($xml_message);
                                $xpath          = '//message';
                                $r              = $dom->xpath($xpath);
                                $span_count_mes = (count($r)>0)? "<span class=\"_51jx\">" . count($r) . "</span>" : '';
                            ?>   
                                <li id="user_status_<?php echo $v_user_id;?>_<?php echo $v_user_ou_send?>" >
                                    <a href="#user_<?php echo $v_user_id;?>_<?php echo $v_user_ou_send?>" data-user_id ="<?php echo $v_user_id;?>" data-user_ou="<?php echo $v_user_ou_send?>">
                                        <span class="user-offline"></span>
                                        <i class="icon-user"></i>
                                         <?php echo $v_user_name . $span_count_mes;?>
                                        <button title="Gọi" class="btnCall" onclick="call(<?php echo $v_user_id?>,'<?php echo $v_user_ou_send?>')"></button>
                                        <button title="Dừng cuộc gọi" style="display: none;" class="btnEndCall" onclick="endCall(<?php echo $v_user_id?>,'<?php echo $v_user_ou_send?>')"></button>
                                        <button title="Đang có cuộc gọi đến" style="display: none;" class="btnCallTo"></button></a>
                                    </a>
                                </li>
                            <?php endforeach;?>
                            
                            <?php foreach($arr_all_user as $arr_ou):
                                    $v_ou_name  = $arr_ou['C_NAME'];
                                    $v_xml_user = $arr_ou['C_XML_USER'];
                                    if($v_xml_user == '' OR $v_xml_user == NULL)
                                    {
                                        continue;
                                    }
                            ?>
                            <span><b><?php echo $v_ou_name;?></b></span>
                                <?php
                                    $dom = simplexml_load_string($v_xml_user);
                                    $xpath ='//row';
                                    $r = $dom->xpath($xpath);
                                    $span_count_mes = '';
                                    foreach($r as $arr_user):
                                        $v_user_id   = (int)$arr_user->attributes()->PK_USER;
                                        $v_user_name = $arr_user->attributes()->C_NAME;
                                        $xml_message = isset($arr_all_message[$v_user_id]) ? $arr_all_message[$v_user_id]:'<root/>';
                                        
                                        $dom = simplexml_load_string($xml_message);
                                        $xpath = '//message';
                                        $r = $dom->xpath($xpath);
                                        $span_count_mes = (count($r)>0)? "<span class=\"_51jx\">" . count($r) . "</span>" : '';
                                       
                                ?>
                                    <li id="user_status_<?php echo $v_user_id;?>_<?php echo $v_user_ou?>" >
                                        <a href="#user_<?php echo $v_user_id;?>_<?php echo $v_user_ou?>" data-user_id ="<?php echo $v_user_id;?>" data-user_ou="<?php echo $v_user_ou?>">
                                            <span class="user-offline"></span>
                                            <i class="icon-user"></i>
                                             <?php echo $v_user_name . $span_count_mes;?>
                                        </a>
                                    </li>
                                <?php endforeach;?>
                            
                            <?php endforeach;?>
                            
                        </ul>
                        <div style="overflow: hidden;padding-right: 20px;" id="box-chat"> 
                            <div class="tab-single-chat" style="">
                              <!--start box-name-message-->
                                <div id="box-name-message">
                                    <!--danh sach thong bao tin tu sever khac-->
                                    <?php foreach ($arr_all_msg_not_in_server as $arr_single_message_not_in_server): ?>
                                    <?php 
                                            $v_user_id      = $arr_single_message_not_in_server['C_SENDER'];
                                            $v_user_ou_send = $arr_single_message_not_in_server['C_SEND_USER_OU'];
                                            $v_user_name    = $arr_single_message_not_in_server['C_NAME_USER_SEND'];
                                            $xml_message    = $arr_single_message_not_in_server['C_XML_MESSAGE'];

                                            $dom            = simplexml_load_string($xml_message);
                                            $xpath          = '//message';
                                            $r              = $dom->xpath($xpath);
                                            $span_count_mes = (count($r)>0)? "<span class=\"_51jx\">" . count($r) . "</span>" : '';
                                    ?>
                                            <div class="<?php echo $v_user_id . '_' . $v_user_ou_send; ?>">
                                                <a href="javascript:void(0);" user_id-user_ou="<?php echo $v_user_id . '_' . $v_user_ou_send; ?>" onclick="processDom.select_tab_message('<?php echo $v_user_id; ?>','<?php echo $v_user_ou_send ?>');" >
                                                    <?php echo $v_user_name . $span_count_mes; ?>
                                                </a>
                                                <span class="close-user-chat" onclick="processDom.onclick_close_chat_tab('<?php echo $v_user_id; ?>','<?php echo $v_user_ou_send ?>');" ><i class="icon-remove"></i></span>
                                            </div>
                                    <?php endforeach; ?>
                                    
                                    <!--thong bao tin cung server-->
                                <?php foreach($arr_all_user as $arr_ou):
                                            $v_ou_name  = $arr_ou['C_NAME'];
                                            $v_xml_user = $arr_ou['C_XML_USER'];
                                            if($v_xml_user == '' OR $v_xml_user == NULL)
                                            {
                                                continue;
                                            }
                                    ?>
                                        <?php
                                            $dom = simplexml_load_string($v_xml_user);
                                            $xpath ='//row';
                                            $r = $dom->xpath($xpath);
                                            $span_count_mes = '';
                                            foreach($r as $arr_user):
                                                $v_user_id   = (int)$arr_user->attributes()->PK_USER;
                                                $v_user_name = $arr_user->attributes()->C_NAME;
                                                $xml_message = isset($arr_all_message[$v_user_id]) ? $arr_all_message[$v_user_id]:'<root/>';

                                                $dom = simplexml_load_string($xml_message);
                                                $xpath = '//message';
                                                $r = $dom->xpath($xpath);
                                                $span_count_mes = (count($r)>0)? "<span class=\"_51jx\">" . count($r) . "</span>" : '';

                                        ?>
                                        <?php if(count($r)>0):?>
                                    <div class="<?php echo $v_user_id.'_'.$v_user_ou;?>">
                                            <a href="javascript:void(0);" user_id-user_ou="<?php echo $v_user_id.'_'.$v_user_ou;?>" onclick="processDom.select_tab_message('<?php echo $v_user_id;?>','<?php echo $v_user_ou?>');" >
                                               <?php echo $v_user_name . $span_count_mes;?>
                                            </a>
                                            <span class="close-user-chat" onclick="processDom.onclick_close_chat_tab('<?php echo $v_user_id;?>','<?php echo $v_user_ou?>');" ><i class="icon-remove"></i></span>
                                    </div>
                                        <?php endif;?>
                                        <?php endforeach;?>

                                    <?php endforeach;?>
                            </div>                              
                              <!--end #box-name-message-->
                            </div>
                            <div class="clear"></div>
                            <!--End #tab-single-chat-->
                        <div class="tab-content" id="div_chat_content">
                            <div class="clear"></div>
                            <!--noi dung tin chat voi nguoi khac server-->
                            <?php foreach($arr_all_msg_not_in_server as $arr_single_message_not_in_server):?>
                                <?php
                                    $v_user_id      = $arr_single_message_not_in_server['C_SENDER'];
                                    $v_user_ou_send = $arr_single_message_not_in_server['C_SEND_USER_OU'];
                                    $v_user_name    = $arr_single_message_not_in_server['C_NAME_USER_SEND'];
                                    $xml_message    = $arr_single_message_not_in_server['C_XML_MESSAGE'];

                                    $dom            = simplexml_load_string($xml_message);
                                    $xpath          = '//message';
                                    $r              = $dom->xpath($xpath);                                    
                                ?>
                                <div class="tab-pane " id="user_<?php echo $v_user_id;?>_<?php echo $v_user_ou_send?>">
                                    <!--<div class="single-user-name"><b><?php echo $v_user_name; ?></b></div>-->
                                     <?php
                                        foreach($r as $message)
                                        {
                                            $html = '';
                                            $html .= '<div class="conversation">';
                                            $html .= '<a href="#" class="pull-left media-thumb"><img src="' . SITE_ROOT . 'public/images/item-pic.png" width="34" height="34" alt="user"></a>';
                                            $html .= '<div class="conversation-body ">';
                                            $html .= '<h4 class="conversation-heading">' . $v_user_name . ':</h4>';
                                            $html .= '<p>' . $message . '</p>';
                                            $html .= '</div>';
                                            $html .= '</div>';
                                            echo $html;
                                        }
                                    ?>
                                    
                                </div>
                                <?php endforeach;?>
                            
                            <!--noi dung tin chat voi nguoi cung sever-->
                            <?php foreach($arr_all_user as $arr_ou):
                                $v_xml_user = isset($arr_ou['C_XML_USER'])? $arr_ou['C_XML_USER'] : '';
                                if($v_xml_user == '' OR $v_xml_user == NULL)
                                {
                                    continue;
                                }
                                $dom = simplexml_load_string($v_xml_user);
                                $xpath ='//row';
                                $r = $dom->xpath($xpath);
                                foreach($r as $arr_user):
                                    $v_user_id   = (int)$arr_user->attributes()->PK_USER;
                                    $v_user_name = $arr_user->attributes()->C_NAME;
                            ?>
                                <div class="tab-pane " id="user_<?php echo $v_user_id;?>_<?php echo $v_user_ou?>">
                                    <!--<div class="single-user-name"><b><?php echo $v_user_name; ?></b></div>-->
                                     <?php
                                    $xml_message = isset($arr_all_message[$v_user_id]) ? $arr_all_message[$v_user_id]:'';
                                    if($xml_message != '')
                                    {
                                        $dom = simplexml_load_string($xml_message);
                                        $xpath = '//message';
                                        $r = $dom->xpath($xpath);

                                        foreach($r as $message)
                                        {
                                            $html = '';
                                            $html .= '<div class="conversation">';
                                            $html .= '<a href="#" class="pull-left media-thumb"><img src="' . SITE_ROOT . 'public/images/item-pic.png" width="34" height="34" alt="user"></a>';
                                            $html .= '<div class="conversation-body ">';
                                            $html .= '<h4 class="conversation-heading">' . $v_user_name . ':</h4>';
                                            $html .= '<p>' . $message . '</p>';
                                            $html .= '</div>';
                                            $html .= '</div>';
                                            echo $html;
                                        }
                                    }
                                    ?>
                                    
                                </div>
                                <?php endforeach;?>
                            <?php endforeach;?>
                                <!--div screen capture-->
                                <div class="tab-pane " id="screenCapture">
                                    <video id="video_capture" style="width:100%;height:420px" autoplay></video>
                                </div>
                            </div>
                            <div class="show-video" style="width: 0%;height: 400px;float:left;display: none">
                                 <!--my video-->
                                <span id="my-name"></span>
                                <video id="myVideo" style="width: 100%;height: 175px" autoplay ></video>
                                
                                <div style="height: 1px;width: 100%;margin: 24px 0px 25px 0px;border-top: 1px solid #EDEDED">&nbsp</div>
                                <!--remote video-->
                                <div id="box-title-call" ></div>
                                <span id="remote-name"></span>
                                <video id="remoteVideo"style=" width: 100%;height: 175px" autoplay></video>
                            </div>
                            <div class="chat-input" style="left: 5px;bottom: 0;width: 100%;">
                                <textarea type="textbox" style="width: 100%;height: 40px;" class="chat-inputbox" name="input" id="txt_message" ></textarea>
                            </div>
                        </div>
                     
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--mesage_sound-->
    <audio id ="message_sound">
        <source src="<?php echo FULL_SITE_ROOT.'uploads/r3/advchat/message_sound.mp3';?>">
    </audio>
    <audio id ="message_sound_viedeo">
        <source src="<?php echo FULL_SITE_ROOT.'uploads/r3/advchat/video_sound.mp3';?>">
    </audio>
    
</div>
    <!--socket chat-->
    <script src="<?php echo SITE_ROOT; ?>public/js/advchat/adapter.js" type="text/javascript"></script>
    <script src="<?php echo SITE_ROOT; ?>public/js/advchat/processDom.js" type="text/javascript"></script>
    <script src="<?php echo SITE_ROOT; ?>public/js/advchat/advSocket.js" type="text/javascript"></script>
    <script src="<?php echo SITE_ROOT; ?>public/js/advchat/supportSocket.js" type="text/javascript"></script>
    
<script>
$( "#video_capture" ).dblclick(function() {
    if (this.requestFullscreen) {
        this.requestFullscreen();
      } else if (this.msRequestFullscreen) {
        this.msRequestFullscreen();
      } else if (this.mozRequestFullScreen) {
        this.mozRequestFullScreen();
      } else if (this.webkitRequestFullscreen) {
        this.webkitRequestFullscreen();
      }
});
</script>

<script>
    
    $('#chat-tab li a').click(function (e) {
        e.preventDefault();
        var user_id     = $(this).attr('data-user_id');
        var user_ou     = $(this).attr('data-user_ou');
        var user_name   = trim($(this).text());
        var selector_box_name_msg =  '#box-name-message';
        
        $(this).tab('show');
        $(this).find('[class="_51jx"]').remove();
        //Kiem tra item click neu co goi video hien thị giao dien goi video
        $('.btnEndCall',$(this)).filter(
            function(index)
            {
               var v_display = $(this).css('display') ;
               if(v_display == 'block' && advChatCnf.screenCapture != 1)
               {
                    processDom.showVideo();
               }
              else
              {   
                   // processDom.hideVideo();
              }
            }
        );
                // dinh ten nguoi chat vao tieu de
                var html = '<div class="'+user_id +'_'+ user_ou +'"><a href="javascript:void(0);" user_id-user_ou="'+user_id +'_'+ user_ou +'" onclick="processDom.select_tab_message(\''+ user_id +'\',\''+ user_ou +'\');">'
                    + user_name 
                    +'</a>'
                    + '<span class="close-user-chat" onclick="processDom.onclick_close_chat_tab(\''+ user_id +'\',\''+ user_ou + '\');" ><i class="icon-remove"></i></span></div>';
                var count_selector = $('a[user_id-user_ou="'+user_id +'_'+ user_ou +'"]',selector_box_name_msg).length;
                if(count_selector == 0)
                {
                    $(selector_box_name_msg).append(html);
                }
                //Loai bo thong bao tin den chua xem
                $('._51jx',$(selector_box_name_msg + ' a[user_id-user_ou="'+ user_id +'_'+ user_ou +'"]')).remove();
            
                // them class hien thi nguoi dang chat
                $(selector_box_name_msg + ' div').filter('.active').removeClass('active')
                $(selector_box_name_msg + ' div').filter('.'+ user_id +'_'+ user_ou).addClass('active');
                 processDom.fix_height_div_chat();
                 processDom.select_chat_user(user_id,user_ou);
     });

    $('#txt_message').keydown(function (e){
        if(e.keyCode == 13)
        {
            var v_message = $('#txt_message').val();
            if(typeof(v_message) == 'undefined' || v_message.trim().length ==0)
            {
                return false;
            }
            processDom.send_message();
        }
    });
    
    $(document).ready(function()
    {
        advChatCnf.remoteVideo = document.getElementById('remoteVideo');
        advChatCnf.myVideo     = document.getElementById('myVideo');
        var selector_recent = '#recent_user';
        $('._51jx').each(function(){
            var selector_li = $(this).parent('a').parent('li');
            $(selector_li).detach().insertAfter(selector_recent);
            $(selector_recent).show();
        });
        
        //fix auto height #chat-tab
        processDom.fix_height_div_chat(); 
        
        //fix auto width tab usert name chat
        var width_box_content  = $('#div_chat_content').outerWidth() || 0;
        $('#box-name-message').width(width_box_content);        
        $( window ).resize(function(e) {
            processDom.fix_height_div_chat(); 
            var width_box_content_resize  = $('#div_chat_content').outerWidth() || 0; 
            $('#box-name-message').width(width_box_content_resize);
        });
        
        //kiem tra thiet bi
        if(typeof MediaStreamTrack != 'undefined')
        {
            //lay danh sach divice
            MediaStreamTrack.getSources(gotSources);
        }
    });
</script>
<?php $this->template->display('dsp_footer.php');