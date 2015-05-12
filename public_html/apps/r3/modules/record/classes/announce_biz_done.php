<?php

class announce_biz_done extends announce_abstract
{

    /**
     * 
     * @var array 
     */
    protected $_arr_single;

    function __construct($mail_to, $arr_single)
    {
        parent::__construct($mail_to);
        $this->_arr_single = $arr_single;
    }

    function message_body()
    {
        $v_record_no           = $this->_arr_single['C_RECORD_NO'];
        $v_record_type_code    = $this->_arr_single['C_RECORD_TYPE_CODE'];
        $v_record_type_name    = $this->_arr_single['C_RECORD_TYPE_NAME'];
        $v_receive_date        = $this->_arr_single['C_RECEIVE_DATE'];
        $v_return_date         = $this->_arr_single['C_RETURN_DATE'];
        $v_xml_data            = $this->_arr_single['C_XML_DATA'];
        $v_return_phone_number = $this->_arr_single['C_RETURN_PHONE_NUMBER'];

        $v_receive_date        = date_create($v_receive_date)->format('d-m-Y H:i');
        $v_return_date         = date_create($v_return_date)->format('d-m-Y H:i');
        $v_return_date_by_text = r3_View::return_date_by_text($this->_arr_single['C_RETURN_DATE']);

        $dom_data       = new SimpleXMLElement($this->_arr_single['C_XML_DATA'], LIBXML_NOCDATA);
        $v_addr         = get_xml_value($dom_data, "//item[@id='txtDiaChi']/value");
        $v_citizen_name = get_xml_value($dom_data, "//item[@id='txtName']/value");

        $dom_unit_info = simplexml_load_file(SERVER_ROOT . 'public/xml/xml_unit_info.xml');
        $v_unit_name   = $dom_unit_info->full_name;
        $v_unit_phone  = get_xml_value($dom_unit_info, '//phone_number');

        return "$v_unit_name
                BỘ PHẬN TIẾP NHẬN VÀ TRẢ KẾT QUẢ
                --------------------------------
                \n
                THÔNG BÁO HOÀN THÀNH THỤ LÝ
                \n
                Thủ tục:                            $v_record_type_name
                Họ và tên người nộp hồ sơ:          $v_citizen_name
                Địa chỉ thường trú:                 $v_addr
                Ngày giờ tiếp nhận:                 $v_receive_date
                Ngày giờ hẹn trả:                   $v_return_date_by_text
                Bộ phận \"Một cửa\" xin thông báo hồ sơ của Ông/bà đã hoàn thành thụ lý và chuyển đến bước trả kêt quả.
                Vậy mời Ông/bà đến trụ sở Uỷ ban lấy kết quả và mang theo phiếu hẹn, CMTND, lệ phí và giấy uỷ quyền (nếu có).
                Số điện thoại của Bộ phận “Một cửa”: $v_unit_phone
                ";
    }

    function message_subject()
    {
        return 'Bộ phận Một cửa - Thông báo hoàn thành thụ lý';
    }

}