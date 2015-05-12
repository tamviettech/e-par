function sel_license_type_onchange(e,no_submit)
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
    if (IE()) {
        theKey = window.event.keyCode;
    } else {
        theKey = evt.which;
    }

    if (theKey == 13) {
        v_license_type_code = trim($("#txt_license_type_code").val()).toUpperCase();
        console.log(v_license_type_code);
        $("#sel_license_type").val(v_license_type_code);
        if (no_submit) {
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