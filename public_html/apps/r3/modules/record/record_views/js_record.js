function txt_record_type_code_onkeypress(evt)
{
    if (IE()){
        theKey=window.event.keyCode
    } else {
        theKey=evt.which;
    }

    if(theKey == 13){
        show_all_option_of_sel_record_type();
        var v_record_type_code = trim($("#txt_record_type_code").val()).toUpperCase();
        if(v_record_type_code != '')
        {
            var count = 0;
            $("#sel_record_type option").each(function(){
                if($(this).val() == v_record_type_code)
                {
                    $("#frmMain").submit();
                }
                else if($(this).attr('data-mapping') == v_record_type_code)
                {
                    $("#sel_record_type").val($(this).val());
                    count ++;
                }
                else
                {
                    $(this).remove();
                }
            });
            
            if(count == 1)
            {
                $("#frmMain").submit();
            }
        }
    }
    return false;
}
//hien thi tat ca option cua #sel_record_type
function show_all_option_of_sel_record_type()
{
    var all_record_type = JSON.parse($('#hdn_all_record_type_filter').val());
    $("#sel_record_type option").remove();
    var html_option = '';
    var mapping = '';
    var name = '';
    for(var key in all_record_type)
    {
        mapping = all_record_type[key].C_MAPPING_CODE;
        name = all_record_type[key].C_NAME;
        html_option = '<option value="'+key+'" data-mapping="'+mapping+'" data-scope="3">'+key+' - '+name+'</option>';
        $("#sel_record_type").append(html_option);
    }
}

function sel_record_type_onchange(e)
{
    e.form.txt_record_type_code.value = e.value;
    if (trim(e.value) != '')
    {
        e.form.submit();
    }
}
function sel_can_bo_cap_xa_onchange(e)
{
	e.form.submit();
}
function get_notice(v_url)
{  
        //var v_url = $("#controller").val() + 'notice/' + role + '/' + QS + 't=' + getTime();
        $.ajax({
            cache: false,
            url: v_url,
            success: function(data) {
                $("#notice-container").html(data);
            }
        });
}

function get_supplement_notice()
{
    var status = $("#hdn_supplement_status").val();
    var v_url = $("#controller").val() + 'supplement_notice/' + status;
    $.ajax({
        cache: false,
        dataType: 'json',
        url: v_url,
        success: function(data) {
            $("#notice-container").html('<ul></ul>');
            html = '<ul>';
            var v_count_total = 0;
            $.each(data, function(key, val) {
                v_record_type_code = val.record_type_code;
                v_record_type_name = val.record_type_name;
                v_count = val.count_record;

                v_count_total += parseInt(v_count);

                html += '<li><a href="javascript:void(0)" onclick="set_record_type(\'' + v_record_type_code + '\')">'
                        + v_record_type_code + ' - ' + v_record_type_name
                        + ' có <span class="count">' + v_count + '</span> hồ sơ </a></li>';
            });
            html += '</ul>';
            $("#notice-container").html(html);

            q = "#supplement_count_" + status;
            parent.$(q).html('(' + v_count_total + ')');
        }
    });
    
//    $.getJSON(url, function(data) {
//        $("#notice-container").html('<ul></ul>');
//        html ='<ul>';
//        var v_count_total = 0;
//        $.each(data, function(key, val) {
//            v_record_type_code = val.record_type_code;
//            v_record_type_name = val.record_type_name;
//            v_count = val.count_record;
//
//            v_count_total += parseInt(v_count);
//
//            html += '<li><a href="javascript:void(0)" onclick="set_record_type(\'' + v_record_type_code + '\')">'
//                + v_record_type_code + ' - ' + v_record_type_name
//                + ' có <span class="count">' + v_count + '</span> hồ sơ </a></li>';
//        });
//        html +='</ul>';
//        $("#notice-container").html(html);
//
//        q = "#supplement_count_" + status;
//        parent.$(q).html('(' + v_count_total + ')');
//    });
}

function set_record_type(code)
{
    $("#sel_record_type").val(code);
    if ($("#sel_record_type").val() != '')
    {
    	try
    	{
    		$("#sel_can_bo_cap_xa").val('');
    	}
    	catch (e){;}
        $("#frmMain").submit();
    }
}

function dsp_single_record_statistics(record_id, tab)
{
    var url = $("#controller").val() + 'statistics/' + record_id + '&hdn_item_id=' + record_id + '&pop_win=1';
    if (typeof(tab) !== 'undefined')
	{
    	url += '&tab=' + tab;
	}
    
    if (window.parent)
    {
        window.parent.showPopWin(url, 1000, 600, null, true);
    }
    else
    {
        showPopWin(url, 1000, 600, null, true);
    }
}
