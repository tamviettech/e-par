/**
// File name   : js_record.js
// Version     : 1.0.0.1
// Begin       : 2012-12-01
// Last Update : 2010-12-25
// Author      : TamViet Technology, Ha Noi, Viet Nam. http://www.tamviettech.vn
// License     : GNU-LGPL v3 (http://www.gnu.org/copyleft/lesser.html)
// -------------------------------------------------------------------
//Copyright (C) 2012-2013  TamViet Technology, Ha Noi, Viet Nam. http://www.tamviettech.vn

// E-PAR is free software: you can redistribute it and/or modify it
// under the terms of the GNU Lesser General Public License as
// published by the Free Software Foundation, either version 3 of the
// License, or (at your option) any later version.
//
// E-PAR is distributed in the hope that it will be useful, but
// WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
// See the GNU Lesser General Public License for more details.
//
// See LICENSE.TXT file for more information.
*/
function txt_record_type_code_onkeypress(evt)
{
    if (IE()){
        theKey=window.event.keyCode
    } else {
        theKey=evt.which;
    }

    if(theKey == 13){
        v_record_type_code = trim($("#txt_record_type_code").val()).toUpperCase();
        $("#sel_record_type").val(v_record_type_code);
        if ($("#sel_record_type").val() != '')
        {
            $("#frmMain").submit();
        }
        else
        {
            $("#procedure").html('');
        }
    }
    return false;
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
function get_notice(role)
{
    if ($("#notice-container").length > 0)
    {
        if (typeof(role) == 'undefined')
        {
            role = $("#hnd_role").val();
        }

        var url = $("#controller").val() + 'notice/' + role;

        $.getJSON(url, function(data) {

            html ='<ul>';
            $.each(data, function(key, val) {
                v_record_type_code = val.record_type_code;
                v_record_type_name = val.record_type_name;
                v_count = val.count_record;

                /*
                if (parseInt(v_count) < 10)
                {
                    v_count = '0' + v_count;
                }
                */

                html += '<li><a href="javascript:void(0)" onclick="set_record_type(\'' + v_record_type_code + '\')">- '
                    + v_record_type_code + ' - ' + v_record_type_name
                    + ' có <span class="count">' + v_count + '</span> hồ sơ </a></li>';
            });
            html +='</ul>';
            $("#notice-container").html('<ul></ul>');
            $("#notice-container").html(html);
        });
    }
}

function get_supplement_notice()
{
    var status = $("#hdn_supplement_status").val();
    var url = $("#controller").val() + 'supplement_notice/' + status;

    $.getJSON(url, function(data) {
        $("#notice-container").html('<ul></ul>');
        html ='<ul>';
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
        html +='</ul>';
        $("#notice-container").html(html);

        q = "#supplement_count_" + status;
        parent.$(q).html('(' + v_count_total + ')');
    });
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
