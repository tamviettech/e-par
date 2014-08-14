//Kiem tra browser la MS IE
function IE(){
    return (navigator.appName == 'Microsoft Internet Explorer');
}

function DoCal(p_obj_id,p_delim){
    if (typeof(p_delim) == 'undefined')
    {
        p_delim = '-';
    }

    var q = '#' + p_obj_id;
    $(q).datepicker({
        changeYear: true,
        changeMonth: true,
        dateFormat: 'dd' + p_delim + 'mm' + p_delim + 'yy'
    });
    $(q).focus();
}

function handleEnter (field, event) {
    var keyCode = event.keyCode ? event.keyCode : event.which ? event.which : event.charCode;
    if (keyCode == 13) {
        var i;
        for (i = 0; i < field.form.elements.length; i++)
            if (field == field.form.elements[i])
                break;
        i = (i + 1) % field.form.elements.length;
        field.form.elements[i].focus();
        return false;
    }
    else {
        return true;
    }
}

function parseBoolean(string) {
    if (string == '') {
        return false;
    }
    switch (String(string).toLowerCase()) {
        case "true":
        case "1":
        case "yes":
        case "y":
            return true;
    }

    return false;
}

function show_error(control_id, mess){
    var dom = document.getElementById(control_id);
    if (dom != null) {

        var id = "error_" + control_id;

        var exist = document.getElementById(id);
        //Xoa thong bao loi neu da co
        if (exist) {
            dom.parentNode.removeChild(exist);
        }

        //Tao thong bao loi
        var errorMessage = document.createElement("div");
        errorMessage.setAttribute("id", id);

        var error = document.createElement("span");
        error.setAttribute("id", id);
        error.style.color = "red";
        error.innerHTML = mess;

        errorMessage.appendChild(error);
        dom.parentNode.appendChild(errorMessage);
        dom.focus();
    }
}

function clear_error(control_id){
    var dom = document.getElementById(control_id);
    if (dom != null) {

        var id = "error_" + control_id;

        var exist = document.getElementById(id);
        //Xoa thong bao loi neu da co
        if (exist) {
            dom.parentNode.removeChild(exist);
        }
    }
}

// Xu ly khi NSD nhan phim ENTER trong o textbox "Loc theo..."
function txt_filter_onkeypress(btn_filter,evt){
    if (IE()){
        theKey=window.event.keyCode
    } else {
        theKey=evt.which;
    }
    if(theKey == 13){
        btn_filter.click();
        return;
    }
}

function btn_filter_onclick(){
    var f = document.frmMain;
    m = $("#controller").val() + f.hdn_dsp_all_method.value;
    $("#frmMain").attr("action", m);
    f.submit();
}

function btn_addnew_onclick(){
    var f = document.frmMain;
    m = $("#controller").val() + f.hdn_dsp_single_method.value + '/0/';
    $("#frmMain").attr("action", m);
    f.submit();
}

function check_all(field){

    if (field.length){
        for (i = 0; i < field.length; i++) {
            if (!field[i].disabled){
                field[i].checked = true;
            }
        }
    } else {
        if (!field.disabled){
            field.checked = true;
        }
    }

}

function uncheck_all(field){

    if (field.length){
        for (i = 0; i < field.length; i++) {
            if (!field[i].disabled){
                field[i].checked = false;
            }
        }
    }else {
        if (!field.disabled){
            field.checked = false;
        }
    }

}

function toggle_check_all(chk_obj,field){
    if (chk_obj.checked){
        check_all(field);
    }
    else{
        uncheck_all(field);
    }
}

function row_onclick(item_id,chk_name){
    var f = document.frmMain;
    if (typeof(chk_name) == 'undefined'){
        chk_name = f.chk;
    }

    if (chk_name.length){
        for (i=0;i < chk_name.length;i++){
            chk_name[i].checked = (chk_name[i].value == item_id);
        }
    } else{
        chk_name.checked = (chk_name.value == item_id);
    }

    f.hdn_item_id.value =  item_id;
    m = $("#controller").val() + f.hdn_dsp_single_method.value + '/' + item_id;
    $("#frmMain").attr("action", m);
    f.submit();
}

