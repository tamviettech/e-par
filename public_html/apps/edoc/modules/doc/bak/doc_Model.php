<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed');

class doc_Model extends Model {

    function __construct() {
        parent::__construct();
    }

    //Lay Loai van ban dau tien (theo C_ORDER) theo chieu di/den cua van ban
    public function qry_first_doc_type_by_direction($direction)
    {
        $direction = strtoupper($this->replace_bad_char($direction));
        $stmt = "Select top 1 L.C_CODE "
                . " From T_LIST as L CROSS APPLY C_XML_DATA.nodes('/data') t(item)"
                . " Where L.FK_LISTTYPE=(Select PK_LISTTYPE From T_LISTTYPE Where C_CODE='DM_LOAI_VAN_BAN')"
                . "     And item.value('(item[@id='?']/value/text())[1]','varchar(50)')='true'"
                . " Order by L.C_ORDER";
        $params = array($direction);
        return $this->db->getOne($stmt, $params);
    }

    //Lay danh sach Loai van ban, theo chieu di/den
    public function qry_all_doc_type_by_direction($direction)
    {
        $direction = strtoupper($this->replace_bad_char($direction));
        $stmt = "Select LOWER(a.C_CODE), (a.C_NAME + ' (' + convert(varchar, a.COUNT_NEW_DOC) + ')') as C_NAME
            From (Select L.C_CODE,L.C_NAME, L.C_ORDER, (Select COUNT(*) From T_EDOC_DOC Where C_DIRECTION=? And C_TYPE=L.C_CODE AND PK_DOC in (Select distinct FK_DOC DOC_ID From T_EDOC_MESSAGE Where (C_TO_USER_ID=? OR C_FROM_USER_ID=?) UNION ALL Select PK_DOC DOC_ID From T_EDOC_DOC Where FK_USER=?)) as COUNT_NEW_DOC "
                . " From T_LIST as L CROSS APPLY C_XML_DATA.nodes('/data') t(item) "
                . " Where L.FK_LISTTYPE=(Select PK_LISTTYPE From T_LISTTYPE Where C_CODE='DM_LOAI_VAN_BAN') "
                    . " And item.value('(item[@id='?']/value/text())[1]','varchar(50)')='true') a"
                . " Order By a.C_ORDER";
        $params = array($direction, Session::get('user_id'), Session::get('user_id'), Session::get('user_id'), $direction);
        return $this->db->getAssoc($stmt, $params);
    }

    //Lay danh sach VBDEN -> tra cuu
    public function qry_all_doc_type_for_lookup()
    {
        //Quyen giam sat
        $v_limit_view_query = " AND doc.value('(item[@id=''cho_phep_tra_cuu_cong_khai'']/value/text())[1]','varchar(50)')='true'";
        if (Controller::check_permision('GIAM_SAT_TOAN_BO_VAN_BAN'))
        {
            $v_limit_view_query = '';
        }

        $sql = "SELECT LOWER(a.C_CODE),
                    (a.C_NAME + ' (' + convert(varchar, a.COUNT_NEW_DOC) + ')') AS C_NAME
                FROM (
                    SELECT L.C_CODE,
                            L.C_NAME,
                            L.C_ORDER,
                            (SELECT COUNT(*)
                                FROM T_EDOC_DOC as d CROSS APPLY C_XML_DATA.nodes('/data') t(doc)
                                WHERE (d.C_DIRECTION='VBDEN' Or d.C_DIRECTION='VBDI')
                                    AND d.C_TYPE=L.C_CODE $v_limit_view_query
                            ) AS COUNT_NEW_DOC
                    FROM T_LIST AS L CROSS APPLY C_XML_DATA.nodes('/data') t(doc_type)
                    WHERE L.FK_LISTTYPE= (SELECT PK_LISTTYPE
                                            FROM T_LISTTYPE
                                            WHERE C_CODE='DM_LOAI_VAN_BAN'
                                          )
                        AND (
                            doc_type.value('(item[@id=''VBDEN'']/value/text())[1]','varchar(50)')='true'
                            Or
                            doc_type.value('(item[@id=''VBDI'']/value/text())[1]','varchar(50)')='true'
                            )
                ) a
                ORDER BY a.C_ORDER";

        return $this->db->getAssoc($sql);
    }

    public function qry_all_doc_type_option($doc_type='vbden')
    {
        //Lay danh sach loai van ban + dem so van ban lien quan toi NSD
        $doc_type = $this->replace_bad_char($doc_type);
        $stmt = "Select L.C_CODE,L.C_NAME "
                . " From T_LIST as L CROSS APPLY C_XML_DATA.nodes('/data') t(item) "
                . " Where L.FK_LISTTYPE=(Select PK_LISTTYPE From T_LISTTYPE Where C_CODE='DM_LOAI_VAN_BAN')"
                        . " And item.value('(item[@id='?']/value/text())[1]','varchar(50)')='true'"
                . " Order By L.C_ORDER";

        $params = array(strtoupper($doc_type));
        return $this->db->getAssoc($stmt, $params);
    }

    public function qry_all_doc($direction, $type, $filter='')
    {
        $direction  = strtoupper($this->replace_bad_char($direction));
        $type       = strtoupper($this->replace_bad_char($type));
        $v_filter   = $this->replace_bad_char($filter);

        $v_processing_filter            = isset($_POST['sel_processing']) ? $_POST['sel_processing'] : '-1';

        //Luu dieu kien loc
        $v_page = isset($_POST['sel_goto_page']) ? $this->replace_bad_char($_POST['sel_goto_page']) : 1;
        $v_rows_per_page = isset($_POST['sel_rows_per_page']) ? $this->replace_bad_char($_POST['sel_rows_per_page']) : _CONST_DEFAULT_ROWS_PER_PAGE;

        $v_start = $v_rows_per_page * ($v_page - 1) + 1;
        $v_end = $v_start + $v_rows_per_page - 1;

        $v_view_name = 'VIEW_DOC';
        if ($v_processing_filter == 1)
        {
            $v_view_name = '[VIEW_PROCESSING_DOC]';
        }
        elseif ($v_processing_filter == 2)
        {
            $v_view_name = '[VIEW_PROCESSING_EXPIRED_DOC]';
        }
        elseif ($v_processing_filter == 3)
        {
            $v_view_name = '[VIEW_COMMITTED_DOC]';
        }
        elseif ($v_processing_filter == 4)
        {
            $v_view_name = '[VIEW_COMMITTED_EXPIRED_DOC]';
        }

        $v_user_id = Session::get('user_id');

        //Dieu kien loc theo chieu di/den va loai van ban
        $condition_query = " C_DIRECTION='$direction' "
                            . " And C_TYPE='$type' "
                            . " And ( PK_DOC in (SELECT DISTINCT FK_DOC DOC_ID
                                                    FROM   T_EDOC_MESSAGE
                                                    WHERE  ( C_TO_USER_ID = $v_user_id
                                                                OR C_FROM_USER_ID = $v_user_id)
                                                UNION ALL
                                                Select PK_DOC DOC_ID
                                                From T_EDOC_DOC
                                                Where FK_USER=$v_user_id
                                                )
                                    ) ";

