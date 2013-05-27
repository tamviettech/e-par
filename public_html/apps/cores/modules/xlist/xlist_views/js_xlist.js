function btn_select_listype_xml_file_onclick(){
    var url =  $("#controller").val() + 'dsp_all_file_xml';
    showPopWin(url ,800,500, null);
}
function sel_listtype_filter_onchange(listtype_id, method){
    var f=document.frmMain;
    
    if (typeof(method) == 'undefined')
    {
        method = f.hdn_dsp_all_method.value;
    }
    m = $("#controller").val() + method;
    $("#frmMain").attr("action", m);
    f.submit();
}