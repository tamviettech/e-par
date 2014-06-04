function style_treeview(){
    $('.treeview').each(function(){
        i = 0;
        $(this).find('li:visible').each(function(){
            v_class = 'row' + (i%2);
            $(this).removeClass('row0').removeClass('row1').addClass(v_class);
            i++;
        });
    });
}
function dsp_folder(json_folder){
    $('#folder .loading_overlay').show();
    var selected_item = $('.filetree.selected li.selected');
    if($(selected_item).length == 0){
        selected_item = $('#folder ul#root_directory').parent();
        $.each(json_folder, function(k, v){
            html = '<li class="closed" data-loaded="0" data-name="'+k+'" data-path="'+ v.path +'"><span class="folder" onclick="item_onclick(this)"  >'+ k +'</span><ul id="'+v.path+'"></ul></li>';
            var branches = $(html).appendTo($(selected_item).children('ul'));
        });
        $('#left .filetree').treeview({
            persist: "location",
            collapsed: true,
            unique: true
        });
    }else{
        html = '';
        $.each(json_folder, function(k, v){
            html += '<li class="closed" data-loaded="0" data-name="'+k+'" data-path="'+ v.path +'"><span class="folder" onclick="item_onclick(this)" >'+ k +'</span><ul id="'+v.path+'"></ul></li>';
        });
        var branches = $(html).appendTo($(selected_item).children('ul'));
    }
    if(json_folder.length > 0){
        $(selected_item).children('ul').treeview({
            add: branches,
            collapsed: true
        });
    }
    $('#folder .loading_overlay').hide();
    style_treeview();
}
function load_info(item_obj){
    if(item_obj == null){ //root
        v_dirname = '';
    }
    else
    {
        item_obj = $(item_obj);
        
    }
    first_sep = $('#htaccess') ? '?' : '&';
    v_url = $('#controller').val() + $('#get_dir_content').val();
    $.ajax({
        dataType: "json",
        type: 'get',
        url: v_url,
        data: {
            dir: v_dirname
        }
        ,
        success: function(json_obj){
            dsp_folder(json_obj.folder);
        }
    });
}

function dsp_all_folder_items(json){
    $('#right table.data').find('tr').remove();
    v_path = $('#txt_path').val();
    if(v_path && v_path != '/'  && v_path != '\\')
    {
        v_parent_path = v_path.replace(/\\/g,'/').replace(/\/[^\/]*$/, '');
        html = '<tr data-type="'+$('#lang_directory').val()+'" data-name="../" data-path="'+v_parent_path+'">';
        html += '<td class="Center">&nbsp;</td>';
        html += '<td><a href="javascript:;" onclick="folder_details_onclick(this)"><span class="folder">&nbsp;../&nbsp;</span></a></td>';
        html += '<td>'+ $('#lang_up_a_level').val() +'</td>';
        html += '<td>&nbsp;</td>';
        html += '</tr>';
        $('#right table.data').append(html);
    }

    
    $.each(json.folder, function(k, v){
        html = '<tr data-type="'+v.type+'" data-name="'+k+'" data-path="'+v.path+'">';
        html += '<td class="Center"><input type="checkbox" class="chk_item"/></td>';
        html += '<td><a href="javascript:;" onclick="folder_details_onclick(this);return false;"><span class="folder">&nbsp;'+ k +'</span></a></td>';
        html += '<td>'+ v.type +'</td>';
        html += '<td>'+ v.date +'</td>';
        html += '</tr>';
        $('#right table.data').append(html);
    });
    
    
    $.each(json.file, function(k, v){
        v_url = SITE_ROOT + 'uploads/public_service' + v.path;
        html = '<tr onmouseover="item_onmouseover(this)" onmouseout="item_onmouseout()" data-type="'+v.type+'" data-name="'+k+'" data-path="'+v.path+'">';
        html += '<td class="Center"><input class="chk_item" type="checkbox" data-type="'+v.type+'" data-name="'+k+'" data-path="'+v.path+'"/></td>';
        html += '<td><a href="'+v_url+'" onclick="folder_details_onclick(this);return false;"><span class="file">&nbsp;'+ k +'</span></a></td>';
        html += '<td>'+ v.type +'</td>';
        html += '<td>'+ v.date +'</td>';
        html += '</tr>';
        $('#right table.data').append(html);
    });
}