function delete_row(item_id,chk_name){
    var f = document.frmMain;
    try
    {
        if (typeof(chk_name) == 'undefined'){
            chk_name = f.chk;
        }

        if (chk_name.length){
            for (i=0;i < chk_name.length;i++){
                chk_name[i].checked = (chk_name[i].value == item_id);
            }
        } else{
            chk_name.checked = (chk_name.value == item_id);
        }
    }
    catch (e){
    ;
    }

    f.hdn_item_id.value =  item_id;
    btn_delete_onclick();
}


function btn_update_onclick(){
    var f = document.frmMain;
    m = $("#controller").val() + f.hdn_update_method.value + '/0/';
    var xObj = new DynamicFormHelper('','',f);
    if (xObj.ValidateForm(f)){
        f.XmlData.value = xObj.GetXmlData();
        $("#frmMain").attr("action", m);
        f.submit();
    }
}

function btn_back_onclick(){
    var f = document.frmMain;
    m = $("#controller").val() + f.hdn_dsp_all_method.value;
    $("#frmMain").attr("action", m);
    f.submit();
}

/**
 * Xu li su kien nhan nut trash (Xoa)
 */
function btn_delete_onclick(hdn_item_id_list){
    var f = document.frmMain;
    var v_item_id = "0";
    var v_item_id_list = "";
    var error_message = 'Chưa có đối tượng nào được chọn!';

    if (typeof(f.chk) == 'undefined' ){
        alert(error_message);
        return;
    }

    v_item_id_list = get_all_checked_checkbox(f.chk,",");

    if (v_item_id_list == ""){
        alert(error_message);
        return;
    }

    if (confirm('Bạn chắc chắn xoá các đối tượng đã chọn?')){
        f.hdn_item_id_list.value =  v_item_id_list;
        m = $("#controller").val() + f.hdn_delete_method.value;
        $("#frmMain").attr("action", m);
        f.submit();
    }
}

function quick_delete_item(item_id)
{
    var f = document.frmMain;
    if (confirm('Bạn chắc chắn xoá đối tượng đã chọn?')){
        f.hdn_item_id_list.value =  item_id;
        m = $("#controller").val() + f.hdn_delete_method.value;
        $("#frmMain").attr("action", m);
        f.submit();
    }
}


/**
* Lay tat ca gia tri cua cac checkbox da duoc check tren form
* @param string Ten doi tuong checkbox
* @param string dau hieu phan cach giua cac gia tri duoc tra ve
* @return string Xau the hien cac gia tri cua cac checkbox da duoc check, moi gia tri cach nhau boi dau hieu phan cach
*/
function get_all_checked_checkbox(checkbox_name,separator){
    //Chu y khi truyen tham tri: checkbox_name co dang document.forms[0].ten_checkbox
    var ret_string;
    var i;
    var int_checkbox_count;

    if (separator == null) separator = ',';

    if (typeof(checkbox_name) == 'undefined') return '';

    if (checkbox_name.length){
        int_checkbox_count = checkbox_name.length;
    } else {
        int_checkbox_count = 0;
    }
    ret_string="";

    if (!checkbox_name.length){
        if (checkbox_name.checked){
            ret_string = checkbox_name.value;
        }
    } else{
        for(i = 0;i < int_checkbox_count; i++){
            if (checkbox_name[i].checked){
                if (ret_string=="")
                    ret_string+=checkbox_name[i].value;
                else
                    ret_string+=separator + checkbox_name[i].value;
            }
        }
    }

    return ret_string;
}//end func get_all_checked_checkbox()


function inputInt(obj){
    if(obj.value==""){
        return true;
    }
    var i,c,value;
    value="";
    for(i=0;i<obj.value.length;i++){
        c=obj.value.charAt(i);
        if(c=="-"){
            if(i==0){
                value+=c;
            }
        }else{
            if(c>="0"&&c<="9"){
                value+=c;
            }
        }
    }
    if(obj.value!=value){
        obj.value=value;
    }
    return false;
}

function inputIntPlus(obj){
    if(obj.value==""){
        return true;
    }
    var i,c,value;
    value="";
    for(i=0;i<obj.value.length;i++){
        c=obj.value.charAt(i);
        if(c>="0"&&c<="9")
        {
            value+=c;
        }
    }
    if(obj.value!=value){
        obj.value=value;
    }
    return false;
}
function GetNexVal(seq_name, html_obj_id)
{
    var v_url = SITE_ROOT + 'cores/sequence/next_val/'  + seq_name;

    var q = '#' + html_obj_id;
    $.ajax({
        url:v_url
        ,
        success:function(result){
            $(q).val(result);
        }
    });

    var f = document.frmMain;
    f.seq_name.value = seq_name;
}

