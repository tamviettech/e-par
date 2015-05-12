<?php

class announce_suppliment extends announce_abstract
{

    protected $_arr_single_record;
    protected $_record_type_name;

    function __construct($mail_to, $arr_single_record, $record_type_name)
    {
        $this->_arr_single_record = $arr_single_record;
        $this->_record_type_name  = $record_type_name;
        parent::__construct($mail_to);
    }

    function message_body()
    {
        $receive_date   = date_create($this->_arr_single_record['C_RECEIVE_DATE'])->format('d-m-Y H:i');
        $dom_processing = simplexml_load_string($this->_arr_single_record['C_XML_PROCESSING']);
        $v_reason       = xpath($dom_processing, "//next_task/@reason", true);
        return "Kính gửi: Ông/bà: ĐẶNG MINH HƯỜNG\n"
                . "Ngày {$receive_date}, Bộ phận tiếp nhận và trả hồ sơ đã tiếp nhận hồ sơ \"{$this->_record_type_name}\" của Ông/bà."
                . " Sau khi kiểm tra hồ sơ, đối chiếu với quy định pháp luật. Bộ phận tiếp nhận và trả hồ sơ đề nghị Ông/bà bổ sung hồ sơ với lý do cụ thể như sau:\n"
                . strval($v_reason) . "\n"
                . "Vậy, đề nghị Ông/bà bổ sung theo quy định.";
    }

    function message_subject()
    {
        return 'Bộ phận một cửa - Thông báo bổ sung hồ sơ';
    }

}