function folder_details_onclick(a_object){
    tr = $(a_object).parents('tr');
    v_path = $(tr).attr('data-path');
    v_type = $(tr).attr('data-type');
    v_name = $(tr).attr('data-name');
    $('#txt_path').val(v_path);
    
    if(v_type == $('#lang_directory').val()){
        v_url = $('#controller').val() + $('#get_dir_content').val();
        $.ajax({
            dataType: "json",
            type: 'get',
            url: v_url,
            data: {
                dir: v_path
            }
            ,
            success: function(json_obj){ 
                if(json_obj.folder && $('#left li[data-path="'+v_path+'"]').attr('data-loaded') == 0){
                    dsp_folder(json_obj.folder); 
                    $('#left li[data-path="'+v_path+'"]').attr('data-loaded', 1);
                }
                if(typeof(json_obj.file) != 'undefined' || typeof(json_obj.folder) != 'undefined'){
                    dsp_all_folder_items(json_obj);
                    init_upload(v_path);
                }
                $('#folder .loading_overlay').hide();
            }
        });
    }else if(window.parent.location != window.location){
        data = [{
            'path': v_path.replace(/^\//, ''),
            'type': v_type,
            'name': v_name
        }];
        returnVal = data;
        window.parent.hidePopWin(true);
    }
    
}

function chk_all_onclick(){
    $('.chk_item').attr('checked', $('#chk_all').attr('checked')).live('click',function(){
        if($(this).attr('checked')){
            $('#chk_all').removeAttr('checked');
        } 
    });
}

function item_onclick(span_object){
    li_object = $(span_object).parent();
    //$('#folder .loading_overlay').show();
    $('#uploaded').html('');
    try{
        
    }catch(ex){
        if($(window.event.target).parent().attr('data-path') !== $(li_object).attr('data-path')) {
            return;
        }
    }
    init_upload($(li_object).attr('data-path'));
    
    $('#txt_path').val($(li_object).attr('data-path'));
    $('ul.filetree').removeClass('selected');     
    $(li_object).parents('#folder').find('.loading_overlay').show();
    $(li_object).parents('.filetree').addClass('selected');
    $(li_object).parents('.filetree').find('li.selected').removeClass('selected');
    $(li_object).addClass('selected');
    
    //neu doi tuong duoc chon o cot ben trai
    if($(li_object).parents('#left').length == 1){
        v_url = $('#controller').val() + $('#get_dir_content').val();
        v_dirname = $(li_object).attr('data-path');
        $.ajax({
            dataType: "json",
            type: 'get',
            url: v_url,
            data: {
                dir: v_dirname
            }
            ,
            success: function(json_obj){ 
                if(json_obj.folder && $(li_object).attr('data-loaded') == 0){
                    dsp_folder(json_obj.folder); 
                    $(li_object).attr('data-loaded', 1);
                }
                if(json_obj.folder && json_obj.file){
                    dsp_all_folder_items(json_obj);
                }
                $('#folder .loading_overlay').hide();
            }
        });
    }
    $('#folder .loading_overlay').hide();
}

function item_onmouseover(item_object){
    $('#details .loading_overlay').show().css({
        height: '200px', 
        background: 'white'
    });
    $.ajax({
        type: 'get',
        url: $('#controller').val() + $('#hdn_details_method').val(),
        data:{
            path: $(item_object).attr('data-path')
        },
        success: function(returnHTML){
            $('#details .loading_overlay').html(returnHTML);
        }
    });
}

function item_onmouseout(){
    $('#details .loading_overlay').hide();
}

function pick_file_onclick(){
    if(window.parent){
        v_data = [];
        v_lang_dir = $('#lang_directory').val();
        $('#right table.data input[type="checkbox"]:checked').each(function(){
            tr = $(this).parents('tr[data-type!="'+v_lang_dir+'"]');
            if($(tr).length > 0){
                item = {
                    path: $(tr).attr('data-path').replace(/^\//, ''),
                    type: $(tr).attr('data-type'),
                    name: $(tr).attr('data-name')
                };
                v_data.push(item);
            }
            
        });
        returnVal = v_data;
        window.parent.hidePopWin(true);
    }
}

function init_upload(dirpath){
    var errors="";
    v_parent = $('#upload').parent();
    $('#upload').remove();
    $(v_parent).prepend('<div id="upload"></div>');
    $('#upload').mfupload({	
        type		: $('#extensions').val(),	
        maxsize		: 2,
        post_upload	: $('#controller').val() + $('#hdn_upload').val(),
        folder		: dirpath,
        ini_text	: $('#hdn_drag_text').val(),
        over_text	: "Drop Here",
        over_col	: 'white',
        over_bkcol	: 'green',
        
        init		: function(){		
            $("#uploaded").empty();
        },
		
        start		: function(result){		
            $("#uploaded").append("<div id='FILE"+result.fileno+"' class='files'>"+result.filename+"<div id='PRO"+result.fileno+"' class='progress'></div></div>");	
        },

        loaded		: function(result){
            $("#PRO"+result.fileno).remove();
            $("#FILE"+result.fileno).html($('#hdn_uploaded').val() + ": "+result.filename+" ("+result.size+")");
            v_url = $('#controller').val() + $('#get_dir_content').val();
            v_dirname = $('#txt_path').val();
            $.ajax({
                dataType: "json",
                type: 'get',
                url: v_url,
                data: {
                    dir: v_dirname
                }
                ,
                success: function(json_obj){
                    if(typeof(json_obj) != 'object'){
                        json_obj = $.parseJSON(json_obj);
                    }
                    try{
                        dsp_all_folder_items(json_obj);
                    }catch(ex){
                        
                    }

                    $('#folder .loading_overlay').hide();
                }
            });
        },

        progress	: function(result){
            $("#PRO"+result.fileno).css("width", result.perc+"%");
        },

        error		: function(error){
            errors += error.filename+": "+error.err_des+"\n";
        },

        completed	: function(){
            if (errors != "") {
                alert(errors);
                errors = "";
                
            }
        }
    });   	
}

function newdir_onclick(){
    v_path = $('#txt_path').val();
    v_dirname = window.prompt($('#lang_newdir_msg').val(),"new folder");
    if(!v_dirname){
        return;
    }
    $.ajax({
        type: 'post'
        ,
        url: $('#controller').val() + $('#hdn_newdir_method').val()
        ,
        data: {
            path: v_path, 
            dirname: v_dirname
        }
        ,
        success: function(msg){
            msg = $.parseJSON(msg);
            if(msg.errors){
                alert(msg.errors);
            }else if(msg.dirname){
                v_dirname = msg.dirname;
                html = '<li class="closed" data-loaded="0" data-name="'+v_dirname+'" data-path="'+ v_path + $('#DS').val() +v_dirname+'"><span class="folder" onclick="item_onclick(this)"  >'+ v_dirname +'</span><ul id="'+v_path+'"></ul></li>';
                var branches = $(html).prependTo($('#left li.selected').children('ul'));
                $('#left li.selected').children('ul').treeview({
                    add: branches,
                    collapsed: true
                });
                v_url = $('#controller').val() + $('#get_dir_content').val();
                //hien thi folder items
                $.ajax({
                    dataType: "json",
                    type: 'get',
                    url: v_url,
                    data: {
                        dir: $('#txt_path').val()
                    }
                    ,
                    success: function(json_obj){ 
                        if(typeof(json_obj) !== 'object'){
                            json_obj = $.parseJSON(json_obj);
                        }                      
                        dsp_all_folder_items(json_obj);
                        $('#folder .loading_overlay').hide();
                    }
                });
            }
            
        }
    });
}

function delete_onclick(){
    if(!confirm($('#hdn_delete_msg').val())){
        return;
    }
    var selected_items = [];
    $('#right input[type="checkbox"]:checked').each(function(){
        selected_items.push($(this).parents('tr').attr('data-name')); 
    });
    $.ajax({
        type: 'post',
        datatype: 'json',
        url: $('#controller').val() + $('#hdn_delete_method').val(), 
        data: {
            'items': selected_items,
            'parent_dir': $('#txt_path').val()
        },
        success: function(return_msg){
            return_msg = $.parseJSON(return_msg);
            if(return_msg.errors){
                alert(return_msg.errors);
            }
            if(return_msg.deleted_folders){
                $.each(return_msg.deleted_folders, function(k, v){
                    $('#right table tr[data-name="'+v+'"]').remove();
                    $('#left li.selected li[data-name="'+v+'"]').hide();
                });
                $.each(return_msg.deleted_files, function(k, v){
                    $('#right table tr[data-name="'+v+'"]').remove();
                });
            }
        }
    });
}

$(document).ready(function(){
    init_upload('');
    $('#folder .loading_overlay').show();
    $('#right').click(function(){
        $('ul.filetree').removeClass('selected');
    });
    load_info(null); 
    $('#filemenu > a').button();
    $('#btn_left_pane').click(function(){
        if($('#left').is(":visible") == false){
            $('#left').show();
            $('#right').width('70%');
        }
        else{
            $('#left').hide();
            $('#right').width('95%');
        }
    });
});
