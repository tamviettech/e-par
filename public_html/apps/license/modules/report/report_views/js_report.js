function sel_report_type_onchange(){
    var report_type = $('#sel_report_type').val();
    var $license_type = $('#control-group-license-type');
    var $report_year = $('#control-group-year');
    if(report_type == 1){
        $license_type.css('display','block');
        $report_year.css('display','block');
    }else if(report_type == 2){
        $license_type.css('display','none');
        $report_year.css('display','block');
    }else if(report_type == 3){
        $license_type.css('display','none');
        $report_year.css('display','block');
    }
}

function sel_license_type_onchange(e, no_submit)
{
    e.form.txt_license_type_code.value = e.value;

    if (no_submit == 1) {
        return false;
    }
    if (trim(e.value) != '')
    {
        e.form.submit();
    }
}
function txt_license_type_code_onkeypress(evt, no_submit)
{
    
    evt.preventDefault();
    if (IE()) {
        theKey = window.event.keyCode;
    } else {
        theKey = evt.which;
    }
    v_license_type_code = trim($("#txt_license_type_code").val()).toUpperCase();
    console.log(v_license_type_code);
    $("#sel_license_type").val(v_license_type_code);
    if (theKey == 13) {
        if (no_submit) {
            return false;
        } else if ($("#sel_license_type").val() != '')
        {
            $("#frmMain").submit();
        }
        else
        {
            $("#table_record").html('');
        }
    }
    return false;
}