// LTrim(string) : Returns a copy of a string without leading spaces.
function ltrim(str)
{
    var whitespace = new String(" \t\n\r");
    var s = new String(str);
    if (whitespace.indexOf(s.charAt(0)) != -1) {
        var j=0, i = s.length;
        while (j < i && whitespace.indexOf(s.charAt(j)) != -1)
            j++;
        s = s.substring(j, i);
    }
    return s;
}

//RTrim(string) : Returns a copy of a string without trailing spaces.
function rtrim(str)
{
    var whitespace = new String(" \t\n\r");
    var s = new String(str);
    if (whitespace.indexOf(s.charAt(s.length-1)) != -1) {
        var i = s.length - 1;       // Get length of string
        while (i >= 0 && whitespace.indexOf(s.charAt(i)) != -1)
            i--;
        s = s.substring(0, i+1);
    }
    return s;
}

// Trim(string) : Returns a copy of a string without leading or trailing spaces
function trim(str) {
    return rtrim(ltrim(str));
}

function loadXMLString(txt)
{
    if (window.DOMParser)
    {
        parser=new DOMParser();
        xmlDoc=parser.parseFromString(txt,"text/xml");
    }
    else // Internet Explorer
    {
        xmlDoc=new ActiveXObject("Microsoft.XMLDOM");
        xmlDoc.async=false;
        xmlDoc.loadXML(txt);
    }
    return xmlDoc;
}
// doc so thanh chu
function numberic_to_string(so)
{
    var i;
    var j;
    var kq = "";
    var l;
    var dk;
    var tmp = "";
    var check = false;
    var a = new Array(32);

    //kiem tra kieu so
    //Loai het so 0 o dau
    while (so.length > 0 && so.charAt(0) == "0"){
        so = so.substring(1,so.length);
    }
    //alert(so);
    l = so.length;
    if (l > 28){
        return "Số không hợp lệ";
    }

    //Load cac chu so cua so can doc
    //vao mang a
    for (var i=1;i<=l;i++){
        a[i] = parseInt(so.charAt(i-1));
    }
    //Bat dau doc tu trai sang phai
    for (var i=1;i<=l;i++){

        if((l - i) % 3 == 2 && a[i] == 0 && (l - i >= 2)) {
            if (a[i + 1] != 0 || a[i + 2] != 0) {
                kq = kq + "không ";
            }
        }

        if (a[i] == 2){
            kq = kq + "hai ";
        }
        if (a[i] == 3){
            kq = kq + "ba ";
        }
        if (a[i] == 6){
            kq = kq + "sáu ";
        }
        if (a[i] == 7){
            kq = kq + "bảy ";
        }
        if (a[i] == 8){
            kq = kq + "tám ";
        }
        if (a[i] == 9){
            kq = kq + "chín ";
        }


        //Xu ly cach doc so 4
        if (a[i] == 4) {
            if (i > 1 && (l - i) % 3 == 0){
                if (a[i - 1] > 1){
                    kq = kq + "tư ";
                }else{
                    kq = kq + "bốn ";
                }
            }else{
                kq = kq + "bốn ";
            }
        } //a(i)=4

        //Xu ly cach doc so 5
        if (a[i] == 5){
            if (i > 1 && (l - i)% 3 == 0){
                if (a[i - 1] != 0 ){
                    kq = kq + "lăm ";
                }else{
                    kq = kq + "năm ";
                }
            }else{
                kq = kq + "năm ";
            }
        } //a(i)=5

        //Xu ly cach doc so 1
        if (a[i] == 1) {
            //doc la muoi neu no la hang chuc
            if ((l - i) % 3 == 1) {
                kq = kq + "mười ";	//doc la mot neu la hang don vi	//va hang chuc >1
            }else{
                if ((l - i) % 3 == 0 && (i > 1)){
                    if (a[i - 1] > 1){
                        kq = kq + "mốt ";
                    }else{
                        kq = kq + "một ";
                    }
                }else{
                    kq = kq + "một ";
                }
            }
        } //a(i)=1


        //Doc tiep la muoi neu
        //No la so hang chuc va
        //Khac 1 va 0
        if ((l - i) % 3 == 1 && a[i] != 0 && a[i] != 1){
            kq = kq + "mươi ";
        }

        if ((l - i) % 3 == 1 && a[i] == 0 && a[i + 1] != 0){
            kq = kq + "linh ";
        }

        if ((l - i) % 3 == 2 && (a[i + 1] != 0 || a[i + 2] != 0)){
            kq = kq + "trăm ";
        }

        if ((i + 2) <= l) {
            if (a[i] != 0 && (l - i) % 3 == 2){
                if (a[i + 1] == 0 && a[i + 2] == 0){
                    kq = kq + "trăm ";
                }
            }
        }

        if ((l - i) == 3){
            kq = kq + "nghìn ";
        }
        if ((l - i) == 6){
            kq = kq + "triệu ";
        }
        if ((l - i) == 9){
            kq = kq + "tỷ ";
        }

        if ((l - i) == 12){
            check = true;
            for (j=i+1;i<l;i++){
                if (a[i + 1] != 0){
                    check = false;
                }
            }
            if (check == false) {
                kq = kq + "nghìn ";
            }else{
                kq = kq + "nghìn tỷ ";
            }
        }

        if ((l - i) == 15){
            kq = kq + "triệu tỷ ";
        }
        if ((l - i) == 18){
            kq = kq + "tỷ tỷ ";
        }
        if ((l - i) == 21){
            kq = kq + "nghìn tỷ tỷ ";
        }
        if ((l - i) == 24){
            kq = kq + "triệu tỷ tỷ ";
        }
        if ((l - i) == 27){
            kq = kq + "tỷ tỷ tỷ ";
        }
        if ((l - i) == 30){
            kq = kq + "nghìn tỷ tỷ ";
        }

        //Xu ly bo 3 so khong
        if (((l - i) % 3 == 2) && (a[i] == 0) && (a[i + 1] == 0) && (a[i + 2] == 0)){
            i = i + 2;
        }

        //Xu ly tat ca so khong con lai
        if ((l - i) % 3 == 0){
            dk = 1;
            for (j=i+1;j<=l;j++){
                if (a[j] != 0){
                    dk = 0;
                }
            }
        }
        if (dk == 1){
            break;
        }

    }

    //Viet hoa chu cai dau tien
    if (kq == "") kq = "không"
    while (kq.charAt(kq.length) == ","){
        kq = kq.substring(0,kq.length-1);
    }
    kq = kq.charAt(0).toUpperCase() + kq.substring(1,kq.length);
    return kq + " đồng";
}
function addCommas(nStr)
{
    nStr += '';
    nStr = removeCommas(nStr);
    x = nStr.split('.');
    x1 = x[0];
    x2 = x.length > 1 ? '.' + x[1] : '';
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1)) {
        x1 = x1.replace(rgx, '$1' + ',' + '$2');
    }
    return x1 + x2;
}
function removeCommas(nStr){
    if ((nStr == ''))
    {
        return nStr;
    }
    return nStr.replace(/,/g,'');
}
function ReadNumberToString(control_id,view_id)
{
    $('#' + view_id).html(numberic_to_string(removeCommas($('#' + control_id).val())));
    $('#' + control_id).val(addCommas($('#' + control_id).val()));

}
//Convert date from ddmmyyyy format to mmddyyyy fromat
function ddmmyyyy_to_yyyymmdd(theDate)
{
    strSeparator = "";

    if (theDate.indexOf("/")!=-1) strSeparator = "/";
    if (theDate.indexOf("-")!=-1) strSeparator = "-";
    if (theDate.indexOf(".")!=-1) strSeparator = ".";

    if (strSeparator == "") return "";

    parts=theDate.split(strSeparator);
    day=parts[0];
    month=parts[1];
    year=parts[2];

    return year.substr(0,4) + strSeparator + month + strSeparator + day;

}

function w(string)
{
	document.write(string);
}

function getTime()
{
    var v = (new Date());
    var h, m, s,t;

    h = v.getHours();
    m = v.getMinutes();
    s = v.getSeconds();
    t=h*10000+m*100+s;
	
    return t;
}

function login_name_validate(str_login_name)
{
    patt = /^([a-z]+)([a-z0-9.@]{4,30})$/;
    return patt.test(str_login_name);
}