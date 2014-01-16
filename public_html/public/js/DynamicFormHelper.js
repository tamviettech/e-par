// Declaring required variables
var digits = "0123456789";
// non-digit characters which are allowed in phone numbers
var phoneNumberDelimiters = "()- ";
// characters which are allowed in international phone numbers
// (a leading + is OK)
var validWorldPhoneChars = phoneNumberDelimiters + "+";
// Minimum no of digits in an international phone no.
var minDigitsInIPhoneNumber = 7; //10


function DynamicFormHelper(data, file,f) {

    if ( (typeof(f) == 'undefined') || (f == null) ){
        f = document.forms[0];
    }

    this._xmlData = null;
    this._xmlFile = null;
    this._xmlDom = null;

    //Khoi tao xmlDom de lay thong tin danh sach xml element
    this._fields = new Array(f.length);
    for (i=0;i<f.length;i++) {
        var e=f.elements[i];
        var validate = e.getAttribute("data-validate");
        var allownull = e.getAttribute("data-allownull");
        var name=e.getAttribute("data-name");
        var id=e.id;

        var item = null;
        if (parseBoolean(e.getAttribute("data-xml"))){
            if (parseBoolean(e.getAttribute("data-doc"))){
                item = {
                    "Id": id,
                    "Name": name,
                    "Type": e.type,
                    "AllowNull": allownull,
                    "Validate": validate,
                    "doc": "true"
                };
            } else {
                item = {
                    "Id": id,
                    "Name": name,
                    "Type": e.type,
                    "AllowNull": allownull,
                    "Validate": validate,
                    "doc": "false"
                };
            }
            this._fields[i] = item;
        }
    }

    /****************************************************************************/
    /*         private methods
    /****************************************************************************/

    this.isIEBrowser = function() {
        return (navigator.appName == 'Microsoft Internet Explorer');
    }

    //check null string
    this.isEmpty = function(s) {
        return ((s == null) || (s.length == 0));
    }

    //check blank
    this.checkblank = function(str) {
        if (trim(str) == '') return true;
        else return false;
    }

    this.checkWhiteSpace = function(str_value) {
        //loai het cac khoang trang o dau
        while (str_value.length > 0 && str_value.charAt(0) == " ") {
            str_value = str_value.substring(1, str_value.length);
        }
        //loai het cac khoang trang o cuoi
        while (str_value.length > 0 && str_value.charAt(str_value.length - 1) == " ") {
            str_value = str_value.substring(0, str_value.length - 1);
        }

        return str_value;
    }

    this.isdate = function(dateStr) {
        var datePat = /^(\d{1,2})(\/|-)(\d{1,2})(\/|-)(\d{4})$/;
        var matchArray = dateStr.match(datePat); // is the format ok?

        if (matchArray == null) {
            //alert("Please enter date as either mm/dd/yyyy or mm-dd-yyyy.");
            return false;
        }
    }

    this.checkInternationalPhone = function(strPhone) {
        s = this.stripCharsInBag(strPhone, validWorldPhoneChars);

        return (this.isInteger(s) && s.length >= minDigitsInIPhoneNumber);
    }

    this.stripCharsInBag = function(s, bag) {
        var i;
        var returnString = "";
        // Search through string's characters one by one.
        // If character is not in bag, append to returnString.

        for (i = 0; i < s.length; i++) {
            // Check that current character isn't whitespace.
            var c = s.charAt(i);
            if (bag.indexOf(c) == -1) returnString += c;
        }
        return returnString;
    }

    this.isInteger = function(s) {
        var i;
        for (i = 0; i < s.length; i++) {
            // Check that current character is number.
            var c = s.charAt(i);
            if (((c < "0") || (c > "9"))) return false;
        }
        // All characters are numbers.
        return true;
    }

    this.formatCurrency = function(control_id, str_number) {
        /*Convert tu 1000->1.000*/
        /*var mynumber=1000;str_number = str_number.replace(/\./g,"");*/
        var mess = this.addCommas(str_number);
        if (mess == "fail") {
            document.getElementById(control_id).value = "";
        }
        else {
            document.getElementById(control_id).value = addCommas(str_number);
        }
    }

    this.addCommas = function(nStr) {
        var temp = nStr.replace(/,/g, "");
        if (isNaN(temp)) {
            alert("Không hợp lệ. Hãy nhập kiểu số !");
            return "fail";
        }
        else {
            temp += '';
            x = temp.split('.');
            x1 = x[0];
            x2 = "";
            x2 = x.length > 1 ? '.' + x[1] : '';

            var rgx = /(\d+)(\d{3})/;

            while (rgx.test(x1)) {
                x1 = x1.replace(rgx, '$1' + ',' + '$2');
            }
            return x1 + x2;
        }
    }

    //check validate a string compare a string was defined
    this.validateNo = function(NumStr, String) {
        for (var Idx = 0; Idx < NumStr.length; Idx++) {
            var Char = NumStr.charAt(Idx);
            var Match = false;

            for (var Idx1 = 0; Idx1 < String.length; Idx1++) {
                if (Char == String.charAt(Idx1)) {
                    Match = true;
                    break;
                }
            }
            if (!Match)
                return false;
        }
        return true;
    }

    this.checkValidate = function(control_id, name, allow, validate) {

        var mess = "";
        switch (validate) {
            case "text": //text validate
                mess = mess + this.checkString(control_id, name, allow);
                break;
            case "email": //email validate
                mess = mess + this.check_Email(control_id, name, allow);
                break;
            case "number": //number validate
                mess = mess + this.checkNumber(control_id, name, allow);
                break;
            case "phone": //phone validate
                mess = mess + this.validatePhoneNumber(control_id, name, allow);
                break;
            case "money": //money validate
                mess = mess + this.validateMoney(control_id, name, allow);
                break;
            case "date": //date validate
                mess = mess + this.checkDate(control_id, name, allow);
                break;
            case "ddli": //drop down list box validate
                mess = mess + this.checkDropDownListBox(control_id, name, allow);
                break;
            case "fax": //fax number validate
                mess = mess + this.validateFaxNumber(control_id, name, allow);
                break;
            case "numberString": //fax number string
                mess = mess + this.checkNumberString(control_id, name, allow);
                break;
                
            case "username": //Ten dang nhap
                mess = mess + this.checkUsername(control_id, name, allow);
                break;
                
            case "listcode": //Ten dang nhap
                mess = mess + this.checkListCode(control_id, name, allow);
                break;
                
            default:
                break;
        }
        //if (validate == 'phone')
        
        if (trim(mess) != '')//focus toi control bi loi
        {
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
                //var br = document.createElement('br');
                //errorMessage.appendChild(br);

                var error = document.createElement("span");
                error.setAttribute("id", id + "_span");
                //error.setAttribute("style", "color:red");
                error.style.color = "red";
                error.innerHTML = mess;

                errorMessage.appendChild(error);
                dom.parentNode.appendChild(errorMessage);
                dom.focus();
            }
        } else {
            //neu da sua loi roi thi tat thong bao loi di
            try {
                $("#error_" + control_id).html('');
            } catch (e){}
        }

        return mess;
    }

    this.checkString = function(control_id, name, allow) {
        var mess = "";
        var number = document.getElementById(control_id).value;
        var str = new String(number);

        if (allow == "no") {
            if (this.isEmpty(str)) {
                mess = mess + " Bạn chưa nhập " + name + " !";
            }
            else {
                var test_white_space = this.checkWhiteSpace(str);
                if (test_white_space == "") {
                    mess = mess + " " + name + " không được toàn là khoảng trắng!";
                }
            }
        }
        return mess;
    }
    
    /**
     * 
     * @param {type} control_id
     * @param {type} name
     * @param {type} allow
     * @returns {String}
     */
    this.checkUsername = function (control_id, name, allow) {
        var mess = "";
        var number = document.getElementById(control_id).value;
        var str = new String(number);

        if (allow == "no") {
            if (this.isEmpty(str)) {
                mess = mess + " Bạn chưa nhập " + name + " !";
            }
            else {
                var test_white_space = this.checkWhiteSpace(str);
                if (test_white_space == "") {
                    mess = mess + " " + name + " không được toàn là khoảng trắng!";
                } 
                else
                {
                    var ck_username = /^([a-zA-Z]+)([A-Za-z0-9.]*)$/;
                    if (!ck_username.test(str)) {
                        mess = mess + " " + name + " không hợp lệ";
                    }
                }
            }
        }
        return mess;
    }
    
    /**
     * Kieu ma danh muc
     * @param {type} control_id
     * @param {type} name
     * @param {type} allow
     * @returns {undefined}
     */
    this.checkListCode = function (control_id, name, allow) {
        var mess = "";
        var number = document.getElementById(control_id).value;
        var str = new String(number);

        if (allow == "no") {
            if (this.isEmpty(str)) {
                mess = mess + " Bạn chưa nhập " + name + " !";
            }
            else {
                var test_white_space = this.checkWhiteSpace(str);
                if (test_white_space == "") {
                    mess = mess + " " + name + " không được toàn là khoảng trắng!";
                } 
                else
                {
                    var ck_username = /^([A-Z]+)([A-Z0-9_]*)$/;
                    if (!ck_username.test(str)) {
                        mess = mess + " " + name + " không hợp lệ";
                    }
                }
            }
        }
        return mess;
    }

    this.check_Email = function(mail_id, name, allow) {
        var mess = "";
        var mail = document.getElementById(mail_id).value;
        var str = new String(mail);
        if (allow == "no") {
            if (this.isEmpty(str)) {
                mess = mess + " Bạn chưa nhập " + name + "!";
            }
            else {
                var filter = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
                if (!filter.test(str))
                    mess = mess + name + " không hợp lệ!";
                else
                    mess = mess + "";
            }
        }
        else {
            if (!this.isEmpty(str)) {
                var filter = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
                if (!filter.test(str))
                    mess = mess + name + " không hợp lệ!";
                else
                    mess = mess + "";
            }
        }
        return mess;
    }

    // kiem tra kieu so
    this.checkNumber = function(control_id, name, allow) {
        var mess = "";
        var number = document.getElementById(control_id).value;
        var str = new String(number);
        if (allow == "no") {
            if (this.isEmpty(str)) {
                mess = mess + " Bạn chưa nhập " + name + " !";
            }
            else {
                if (isNaN(str)) {
                    mess = name + " không hợp lệ. Hãy nhập kiểu số !";
                }
            }
        }
        else {
            if (isNaN(str)) {
                mess = name + " không hợp lệ. Hãy nhập kiểu số !";
            }
        }
        return mess;
    }

    this.validatePhoneNumber = function(control_id, name, allow) {
        var mess = "";

        var Phone = document.getElementById(control_id).value;



        if (allow == "no") {
            if ((Phone == null) || (Phone == "")) {
                mess = mess + " Bạn chưa nhập " + name;
                return mess;
            }
            if (Phone.length < 7 || Phone.length > 15) {
                mess = mess + " " + name + "  không đúng kiểu";
            }
            else {
                if (this.checkInternationalPhone(Phone) == false) {
                    mess = mess + " " + name + "  không đúng kiểu";
                    return mess;
                }
            }
        }
        else {

            if (!this.isEmpty(Phone)) {

                if (Phone.length < 7 || Phone.length > 15) {
                    mess = mess + " " + name + "  không đúng kiểu";
                }
                else {

                    if (this.checkInternationalPhone(Phone) == false) {
                        mess = mess + " " + name + "  không đúng kiểu";
                        return mess;
                    }
                }
            }
        }

        return mess;
    }

    this.validateMoney = function(control_id, name, allow) {
        var mess = "";
        var number = document.getElementById(control_id).value;
        var str = new String(number);
        str = str.replace(/,/g, "");
        if (allow == "no") {
            if (this.isEmpty(str)) {
                mess = mess + " Bạn chưa nhập " + name + " !";
            }
            else {
                if (isNaN(str)) {
                    mess = name + " không hợp lệ. Hãy nhập kiểu số !";
                }
            }
        }
        else {

        }
        return mess;
    }

    //Ham kiem tra dinh dang kieu ngay
    this.checkDate = function(control_id, name, allow) {
        var mess = "";
        var check;
        var the_date = document.getElementById(control_id).value;
        if (allow == "no") {
            if (this.isEmpty(the_date)) {
                mess = mess + " Bạn chưa nhập " + name + " !";
            }
            else {
                //            if(chkdate(the_date)==false)
                if (the_date == "Ngày/Tháng/Năm") {
                    mess = mess + " Bạn chưa nhập " + name + " !";
                }
                else {
                    if (this.isdate(the_date) == false) {
                        mess = name + " không hợp lệ !";
                    }
                }

            }
        }
        else {
            if (!this.isEmpty(the_date)) {
                if (the_date != "Ngày/Tháng/Năm") {
                    check = this.isdate(the_date)
                    if (check == false) {
                        mess = name + " không hợp lệ !";
                    }
                }
            }
        }
        return mess;
    }

    //check drop down list box
    this.checkDropDownListBox = function(control_id, name, allow) {
        var mess = "";
        var selectbox = document.getElementById(control_id);
        var ddlValue = selectbox.options[selectbox.selectedIndex].value;
        if (allow == "no") {
            if (ddlValue == "") {
                mess = mess + " Bạn chưa chọn " + name + "!";
            }
            else {

            }
        }
        else {

        }
        return mess;
    }

    //check fax number
    this.validateFaxNumber = function(control_id, name, allow) {
        var mess = "";
        var fax = document.getElementById(control_id).value;
        if (allow == "no") {
            if (fax == "") {
                mess = mess + " Bạn chưa nhập " + name + "!";
            }
            else {
                if (fax.length < 7 || fax.length > 15) {
                    mess = mess + name + " không đúng định dạng!";
                }
                else {
                    if (!this.validateNo(fax, "1234567890+-(). ")) {
                        mess = mess + name + " không đúng định dạng!";
                    }
                }
            }
        }
        else {
            if (fax != "") {
                if (fax.length < 7 || fax.length > 15) {
                    mess = mess + " " + name + " không đúng định dạng!";
                }
                else
                if (!this.validateNo(fax, "1234567890+-(). ")) {
                    mess = mess + " " + name + " không đúng định dạng!";
                }
            }
        }
        return mess;
    }

    //check number string
    this.checkNumberString = function(control_id, name, allow) {
        var mess = "";
        var numberString = document.getElementById(control_id).value;
        if (allow == "no") {
            if (numberString == "") {
                mess = mess + " Bạn chưa nhập " + name + "!";
            }
            else {
                if (!this.validateNo(numberString, "1234567890.,")) {
                    mess = mess + name + " không hợp lệ.Hãy nhập số!";
                }
            }
        }
        else {
            if (numberString != "") {
                if (!this.validateNo(numberString, "1234567890.,")) {
                    mess = mess + name + " không hợp lệ.Hãy nhập số!";
                }
            }
        }
        return mess;
    }

    this.findNodeById = function(id, collection) {
        for (var index = 0; index < collection.length; index++) {

            var obj = collection[index];

            if (obj.attributes != null && obj.nodeType == '1') {

                for (var subindex = 0; subindex < obj.attributes.length; subindex++) {
                    if (obj.attributes[subindex].nodeValue.toLowerCase() == id.toLowerCase()) {
                        return obj;
                    } //if obj
                } //for subindex
            } //if obj att
        } //for
    }

    this.findNodeByElementName = function(name, collection) {
        for (var index = 0; index < collection.length; index++) {

            var obj = collection[index];
            if (obj.tagName == name && obj.nodeType == '1') {

                return obj;
            }
        }
    }

    //Thuc thi truy van bang xpath
    this.executeQuery = function(xpath) {
        //alert(this._xmlFile);
        this._xmlDom.async = true;
        this._xmlDom.open("GET", this._xmlFile, false);
        this._xmlDom.send("");

        var xml = this._xmlDom.responseXML;
        //        alert(xml);
        var nodes = null;

        // code for IE
        if (window.ActiveXObject) {
            nodes = xml.selectNodes(xpath);
        }
        // code for Mozilla, Firefox, Opera, etc.
        else if (document.implementation && document.implementation.createDocument) {

            nodes = xml.evaluate(xpath, xml, null, XPathResult.ANY_TYPE, null);
        }

        return nodes;
    }        //function this
} //init object



