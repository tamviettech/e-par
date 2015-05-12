//tao tree view
   function init_tree_view()
   {
       $("#browser").treeview({
           persist: "location",
           unique: true
       });    
       $("#browser-public").treeview({
           persist: "location",
           unique: true
       });    
   }
   //hien thi man hinh upload
   function dsp_upload_onclick(is_public)
   {
       var  tab_selected  = $('#hdn_tab_selected').val();
       var user_id       = $('#hdn_user_share_to_you').val();
       
       //tao bien get
       var get_user_id   = '';
       var get_parent_id = '';
       //media
       if(tab_selected == '0' || tab_selected == '2')
       {
           var parent_folder = $('#hdn_folder_id').val();
       }
       //share
       else
       {
           var parent_folder = $('#hdn_share_folder_id').val();
           get_user_id = '&user_id=' + user_id;
       }
       
       //kiem tra dieu kien
       if(parent_folder != '' && parent_folder != null && typeof(parent_folder) != 'undefined')
       {
            get_parent_id = '&parent=' + parent_folder;
       }
       var url = $('#controller').val() + $('#hdn_upload_method').val()+ '&pop_win=1'+get_parent_id+get_user_id;
       if(is_public == '1')
        {
             url = $('#controller').val() + $('#hdn_upload_method').val()+ '&pop_win=1'+get_parent_id+get_user_id + '&is_public=1'; 
        }
       
       showPopWin(url,800,300,function(respond){
           obj_ret = JSON.parse(respond);
           var folder_id = obj_ret.hdn_folder_id;
           if(folder_id == '' || folder_id == null || typeof(folder_id) == 'undefined')
           {
               change_folder();
           }
           else
           {
               change_folder(folder_id);
              
           }
       });
   }
   //tao thu muc
   function newdir_onclick(is_public)
   {
       var parent_id = $('#hdn_folder_id').val();
       var v_dirname = window.prompt("Nhập tên thư mục","new folder");
       if(v_dirname == '' || v_dirname == null || typeof(v_dirname) == 'undefined')
       {
           return false;
       }

       var url = $('#controller').val() + $('#hdn_newdir_method').val();
       if(is_public == '1')
        {
            var url = $('#controller').val() + $('#hdn_newdir_method').val() + '&media_public=1';
        }
       $.ajax({
           type: "POST",
           url: url,
           data:{parent_id:parent_id,dir_name:v_dirname},
           success: function(respond){
               var obj_folder = JSON.parse(respond);
               var check_success = obj_folder.success;
               var new_id        = obj_folder.new_id;


               if(check_success == 'true')
               {
                    if(is_public == '1' && typeof(is_public) != 'undefined')
                    {
                            //media public
                            //tao selector den thu muc cha
                           if(parent_id == '' || parent_id == null)
                           {
                               var parent_folder_id = '#browser-public #folder_0-public';
                           }
                           else
                           {
                               var parent_folder_id = '#browser-public #folder_public_' + parent_id;
                           }
                           var parent_li = $(parent_folder_id).parent().parent();
                           //kiem tra ul
                           if($(parent_li).children('ul').length < 1)
                           {
                               $(parent_li).attr('class','');
                               $(parent_li).find('.hitarea').remove();
                               $(parent_li).prepend('<div class="hitarea expandable-hitarea"></div>');
                               $(parent_li).append('<ul></ul>');
                           }

                           //tao node moi
                           var newnode = $('<li class="closed">'  
                                   + '<span class="folder">'
                                   +   '<a href="javascript:void(0)" id="folder_public_' + new_id + '" onclick="change_folder('+ new_id +')" >' + v_dirname + '</a>'
                                   + '</span>'
                                   + '</li>').appendTo($(parent_li).children('ul'));

                           //them node moi
                           $('#browser-public').treeview({
                             add: newnode
                           });  
                       }
                       else
                       {
                            //tao selector den thu muc cha
                            if(parent_id == '' || parent_id == null)
                            {
                                 parent_folder_id = '#browser #folder_0';
                            }
                            else
                            {
                                 parent_folder_id = '#browser #folder_' + parent_id;
                            }
                             parent_li = $(parent_folder_id).parent().parent();
                            //kiem tra ul
                            if($(parent_li).children('ul').length < 1)
                            {
                                $(parent_li).attr('class','');

                                $(parent_li).find('.hitarea').remove();
                                $(parent_li).prepend('<div class="hitarea expandable-hitarea"></div>');
                                $(parent_li).append('<ul></ul>');
                            }

                            //tao node moi
                             newnode = $('<li class="closed">'  
                                    + '<span class="folder">'
                                    +   '<a href="javascript:void(0)" id="folder_' + new_id + '" onclick="change_folder('+ new_id +')" >' + v_dirname + '</a>'
                                    + '</span>'
                                    + '</li>').appendTo($(parent_li).children('ul'));

                            //them node moi
                            $('#browser').treeview({
                              add: newnode
                            });
                       }
                   
                   
                   //reset thu muc
                   change_folder(parent_id);
                  
               }
               else
               {
                   alert('Xảy ra sự cố không thể tạo được thư mục !!!');
               }

           }
       });
   }
   
    /**
     * Lay danh sach media public root
     */
    function change_folder_public(folder_id) 
    {
       if(typeof(folder_id) == 'undefined')
       {
           folder_id = '';
       }
        //tab media public
        //active folder
       $('#browser-public li .folder a').removeClass('treeview_active');
       if(folder_id == '')
       {
           $('#folder_0-public').addClass('treeview_active');
       }
       else
       {
           var html_id = '#folder_public_' + folder_id;
           $(html_id).addClass('treeview_active');
       }

       //gan folder id vao hidden
       $('#hdn_folder_id').val(folder_id);
       
        var url = $('#controller').val() + $('#hdn_mof_method').val();
        var list_content = "#content_media_public";
        $.ajax({
            type: "POST",
            url: url,
            data:{folder_id:folder_id,is_share:'',user_share:'',is_public:'1'},
            beforeSend: function() {
                     img ='<center><img src="' + SITE_ROOT + 'public/images/loading.gif"/></center>';
                         $(list_content).html(img);
                 },
            success: function(respond){
                    $(list_content).html(respond);
            }
        });
    }
   //thay doi thu muc
   function change_folder(folder_id,grant)
   {
       if(typeof(folder_id) == 'undefined')
       {
           folder_id = '';
       }
       var tab_selected = $('#hdn_tab_selected').val();
       
       //neu la media
       if(tab_selected == '0')
       {
           //active folder
           $('#browser li .folder a').removeClass('treeview_active');
           if(folder_id == '')
           {
               $('#folder_0').addClass('treeview_active');
           }
           else
           {
               var html_id = '#folder_' + folder_id;
               $(html_id).addClass('treeview_active');
           }

           //gan folder id vao hidden
           $('#hdn_folder_id').val(folder_id);

           var is_public  = '0';
           var is_share   = 0;
           //id cua div list content
           var list_content = "#content_media";
           var user_share = '';
       }
       //neu la share
       else if(tab_selected == '1')
       {
           //neu la '' hoac id parent = thu muc cha da share
           if(folder_id == '' || folder_id == $('#hdn_share_folder_id').val())
           {
               var share_user = $('#hdn_user_share_to_you').val();
               change_user_share(share_user);
               return;
           }
           
           //kiem tra phan quyen cho folder
            if(grant == '' || grant == null || typeof(grant) == 'undefined')
            {
                //lay quyen chia se
                var cur_grant = $('#hdn_grant_permit_share').val();
                if(cur_grant != '')
                {
                    var arr_grant = cur_grant.split(',');
                    for(i=0;i<arr_grant.length;i++)
                    {
                        id_btn_upload = '#btn_' + arr_grant[i];
                        $(id_btn_upload).show();
                    }
                }
                else
                {
                    var id_btn_upload = '#btn_' + CONST_PERMIT_UPLOAD_MEDIA;
                    $(id_btn_upload).hide();
                    
                    var id_btn_delete = '#btn_' + CONST_PERMIT_DELETE_MEDIA;
                    $(id_btn_delete).hide();
                }
            }
            else 
            {
                
                //gan quyen chia se
                $('#hdn_grant_permit_share').val(grant);
                arr_grant = grant.split(',');
                for(i=0;i<arr_grant.length;i++)
                {
                    id_btn_upload = '#btn_' + arr_grant[i];
                    $(id_btn_upload).show();
                }
            }
           //gan folder share hien tai
           $('#hdn_share_folder_id').val(folder_id);
           is_public = '0';
           is_share = '1';
           //id cua div list content
           list_content = "#content_media_shared";
           user_share = $('#hdn_user_share_to_you').val();
       }
       else
       {
           //tab media public
            //active folder
           $('#browser-public li .folder a').removeClass('treeview_active');
           if(folder_id == '')
           {
               $('#folder_0-public').addClass('treeview_active');
           }
           else
           {
               var html_id = '#folder_public_' + folder_id;
               $(html_id).addClass('treeview_active');
           }

           //gan folder id vao hidden
           $('#hdn_folder_id').val(folder_id);
           
           is_public = '1';
           is_share = '0';
           //id cua div list content
           list_content = "#content_media_public";
           user_share = '';
       }
       var url = $('#controller').val() + $('#hdn_mof_method').val();

       $.ajax({
           type: "POST",
           url: url,
           data:{folder_id:folder_id,is_share:is_share,user_share:user_share,is_public:is_public},
           beforeSend: function() {
                    img ='<center><img src="' + SITE_ROOT + 'public/images/loading.gif"/></center>';
                        $(list_content).html(img);
                },
           success: function(respond){
                   $(list_content).html(respond);
           }
       });
   }

   //xoa media
   function btn_media_delete_onclick(v_public)
   {
       var v_item_id_list = "";
       var error_message = 'Chưa có đối tượng nào được chọn!';
       var tab_selected = $('#hdn_tab_selected').val();
       var user_id = '';
       //media
       if(tab_selected == '0')
       {
           //obj list media in frmMain
            var f = $('#frmMain #content_media');
            //lay thu muc hien hanh
            var folder_id = $('#hdn_folder_id').val();
       }
       //share
       else if(tab_selected == '1')
       {
           //obj list share media in frmMain
           var f = $('#frmMain #content_media_shared');
           //lay thu muc hien hanh
           var folder_id = $('#hdn_share_folder_id').val();
           
           user_id = $('#hdn_user_share_to_you').val();
       }
       else
        {
            //obj list media public in frmMain
             var f = $('#frmMain #content_media_public');
             //lay thu muc hien hanh
             var folder_id = $('#hdn_folder_id').val();
        }
       if (typeof($(f).find('[name="chk"]:checked')) == 'undefined' ){
           alert(error_message);
           return;
       }

       v_item_id_list = get_all_checked_checkbox($(f).find('[name="chk"]:checked'),",");
       
       if (v_item_id_list == ""){
           alert(error_message);
           return;
       }
       var url = $('#controller').val() + $('#hdn_delete_method').val();
       
       if (confirm('Bạn chắc chắn xoá các đối tượng đã chọn?')){
           $.ajax({
               type: 'POST',
               url: url,
               data: {'hdn_item_id_list':v_item_id_list,user_id:user_id,media_public:'1'},
               success: function(respond){
                   var obj_delete = JSON.parse(respond);
                   var message    = obj_delete.message;
                   var deleted_id = obj_delete.deleted_id;

                   //hien thi message
                   if(message != '' && message != null && typeof(message) != 'undefined')
                   {
                       alert(message);
                   }
                   //xoa thu muc tai tree view
                   var arr_delete = deleted_id.split(',');
                   for(i=0;i<arr_delete.length;i++)
                   {
                       if(v_public == '1' && typeof(v_public) != 'undefined')
                       {
                           //media public
                            var parent_folder_id = '#browser-public #folder_public_' + arr_delete[i];
                            var parent_li        = $(parent_folder_id).parent().parent();
                       }
                       else
                       {
                             parent_folder_id = '#browser #folder_' + arr_delete[i];
                             parent_li        = $(parent_folder_id).parent().parent();
                       }
                      
                        $(parent_li).remove();
                      
                      
                   }

                  
                   //reset thu muc
                   if(folder_id == null || folder_id == '')
                   {
                       change_folder();
                   }
                   else
                   {
                       change_folder(folder_id);
                   
                   }
               }
           });
       }
   }
   //rename
   function btn_rename_onclick(v_id,v_public)
   {
       
       var parent_id = $('#hdn_folder_id').val();
       var v_newname = window.prompt("Nhập tên mới");
       if(v_newname == '' || v_newname == null || typeof(v_newname) == 'undefined')
       {
           return false;
       }

       var url = $('#controller').val() + $('#hdn_rename_method').val();
        if(typeof(v_public) == 'undefined')
        {
            v_public = '';
        }
        
       $.ajax({
           type: "POST",
           url: url,
           data:{v_id:v_id,new_name:v_newname,v_public:v_public},
           success: function(message){
                    if(parseInt(message) != 1)
                    {
                        alert(message);
                        return false
                    }
                    else
                    {
                        //thay doi ten
                        var select_a = '#folder_' + v_id;
                        $(select_a).html(v_newname);
                        
                        var select_a_public = '#folder_public_' + v_id;
                        $(select_a_public).html(v_newname);
                        //reset thu muc
                        change_folder(parent_id);
                    
                    }
           }
       });
   }

   //share file hoac folder
   function btn_share_onclick(v_id)
   {
       var url = $('#controller').val() + $('#hdn_share_method').val() + '&pop_win=1&media_id=' + v_id;
       showPopWin(url,800,600,function(respond){
           var obj_share = JSON.parse(respond);
           var message = obj_share.message;
           if(message != '' && message != null && typeof(message) && 'undefined')
           {
               alert(message);
           }
           //load folder
           var folder_id = $('#hdn_folder_id').val();
           if(folder_id == '' || folder_id == null || typeof(folder_id) == 'undefined')
           {
               change_folder();
           }
           else
           {
               change_folder(folder_id);
           }
       });
   }
   //thay doi nguoi share sile
   function change_user_share(user_id)
   {
       if(typeof(user_id) == 'undefined')
       {
           return false;
       }
       //disable btn
        var id_btn_upload = '#btn_' + CONST_PERMIT_UPLOAD_MEDIA;
        $(id_btn_upload).hide();
        var id_btn_delete = '#btn_' + CONST_PERMIT_DELETE_MEDIA;
        $(id_btn_delete).hide();
        
       //xoa hdn_share_folder_id
       $('#hdn_share_folder_id').val('');
       
       //gan quyen chi se ve ''
       $('#hdn_grant_permit_share').val('')
       
       //active folder
       $('#list_user_share > a').attr('class','');
       var selector = '#user_share_' + user_id;
       $(selector).addClass('user_active');

       //gan folder id vao hidden
       $('#hdn_user_share_to_you').val(user_id);
       //tao url
       var url = $('#controller').val() + $('#hdn_sty_method').val();
       $.ajax({
           type: "POST",
           url: url,
           data:{user_id:user_id,parent_folder_id:'-1'},
           beforeSend: function() {
                    var img ='<center><img src="' + SITE_ROOT + 'public/images/loading.gif"/></center>';
                    $('#content_media_shared').html(img);
                },
           success: function(respond){
               $('#content_media_shared').html(respond);
           }
       });
   }