        if ($v_filter != '')
        {
            $condition_query .= " And (
                            (D.DOC_NOI_GUI like '%$v_filter%')
                        Or (D.DOC_NOI_NHAN like '%$v_filter%')
                        Or (D.DOC_SO_KY_HIEU like '%$v_filter%')
                        Or (D.DOC_TRICH_YEU like '%$v_filter%')
                        Or (D.DOC_NGAY_DEN like '%$v_filter%')
                        Or (D.DOC_NGAY_DI like '%$v_filter%')
                        Or (D.C_XML_DATA.value('(//item[@id=''doc_so_den'']/value/text())[1]', 'varchar(50)') like '%$v_filter%')

                                )";
        }
        //Dem tong ban ghi
        $sql_count_record = "Select Count(*) From $v_view_name D Where (1 > 0) And $condition_query";
        //result
        $v_rank_over = 'Convert(Int, [dbo].[f_get_outcomming_doc_number](D.DOC_SO_KY_HIEU)) Desc';
        if ($direction == 'VBDEN')
        {
            $v_rank_over = "D.C_XML_DATA.value('(//item[@id=''doc_so_den'']/value/text())[1]', 'Int') Desc";

        }
        $sql = "Select D.*
                    ,($sql_count_record) as TOTAL_RECORD
                    ,ROW_NUMBER() OVER (ORDER BY $v_rank_over) as RN
                    ,Convert(Xml,(Select PK_DOC_FILE, FK_DOC, C_FILE_NAME From T_EDOC_DOC_FILE df Where df.FK_DOC=D.PK_DOC For xml raw),1) as DOC_ATTACH_FILE_LIST
                    ,Convert(Xml, (Select F.PK_FOLDER, F.C_CODE, F.C_NAME From T_EDOC_FOLDER F Left Join T_EDOC_FOLDER_DOC as FD On F.PK_FOLDER=FD.FK_FOLDER Where FD.FK_DOC=D.PK_DOC For xml raw),1) as DOC_FOLDER_LIST
                    ,(Select Count(*) From T_EDOC_FOLDER_DOC Where FK_DOC=D.PK_DOC) as C_FOLDED
                From $v_view_name D
                Where (1>0) And $condition_query";
        return $this->db->GetAll("Select a.* From ($sql) a Where a.rn>=$v_start And a.rn<=$v_end Order By a.rn");
        //return $this->db->CacheGetAll(300, "Select * From ($sql) a Where a.rn>=$v_start And a.rn<=$v_end");
    }

    public function qry_single_doc($doc_id)
    {
        if ($doc_id > 0)
        {
            $stmt = 'Select D.PK_DOC
                            , D.C_TYPE
                            , D.C_DIRECTION
                            , D.C_XML_DATA
                            , D.C_XML_PROCESSING
                            ,(Select Count(*) From T_EDOC_FOLDER_DOC Where FK_DOC=D.PK_DOC) as C_FOLDED
                    From T_EDOC_DOC D
                    Where D.PK_DOC=?';
            $params = array(intval($doc_id));
            return $this->db->getRow($stmt, $params);
        }
        else
        {
            return array();
        }
    }

    /**
     * lay danh sach file dinh kem cua van ban
     * @param int $doc_id: ID cua van ban
     */
    public function qry_all_doc_file($doc_id)
    {
        if (intval($doc_id)<1)
        {
            return NULL;
        }
        $stmt = 'Select PK_DOC_FILE, C_FILE_NAME FROM T_EDOC_DOC_FILE Where FK_DOC=?';
        return $this->db->getAll($stmt, array($doc_id));
    }

    public function update_doc()
    {
        if (!isset($_POST))
        {
            $this->exec_done($this->goback_url);
            return;
        }

        $v_doc_id       = $this->replace_bad_char($_POST['hdn_item_id']);
        $v_type         = $this->replace_bad_char($_POST['type']);
        $v_xml_data     = $_POST['XmlData'];

        $v_direction    = $this->replace_bad_char($_POST['direction']);
        $v_submit       = isset($_POST['chk_submit']) ? 1 : 0;

        $v_type         = strtoupper($v_type);
        $v_direction    = strtoupper($v_direction);

        if ($v_doc_id > 0)
        {
            //update
            $stmt = 'Update T_EDOC_DOC Set
                        C_TYPE=?
                        ,C_DIRECTION=?
                        ,C_XML_DATA=Convert(Xml, N?, 1)
                     Where PK_DOC=?';
            $params = array($v_type, $v_direction, $v_xml_data, $v_doc_id);
            $this->db->Execute($stmt, $params);
        }
        else
        {
            //Insert
            $stmt = 'Insert Into T_EDOC_DOC (
                        C_TYPE
                        ,C_DIRECTION
                        ,C_DATE_CREATED
                        ,C_XML_DATA
                        ,FK_USER
                    ) Values (?,?,getDate(),Convert(XML,N?,1), ?)';
            $params = array($v_type, $v_direction, $v_xml_data, Session::get('user_id'));
            $this->db->Execute($stmt, $params);

            $v_doc_id = $this->db->getOne("SELECT IDENT_CURRENT('T_EDOC_DOC')");

            //Ghi log qua trinh xu ly van ban
            $action = '';
            $permit = 'nothing__';
            switch ($v_direction)
            {
                case 'VBDEN':
                    $action = 'Vào sổ văn bản đến';
                    $permit =  'TRINH_VAN_BAN_DEN';
                    break;

                case 'VBDI':
                    $action = 'Khởi tạo văn bản đi';
                    $permit =  'TRINH_DUYET_VAN_BAN_DI';
                    break;

                case 'VBNOIBO':
                    $action = 'Khởi tạo văn bản nội bộ';
                    break;

                case 'VBTRACUU':
                    $action = 'Nhập văn bản tra cứu';
                    break;
            }

            $v_step_seq = $this->get_new_seq_val('SEQ_DOC_PROCESSING');
            $step = '<step seq="' . $v_step_seq . '" code="init">';
            $step .= '<init_user_id>' . Session::get('user_id') . '</init_user_id>';
            $step .= '<init_user_name>' . Session::get('user_name') . '</init_user_name>';
            $step .= '<action_text><![CDATA[' . $action . ']]></action_text>';
            $step .= '<datetime>' . $this->getDate() . '</datetime>';
            $step .= '</step>';

            $xml_processing = '<?xml version="1.0" standalone="yes"?><data>' . $step . '</data>';

            $stmt = 'Update T_EDOC_DOC
                     Set C_XML_PROCESSING=Convert(Xml, N?, 1)
                     Where PK_DOC=?';
            $params = array($xml_processing, $v_doc_id);
            $this->db->Execute($stmt, $params);

            //Cap nhat NSD lien quan den VB
			if ($v_direction == 'VBDEN')
			{
				//1. "Nguoi lien quan" la nguoi co quyen "Trình lãnh đạo": TRINH_VAN_BAN_DEN hoặc TRINH_DUYET_VAN_BAN_DI
				$stmt = "Insert Into T_EDOC_MESSAGE (C_TO_USER_ID,C_TO_USER_NAME, C_DATE_CREATED, C_FROM_USER_ID, C_FROM_USER_NAME, C_MESSAGE_CONTENT, C_MESSAGE_TITLE, FK_STEP_SEQ, FK_DOC)
							Select u.PK_USER, u.C_NAME, getDate(), ?, N?, N?, N?, ?, ?
							FROM T_USER as u CROSS APPLY C_XML_DATA.nodes('/data') t(item)
							Where item.value('(item[@id='?']/value/text())[1]','varchar(50)')='true'";
				$params = array(
							Session::get('user_id')
							,Session::get('user_name')
							,'Đề nghị trình văn bản lên lãnh đạo'
							,'Đã vào sổ văn bản'
							,$v_step_seq
							,$v_doc_id
							,$permit
				);
				$this->db->Execute($stmt, $params);
			}
        }

        //Cap nhat file dinh kem
        require_once(_PATH_TO_PEAR . 'upload.php');
        $arr_result = $this->upload_file('user_file', CONST_SERVER_DOC_FILE_UPLOAD_DIR, 'real','vi');

        if (!$arr_result['error']){
            //Cap nhat thong tin file dinh kem cua van ban
            $v_des_file_name        = $arr_result['des_file_name'];
            $v_uploaded_image_path  = CONST_SERVER_DOC_FILE_UPLOAD_DIR . $v_des_file_name;

            $stmt = 'Insert Into T_EDOC_DOC_FILE(FK_DOC, C_FILE_NAME) Values(?, ?)';
            $this->db->Execute($stmt, array($v_doc_id, $v_des_file_name));
        }

        //Xoa file dinh kem
        $v_deleted_doc_file_id_list = ltrim($_POST['hdn_deleted_doc_file_id_list'],',');
        if ($v_deleted_doc_file_id_list != '')
        {
            $sql = "Delete From T_EDOC_DOC_FILE Where PK_DOC_FILE in ($v_deleted_doc_file_id_list)";
            $this->db->Execute($sql);
        }


        $v_pop_win = isset($_POST['pop_win']) ? $_POST['pop_win'] : '';
        if ($v_pop_win === '')
        {
            $this->exec_done($this->goback_url . strtolower($_POST['direction']) . '/' . strtolower($_POST['type']), null);
        }
        else
        {
            $this->popup_exec_done();
        }
    }

    public function delete_doc()
    {
        if (!isset($_POST))
        {
            $this->exec_done($this->goback_url);
            return;
        }

        $v_item_id_list = isset($_POST['hdn_item_id_list']) ? $this->replace_bad_char($_POST['hdn_item_id_list']) : '';
        if ($v_item_id_list != '')
        {
            //Nhieu doi tuong duoc chon
            $this->db->Execute("Delete From T_EDOC_DOC Where PK_DOC in ($v_item_id_list)");
            //Xoa file dinh kem cua cac van ban nay
            $this->db->Execute("Delete From T_EDOC_DOC_FILE Where FK_DOC in ($v_item_id_list)");
        }
        $this->exec_done($this->goback_url . strtolower($_POST['direction']) . '/' . strtolower($_POST['type']));
    }

    public function do_submit_doc()
    {
        if (empty($_POST))
        {
            $this->popup_exec_done();
            return;
        }

        $v_doc_id           = $this->replace_bad_char($_POST['hdn_item_id']);
        $v_allot_user_id    = $this->replace_bad_char($_POST['sel_allot_user']);
        $v_allot_user_name  = $this->replace_bad_char($_POST['hdn_allot_user_name']);
        $v_message_content  = $this->replace_bad_char($_POST['txt_submit_message']);

        //Ghi log qua trinh xu ly van ban
        $v_step_seq = $this->get_new_seq_val('SEQ_DOC_PROCESSING');

        //Văn bản đã từng được trình chưa?

        $sql = "Select step.value('count(//step[@code=''submit''])', 'Int')
                From  dbo.T_EDOC_DOC D CROSS APPLY C_XML_PROCESSING.nodes('/data') t(step)
                Where D.PK_DOC=$v_doc_id";
        $v_count_submited = $this->db->getOne($sql);

        $v_action_text =  'Trình lãnh đạo';
        if ($v_count_submited > 0)
        {
            $v_action_text = 'Trình lại lần thứ ' . ($v_count_submited + 1);
        }

        $step = '<step seq="' . $v_step_seq . '" code="submit">';
            $step .= $this->create_single_xml_node('submit_user_id', Session::get('user_id'));
            $step .= $this->create_single_xml_node('submit_user_name', Session::get('user_name'), TRUE);
            $step .= $this->create_single_xml_node('allot_user_id', $v_allot_user_id);
            $step .= $this->create_single_xml_node('allot_user_name', $v_allot_user_name, TRUE);
            $step .= $this->create_single_xml_node('action_text', $v_action_text, TRUE);
            $step .= $this->create_single_xml_node('datetime', $this->getDate());
        $step .= '</step>';

        $stmt = 'Exec [dbo].[insert_doc_processing_step] ?, N?';
        $params = array($v_doc_id, $step);
        $this->db->Execute($stmt, $params);
        //End ghi log

        //Tao message phuc vu cong tac nhac nho, canh bao
        $stmt = 'Insert Into [dbo].[T_EDOC_MESSAGE]
                (
                    C_FROM_USER_ID
                    ,C_FROM_USER_NAME
                    ,C_TO_USER_ID
                    ,C_TO_USER_NAME
                    ,C_MESSAGE_CONTENT
                    ,C_MESSAGE_TITLE
                    ,FK_STEP_SEQ
                    ,FK_DOC
                    ,C_DATE_CREATED
                ) Values
                (
                    ?
                    ,?
                    ,?
                    ,?
                    ,?
                    ,N?
                    ,?
                    ,?
                    ,getDate()
                )';
        $params = array(
                    Session::get('user_id')
                    ,Session::get('user_name')
                    ,$v_allot_user_id
                    ,$v_allot_user_name
                    ,$v_message_content
                    ,'Trình văn bản đến'
                    ,$v_step_seq
                    ,$v_doc_id
        );
        $this->db->Execute($stmt, $params);


        $this->popup_exec_done();
    }

    public function do_add_doc_to_folder()
    {
        if (empty($_POST))
        {
            $this->popup_exec_done();
            return;
        }

        $v_doc_id       = $this->replace_bad_char($_POST['hdn_item_id']);
        $v_folder_id    = $this->replace_bad_char($_POST['sel_folder']);
        $v_user_id      = session::get('user_id');

        $sql = "Delete From T_EDOC_FOLDER_DOC Where FK_FOLDER=$v_folder_id And FK_DOC=$v_doc_id";
        $this->db->Execute($sql);

        $sql = "Insert Into T_EDOC_FOLDER_DOC(FK_FOLDER, FK_DOC, FK_USER, C_CREATE_DATE) Values($v_folder_id, $v_doc_id, $v_user_id, getDate())";
        $this->db->Execute($sql);

        $this->popup_exec_done();
    }

    //Lay danh sach phong ban NHAN phan cong thu lý
    public function qry_all_ou_option()
    {
        $sql = 'Select PK_OU, C_NAME From T_OU Where C_STATUS > 0 Order By C_ORDER';

        return $this->db->getAssoc($sql);
    }

    ///Lay danh sach CB phan phoi van ban cua phong minh
    public function qry_all_monitor_user()
    {
        $sql = "Select u.PK_USER, u.C_NAME
                From T_USER as u CROSS APPLY C_XML_DATA.nodes('/data') t(item)
                    left join T_OU ou on ou.PK_OU=u.FK_OU
                Where 1 > 0
                    And item.value('(item[@id=''PHU_TRACH_PHAN_PHOI_VAN_BAN_CUA_PHONG'']/value/text())[1]','varchar(50)')='true'
                Order By ou.C_ORDER, u.C_ORDER";
        return $this->db->getAssoc($sql);
    }

    //Lay danh sach CB co quyen duyet VBDEN
    public function qry_all_allot_user($direction='VBDEN')
    {
        $right = 'DUYET_VAN_BAN_DEN';
        if ($direction == 'VBDEN')
        {
            $right = 'DUYET_VAN_BAN_DEN';
        }
        elseif ($direction == 'VBDI')
        {
            $right = 'DUYET_VAN_BAN_DI';
        }
        elseif ($direction == 'VBNOIBO')
        {
            $right = 'DUYET_VAN_BAN_NOI_BO';
        }

        $sql = "Select u.PK_USER, u.C_NAME
                From T_USER as u CROSS APPLY C_XML_DATA.nodes('/data') t(item)
                Where u.C_STATUS > 0
                    And item.value('(item[@id=''$right'']/value/text())[1]','varchar(4)')='true'";
        return $this->db->getAssoc($sql);
    }

    //Lay danh sach CB co quyen thu ly van ban
    public function qry_all_exec_user()
    {
        $sql = "Select u.PK_USER, u.C_NAME
                From T_USER as u CROSS APPLY C_XML_DATA.nodes('/data') t(item)
                Where u.C_STATUS > 0
                    And item.value('(item[@id=''THU_LY_VAN_BAN_DEN'']/value/text())[1]','varchar(50)')='true'";
        return $this->db->getAssoc($sql);
    }

    public function qry_all_exec_user_in_ou(){
        //ID cua truong phong
        $v_leader_user_id = Session::get('user_id');
        $sql = "Select u.PK_USER, u.C_NAME
                From T_USER as u CROSS APPLY C_XML_DATA.nodes('/data') t(item)
                Where u.C_STATUS > 0
                    And u.PK_USER <> $v_leader_user_id
                    And FK_OU = (Select FK_OU From T_USER Where PK_USER=$v_leader_user_id)
                    And item.value('(item[@id=''THU_LY_VAN_BAN_DEN'']/value/text())[1]','varchar(50)')='true'";
        return $this->db->getAssoc($sql);

    }

    public function do_allot_doc()
    {
        if (empty($_POST))
        {
            $this->popup_exec_done();
            return;
        }

        $v_doc_id = $this->replace_bad_char($_POST['hdn_item_id']);

        //Phan cong theo PHONG hay CA NHAN
        $v_allot_by = $this->replace_bad_char($_POST['hdn_allot_by']);
        //theo phong ban
        if ($v_allot_by == 'p')
        {
            //ID phong thu ly chinh
            $v_direct_exec_ou_id    = $this->replace_bad_char($_POST['hdn_direct_exec_ou_id']);
            $v_direct_exec_ou_name  = $this->replace_bad_char($_POST['hdn_direct_exec_ou_name']);

            //Danh sach phong ban phoi hop
            $v_co_exec_ou_id_list = $this->replace_bad_char($_POST['hdn_co_exec_ou_id_list']);

            //Lanh dao phu trach
            $v_monitor_user_id      = $this->replace_bad_char($_POST['hdn_monitor_user_id']);
            $v_monitor_user_name    = $this->replace_bad_char($_POST['hdn_monitor_user_name']);

            //Y kien chi dao
            $v_allot_message = $this->replace_bad_char($_POST['txt_allot_message']);

            //Thoi han xu ly
            $v_exec_day             = $this->replace_bad_char($_POST['txt_exec_day']);
            $v_deadline_exec_date   = $this->replace_bad_char($_POST['txt_deadline_exec_date']);

            //Lap ho so vu viec
            $v_open_doc_profile = isset($_POST['chk_open_doc_profile']) ? 1 : 0;

            //Ghi Log tien trinh xu ly
            $v_step_seq = $this->get_new_seq_val('SEQ_DOC_PROCESSING');
            $step = '<step seq="' . $v_step_seq . '" code="allot">';
                $step .= $this->create_single_xml_node('allot_user_id',  Session::get('user_id'));
                $step .= $this->create_single_xml_node('allot_user_name',  Session::get('user_name'), TRUE);
                $step .= $this->create_single_xml_node('action_text', 'Phân công thụ lý cho ' . $v_direct_exec_ou_name  , TRUE);
                $step .= $this->create_single_xml_node('datetime', $this->getDate(), TRUE);
                $step .= '<allot_detail type="p">';
                    $step .= $this->create_single_xml_node('monitor_user_id', $v_monitor_user_id, TRUE);
                    $step .= $this->create_single_xml_node('direct_exec_ou', $v_direct_exec_ou_id, TRUE);
                    $step .= $this->create_single_xml_node('co_exec_ou', $v_co_exec_ou_id_list, TRUE);
                    $step .= $this->create_single_xml_node('allot_message', $v_allot_message, TRUE);
                    $step .= $this->create_single_xml_node('exec_day', $v_exec_day);
                    $step .= $this->create_single_xml_node('deadline_exec_date', $v_deadline_exec_date, TRUE);
                $step .= '</allot_detail>';
            $step .= '</step>';

            $stmt = 'Exec [dbo].[insert_doc_processing_step] ?, N?';
            $params = array($v_doc_id, $step);
            $this->db->Execute($stmt, $params);

            //Tao message canh bao
            //1. Canh bao toi CB phu trach
            $stmt = 'Insert Into [dbo].[T_EDOC_MESSAGE]
                    (
                        C_FROM_USER_ID
                        ,C_FROM_USER_NAME
                        ,C_TO_USER_ID
                        ,C_TO_USER_NAME
                        ,C_MESSAGE_CONTENT
                        ,C_MESSAGE_TITLE
                        ,FK_STEP_SEQ
                        ,FK_DOC
                        ,C_DATE_CREATED
                    ) Values
                    (
                        ?
                        ,?
                        ,?
                        ,?
                        ,N?
                        ,N?
                        ,?
                        ,?
                        ,getDate()
                    )';
            $params = array(
                        Session::get('user_id')
                        ,Session::get('user_name')
                        ,$v_monitor_user_id
                        ,$v_monitor_user_name
                        ,$v_allot_message
                        ,'Phân công phụ trách theo dõi thụ lý văn bản đến'
                        ,$v_step_seq
                        ,$v_doc_id
            );
            $this->db->Execute($stmt, $params);

            //2. --Cảnh báo tới tất cả cán bộ trong phòng được phân công thụ lý chính
            //2. --Cảnh báo tới Người Phụ trách phân phối văn bản của phòng
            $stmt = "Insert Into T_EDOC_MESSAGE (C_TO_USER_ID,C_TO_USER_NAME, C_DATE_CREATED, C_FROM_USER_ID, C_FROM_USER_NAME, C_MESSAGE_CONTENT, C_MESSAGE_TITLE, FK_STEP_SEQ, FK_DOC)
                        Select u.PK_USER, u.C_NAME, getDate(), ?, N?, N?, N?, ?, ?
                        FROM T_USER as u CROSS APPLY C_XML_DATA.nodes('/data') t(item)
                        Where FK_OU=?
                            And item.value('(item[@id=''PHU_TRACH_PHAN_PHOI_VAN_BAN_CUA_PHONG'']/value/text())[1]','varchar(50)')='true'";

            $params = array(
                        Session::get('user_id')
                        ,Session::get('user_name')
                        ,$v_allot_message
                        ,'Phân công thụ lý chính cho phòng ' . $v_direct_exec_ou_name
                        ,$v_step_seq
                        ,$v_doc_id
                        ,$v_direct_exec_ou_id
            );
            $this->db->Execute($stmt, $params);

            //3. Cảnh báo tới tất cả cán bộ trong phòng được phân công phối hợp
            //3. Cảnh báo tới Người "Phụ trách phân phối văn bản của phòng" trong phòng được phân công phối hợp
            if ($v_co_exec_ou_id_list != '')
            {
                $stmt = "Insert Into T_EDOC_MESSAGE (C_TO_USER_ID,C_TO_USER_NAME, C_DATE_CREATED, C_FROM_USER_ID, C_FROM_USER_NAME, C_MESSAGE_CONTENT, C_MESSAGE_TITLE, FK_STEP_SEQ, FK_DOC)
                            Select PK_USER, C_NAME, getDate(), ?, N?, N?, N?,?, ?
                            FROM T_USER
                            Where FK_OU in ($v_co_exec_ou_id_list)";
                $params = array(
                            Session::get('user_id')
                            ,Session::get('user_name')
                            ,$v_allot_message
                            ,'Phân công phối hợp thụ lý văn bản'
                            ,$v_step_seq
                            ,$v_doc_id
                );
                $this->db->Execute($stmt, $params);
            }
        } //end if ($v_allot_by == 'p')
        else //Phan theo CB
        {
            //ID Can bo thu ly chinh
            $v_direct_exec_user_id      = $this->replace_bad_char($_POST['hdn_direct_exec_user_id']);
            $v_direct_exec_user_name    = $this->replace_bad_char($_POST['hdn_direct_exec_user_name']);

            //Danh sach CB phoi hop
            $v_co_exec_user_id_list      = $this->replace_bad_char($_POST['hdn_co_exec_user_id_list']);

            //Lanh dao phu trach
            $v_monitor_user_id      = $this->replace_bad_char($_POST['hdn_monitor_user_id']);
            $v_monitor_user_name    = $this->replace_bad_char($_POST['hdn_monitor_user_name']);

            //Y kien chi dao
            $v_allot_message = $this->replace_bad_char($_POST['txt_allot_message']);

            //Thoi han xu ly
            $v_exec_day             = $this->replace_bad_char($_POST['txt_exec_day']);
            $v_deadline_exec_date   = $this->replace_bad_char($_POST['txt_deadline_exec_date']);

            //Lap ho so vu viec
            $v_open_doc_profile = isset($_POST['chk_open_doc_profile']) ? 1 : 0;

            //Ghi Log tien trinh xu ly
            $v_step_seq = $this->get_new_seq_val('SEQ_DOC_PROCESSING');
            $step = '<step seq="' . $v_step_seq . '" code="allot">';
                $step .= $this->create_single_xml_node('allot_user_id',  Session::get('user_id'));
                $step .= $this->create_single_xml_node('allot_user_name',  Session::get('user_name'), TRUE);
                $step .= $this->create_single_xml_node('action_text', 'Phân công thụ lý cho: ' . $v_direct_exec_user_name, TRUE);
                $step .= $this->create_single_xml_node('datetime', $this->getDate(), TRUE);
                $step .= '<allot_detail type="c">';
                    $step .= $this->create_single_xml_node('monitor_user_id', $v_monitor_user_id, TRUE);
                    $step .= $this->create_single_xml_node('direct_exec_user', $v_direct_exec_user_id, TRUE);
                    $step .= $this->create_single_xml_node('co_exec_user', $v_co_exec_user_id_list, TRUE);
                    $step .= $this->create_single_xml_node('allot_message', $v_allot_message, TRUE);
                    $step .= $this->create_single_xml_node('exec_day', $v_exec_day);
                    $step .= $this->create_single_xml_node('deadline_exec_date', $v_deadline_exec_date, TRUE);
                $step .= '</allot_detail>';
            $step .= '</step>';

            $stmt = 'Exec [dbo].[insert_doc_processing_step] ?, N?';
            $params = array($v_doc_id, $step);
            $this->db->Execute($stmt, $params);

            //Tao message canh bao
            //1. Canh bao toi CB phu trach
            $stmt = 'Insert Into [dbo].[T_EDOC_MESSAGE]
                    (
                        C_FROM_USER_ID
                        ,C_FROM_USER_NAME
                        ,C_TO_USER_ID
                        ,C_TO_USER_NAME
                        ,C_MESSAGE_CONTENT
                        ,C_MESSAGE_TITLE
                        ,FK_STEP_SEQ
                        ,FK_DOC
                        ,C_DATE_CREATED
                    ) Values
                    (
                        ?
                        ,?
                        ,?
                        ,?
                        ,N?
                        ,N?
                        ,?
                        ,?
                        ,getDate()
                    )';
            $params = array(
                        Session::get('user_id')
                        ,Session::get('user_name')
                        ,$v_monitor_user_id
                        ,$v_monitor_user_name
                        ,$v_allot_message
                        ,'Phân công phụ trách theo dõi thụ lý văn bản đến'
                        ,$v_step_seq
                        ,$v_doc_id
            );
            $this->db->Execute($stmt, $params);

            //2. Canh bao toi CB thu ly chinh
            $stmt = "Insert Into T_EDOC_MESSAGE (C_TO_USER_ID,C_TO_USER_NAME, C_DATE_CREATED, C_FROM_USER_ID, C_FROM_USER_NAME, C_MESSAGE_CONTENT, C_MESSAGE_TITLE, FK_STEP_SEQ, FK_DOC)
                        Values($v_direct_exec_user_id, N'$v_direct_exec_user_name', getDate(), ?, N?, N?, N?, ?, ?)";

            $params = array(
                        Session::get('user_id')
                        ,Session::get('user_name')
                        ,$v_allot_message
                        ,'Phân công thụ lý chính cho ' . $v_direct_exec_user_name
                        ,$v_step_seq
                        ,$v_doc_id
            );
            $this->db->Execute($stmt, $params);

            //3. Canh bao toi tat ca CB phoi hop
            if ($v_co_exec_user_id_list != '')
            {
                $stmt = "Insert Into T_EDOC_MESSAGE (C_TO_USER_ID,C_TO_USER_NAME, C_DATE_CREATED, C_FROM_USER_ID, C_FROM_USER_NAME, C_MESSAGE_CONTENT, C_MESSAGE_TITLE, FK_STEP_SEQ, FK_DOC)
                            Select PK_USER, C_NAME, getDate(), ?, N?, N?, N?,?, ?
                            FROM T_USER
                            Where PK_USER in ($v_co_exec_user_id_list)";
                $params = array(
                            Session::get('user_id')
                            ,Session::get('user_name')
                            ,$v_allot_message
                            ,'Phân công phối hợp thụ lý văn bản'
                            ,$v_step_seq
                            ,$v_doc_id
                );
                $this->db->Execute($stmt, $params);
            }
        }



        $this->popup_exec_done();
    }

    public function do_approve_doc()
    {
        if (empty($_POST))
        {
            $this->popup_exec_done();
            return;
        }

        $v_doc_id = $this->replace_bad_char($_POST['hdn_item_id']);

        //Trang thai duyet
        $v_approve = $this->replace_bad_char($_POST['rad_approve']);
        $v_reject_reason = $this->replace_bad_char($_POST['txt_reject_reason']);

        $v_approve_text = ($v_approve == 0) ? 'Văn bản chưa được phê duyệt.' : 'Văn bản đã được duyệt';
        $v_step_code = ($v_approve == 0) ? 'reject' : 'approve';

        //Cap nhat trang thai xu ly van ban
        $v_step_seq = $this->get_new_seq_val('SEQ_DOC_PROCESSING');
        $step = '<step seq="' . $v_step_seq . '" code="' . $v_step_code . '">';
            $step .= $this->create_single_xml_node('approve_user_id',  Session::get('user_id'));
            $step .= $this->create_single_xml_node('approve_user_name',  Session::get('user_name'), TRUE);
            $step .= $this->create_single_xml_node('action_text', $v_approve_text, TRUE);
            $step .= $this->create_single_xml_node('datetime', $this->getDate(), TRUE);
            $step .= $this->create_single_xml_node('reject_reason', $v_reject_reason, TRUE);
        $step .= '</step>';

        $stmt = 'Exec [dbo].[insert_doc_processing_step] ?, N?';
        $params = array($v_doc_id, $step);
        $this->db->Execute($stmt, $params);

        //------------------------------------------------------------------------------
        //Thong bao toi nguoi trinh ve trang thai phe duyet
        $v_message_content = ($v_approve == 0) ? ' Từ chối phê duyệt văn bản đi (Lý do: ' . $v_reject_reason . ')' : 'Duyệt văn bản đi';
        $stmt = "Insert Into T_EDOC_MESSAGE (C_TO_USER_ID,C_TO_USER_NAME, C_DATE_CREATED, C_FROM_USER_ID, C_FROM_USER_NAME, C_MESSAGE_CONTENT, C_MESSAGE_TITLE, FK_STEP_SEQ, FK_DOC)
                    Select PK_USER, C_NAME, getDate(), ?, N?, N?, N?, ?, ? From T_USER Where PK_USER=(Select FK_USER From T_EDOC_DOC Where PK_DOC=?)";
        $params = array(
                    Session::get('user_id')
                    ,Session::get('user_name')
                    ,$v_message_content
                    ,$v_approve_text
                    ,$v_step_seq
                    ,$v_doc_id
                    ,$v_doc_id
        );
        $this->db->Execute($stmt, $params);

        //Chieu VB?
        $v_doc_direction = $this->db->getOne('Select C_DIRECTION From VIEW_DOC Where PK_DOC=?', array($v_doc_id));

        if ($v_approve == 1)
        {
            //Chuyen thong bao cho nguoi co quyen vao so VB
            //Lay danh sách người có quyền váo sổ VB đi
            if ($v_doc_direction == 'VBDI')
            {
                $sql = "Select u.PK_USER, u.C_NAME
                        From T_USER u CROSS APPLY C_XML_DATA.nodes('/data') t(item)
                        Where item.value('(item[@id=''VAO_SO_VAN_BAN_DI'']/value/text())[1]','varchar(4)')='true'";
                $arr_user = $this->db->getAssoc($sql);

                $v_message_content = 'Văn bản chờ vào sổ';

                echo '<hr>' . $v_message_content;

                foreach ($arr_user as $user_id => $user_name)
                {
                    $stmt = "Insert Into T_EDOC_MESSAGE (C_TO_USER_ID,C_TO_USER_NAME, C_DATE_CREATED, C_FROM_USER_ID, C_FROM_USER_NAME, C_MESSAGE_CONTENT, C_MESSAGE_TITLE, FK_STEP_SEQ, FK_DOC)
                                Values(?,?, getDate(), ?, N?, N?, N?, ?, ?)";
                    $params = array(
                                $user_id
                                ,$user_name
                                ,Session::get('user_id')
                                ,Session::get('user_name')
                                ,$v_message_content
                                ,$v_approve_text
                                ,$v_step_seq
                                ,$v_doc_id
                    );
                    $this->db->Execute($stmt, $params);
                }
            }
        }

        $this->popup_exec_done();
    }

    public function do_sub_allot_doc()
    {
        if (empty($_POST))
        {
            $this->popup_exec_done();
            return;
        }

        $v_doc_id = $this->replace_bad_char($_POST['hdn_item_id']);

        //ID Can bo thu ly chinh
        $v_direct_exec_user_id      = $this->replace_bad_char($_POST['hdn_direct_exec_user_id']);
        $v_direct_exec_user_name    = $this->replace_bad_char($_POST['hdn_direct_exec_user_name']);

        //Danh sach CB phoi hop
        $v_co_exec_user_id_list      = $this->replace_bad_char($_POST['hdn_co_exec_user_id_list']);

        //Ghi Log tien trinh xu ly
        $v_step_seq = $this->get_new_seq_val('SEQ_DOC_PROCESSING');
        $step = '<step seq="' . $v_step_seq . '" code="sub_allot">';
            $step .= $this->create_single_xml_node('allot_user_id',  Session::get('user_id'));
            $step .= $this->create_single_xml_node('allot_user_name',  Session::get('user_name'), TRUE);
            $step .= $this->create_single_xml_node('action_text', 'Phân công thụ lý cho: ' . $v_direct_exec_user_name , TRUE);
            $step .= $this->create_single_xml_node('datetime', $this->getDate(), TRUE);
            $step .= '<allot_detail type="c">';
                $step .= $this->create_single_xml_node('direct_exec_user', $v_direct_exec_user_id, TRUE);
                $step .= $this->create_single_xml_node('co_exec_user', $v_co_exec_user_id_list, TRUE);
            $step .= '</allot_detail>';
        $step .= '</step>';

        $stmt = 'Exec [dbo].[insert_doc_processing_step] ?, N?';
        $params = array($v_doc_id, $step);
        $this->db->Execute($stmt, $params);

        //Tao message canh bao
        //2. Canh bao toi CB thu ly chinh
        $stmt = "Insert Into T_EDOC_MESSAGE (C_TO_USER_ID,C_TO_USER_NAME, C_DATE_CREATED, C_FROM_USER_ID, C_FROM_USER_NAME, C_MESSAGE_CONTENT, C_MESSAGE_TITLE, FK_STEP_SEQ, FK_DOC)
                    Values($v_direct_exec_user_id, N'$v_direct_exec_user_name', getDate(), ?, N?, N?, N?, ?, ?)";

        $params = array(
                    Session::get('user_id')
                    ,Session::get('user_name')
                    ,'Phân công thụ lý cho: ' . $v_direct_exec_user_name
                    ,'Phân công thụ lý chính cho ' . $v_direct_exec_user_name
                    ,$v_step_seq
                    ,$v_doc_id
        );
        $this->db->Execute($stmt, $params);

        //3. Canh bao toi tat ca CB phoi hop
        if ($v_co_exec_user_id_list != '')
        {
            $stmt = "Insert Into T_EDOC_MESSAGE (C_TO_USER_ID,C_TO_USER_NAME, C_DATE_CREATED, C_FROM_USER_ID, C_FROM_USER_NAME, C_MESSAGE_CONTENT, C_MESSAGE_TITLE, FK_STEP_SEQ, FK_DOC)
                        Select PK_USER, C_NAME, getDate(), ?, N?, N?, N?,?, ?
                        FROM T_USER
                        Where PK_USER in ($v_co_exec_user_id_list)";
            $params = array(
                        Session::get('user_id')
                        ,Session::get('user_name')
                        ,'Phối hợp thụ lý văn bản'
                        ,'Phân công phối hợp thụ lý văn bản'
                        ,$v_step_seq
                        ,$v_doc_id
            );
            $this->db->Execute($stmt, $params);
        }


        $this->popup_exec_done();
    }

    public function do_exec_doc($doc_id)
    {
        if (empty($_POST))
        {
            die('aaaaaaaaa');
        }

        $v_controller = $this->replace_bad_char($_POST['controller']);
        $v_direction = strtolower($this->replace_bad_char($_POST['direction']));
        $v_type = strtolower($this->replace_bad_char($_POST['type']));
        $v_pop_win = strtolower($this->replace_bad_char($_POST['pop_win']));

        $v_exec_text = $this->replace_bad_char($_POST['txt_exec']);

        //Hoan thanh thu ly?
        if (isset($_POST['chk_commit']))
        {
            //Thuc hien cap nhat tien do
            $v_step_seq = $this->get_new_seq_val('SEQ_DOC_PROCESSING');
            $step = '<step seq="' . $v_step_seq . '" code="commit">';
                $step .= $this->create_single_xml_node('commit_user_id',  Session::get('user_id'));
                $step .= $this->create_single_xml_node('commit_user_name',  Session::get('user_name'), TRUE);
                $step .= $this->create_single_xml_node('action_text', $v_exec_text, TRUE);
                $step .= $this->create_single_xml_node('datetime', $this->getDate(), TRUE);
            $step .= '</step>';
            $stmt = 'Exec [dbo].[insert_doc_processing_step] ?, N?';
            $params = array($doc_id, $step);
            $this->db->Execute($stmt, $params);

            //Thong bao cho cac bo phan lien quan cong viec da hoan thanh
            //tim nguoi phan cong thu ly
        }
        else
        {
            //Thuc hien cap nhat tien do
            $v_step_seq = $this->get_new_seq_val('SEQ_DOC_PROCESSING');
            $step = '<step seq="' . $v_step_seq . '" code="exec">';
                $step .= $this->create_single_xml_node('exec_user_id',  Session::get('user_id'));
                $step .= $this->create_single_xml_node('exec_user_name',  Session::get('user_name'), TRUE);
                $step .= $this->create_single_xml_node('action_text', $v_exec_text, TRUE);
                $step .= $this->create_single_xml_node('datetime', $this->getDate(), TRUE);
            $step .= '</step>';

            $stmt = 'Exec [dbo].[insert_doc_processing_step] ?, N?';
            $params = array($doc_id, $step);
            $this->db->Execute($stmt, $params);
        }

        //Quay lai man hinh cap nhat tien do
        $filter_array = array(
            'doc_id' => $doc_id,
            'hdn_item_id' => $doc_id,
            'pop_win' => $v_pop_win,
            'direction' => $v_direction,
            'type' => $v_type,
        );
        $this->exec_done($v_controller . '/dsp_exec_doc/', $filter_array);
    }

    public function  delete_step_exec_doc()
    {

        if (!isset($_POST))
        {
            $this->exec_done($this->goback_url);
            return;
        }

        $v_controller = $this->replace_bad_char($_POST['controller']);
        $v_direction = strtolower($this->replace_bad_char($_POST['direction']));
        $v_type = strtolower($this->replace_bad_char($_POST['type']));
        $v_pop_win = strtolower($this->replace_bad_char($_POST['pop_win']));

        $v_doc_id = $this->replace_bad_char($_POST['hdn_item_id']);

        $v_step_id_list = isset($_POST['hdn_deleted_step_id_list']) ? $this->replace_bad_char($_POST['hdn_deleted_step_id_list']) : '';
        if ($v_step_id_list != '')
        {
            $arr_step = explode(',', $v_step_id_list);
            foreach ($arr_step as $step_seq)
            {
                $stmt = 'Exec dbo.delete_doc_processing_step ?, ?';
                $params = array($v_doc_id, $step_seq);
                $this->db->Execute($stmt, $params);
            }
        }

        //Quay lai man hinh cap nhat tien do
        $filter_array = array(
            'doc_id' => $v_doc_id,
            'hdn_item_id' => $v_doc_id,
            'pop_win' => $v_pop_win,
            'direction' => $v_direction,
            'type' => $v_type,
        );
        $this->exec_done($v_controller . '/dsp_exec_doc/', $filter_array);
    }

    public function qry_all_doc_for_lookup($type, $arr_filter = array())
    {
        $v_filter               = isset($arr_filter['filter']) ? $this->replace_bad_char($arr_filter['filter']) : '';
        $v_processing_filter    = isset($arr_filter['processing']) ? $this->replace_bad_char($arr_filter['processing']) : '-1';

        $type = $this->replace_bad_char($type);

        //Phan trang
        $v_page = isset($_POST['sel_goto_page']) ? $this->replace_bad_char($_POST['sel_goto_page']) : 1;
        $v_rows_per_page = isset($_POST['sel_rows_per_page']) ? $this->replace_bad_char($_POST['sel_rows_per_page']) : _CONST_DEFAULT_ROWS_PER_PAGE;

        $v_start = $v_rows_per_page * ($v_page - 1) + 1;
        $v_end = $v_start + $v_rows_per_page - 1;

        $v_view_name = 'VIEW_DOC';
        if ($v_processing_filter == 1)
        {
            $v_view_name = '[VIEW_PROCESSING_DOC]';
        }
        elseif ($v_processing_filter == 2)
        {
            $v_view_name = '[VIEW_PROCESSING_EXPIRED_DOC]';
        }
        elseif ($v_processing_filter == 3)
        {
            $v_view_name = '[VIEW_COMMITTED_DOC]';
        }
        elseif ($v_processing_filter == 4)
        {
            $v_view_name = '[VIEW_COMMITTED_EXPIRED_DOC]';
        }

        $condition_query = " d.C_TYPE='$type' ";
        //Quyen giam sat
        $v_is_view_all = Controller::check_permision('GIAM_SAT_TOAN_BO_VAN_BAN');
        if ($v_is_view_all == FALSE)
        {
            $condition_query .= " And item.value('(item[@id=''cho_phep_tra_cuu_cong_khai'']/value/text())[1]','varchar(50)')='true'";
        }

        if ($v_filter != '')
        {
            $condition_query .= " And (
                                    (D.DOC_NOI_GUI like '%$v_filter%')
                                    Or (D.DOC_NOI_NHAN like '%$v_filter%')
                                    Or (D.DOC_SO_KY_HIEU like '%$v_filter%')
                                    Or (D.DOC_TRICH_YEU like '%$v_filter%')
                                    Or (D.DOC_NGAY_DEN like '%$v_filter%')
                                    Or (D.DOC_NGAY_DI like '%$v_filter%')
                                    Or (D.C_XML_DATA.value('(//item[@id=''doc_so_den'']/value/text())[1]', 'varchar(50)') like '%$v_filter%')
                                )";
        }

        //Dem tong ban ghi
        $sql_count_record = "Select Count(*) From [dbo].$v_view_name D Where (1 > 0) And $condition_query";

        //Lấy danh sách văn bản đến cho phép tra cứu công khai
        $sql = "Select d.*
                    ,($sql_count_record) as TOTAL_RECORD
                    ,ROW_NUMBER() OVER (ORDER BY D.C_DATE_CREATED Desc) as RN
                    ,Convert(Xml,(Select PK_DOC_FILE, FK_DOC, C_FILE_NAME From T_EDOC_DOC_FILE df Where df.FK_DOC=D.PK_DOC For xml raw),1) as DOC_ATTACH_FILE_LIST
                    ,(Select Count(*) From T_EDOC_FOLDER_DOC Where FK_DOC=D.PK_DOC) as C_FOLDED
                From $v_view_name d CROSS APPLY C_XML_DATA.nodes('/data') t(item)
                Where (d.C_DIRECTION='VBDEN' Or d.C_DIRECTION='VBDI')
                    And $condition_query ";

        return $this->db->getAll("Select * From ($sql) a Where a.rn>=$v_start And a.rn<=$v_end Order By a.rn");
    }


    public function test1M()
    {
        return;
/*
        set_time_limit(100000);
        $v_from = 70000;
        $v_to = 80000;
        for ($i=$v_from; $i<$v_to; $i++)
        {

            $insert = 'Insert Into T_EDOC_DOC (C_TYPE ,C_DIRECTION ,C_DATE_CREATED ,C_XML_DATA ,FK_USER ) '
                    . ' Values (\'CONG_VAN\',\'VBDEN\',getDate(),Convert(XML,N\'<?xml version="1.0" standalone="yes"?><data><item doc="true" id="cho_phep_tra_cuu_cong_khai"><value>true</value></item><item id="doc_ngay_den"><value><![CDATA[01/06/2012]]></value></item><item id="doc_so_ky_hieu"><value><![CDATA[CV-' . $i . '/VPCP-KTTH]]></value></item><item id="txt_nguoi_ky"><value><![CDATA[Phạm Văn Phượng-[' . $i . ']]]></value></item><item id="doc_trich_yeu"><value><![CDATA[V/v báo cáo tình hình làm giả, mua bán Giấy chứng nhận nghỉ việc hưởng chế độ BHXH]]></value></item><item id="doc_noi_gui"><value><![CDATA[Văn phòng Chính phủ]]></value></item></data>\',1), 2)';

            $this->db->Execute($insert);
            $v_doc_id = $this->db->getOne("SELECT IDENT_CURRENT('T_EDOC_DOC')");

            $step = '<step seq="' . $this->get_new_seq_val('SEQ_DOC_PROCESSING') . '" code="init">';
            $step .= '<init_user_id>2</init_user_id>';
            $step .= '<init_user_name>>Trần Thị Hoài Thanh</init_user_name>';
            $step .= '<action_text><![CDATA[Vào sổ văn bản đến]]></action_text>';
            $step .= '<datetime>' . $this->getDate() . '</datetime>';
            $step .= '</step>';

            $xml_processing = '<?xml version="1.0" standalone="yes"?><data>' . $step . '</data>';

            $stmt = 'Update T_EDOC_DOC
                        Set C_XML_PROCESSING=Convert(Xml, N?, 1)
                        Where PK_DOC=?';
            $params = array($xml_processing, $v_doc_id);
            $this->db->Execute($stmt, $params);
        }
*/
    }

    public function get_next_doc_seq($direction, $type)
    {
        $direction  = $this->replace_bad_char($direction);
        $type       = $this->replace_bad_char($type);

        $id = ($direction == 'VBDEN' ) ? 'doc_so_den' : 'doc_so_ky_hieu';
        $stmt = "   Select isnull(max(a.SO_DI_DEN),0) MAX_VALUE
                    From
                        (	Select
                                dbo.f_get_outcomming_doc_number(item.value('(item[@id='?']/value/text())[1]','varchar(10)')) as SO_DI_DEN
                            From dbo.T_EDOC_DOC d CROSS APPLY C_XML_DATA.nodes('/data') t(item)
                            Where d.C_DIRECTION=?
                            and C_TYPE=?
                        ) a";


        $params = array($id, $direction, $type);
        $this->db->debug = 0;
        return $this->db->getOne($stmt, $params);
    }

    public function qry_all_doc_for_print($p_direction, $p_type, $p_begin_date, $p_end_date)
    {
        $stmt = "Select D.*
                From VIEW_DOC D
                Where D.C_DIRECTION=?
                    And D.C_TYPE=?";

        if ($p_direction == 'VBDEN')
        {
            $stmt .= ' And Datediff(day,C_DATE_CREATED,Convert(datetime,?,111)) <=0  And Datediff(day, C_DATE_CREATED, convert(datetime,?,111)) >=0';
            $stmt .= " Order By D.C_XML_DATA.value('(//item[@id=''doc_so_den'']/value/text())[1]', 'Int'), C_DATE_CREATED";
        }
        elseif ($p_direction == 'VBDI')
        {
            //$stmt .= 'And (Convert(date, DOC_NGAY_DI, 103) between ? And ?)';

			$stmt .= " And (Convert(Date, D.C_XML_DATA.value('(//item[@id=''doc_ngay_van_ban'']/value/text())[1]', 'varchar(10)') ,103) between ? And ?)";
            $stmt .= " Order By Convert(Int, [dbo].[f_get_outcomming_doc_number](D.DOC_SO_KY_HIEU)), C_DATE_CREATED ";
        }

        $params = array($p_direction, $p_type, $p_begin_date, $p_end_date );

        //return $this->db->CachegetAll(5000, $stmt, $params);
        return $this->db->getAll($stmt, $params);
    }

    public function qry_all_folder($p_user_id)
    {
        $sql = "Select F.PK_FOLDER, (F.C_CODE + ' - ' + F.C_NAME) as C_NAME
                From T_EDOC_FOLDER F Left Join T_EDOC_FOLDER_USER FU On F.PK_FOLDER=FU.FK_FOLDER
                Where (F.C_STATUS > 0)
                        And (F.C_IS_CLOSED < 1)
                        And (
                                (F.FK_USER=$p_user_id)
                                Or (FU.C_USER_TYPE='USER' And FU.FK_USER=$p_user_id)
                                Or (FU.C_USER_TYPE='OU' And FU.FK_USER=(Select FK_OU From T_USER Where PK_USER=$p_user_id))
                            )";

        return $this->db->getAssoc($sql);
    }

    public function remove_doc_folder()
    {
        if (!isset($_POST))
        {
            $this->exec_done($this->goback_url);
            return;
        }

        $v_doc_id       = isset($_POST['hdn_item_id']) ? $this->replace_bad_char($_POST['hdn_item_id']) : '0';
        $v_folder_id    = isset($_POST['hdn_folder_id']) ? $this->replace_bad_char($_POST['hdn_folder_id']) : '0';

        $stmt = 'Delete From T_EDOC_FOLDER_DOC Where FK_DOC=? And FK_FOLDER=?';
        $params = array($v_doc_id, $v_folder_id);
        $this->db->Execute($stmt, $params);



        $this->exec_done($this->goback_url . strtolower($_POST['direction']) . '/' . strtolower($_POST['type']));
    }

    public function qry_all_doc_to_add($p_filter)
    {

        $v_user_id = session::get('user_id');

        $sql = "Select D.PK_DOC
                        ,D.DOC_SO_KY_HIEU
                        ,D.DOC_TRICH_YEU
                        ,D.C_DIRECTION
                        ,D.C_TYPE
                        ,D.C_XML_DATA.value('(//item[@id=''doc_ngay_van_ban'']/value/text())[1]', 'varchar(50)') DOC_NGAY_VAN_BAN
                From VIEW_DOC D
                Where D.PK_DOC in (SELECT DISTINCT FK_DOC DOC_ID
                                        FROM   T_EDOC_MESSAGE
                                        WHERE  ( C_TO_USER_ID = $v_user_id
                                                    OR C_FROM_USER_ID = $v_user_id)
                                    UNION ALL
                                    Select PK_DOC DOC_ID
                                    From T_EDOC_DOC
                                    Where FK_USER=$v_user_id)";
        if ($p_filter != '')
        {
            $sql .= " And (D.DOC_SO_KY_HIEU like '%$p_filter%' Or D.DOC_TRICH_YEU like '%$p_filter%')";
        }
        return $this->db->getAll($sql);
    }
}