/****************************************************************************/
/*         Public methods
/****************************************************************************/

/// Kiểm tra tính hợp lệ của dữ liệu trên form, va tra thong diep loi vao messagePlace
DynamicFormHelper.prototype.ValidateForm = function(f) {
    var message = "";
    for (i=0;i<f.length;i++) {
        var e=f.elements[i];
        var validate = e.getAttribute("data-validate");
        var allownull = e.getAttribute("data-allownull");
        var name=e.getAttribute("data-name");
        var id=e.id;

        result = this.checkValidate(id, name, allownull, validate);
        if (result != '') {
            message += "\n---" + result;
        }
    }

    return (message == '');
}

/// Lấy dữ liệu xml từ form động
DynamicFormHelper.prototype.GetXmlData = function() {

    var xmlData = '<?xml version="1.0" standalone="yes"?><data>';

    for (var index in this._fields) {

        var obj = this._fields[index];

        if (obj != null){

            var dom = document.getElementById(obj.Id);
            var objID = obj.Id;

            if (dom != null && dom.value != '' && dom.value != 'Ngày\/Tháng\/Năm') {
                if (dom.tagName.toLowerCase() == 'input' && dom.type == 'checkbox') {

                    //if (!obj.doc)
                    q = "#" + objID;
                    if (parseBoolean($(q).attr('data-doc')))
                    {
                        xmlData += '<item doc="true" id="' + obj.Id + '"><value>' + dom.checked + '</value></item>';
                    }
                    else
                    {
                        xmlData += '<item id="' + obj.Id + '"><value>' + dom.checked + '</value></item>';
                    }
                }
                else {
                    var val = dom.value;
                    val = val.replace(/&/gi,"&amp;");
                    val = val.replace(/>/gi,"&gt;");
                    val = val.replace(/</gi,"&lt;");
                    val = val.replace(/'/gi,"&#39;");
                    val = val.replace(/"/gi,"&quot;");

                    xmlData += '<item id="' + obj.Id + '"><value><![CDATA[' + val + ']]></value></item>';
                }
            }
        }
    }

    xmlData += "</data>";

    return xmlData;
}

/// Disable cac doi tuong XML tren form
DynamicFormHelper.prototype.DisableXmlData = function() {
    for (var index in this._fields) {
        var obj = this._fields[index];
        if (obj != null){
            var dom = document.getElementById(obj.Id);
			dom.disabled = true;
        }
    }

    return false;
}
/// Enable cac doi tuong XML tren form
DynamicFormHelper.prototype.EnableleXmlData = function() {
    for (var index in this._fields) {
        var obj = this._fields[index];
        if (obj != null){
            var dom = document.getElementById(obj.Id);
			dom.disabled = false ;
        }
    }
    return true;
}


/// Điền dữ liệu Xml vào form động
DynamicFormHelper.prototype.BindXmlData = function() {
    try {

        var xmlData = document.getElementById("XmlData");
        var text = xmlData.value;
        var xmlDoc = null;

        if (window.DOMParser) {
            parser = new DOMParser();
            xmlDoc = parser.parseFromString(text, "text/xml");
        }
        else // Internet Explorer
        {
            xmlDoc = new ActiveXObject("Microsoft.XMLDOM");
            xmlDoc.async = false;
            xmlDoc.loadXML(text);
        }

        var collection = xmlDoc.documentElement.childNodes;

        for (var index in this._fields) {

            var obj = this._fields[index];

            var dom = document.getElementById(obj.Id);

            if (dom != null) {

                var node = this.findNodeById(obj.Id, collection);

                if (node != null) {

                    if (dom.tagName.replace(/^[\s]+/, "").replace(/[\s]+$/, "").toUpperCase() === 'INPUT' && dom.type.replace(/^[\s]+/, "").replace(/[\s]+$/, "").toUpperCase() === 'CHECKBOX') {

                        var item = this.findNodeByElementName('value', node.childNodes);
                        var checked = '';

                        if (this.isIEBrowser()) {
							checked = unescape(item.text);
							if (checked == 'undefined')
							{
								checked = unescape(item.textContent.replace(/^[\s]+/, "").replace(/[\s]+$/, ""));
							}
                        }
                        else {
                            checked = unescape(item.textContent.replace(/^[\s]+/, "").replace(/[\s]+$/, ""));
                        } //if IE

						checked = unescape(item.textContent.replace(/^[\s]+/, "").replace(/[\s]+$/, ""));
                        //alert(checked);
                        if (checked == 'true') {
                            dom.checked = true;
                            if (obj.Id == 'ckbTaiLieuKhac')
                            {
                                $("#txtTaiLieuKhac").show();
                            }
                        }
                        else {
                            dom.checked = false;
                        }
                    }
                    else {
                        if (this.isIEBrowser()) {
							dom_value = unescape(node.text);
							if (dom_value == 'undefined')
							{
								dom_value = unescape(node.textContent.replace(/^[\s]+/, "").replace(/[\s]+$/, ""));
							}
                            dom.value = dom_value
                        }
                        else {
                            dom.value = unescape(node.textContent.replace(/^[\s]+/, "").replace(/[\s]+$/, ""));
                        } //if IE
                    }
                } //if node
            }
        }
    }
    catch (ex) {

    }
}

/// Lấy dữ liệu từ nguồn dữ liệu xml
/// @key: là attribute
/// @xmlSource: là nguồn chứa dữ liệu xml
DynamicFormHelper.prototype.GetDataFromXml = function(key, xmlSource) {
    var text = xmlSource;

    var xmlDoc = null;

    if (window.DOMParser) {
        parser = new DOMParser();
        xmlDoc = parser.parseFromString(text, "text/xml");
    }
    else // Internet Explorer
    {
        xmlDoc = new ActiveXObject("Microsoft.XMLDOM");
        xmlDoc.async = "false";
        xmlDoc.loadXML(text);
    }

    var collection = xmlDoc.documentElement.childNodes;

    var node = this.findNodeById(key, collection);

    if (node != null) {
        if (this.isIEBrowser()) {
            return unescape(node.text);
        }
        else {
            return unescape(node.textContent.trim());
        } //if IE
    } //if node

    return null;
}
///******************************************************************************
/// Mehods helper
///******************************************************************************
function ConverUpperCase(controlID, str_) {
    var str_return;
    str_return = str_.toUpperCase();
    document.getElementById(controlID).value = str_return;
}
function ConvertInitCase(controlID, str_) {
    document.getElementById(controlID).value = (str_ + '').replace(/^([a-z])|\s+([a-z])/g, function ($1) {return $1.toUpperCase();});
}
function iif(expression, condition1, condition2) {
    if (expression)
        return condition1;
    else
        return condition2;
}


//tro ve root
//tro ve root

function GetRoot() {
    var url = document.location.toString(); //url
    var e_url = ''; //edited url
    var p = 0; //position
    var p2 = 0; //position 2
    p = url.indexOf("//");
    e_url = url.substring(p + 2);
    p2 = e_url.indexOf("/");
    var root_url = url.substring(0, p + p2 + 3);
    return root_url;
}
// Hiển thị text area
function Textarea(control_id,view_id)
{
   try
   {
        var objCheck = document.getElementById(control_id).checked;
        if(objCheck ==false)
        {
            document.getElementById('txtTaiLieuKhac').style.display="none";
        }
        else
        {
           document.getElementById('txtTaiLieuKhac').style.display="block";
        }
    }
    catch(err)
    {}
}

//dinh dang ngay thang
 function OnfocusoutDate(control_id)
{
       var Obj=document.getElementById(control_id).value;
       if(document.getElementById(control_id).value=="Ngày/Tháng/Năm"||document.getElementById(control_id).value==""||document.getElementById(control_id).value=="null")
       {
           document.getElementById(control_id).value="Ngày/Tháng/Năm";
       }
       else
       {
         document.getElementById(control_id).value=Obj;
       }

}
function onfocusData(control_id)
{
     var Obj=document.getElementById(control_id).value;
       if(document.getElementById(control_id).value=="Ngày/Tháng/Năm"||document.getElementById(control_id).value==""||document.getElementById(control_id).value=="null")
       {
           document.getElementById(control_id).value="";
       }
       else
       {
         document.getElementById(control_id).value=Obj;
       }
}


function GetApplicationView()
{

    var Obj = Page_DocumentApplicationView.LoadData();
    var sXMLReturn ="";
    if(Obj!=null)
    {
        var dataset=Obj.value;
        if(dataset!=null && dataset.Tables.length>0)
        {
             var numRecord=dataset.Tables[0].Rows.length;
             if(numRecord>0)
             {

                sXMLReturn = dataset.Tables[0].Rows[0]["DocumentContent"];
                SetValueControl(sXMLReturn);

                document.getElementById("txtDocumentCode").value = dataset.Tables[0].Rows[0]["DocumentCode"];
                document.getElementById("txtDocumentName").value = dataset.Tables[0].Rows[0]["DocumentName"];
                document.getElementById("txtDateReceiver").value = dataset.Tables[0].Rows[0]["DateReceiver"];
                document.getElementById("txtDateReturn").value = dataset.Tables[0].Rows[0]["DateReturn"];
                /*
                document.getElementById("ddlisNew").value = dataset.Tables[0].Rows[0]["IsNews"];
                */
             }
        }
    }
}
