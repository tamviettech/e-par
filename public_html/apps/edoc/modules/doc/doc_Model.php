<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed');

class doc_Model extends Model {

	function __construct() {
		parent::__construct();
	}

	/**
	 * Thêm một node vào column có kiểu xml
	 * @param string $table  Tên bảng
	 * @param string $c_xml_column Tên cột xml
	 * @param xml $c_xml_value node xml cần chèn
	 * @param string $c_pk_column Tên cột khóa chính
	 * @param int $c_pk_value giá trị của khóa chính
	 */
	public function insert_xml_node($table,$c_xml_column,$c_xml_value,$c_pk_column,$c_pk_value)
	{
		$stmt="select $c_xml_column from $table where $c_pk_column=$c_pk_value";
	
		$v_xml = xml_add_declaration($this->db->GetOne($stmt));
	
		$doc = new DOMDocument();
		$doc->loadXML($v_xml);
		$f = $doc->createDocumentFragment();
		$f->appendXML($c_xml_value);
		$doc->documentElement->appendChild($f);
		$v_new_xml = xml_add_declaration($doc->saveXML(), 0);
	
		$stmt="update $table set $c_xml_column=? where PK_DOC=?";
		$this->db->execute($stmt,array($v_new_xml,$c_pk_value));
	}
	
	//Lay Loai van ban dau tien (theo C_ORDER) theo chieu di/den cua van ban
	public function qry_first_doc_type_by_direction($direction)
	{
		if(DATABASE_TYPE == 'MSSQL')
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
		elseif (DATABASE_TYPE == 'MYSQL')
		{
			$direction = strtoupper($this->replace_bad_char($direction));
			$stmt = "Select top 1 
						L.C_CODE 
					From t_cores_list as L 
					Where L.FK_LISTTYPE=(Select 
											PK_LISTTYPE 
										From t_cores_listype 
										Where C_CODE='DM_LOAI_VAN_BAN'
										)
						  And ExtractValue(L.C_XML_DATA,'//item[@id='?']/value')='true'
					Order by L.C_ORDER";
			$params = array($direction);
			return $this->db->getOne($stmt, $params);
		}
	}

	//Lay danh sach Loai van ban, theo chieu di/den
	public function qry_all_doc_type_by_direction($direction)
	{
		$direction = strtoupper($this->replace_bad_char($direction));

		if(DATABASE_TYPE == 'MSSQL')
		{
			$stmt = "Select 
						LOWER(a.C_CODE)
						, (a.C_NAME + ' (' + convert(varchar, a.COUNT_NEW_DOC) + ')') as C_NAME
					From (Select L.C_CODE,L.C_NAME, L.C_ORDER, (Select COUNT(*) 
																From T_EDOC_DOC 
																Where C_DIRECTION=? 
																And C_TYPE=L.C_CODE 
																AND PK_DOC in (Select distinct FK_DOC DOC_ID 
																			   From T_EDOC_MESSAGE 
																			   Where (C_TO_USER_ID=? OR C_FROM_USER_ID=?) 
																			   UNION ALL Select PK_DOC DOC_ID 
																						 From T_EDOC_DOC 
																						 Where FK_USER=?
																				)
																) as COUNT_NEW_DOC 
						  From T_LIST as L CROSS APPLY C_XML_DATA.nodes('/data') t(item) 
						  Where L.FK_LISTTYPE=(Select PK_LISTTYPE 
											  From T_LISTTYPE 
											  Where C_CODE='DM_LOAI_VAN_BAN'
											  ) 
						  And item.value('(item[@id='?']/value/text())[1]','varchar(50)')='true'
						 ) a
					 Order By a.C_ORDER";
		}
		elseif(DATABASE_TYPE == 'MYSQL')
		{
			$stmt = "SELECT 
						LOWER(a.C_CODE), 
						(CONCAT(a.C_NAME, ' (', a.COUNT_NEW_DOC, ')')) AS C_NAME
					FROM (SELECT L.C_CODE,L.C_NAME, L.C_ORDER, (SELECT COUNT(*) 
																FROM t_edoc_doc 
																WHERE C_DIRECTION = ? AND C_TYPE = L.C_CODE 
																AND PK_DOC IN (SELECT DISTINCT FK_DOC DOC_ID 	
																			   FROM t_edoc_message 
																			   WHERE (C_TO_USER_ID = ? OR C_FROM_USER_ID = ?
																			  ) 	
																			  UNION ALL SELECT PK_DOC DOC_ID   
																						FROM t_edoc_doc  
																						WHERE FK_USER = ?
																			   )
																) AS COUNT_NEW_DOC
							FROM t_cores_list AS L
							WHERE L.FK_LISTTYPE = (SELECT PK_LISTTYPE 
												   FROM t_cores_listtype
												   WHERE C_CODE = 'DM_LOAI_VAN_BAN'
												  )
							AND ExtractValue(L.C_XML_DATA,'//item[@id = '?']/value') = 'true'
						   ) a
					ORDER BY a.C_ORDER ";
		}
		//echo $stmt ; exit;
		$params = array($direction, Session::get('user_id'), Session::get('user_id'), Session::get('user_id'), $direction);
		return $this->db->getAssoc($stmt,$params);

	}

	//Lay danh sach VBDEN -> tra cuu
	public function qry_all_doc_type_for_lookup()
	{
		//Quyen giam sat
		if(DATABASE_TYPE == 'MSSQL')
		{
			$v_limit_view_query = " AND doc.value('(item[@id=''cho_phep_tra_cuu_cong_khai'']/value/text())[1]','varchar(50)')='true'";
		}
		elseif(DATABASE_TYPE == 'MYSQL')
		{
			$v_limit_view_query = " AND ExtractValue(d.C_XML_DATA,'//item[@id=''cho_phep_tra_cuu_cong_khai'']/value')='true'";
		}
		if (Controller::check_permission('GIAM_SAT_TOAN_BO_VAN_BAN'))
		{
			$v_limit_view_query = '';
		}
		if(DATABASE_TYPE == 'MSSQL')
		{
			$sql = "SELECT 
						LOWER(a.C_CODE),
						(a.C_NAME + ' (' + convert(varchar, a.COUNT_NEW_DOC) + ')') AS C_NAME
					FROM (
							SELECT 
								L.C_CODE,
								L.C_NAME,
								L.C_ORDER,
								(SELECT COUNT(*)
								 FROM T_EDOC_DOC as d CROSS APPLY C_XML_DATA.nodes('/data') t(doc)
								 WHERE (d.C_DIRECTION='VBDEN' Or d.C_DIRECTION='VBDI')
								 AND d.C_TYPE=L.C_CODE $v_limit_view_query
								) AS COUNT_NEW_DOC
							FROM T_LIST AS L CROSS APPLY C_XML_DATA.nodes('/data') t(doc_type)
							WHERE L.FK_LISTTYPE = (SELECT PK_LISTTYPE
												   FROM T_LISTTYPE
													WHERE C_CODE='DM_LOAI_VAN_BAN'
													)
							AND (
								doc_type.value('(item[@id=''VBDEN'']/value/text())[1]','varchar(50)')='true'
								OR doc_type.value('(item[@id=''VBDI'']/value/text())[1]','varchar(50)')='true'
								)
						) a
					ORDER BY a.C_ORDER";
		}
		elseif(DATABASE_TYPE == 'MYSQL')
		{
			$sql = "SELECT 
						LOWER(a.C_CODE),
						(CONCAT(a.C_NAME,' (', a.COUNT_NEW_DOC, ')')) AS C_NAME
					FROM (	SELECT 
								L.C_CODE,
								L.C_NAME,
								L.C_ORDER,
								(SELECT 
									COUNT(*)
								FROM t_edoc_doc as d
								WHERE (1>0)
									  AND d.C_TYPE = L.C_CODE $v_limit_view_query
								) AS COUNT_NEW_DOC
							FROM t_cores_list AS L
							WHERE L.FK_LISTTYPE= (SELECT  PK_LISTTYPE
												 FROM t_cores_listtype
												 WHERE C_CODE='DM_LOAI_VAN_BAN'
												 )
								
						) a
				ORDER BY a.C_ORDER";

		}

		return $this->db->getAssoc($sql);
	}

	public function qry_all_doc_type_option($doc_type='vbden')
	{
		//Lay danh sach loai van ban + dem so van ban lien quan toi NSD
		$doc_type = $this->replace_bad_char($doc_type);
		if(DATABASE_TYPE == 'MSSQL')
		{
			$stmt = "Select 
						L.C_CODE
						,L.C_NAME 
					 From T_LIST as L CROSS APPLY C_XML_DATA.nodes('/data') t(item) 
					 Where L.FK_LISTTYPE=(Select PK_LISTTYPE 
										  From T_LISTTYPE 
										  Where C_CODE='DM_LOAI_VAN_BAN'
										 )
					And item.value('(item[@id='?']/value/text())[1]','varchar(50)')='true'
					Order By L.C_ORDER";
		}
		elseif(DATABASE_TYPE == 'MYSQL')
		{
			$stmt = "Select 
						L.C_CODE,
						L.C_NAME 
					 From t_cores_list as L  
					 Where L.FK_LISTTYPE=(Select 
										  	PK_LISTTYPE 
										  From t_cores_listtype 
										  Where C_CODE='DM_LOAI_VAN_BAN'
										 )
					 And ExtractValue(L.C_XML_DATA,'//item[@id='?']/value')='true'
					 Order By L.C_ORDER";
		}
		$params = array(strtoupper($doc_type));
		return $this->db->getAssoc($stmt, $params);
	}

	public function qry_all_doc($direction, $type, $filter='')
	{
		$direction  = strtoupper($this->replace_bad_char($direction));
		$type       = strtoupper($this->replace_bad_char($type));
		$v_filter   = $this->replace_bad_char($filter);
		$v_direct_ou_filter   = isset($_POST['sel_direct_ou']) ? $this->replace_bad_char($_POST['sel_direct_ou']) : -1;
		$v_type_filter = isset($_POST['type_filter']) ? $this->replace_bad_char($_POST['type_filter']) : '';
		
		$v_begin_date  = isset($_POST['txt_begin_date']) ? $this->replace_bad_char($_POST['txt_begin_date']) : '';
		$v_end_date	   = isset($_POST['txt_end_date']) ? $this->replace_bad_char($_POST['txt_end_date']) : '';
		
		$v_processing_filter            = isset($_POST['sel_processing']) ? $_POST['sel_processing'] : '-1';

		//Luu dieu kien loc
		$v_page = isset($_POST['sel_goto_page']) ? $this->replace_bad_char($_POST['sel_goto_page']) : 1;
		$v_rows_per_page = isset($_POST['sel_rows_per_page']) ? $this->replace_bad_char($_POST['sel_rows_per_page']) : _CONST_DEFAULT_ROWS_PER_PAGE;

		$v_start = $v_rows_per_page * ($v_page - 1) + 1;
		$v_end = $v_start + $v_rows_per_page - 1;
		if(DATABASE_TYPE == 'MSSQL')
		{
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
		}
		elseif(DATABASE_TYPE == 'MYSQL')
		{
			$v_view_name = 'view_doc';
			if ($v_processing_filter == 1)
			{
				$v_view_name = 'view_processing_doc';
			}
			elseif ($v_processing_filter == 2)
			{
				$v_view_name = 'view_processing_expired_doc';
			}
			elseif ($v_processing_filter == 3)
			{
				$v_view_name = 'view_committed_doc';
			}
			elseif ($v_processing_filter == 4)
			{
				$v_view_name = 'view_committed_expired_doc ';
			}
		}

		$v_user_id = Session::get('user_id');

		//Dieu kien loc theo chieu di/den va loai van ban
		$condition_query = " C_DIRECTION='$direction' "
						. " And C_TYPE='$type' "
						. " And ( PK_DOC in (SELECT 
												DISTINCT FK_DOC DOC_ID
											FROM   t_edoc_message
											WHERE  ( C_TO_USER_ID = $v_user_id
												     OR C_FROM_USER_ID = $v_user_id
													)
											UNION ALL
												Select PK_DOC DOC_ID
												From t_edoc_doc
												Where FK_USER=$v_user_id
											)
								) ";

		if ($v_filter != '')
		{
			if(DATABASE_TYPE == 'MSSQL')
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
			elseif(DATABASE_TYPE == 'MYSQL')
			{
				$condition_query .= " And ( MATCH (C_XML_DATA) AGAINST ('$v_filter' IN NATURAL LANGUAGE MODE)
											)";
			}
		}
		//Lọc theo đơn vị tiếp nhận
		if ($v_direct_ou_filter > 0)
		{
			if(DATABASE_TYPE == 'MSSQL')
			{
				$condition_query .= " And
										(
											D.DOC_TINH_TRANG_XU_LY.value('(//step/allot_detail/direct_exec_ou/text())[1]', 'Int')=$v_direct_ou_filter
										)
										";
			}
			elseif(DATABASE_TYPE == 'MYSQL')
			{
				$condition_query .= " And
										(
											ExtractValue(D.DOC_TINH_TRANG_XU_LY,'//step/allot_detail/direct_exec_ou')=$v_direct_ou_filter
										)
										";
			}
		}
		
		//Lọc theo loại văn bản nếu là văn bản đến
		if ($direction == 'VBDEN')
		{
					
			if ($v_type_filter !='')
			{
				if(DATABASE_TYPE == 'MSSQL')
				{
					$condition_query .= " And
					(
					D.C_XML_DATA.value('(//item[@id=''loai_van_ban_trong_so'']/value/text())[1]', 'varchar(20)') = $v_type_filter
					)
					";
				}
				elseif(DATABASE_TYPE == 'MYSQL')
				{
				$condition_query .= " And
						(
						ExtractValue(D.C_XML_DATA,'//item[@id=\"loai_van_ban_trong_so\"]/value') = '$v_type_filter'
						)
						";
				}
			}
		}
		//loc theo ngay den cua van ban
		$v_colum_date_filter = 'D.DOC_NGAY_VAN_BAN';
		
		if($direction == 'VBDEN')
		{
			$v_colum_date_filter = 'D.DOC_NGAY_DEN' ;
		}
		
		if($v_begin_date != '')
		{
			$condition_query .= "And $v_colum_date_filter - '$v_begin_date' >= 0  ";
		}
		if($v_end_date != '')
		{
			$condition_query .= "And $v_colum_date_filter - '$v_end_date' <=0 ";
		}
		//Dem tong ban ghi
		$sql_count_record = "Select Count(*) From $v_view_name D Where (1 > 0) And $condition_query";
		//result
		//$v_rank_over = 'Convert(Int, [dbo].[f_get_outcomming_doc_number](D.DOC_SO_KY_HIEU)) Desc';
		if(DATABASE_TYPE == 'MSSQL')
		{
			$v_rank_over = 'Datepart(year, D.C_DATE_CREATED) desc, Convert(Int, [dbo].[f_get_outcomming_doc_number](D.DOC_SO_KY_HIEU)) Desc';
			if ($direction == 'VBDEN')
			{
				//$v_rank_over = "D.C_XML_DATA.value('(//item[@id=''doc_so_den'']/value/text())[1]', 'Int') Desc";
				$v_rank_over = " Datepart(year, D.C_DATE_CREATED) desc, D.C_XML_DATA.value('(//item[@id=''doc_so_den'']/value/text())[1]', 'Int') Desc";
			}

			$sql = "Select D.*
						,($sql_count_record) as TOTAL_RECORD
						,ROW_NUMBER() OVER (ORDER BY $v_rank_over) as RN
						,Convert(Xml,(
										Select 
											PK_DOC_FILE, 
											FK_DOC, 
											C_FILE_NAME 
										From T_EDOC_DOC_FILE df 
										Where df.FK_DOC=D.PK_DOC For xml raw
								 ),1) as DOC_ATTACH_FILE_LIST
						,Convert(Xml, (
										Select 
											F.PK_FOLDER, 
											F.C_CODE, 
											F.C_NAME 
										From T_EDOC_FOLDER F Left Join T_EDOC_FOLDER_DOC as FD On F.PK_FOLDER=FD.FK_FOLDER 
										Where FD.FK_DOC=D.PK_DOC For xml raw
								),1) as DOC_FOLDER_LIST
						,( Select Count(*) 
						   From T_EDOC_FOLDER_DOC 
						   Where FK_DOC=D.PK_DOC
						 ) as C_FOLDED
					From $v_view_name D
					Where (1>0) And $condition_query";

		}
		elseif(DATABASE_TYPE == 'MYSQL')
		{
			$v_rank_over = " YEAR(D.C_DATE_CREATED) desc, CAST(D.DOC_SO_KY_HIEU AS UNSIGNED) Desc";

			if ($direction == 'VBDEN')
			{
				//$v_rank_over = "D.C_XML_DATA.value('(//item[@id=''doc_so_den'']/value/text())[1]', 'Int') Desc";
				$v_rank_over = "  YEAR(D.C_DATE_CREATED) desc,CAST( ExtractValue(D.C_XML_DATA,'//item[@id=''doc_so_den'']/value') AS UNSIGNED) Desc";
			}
			$sql = "Select D.*
						,($sql_count_record) as TOTAL_RECORD
						, (@row := @row + 1) AS  RN
						, (SELECT GROUP_CONCAT('<row '
												,CONCAT(' PK_DOC_FILE=\"', PK_DOC_FILE, '\"')
												,CONCAT(' FK_DOC=\"', FK_DOC, '\"')
												,CONCAT(' C_FILE_NAME=\"', C_FILE_NAME, '\"')
												, ' />'
												SEPARATOR ''
												)
							FROM t_edoc_doc_file df
							WHERE df.FK_DOC=D.PK_DOC
						) AS DOC_ATTACH_FILE_LIST
						,(SELECT GROUP_CONCAT('<row '
												,CONCAT(' PK_FOLDER=\"', F.PK_FOLDER, '\"')
												,CONCAT(' C_CODE=\"', F.C_CODE, '\"')
												,CONCAT(' C_NAME=\"', F.C_NAME, '\"')
												, ' />'
												SEPARATOR ''
											)
						 FROM t_edoc_folder F LEFT JOIN t_edoc_folder_doc AS FD ON F.PK_FOLDER=FD.FK_FOLDER
						 WHERE FD.FK_DOC=D.PK_DOC
						) AS DOC_FOLDER_LIST
						,(Select Count(*) 
						  From t_edoc_folder_doc 
						  Where FK_DOC=D.PK_DOC) as C_FOLDED
					From $v_view_name D,(SELECT @row := 0) temp
					Where (1>0) And $condition_query
					ORDER BY $v_rank_over ";
		//echo $sql; exit;
		}
		return $this->db->GetAll("Select a.* From ($sql) a Where a.RN>=$v_start And a.RN<=$v_end Order By a.RN ");
		//return $this->db->CacheGetAll(300, "Select * From ($sql) a Where a.rn>=$v_start And a.rn<=$v_end");
	}

	public function qry_single_doc($doc_id)
	{
		if ($doc_id > 0)
		{
			//Action Log
			$xml_action_log = '<action id="' . uniqid() . '">';
			$xml_action_log .= '<datetime>' . $this->getDate() . '</datetime>';
			$xml_action_log .= '<user_code>' . Session::get('user_code') . '</user_code>';
			$xml_action_log .= '<code>view</code>';
			$xml_action_log .= '<code_text>Xem văn bản</code_text>';
			$xml_action_log .= '</action>';
			
			
			if(DATABASE_TYPE == 'MSSQL')
			{
				$stmt = "Exec sp_insert_xml_node 't_edoc_doc','C_XML_LOG', 'PK_DOC', ?,?";	
				
				$params = array($doc_id, $xml_action_log);
				$this->db->Execute($stmt, $params);
				
			}
			elseif(DATABASE_TYPE == 'MYSQL')
			{
				//$stmt = "call sp_insert_xml_node 't_edoc_doc','C_XML_LOG', 'PK_DOC', ?,?";
				$this->insert_xml_node('t_edoc_doc', 'C_XML_LOG', $xml_action_log, 'PK_DOC', $doc_id);
			}
			
			
			
			$stmt = 'Select 
						D.PK_DOC
						, D.C_TYPE
						, D.C_DIRECTION
						, D.C_XML_DATA
						, D.C_XML_PROCESSING
						,(Select Count(*) 
						  From t_edoc_folder_doc 
						  Where FK_DOC=D.PK_DOC
						 ) as C_FOLDED
					From t_edoc_doc D
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
		 
		$stmt = 'Select PK_DOC_FILE, C_FILE_NAME FROM t_edoc_doc_file Where FK_DOC=?';
		 
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
			if(DATABASE_TYPE == 'MSSQL')
			{
				$stmt = 'Update T_EDOC_DOC Set
							C_TYPE=?
							,C_DIRECTION=?
							,C_XML_DATA=Convert(Xml, N?, 1)
						Where PK_DOC=?';
			}
			elseif(DATABASE_TYPE == 'MYSQL')
			{
				$stmt = "Update t_edoc_doc Set
							C_TYPE=?
							,C_DIRECTION=?
							,C_XML_DATA=?
						Where PK_DOC=?";
			}
			$params = array($v_type, $v_direction, $v_xml_data, $v_doc_id);
			$this->db->Execute($stmt, $params);

			//Action Log
			$xml_action_log = '<action id="' . uniqid() . '">';
			$xml_action_log .= '<datetime>' . $this->getDate() . '</datetime>';
			$xml_action_log .= '<user_code>' . Session::get('user_code') . '</user_code>';
			$xml_action_log .= '<code>update</code>';
			$xml_action_log .= '<code_text>Sửa văn bản</code_text>';
			$xml_action_log .= '</action>';

			if(DATABASE_TYPE == 'MSSQL')
			{
				$stmt = "Exec sp_insert_xml_node 't_edoc_doc','C_XML_LOG', 'PK_DOC', ?,?";
				$params = array($v_doc_id, $xml_action_log);
				
				$this->db->Execute($stmt, $params);

			}
			elseif(DATABASE_TYPE == 'MYSQL')
			{
				$this->insert_xml_node('t_edoc_doc', 'C_XML_LOG', $xml_action_log, 'PK_DOC', $v_doc_id);
			}
			
		}
		else
		{
			//Insert
			if(DATABASE_TYPE == 'MSSQL')
			{
				$stmt = 'Insert Into T_EDOC_DOC (
												C_TYPE
												,C_DIRECTION
												,C_DATE_CREATED
												,C_XML_DATA
												,FK_USER
												) 
									Values (?,?,getDate(),Convert(XML,N?,1), ?)';
			}
			elseif(DATABASE_TYPE == 'MYSQL')
			{
				$stmt = 'Insert Into t_edoc_doc (
												C_TYPE
												,C_DIRECTION
												,C_DATE_CREATED
												,C_XML_DATA
												,FK_USER
												) 
									Values (?,?,Now(),?, ?)';
			}
			$params = array($v_type, $v_direction, $v_xml_data, Session::get('user_id'));
			$this->db->Execute($stmt, $params);
				
			$v_doc_id = $this->db->Insert_ID('t_edoc_doc');
				


			//Action Log
			$xml_action_log = '<data><action id="' . uniqid() . '">';
			$xml_action_log .= '<datetime>' . $this->getDate() . '</datetime>';
			$xml_action_log .= '<user_code>' . Session::get('user_code') . '</user_code>';
			$xml_action_log .= '<code>create</code>';
			$xml_action_log .= '<code_text>Tạo văn bản</code_text>';
			$xml_action_log .= '</action></data>';

			if(DATABASE_TYPE == 'MSSQL')
			{
				$stmt = 'Update T_EDOC_DOC Set C_XML_LOG=Convert(Xml, N?,1) Where PK_DOC=?';
			}
			elseif(DATABASE_TYPE == 'MYSQL')
			{
				$stmt = 'Update t_edoc_doc Set C_XML_LOG=? Where PK_DOC=?';
			}
			 
			$params = array($xml_action_log, $v_doc_id);
			$this->db->Execute($stmt, $params);

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

			$v_step_seq = $this->get_new_seq_val('seq_doc_processing');
			$step = '<step seq="' . $v_step_seq . '" code="init">';
			$step .= '<init_user_id>' . Session::get('user_id') . '</init_user_id>';
			$step .= '<init_user_name>' . Session::get('user_name') . '</init_user_name>';
			$step .= '<action_text><![CDATA[' . $action . ']]></action_text>';
			$step .= '<datetime>' . $this->getDate() . '</datetime>';
			$step .= '</step>';

			$xml_processing = '<?xml version="1.0" standalone="yes"?><data>' . $step . '</data>';

			if(DATABASE_TYPE == 'MSSQL')
			{
				$stmt = 'Update t_edoc_doc
							Set C_XML_PROCESSING=Convert(Xml, N?, 1)
						Where PK_DOC=?';
			}
			elseif(DATABASE_TYPE == 'MYSQL')
			{
				$stmt = 'Update t_edoc_doc
							Set C_XML_PROCESSING=?
						Where PK_DOC=?';
			}
			$params = array($xml_processing, $v_doc_id);
			$this->db->Execute($stmt, $params);

			//Cap nhat NSD lien quan den VB
			if ($v_direction == 'VBDEN' OR $v_direction == 'VBDI')
			{
				//1. "Nguoi lien quan" la nguoi co quyen "Trình lãnh đạo": TRINH_VAN_BAN_DEN hoặc TRINH_DUYET_VAN_BAN_DI
				if(DATABASE_TYPE == 'MSSQL')
				{
					$stmt = "Insert Into T_EDOC_MESSAGE 
							(C_TO_USER_ID,
							 C_TO_USER_NAME, 
							 C_DATE_CREATED, 
							 C_FROM_USER_ID, 
							 C_FROM_USER_NAME, 
							 C_MESSAGE_CONTENT, 
							 C_MESSAGE_TITLE, 
							 FK_STEP_SEQ, 
							 FK_DOC
							)
							Select u.PK_USER, u.C_NAME, getDate(), ?, N?, N?, N?, ?, ?
							FROM T_USER as u CROSS APPLY C_XML_DATA.nodes('/data') t(item)
							Where item.value('(item[@id='?']/value/text())[1]','varchar(50)')='true'";
				}
				elseif(DATABASE_TYPE == 'MYSQL')
				{
					$stmt = "Insert Into t_edoc_message 
							(C_TO_USER_ID,
							C_TO_USER_NAME, 
							C_DATE_CREATED, 
							C_FROM_USER_ID, 
							C_FROM_USER_NAME, 
							C_MESSAGE_CONTENT, 
							C_MESSAGE_TITLE, 
							FK_STEP_SEQ, 
							FK_DOC
							)
							Select 
								u.PK_USER
								, u.C_NAME
								, Now()
								, ?
								, ?
								, ?
								, ?
								, ?
								, ?
							FROM t_cores_user as u
							Where ExtractValue(C_XML_DATA,'//item[@id='?']/value')='true'";
				}
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
      /*  require_once(_PATH_TO_PEAR . 'upload.php');
        $arr_result = upload_file('user_file', CONST_SERVER_DOC_FILE_UPLOAD_DIR, 'real','vi');
		Var_Dump($arr_result) ;
        if (!$arr_result['error']){ 
            //Cap nhat thong tin file dinh kem cua van ban
            $v_des_file_name        = $arr_result['des_file_name'];
            $v_uploaded_image_path  = CONST_SERVER_DOC_FILE_UPLOAD_DIR . $v_des_file_name;

            $stmt = 'Insert Into t_edoc_doc_file(FK_DOC, C_FILE_NAME) Values(?, ?)';
            $this->db->Execute($stmt, array($v_doc_id, $v_des_file_name));
        }
        */
        $count = count($_FILES['uploader']['name']);
        for ($i = 0; $i < $count; $i++)
        {
	        if ($_FILES['uploader']['error'][$i] == 0  )
	        {
	        	$v_file_name = $_FILES['uploader']['name'][$i];
	        	$v_tmp_name  = $_FILES['uploader']['tmp_name'][$i];
	        
	        	$v_file_ext = array_pop(explode('.', $v_file_name));
	        
	        	if (in_array($v_file_ext, explode('|', _CONST_RECORD_FILE_ACCEPT)))
	        	{
	        		if (move_uploaded_file($v_tmp_name, CONST_SERVER_DOC_FILE_UPLOAD_DIR .$v_file_name))
	        		{
	        			$stmt = 'Insert Into t_edoc_doc_file(FK_DOC, C_FILE_NAME) Values(?, ?)';
	        			$params = array($v_doc_id, $v_file_name);
	        			
	        			$this->db->Execute($stmt, $params);
	                }
	            }
	        }
        }
        //Xoa file dinh kem
        $v_deleted_doc_file_id_list = ltrim($_POST['hdn_deleted_doc_file_id_list'],',');
        if ($v_deleted_doc_file_id_list != '')
        {
            $sql = "Delete From t_edoc_doc_file Where PK_DOC_FILE in ($v_deleted_doc_file_id_list)";
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
			$this->db->Execute("Delete From t_edoc_doc Where PK_DOC in ($v_item_id_list)");
			//Xoa file dinh kem cua cac van ban nay
			$this->db->Execute("Delete From t_edoc_doc_file Where FK_DOC in ($v_item_id_list)");
			//Xóa message liên quan đến văn bản này
			$this->db->Execute("Delete From t_edoc_message where FK_DOC in ($v_item_id_list)");
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
		$v_step_seq = $this->get_new_seq_val('seq_doc_processing');

		//Văn bản đã từng được trình chưa?
		if(DATABASE_TYPE == 'MSSQL')
		{
			$sql = "Select step.value('count(//step[@code=''submit''])', 'Int')
					From  dbo.t_edoc_doc D CROSS APPLY C_XML_PROCESSING.nodes('/data') t(step)
					Where D.PK_DOC=$v_doc_id";
		}
		elseif(DATABASE_TYPE == 'MYSQL')
		{
			$sql = "Select ExtractValue(C_XML_PROCESSING,'count(//step[@code=''submit''])')
					From  t_edoc_doc D
					Where D.PK_DOC=$v_doc_id";
		}
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

		if(DATABASE_TYPE == 'MSSQL')
		{
			$stmt = 'Exec [dbo].[insert_doc_processing_step] ?, N?';
			
			$params = array($v_doc_id, $step);
			$this->db->Execute($stmt, $params);
			
		}
		elseif(DATABASE_TYPE == 'MYSQL')
		{
			$this->insert_xml_node('t_edoc_doc', 'C_XML_PROCESSING', $step, 'PK_DOC', $v_doc_id);
				
		}
		
		
		//End ghi log

		//Tao message phuc vu cong tac nhac nho, canh bao
		if(DATABASE_TYPE == 'MSSQL')
		{
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
		}
		elseif(DATABASE_TYPE == 'MYSQL')
		{
			$stmt = 'Insert Into t_edoc_message
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
					,?
					,?
					,?
					,Now()
					)';
		}
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

		$sql = "Delete From  t_edoc_folder_doc Where FK_FOLDER=$v_folder_id And FK_DOC=$v_doc_id";
		$this->db->Execute($sql);
		if(DATABASE_TYPE == 'MSSQL')
		{
			$sql = "Insert Into t_edoc_folder_doc (FK_FOLDER, FK_DOC, FK_USER, C_CREATE_DATE) Values($v_folder_id, $v_doc_id, $v_user_id, getDate())";
		}
		elseif(DATABASE_TYPE == 'MYSQL')
		{
			$sql = "Insert Into t_edoc_folder_doc (FK_FOLDER, FK_DOC, FK_USER, C_CREATE_DATE) Values($v_folder_id, $v_doc_id, $v_user_id, Now())";
		}
		$this->db->Execute($sql);

		$this->popup_exec_done();
	}

	//Lay danh sach phong ban NHAN phan cong thu lý
	public function qry_all_ou_option()
	{
		if(DATABASE_TYPE == 'MSSQL')
		{
			$sql = 'Select PK_OU, C_NAME From T_OU Where C_STATUS > 0 Order By C_ORDER';
		}
		elseif(DATABASE_TYPE == 'MYSQL')
		{
			$sql = 'Select PK_OU, C_NAME From t_cores_ou Where C_STATUS > 0 Order By C_ORDER';
		}
		return $this->db->getAssoc($sql);
	}
	
	///Lay danh sach CB phan phoi van ban cua phong minh
	public function qry_all_monitor_user()
	{
		if(DATABASE_TYPE == 'MSSQL')
		{
			$sql = "Select u.PK_USER, u.C_NAME
					From T_USER as u CROSS APPLY C_XML_DATA.nodes('/data') t(item)
					left join T_OU ou on ou.PK_OU=u.FK_OU
					Where 1 > 0
					And item.value('(item[@id=''PHU_TRACH_PHAN_PHOI_VAN_BAN_CUA_PHONG'']/value/text())[1]','varchar(50)')='true'
					Order By ou.C_ORDER, u.C_ORDER";
		}
		elseif(DATABASE_TYPE == 'MYSQL')
		{
			$sql = "Select u.PK_USER, u.C_NAME
					From t_cores_user as u	left join t_cores_ou ou on ou.PK_OU=u.FK_OU
					Where 1 > 0
					And ExtractValue(u.C_XML_DATA,'//item[@id=''PHU_TRACH_PHAN_PHOI_VAN_BAN_CUA_PHONG'']/value')='true'
					Order By ou.C_ORDER, u.C_ORDER";
		}
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
		if(DATABASE_TYPE == 'MSSQL')
		{
			$sql = "Select u.PK_USER, u.C_NAME
					From T_USER as u CROSS APPLY C_XML_DATA.nodes('/data') t(item)
					Where u.C_STATUS > 0
					And item.value('(item[@id=''$right'']/value/text())[1]','varchar(4)')='true'";
		}
		elseif(DATABASE_TYPE == 'MYSQL')
		{
			$sql = "Select u.PK_USER, u.C_NAME
					From t_cores_user as u
					Where u.C_STATUS > 0
					And ExtractValue(u.C_XML_DATA,'//item[@id=''$right'']/value')='true'";
		}
		return $this->db->getAssoc($sql);
	}

	//Lay danh sach CB co quyen thu ly van ban
	public function qry_all_exec_user()
	{
		if(DATABASE_TYPE == 'MSSQL')
		{
			$sql = "Select u.PK_USER, u.C_NAME
					From T_USER as u CROSS APPLY C_XML_DATA.nodes('/data') t(item)
					Where u.C_STATUS > 0
					And item.value('(item[@id=''THU_LY_VAN_BAN_DEN'']/value/text())[1]','varchar(50)')='true'";
		}
		elseif(DATABASE_TYPE == 'MYSQL')
		{
			$sql = "Select u.PK_USER, u.C_NAME
					From t_cores_user as u
					Where u.C_STATUS > 0
					And ExtractValue(u.C_XML_DATA,'//item[@id=''THU_LY_VAN_BAN_DEN'']/value')='true'";
		}
		return $this->db->getAssoc($sql);
	}

	public function qry_all_exec_user_in_ou(){
		//ID cua truong phong
		$v_leader_user_id = Session::get('user_id');
		if(DATABASE_TYPE == 'MSSQL')
		{
			$sql = "Select u.PK_USER, u.C_NAME
					From T_USER as u CROSS APPLY C_XML_DATA.nodes('/data') t(item)
					Where u.C_STATUS > 0
					And u.PK_USER <> $v_leader_user_id
					And FK_OU = (Select FK_OU 
								From T_USER 
								Where PK_USER=$v_leader_user_id)
					And item.value('(item[@id=''THU_LY_VAN_BAN_DEN'']/value/text())[1]','varchar(50)')='true'";
		}
		elseif(DATABASE_TYPE == 'MYSQL')
		{
			$sql = "Select u.PK_USER, u.C_NAME
					From t_cores_user as u
					Where u.C_STATUS > 0
					And u.PK_USER <> $v_leader_user_id
					And FK_OU = (Select FK_OU 
								From t_cores_user 
								Where PK_USER=$v_leader_user_id)
					And ExtractValue(u.C_XML_DATA,'//item[@id=''THU_LY_VAN_BAN_DEN'']/value')='true'";
		}
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
			$v_step_seq = $this->get_new_seq_val('seq_doc_processing');
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
				
			if(DATABASE_TYPE == 'MSSQL')
			{
				$stmt = 'Exec [dbo].[insert_doc_processing_step] ?, N?';
				$params = array($v_doc_id, $step);
				
				$this->db->Execute($stmt, $params);
			}
			elseif(DATABASE_TYPE == 'MYSQL')
			{
				$this->insert_xml_node('t_edoc_doc', 'C_XML_PROCESSING', $step, 'PK_DOC', $v_doc_id);
			}
			

			//Tao message canh bao
			//1. Canh bao toi CB phu trach
			if(DATABASE_TYPE == 'MSSQL')
			{
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
			}
			elseif(DATABASE_TYPE == 'MYSQL')
			{
				$stmt = 'Insert Into t_edoc_message
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
						,?
						,?
						,?
						,Now()
						)';
			}
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
			if(DATABASE_TYPE == 'MSSQL')
			{
				$stmt = "Insert Into T_EDOC_MESSAGE 
						(C_TO_USER_ID,
						C_TO_USER_NAME, 
						C_DATE_CREATED, 
						C_FROM_USER_ID, 
						C_FROM_USER_NAME, 
						C_MESSAGE_CONTENT, 
						C_MESSAGE_TITLE, 
						FK_STEP_SEQ, 
						FK_DOC
						)
						Select u.PK_USER, u.C_NAME, getDate(), ?, N?, N?, N?, ?, ?
						FROM T_USER as u CROSS APPLY C_XML_DATA.nodes('/data') t(item)
						Where FK_OU=?
						And item.value('(item[@id=''PHU_TRACH_PHAN_PHOI_VAN_BAN_CUA_PHONG'']/value/text())[1]','varchar(50)')='true'";
			}
			elseif(DATABASE_TYPE == 'MYSQL')
			{
				$stmt = "Insert Into t_edoc_message 
						(C_TO_USER_ID,C_TO_USER_NAME, 
						C_DATE_CREATED, 
						C_FROM_USER_ID, 
						C_FROM_USER_NAME, 
						C_MESSAGE_CONTENT, 
						C_MESSAGE_TITLE, 
						FK_STEP_SEQ, 
						FK_DOC
						)
						Select 
							u.PK_USER
							, u.C_NAME
							, Now()
							, ?
							, ?
							, ?
							, ?
							, ?
							, ?
						FROM t_cores_user as u
						Where FK_OU=?
						And ExtractValue(u.C_XML_DATA,'//item[@id=''PHU_TRACH_PHAN_PHOI_VAN_BAN_CUA_PHONG'']/value')='true'";
			}
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
				if(DATABASE_TYPE == 'MSSQL')
				{
					$stmt = "Insert Into T_EDOC_MESSAGE 
							(C_TO_USER_ID,
							C_TO_USER_NAME, 
							C_DATE_CREATED, 
							C_FROM_USER_ID, 
							C_FROM_USER_NAME, 
							C_MESSAGE_CONTENT, 
							C_MESSAGE_TITLE, 
							FK_STEP_SEQ, 
							FK_DOC
							)
							Select PK_USER, C_NAME, getDate(), ?, N?, N?, N?,?, ?
							FROM T_USER
							Where FK_OU in ($v_co_exec_ou_id_list)";
				}
				elseif(DATABASE_TYPE == 'MYSQL')
				{
					$stmt = "Insert Into t_edoc_message 
							(C_TO_USER_ID,
							C_TO_USER_NAME, 
							C_DATE_CREATED, 
							C_FROM_USER_ID, 
							C_FROM_USER_NAME, 
							C_MESSAGE_CONTENT, 
							C_MESSAGE_TITLE, 
							FK_STEP_SEQ, 
							FK_DOC
							)
							Select PK_USER, C_NAME, Now(), ?, ?, ?, ?, ?, ?
							FROM t_cores_user
							Where FK_OU in ($v_co_exec_ou_id_list)";
				}
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
			$v_step_seq = $this->get_new_seq_val('seq_doc_processing');
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

			if(DATABASE_TYPE == 'MSSQL')
			{
				$stmt = 'Exec [dbo].[insert_doc_processing_step] ?, N?';
				$params = array($v_doc_id, $step);
				
				$this->db->Execute($stmt, $params);
			}
			elseif (DATABASE_TYPE == 'MYSQL')
			{
				$this->insert_xml_node('t_edoc_doc', 'C_XML_PROCESSING', $step, 'PK_DOC', $v_doc_id);
			}
			

			//Tao message canh bao
			//1. Canh bao toi CB phu trach
			if(DATABASE_TYPE == 'MSSQL')
			{
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
			}
			elseif(DATABASE_TYPE == 'MYSQL')
			{
				$stmt = 'Insert Into t_edoc_message
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
						,?
						,?
						,?
						,Now()
						)';
			}
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
			if(DATABASE_TYPE == 'MSSQL')
			{
				$stmt = "Insert Into T_EDOC_MESSAGE 
						(C_TO_USER_ID,
						C_TO_USER_NAME
						, C_DATE_CREATED
						, C_FROM_USER_ID
						, C_FROM_USER_NAME
						, C_MESSAGE_CONTENT
						, C_MESSAGE_TITLE
						, FK_STEP_SEQ
						, FK_DOC
						)Values
						(
						$v_direct_exec_user_id
						, N'$v_direct_exec_user_name'
						, getDate()
						, ?
						, N?
						, N?
						, N?
						, ?
						, ?
						)";
			}
			elseif(DATABASE_TYPE == 'MYSQL')
			{
				$stmt = "Insert Into t_edoc_message 
						(C_TO_USER_ID
						,C_TO_USER_NAME
						, C_DATE_CREATED
						, C_FROM_USER_ID
						, C_FROM_USER_NAME
						, C_MESSAGE_CONTENT
						, C_MESSAGE_TITLE
						, FK_STEP_SEQ
						, FK_DOC
						) Values
						(
						$v_direct_exec_user_id
						, $v_direct_exec_user_name
						, Now()
						, ?
						, ?
						, ?
						, ?
						, ?
						, ?
						)";

			}
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
				if(DATABASE_TYPE == 'MSSQL')
				{
					$stmt = "Insert Into T_EDOC_MESSAGE 
							(C_TO_USER_ID
							,C_TO_USER_NAME
							, C_DATE_CREATED
							, C_FROM_USER_ID
							, C_FROM_USER_NAME
							, C_MESSAGE_CONTENT
							, C_MESSAGE_TITLE
							, FK_STEP_SEQ
							, FK_DOC
							)
							Select PK_USER, C_NAME, getDate(), ?, N?, N?, N?,?, ?
							FROM T_USER
							Where PK_USER in ($v_co_exec_user_id_list)";
				}
				elseif(DATABASE_TYPE == 'MYSQL')
				{
					$stmt = "Insert Into t_edoc_message
							(C_TO_USER_ID
							,C_TO_USER_NAME
							, C_DATE_CREATED
							, C_FROM_USER_ID
							, C_FROM_USER_NAME
							, C_MESSAGE_CONTENT
							, C_MESSAGE_TITLE
							, FK_STEP_SEQ
							, FK_DOC
							)
							Select PK_USER, C_NAME, Now(), ?, ?, ?, ?,?, ?
							FROM t_cores_user
							Where PK_USER in ($v_co_exec_user_id_list)";
				}
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
		$v_step_seq = $this->get_new_seq_val('seq_doc_processing');
		$step = '<step seq="' . $v_step_seq . '" code="' . $v_step_code . '">';
		$step .= $this->create_single_xml_node('approve_user_id',  Session::get('user_id'));
		$step .= $this->create_single_xml_node('approve_user_name',  Session::get('user_name'), TRUE);
		$step .= $this->create_single_xml_node('action_text', $v_approve_text, TRUE);
		$step .= $this->create_single_xml_node('datetime', $this->getDate(), TRUE);
		$step .= $this->create_single_xml_node('reject_reason', $v_reject_reason, TRUE);
		$step .= '</step>';

		if(DATABASE_TYPE == 'MSSQL')
		{
			$stmt = 'Exec [dbo].[insert_doc_processing_step] ?, N?';
			$params = array($v_doc_id, $step);
			
			$this->db->Execute($stmt, $params);
		}
		elseif(DATABASE_TYPE == 'MYSQL')
		{
			$this->insert_xml_node('t_edoc_doc', 'C_XML_PROCESSING', $step, 'PK_DOC', $v_doc_id);
		}
		

		//------------------------------------------------------------------------------
		//Thong bao toi nguoi trinh ve trang thai phe duyet
		$v_message_content = ($v_approve == 0) ? ' Từ chối phê duyệt văn bản đi (Lý do: ' . $v_reject_reason . ')' : 'Duyệt văn bản đi';
		if(DATABASE_TYPE == 'MSSQL')
		{
			$stmt = "Insert Into T_EDOC_MESSAGE 
					(C_TO_USER_ID
					,C_TO_USER_NAME
					, C_DATE_CREATED
					, C_FROM_USER_ID
					, C_FROM_USER_NAME
					, C_MESSAGE_CONTENT
					, C_MESSAGE_TITLE
					, FK_STEP_SEQ
					, FK_DOC)
					Select PK_USER, C_NAME, getDate(), ?, N?, N?, N?, ?, ? 
					From T_USER 
					Where PK_USER=(Select 
									  FK_USER 
								   From T_EDOC_DOC 
								   Where PK_DOC=?
								   )";
		}
		elseif(DATABASE_TYPE == 'MYSQL')
		{
			$stmt = "Insert Into t_edoc_message 
					(C_TO_USER_ID
					,C_TO_USER_NAME
					, C_DATE_CREATED
					, C_FROM_USER_ID
					, C_FROM_USER_NAME
					, C_MESSAGE_CONTENT
					, C_MESSAGE_TITLE
					, FK_STEP_SEQ
					, FK_DOC
					)
					Select 
						PK_USER
						, C_NAME
						, Now()
						, ?
						, ?
						, ?
						, ?
						, ?
						, ? 
					From t_cores_user 
					Where PK_USER=(Select FK_USER 
									From t_edoc_doc 
									Where PK_DOC=?
								   )";
		}
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
				if(DATABASE_TYPE == 'MSSQL')
				{
					$sql = "Select u.PK_USER, u.C_NAME
							From T_USER u CROSS APPLY C_XML_DATA.nodes('/data') t(item)
							Where item.value('(item[@id=''VAO_SO_VAN_BAN_DI'']/value/text())[1]','varchar(4)')='true'";
				}
				elseif(DATABASE_TYPE == 'MYSQL')
				{
					$sql = "Select u.PK_USER, u.C_NAME
							From t_cores_user u
							Where ExtractValue(u.C_XML_DATA,'//item[@id=''VAO_SO_VAN_BAN_DI'']/value')='true'";
				}
				$arr_user = $this->db->getAssoc($sql);

				$v_message_content = 'Văn bản chờ vào sổ';

				echo '<hr>' . $v_message_content;

				foreach ($arr_user as $user_id => $user_name)
				{
					if(DATABASE_TYPE == 'MSSQL')
					{
						$stmt = "Insert Into T_EDOC_MESSAGE 
								(C_TO_USER_ID
								,C_TO_USER_NAME
								, C_DATE_CREATED
								, C_FROM_USER_ID
								, C_FROM_USER_NAME
								, C_MESSAGE_CONTENT
								, C_MESSAGE_TITLE
								, FK_STEP_SEQ
								, FK_DOC
								)Values
								(
								?
								,?
								, getDate()
								, ?
								, N?
								, N?
								, N?
								, ?
								, ?
								)";
					}
					elseif(DATABASE_TYPE == 'MYSQL')
					{
						$stmt = "Insert Into t_edoc_message 
								(C_TO_USER_ID
								,C_TO_USER_NAME
								, C_DATE_CREATED
								, C_FROM_USER_ID
								, C_FROM_USER_NAME
								, C_MESSAGE_CONTENT
								, C_MESSAGE_TITLE
								, FK_STEP_SEQ
								, FK_DOC
								)Values
								(
								?
								,?
								, Now()
								, ?
								, ?
								, ?
								, ?
								, ?
								, ?
								)";
					}
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
		$v_step_seq = $this->get_new_seq_val('seq_doc_processing');
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

		if(DATABASE_TYPE == 'MSSQL')
		{
			$stmt = 'Exec [dbo].[insert_doc_processing_step] ?, N?';
			$params = array($v_doc_id, $step);
			
			$this->db->Execute($stmt, $params);
		}
		elseif(DATABASE_TYPE == 'MYSQL')
		{
			$this->insert_xml_node('t_edoc_doc', 'C_XML_PROCESSING', $step, 'PK_DOC', $v_doc_id);
		}
		

		//Tao message canh bao
		//2. Canh bao toi CB thu ly chinh
		if(DATABASE_TYPE == 'MSSQL')
		{
			$stmt = "Insert Into T_EDOC_MESSAGE 
					(C_TO_USER_ID
					, C_TO_USER_NAME
					, C_DATE_CREATED
					, C_FROM_USER_ID
					, C_FROM_USER_NAME
					, C_MESSAGE_CONTENT
					, C_MESSAGE_TITLE
					, FK_STEP_SEQ
					, FK_DOC
					)Values
					(
					$v_direct_exec_user_id
					, N'$v_direct_exec_user_name'
					, getDate()
					, ?
					, N?
					, N?
					, N?
					, ?
					, ?
					)";
		}
		elseif(DATABASE_TYPE == 'MYSQL')
		{
			$stmt = "Insert Into t_edoc_message 
					(
					C_TO_USER_ID
					,C_TO_USER_NAME
					, C_DATE_CREATED
					, C_FROM_USER_ID
					, C_FROM_USER_NAME
					, C_MESSAGE_CONTENT
					, C_MESSAGE_TITLE
					, FK_STEP_SEQ
					, FK_DOC
					)Values
					(
					$v_direct_exec_user_id
					, '$v_direct_exec_user_name'
					, Now()
					, ?
					, ?
					, ?
					, ?
					, ?
					, ?
					)";

		}
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
			if(DATABASE_TYPE == 'MSSQL')
			{
				$stmt = "Insert Into T_EDOC_MESSAGE 
						(
						C_TO_USER_ID
						,C_TO_USER_NAME
						, C_DATE_CREATED
						, C_FROM_USER_ID
						, C_FROM_USER_NAME
						, C_MESSAGE_CONTENT
						, C_MESSAGE_TITLE
						, FK_STEP_SEQ
						, FK_DOC
						)
						Select 
							PK_USER
							, C_NAME
							, getDate()
							, ?
							, N?
							, N?
							, N?
							,?
							, ?
						FROM T_USER
						Where PK_USER in ($v_co_exec_user_id_list)";
			}
			elseif(DATABASE_TYPE == 'MYSQL')
			{
				$stmt = "Insert Into t_edoc_message 
						(C_TO_USER_ID
						,C_TO_USER_NAME
						, C_DATE_CREATED
						, C_FROM_USER_ID
						, C_FROM_USER_NAME
						, C_MESSAGE_CONTENT
						, C_MESSAGE_TITLE
						, FK_STEP_SEQ
						, FK_DOC
						)
						Select 
							PK_USER
							, C_NAME
							, Now()
							, ?
							, ?
							, ?
							, ?
							, ?
							, ?
						FROM t_cores_user
						Where PK_USER in ($v_co_exec_user_id_list)";
			}
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
			$v_step_seq = $this->get_new_seq_val('seq_doc_processing');
			$step = '<step seq="' . $v_step_seq . '" code="commit">';
			$step .= $this->create_single_xml_node('commit_user_id',  Session::get('user_id'));
			$step .= $this->create_single_xml_node('commit_user_name',  Session::get('user_name'), TRUE);
			$step .= $this->create_single_xml_node('action_text', $v_exec_text, TRUE);
			$step .= $this->create_single_xml_node('datetime', $this->getDate(), TRUE);
			$step .= '</step>';

			if(DATABASE_TYPE == 'MSSQL')
			{
				$stmt = 'Exec [dbo].[insert_doc_processing_step] ?, N?';
				$params = array($doc_id, $step);
				
				$this->db->Execute($stmt, $params);
			}
			elseif(DATABASE_TYPE == 'MYSQL')
			{
				$this->insert_xml_node('t_edoc_doc', 'C_XML_PROCESSING', $step, 'PK_DOC', $doc_id);
			}
			

			//Thong bao cho cac bo phan lien quan cong viec da hoan thanh
			//tim nguoi phan cong thu ly
		}
		else
		{
			//Thuc hien cap nhat tien do
			$v_step_seq = $this->get_new_seq_val('seq_doc_processing');
			$step = '<step seq="' . $v_step_seq . '" code="exec">';
			$step .= $this->create_single_xml_node('exec_user_id',  Session::get('user_id'));
			$step .= $this->create_single_xml_node('exec_user_name',  Session::get('user_name'), TRUE);
			$step .= $this->create_single_xml_node('action_text', $v_exec_text, TRUE);
			$step .= $this->create_single_xml_node('datetime', $this->getDate(), TRUE);
			$step .= '</step>';
				
			if(DATABASE_TYPE == 'MSSQL')
			{
				$stmt = 'Exec [dbo].[insert_doc_processing_step] ?, N?';
				$params = array($doc_id, $step);
				
				$this->db->Execute($stmt, $params);
				
			}
			elseif(DATABASE_TYPE == 'MYSQL')
			{
				$this->insert_xml_node('t_edoc_doc', 'C_XML_PROCESSING', $step, 'PK_DOC', $doc_id);
			}
			
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
				if(DATABASE_TYPE == 'MSSQL')
				{
					$stmt = 'Exec dbo.delete_doc_processing_step ?, ?';
					
					$params = array($v_doc_id, $step_seq);
					$this->db->Execute($stmt, $params);
				}
				elseif(DATABASE_TYPE == 'MYSQL')
				{//xóa một node trong xml
					$stmt="select C_XML_PROCESSING from t_edoc_doc where PK_DOC=$v_doc_id";
					$v_xml_processing = xml_add_declaration($this->db->GetOne($stmt));
					
					$doc = new DOMDocument();
					$doc->loadXML($v_xml_processing);
					$steps = $doc->documentElement->getElementsByTagName("step");
					
					foreach($steps as $step)
					{
						$seq = $step->getAttribute("seq");
					
						if ($seq == $step_seq)
						{
							$parent = $step->parentNode;
							$parent->removeChild($step);
						}
					}
					$v_new_xml = xml_add_declaration($doc->saveXML(), 0);
					
					$stmt="update t_edoc_doc set C_XML_PROCESSING=? where PK_DOC=?";
					$this->db->execute($stmt,array($v_new_xml,$v_doc_id));
				}
				
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
		if(DATABASE_TYPE == 'MSSQL')
		{
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
		}
		elseif (DATABASE_TYPE == 'MYSQL')
		{
			$v_view_name = 'view_doc';
			if ($v_processing_filter == 1)
			{
				$v_view_name = 'view_processing_doc';
			}
			elseif ($v_processing_filter == 2)
			{
				$v_view_name = 'view_processing_expired_doc';
			}
			elseif ($v_processing_filter == 3)
			{
				$v_view_name = 'view_committed_doc';
			}
			elseif ($v_processing_filter == 4)
			{
				$v_view_name = 'view_committed_expired_doc';
			}
		}
		$condition_query = " D.C_TYPE='$type' ";
		//Quyen giam sat
		$v_is_view_all = Controller::check_permission('GIAM_SAT_TOAN_BO_VAN_BAN');
		if ($v_is_view_all == FALSE)
		{
			if(DATABASE_TYPE == 'MSSQL')
			{
				$condition_query .= " And item.value('(item[@id=''cho_phep_tra_cuu_cong_khai'']/value/text())[1]','varchar(50)')='true'";
			}
			elseif(DATABASE_TYPE == 'MYSQL')
			{
				$condition_query .= " And ExtractValue(D.C_XML_DATA,'//item[@id=''cho_phep_tra_cuu_cong_khai'']/value')='true'";
			}
		}

		if ($v_filter != '')
		{
			if(DATABASE_TYPE == 'MSSQL')
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
			elseif(DATABASE_TYPE == 'MYSQL')
			{
				$condition_query .= " And (MATCH (C_XML_DATA) AGAINST ('$v_filter' IN NATURAL LANGUAGE MODE) 
											Or (D.DOC_NGAY_DEN like '%$v_filter%')
											Or (D.DOC_NGAY_DI like '%$v_filter%')
											)";
			}
		}

		//Dem tong ban ghi
		if(DATABASE_TYPE == 'MSSQL')
		{
			$sql_count_record = "Select Count(*) From [dbo].$v_view_name D Where (1 > 0) And $condition_query";
		}
		elseif(DATABASE_TYPE == 'MYSQL')
		{
			$sql_count_record = "Select Count(*) From $v_view_name D Where (1 > 0) And ExtractValue(C_XML_DATA,'//item[@id=''cho_phep_tra_cuu_cong_khai'']/value')='true' And $condition_query";
		}
		//Lấy danh sách văn bản đến cho phép tra cứu công khai
		if(DATABASE_TYPE == 'MSSQL')
		{
			$sql = "Select d.*
						,($sql_count_record) as TOTAL_RECORD
						,ROW_NUMBER() OVER (ORDER BY D.C_DATE_CREATED Desc) as RN
						,Convert(Xml,(Select 
										PK_DOC_FILE
										, FK_DOC
										, C_FILE_NAME 
									From T_EDOC_DOC_FILE df 
									Where df.FK_DOC=D.PK_DOC For xml raw
								),1) as DOC_ATTACH_FILE_LIST
						,(Select Count(*) 
						  From T_EDOC_FOLDER_DOC 
						  Where FK_DOC=D.PK_DOC
						  ) as C_FOLDED
					From $v_view_name d CROSS APPLY C_XML_DATA.nodes('/data') t(item)
					Where (d.C_DIRECTION='VBDEN' Or d.C_DIRECTION='VBDI')
					And $condition_query ";
		}
		elseif(DATABASE_TYPE == 'MYSQL')
		{
			$sql = "Select D.*
						,($sql_count_record) as TOTAL_RECORD
						, @i := @i + 1 AS  RN
						, (SELECT GROUP_CONCAT('<row '
												,CONCAT(' PK_DOC_FILE=\"', PK_DOC_FILE, '\"')
												,CONCAT(' FK_DOC=\"', FK_DOC, '\"')
												,CONCAT(' C_FILE_NAME=\"', C_FILE_NAME, '\"')
												, ' />'
												SEPARATOR ''
											  )
							FROM t_edoc_doc_file df
							WHERE df.FK_DOC=D.PK_DOC
						  ) AS DOC_ATTACH_FILE_LIST
						,(Select Count(*) 
						  From t_edoc_folder_doc 
						  Where FK_DOC=D.PK_DOC
						  ) as C_FOLDED
					From $v_view_name D,(SELECT @i := 0) temp
					Where (1>0) And $condition_query
					ORDER BY  D.C_DATE_CREATED Desc ";
		}

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
		/*
		 $stmt = "   Select isnull(max(a.SO_DI_DEN),0) MAX_VALUE
		From
		(	Select
				dbo.f_get_outcomming_doc_number(item.value('(item[@id='?']/value/text())[1]','varchar(10)')) as SO_DI_DEN
				From dbo.T_EDOC_DOC d CROSS APPLY C_XML_DATA.nodes('/data') t(item)
				Where d.C_DIRECTION=?
				and C_TYPE=?
		) a";
		*/
		if(DATABASE_TYPE == 'MSSQL')
		{
			$stmt = "Select isnull(max(a.SO_DI_DEN),0) MAX_VALUE
					 From (	Select
								dbo.f_get_outcomming_doc_number(item.value('(item[@id='?']/value/text())[1]','varchar(10)')) as SO_DI_DEN
								,C_DATE_CREATED
							From dbo.T_EDOC_DOC d CROSS APPLY C_XML_DATA.nodes('/data') t(item)
							Where d.C_DIRECTION=?
							and C_TYPE=?
						  ) a
					Where Datediff(year,C_DATE_CREATED,getDate())=0";
		}
		elseif(DATABASE_TYPE == 'MYSQL')
		{
			$stmt = "Select ifnull(max(a.SO_DI_DEN),0) MAX_VALUE
					 From (	Select
								CAST(ExtractValue(C_XML_DATA,'//item[@id='?']/value') AS UNSIGNED) as SO_DI_DEN
								,C_DATE_CREATED
							From t_edoc_doc d
							Where d.C_DIRECTION=?
							and C_TYPE=?
						  ) a
					Where YEAR(NOW()) - YEAR(C_DATE_CREATED)=0";
		}

		$params = array($id, $direction, $type);
		$this->db->debug = 0;
		return $this->db->getOne($stmt, $params);
	}

	public function qry_all_doc_for_print($p_direction, $p_type, $p_begin_date, $p_end_date)
	{
		if(DATABASE_TYPE == 'MSSQL')
		{
			$stmt = "Select D.*
					From VIEW_DOC D
					Where D.C_DIRECTION=?
					And D.C_TYPE=?";
			 
			if ($p_direction == 'VBDEN')
			{
				$stmt .= ' And (C_DATE_CREATED - Convert(datetime,?,111)) <=0  
						   And (C_DATE_CREATED - convert(datetime,?,111)) >=0';
				$stmt .= " Order By D.C_XML_DATA.value('(//item[@id=''doc_so_den'']/value/text())[1]', 'Int'), C_DATE_CREATED";
			}
			elseif ($p_direction == 'VBDI')
			{
				//$stmt .= 'And (Convert(date, DOC_NGAY_DI, 103) between ? And ?)';

				$stmt .= " And (Convert(Date, D.C_XML_DATA.value('(//item[@id=''doc_ngay_van_ban'']/value/text())[1]', 'varchar(10)') ,103) between ? And ?)";
				$stmt .= " Order By Convert(Int, [dbo].[f_get_outcomming_doc_number](D.DOC_SO_KY_HIEU)), C_DATE_CREATED ";
			}
		} 
		elseif(DATABASE_TYPE == 'MYSQL')
		{
			$stmt = "Select D.*
					From view_doc D
					Where D.C_DIRECTION=?
					And D.C_TYPE=?";
			if ($p_direction == 'VBDEN')
			{
				$stmt .= ' And (? - C_DATE_CREATED) <=0  
						   And (? - C_DATE_CREATED) >=0';
				$stmt .= " Order By ExtractValue(D.C_XML_DATA,'//item[@id=''doc_so_den'']/value'), C_DATE_CREATED";
			}
			elseif ($p_direction == 'VBDI')
			{
				//$stmt .= 'And (Convert(date, DOC_NGAY_DI, 103) between ? And ?)';
				 
				$stmt .= " And (DATE_FORMAT(ExtractValue(D.C_XML_DATA,'//item[@id=''doc_ngay_van_ban'']/value'),'%d/%m/%y') between ? And ?)";
				$stmt .= " Order By CAST(D.DOC_SO_KY_HIEU AS UNSIGNED), C_DATE_CREATED ";
			}
		}
		$params = array($p_direction, $p_type, $p_begin_date, $p_end_date );
		//return $this->db->CachegetAll(5000, $stmt, $params);
		return $this->db->getAll($stmt, $params);
	}

	public function qry_all_folder($p_user_id)
	{
		if(DATABASE_TYPE == 'MSSQL')
		{
			$sql = "Select 
						F.PK_FOLDER
						, (F.C_CODE + ' - ' + F.C_NAME) as C_NAME
					From T_EDOC_FOLDER F Left Join T_EDOC_FOLDER_USER FU On F.PK_FOLDER=FU.FK_FOLDER
					Where (F.C_STATUS > 0)
					And (F.C_IS_CLOSED < 1)
					And (
						(F.FK_USER=$p_user_id)
						Or (FU.C_USER_TYPE='USER' And FU.FK_USER=$p_user_id)
						Or (FU.C_USER_TYPE='OU' And FU.FK_USER=(Select FK_OU 
																From T_USER 
																Where PK_USER=$p_user_id
																)
																
						)";
		}
		elseif(DATABASE_TYPE == 'MYSQL')
		{
			$sql = "Select 
						f.PK_FOLDER
						, CONCAT(f.C_CODE , ' - ' , f.C_NAME) as C_NAME
					From t_edoc_folder f Left Join t_edoc_folder_user fu On f.PK_FOLDER=fu.FK_FOLDER
					Where (f.C_STATUS > 0)
					And (f.C_IS_CLOSED < 1)
					And (
							(f.FK_USER=$p_user_id)
							Or (fu.C_USER_TYPE='USER' And fu.FK_USER=$p_user_id)
							Or (fu.C_USER_TYPE='OU' And fu.FK_USER=(Select FK_OU From t_cores_user Where PK_USER=$p_user_id)
						)
					)";
		}
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

		$stmt = 'Delete From t_edoc_folder_doc Where FK_DOC=? And FK_FOLDER=?';
		$params = array($v_doc_id, $v_folder_id);
		$this->db->Execute($stmt, $params);



		$this->exec_done($this->goback_url . strtolower($_POST['direction']) . '/' . strtolower($_POST['type']));
	}

	public function qry_all_doc_to_add($p_filter)
	{

		$v_user_id = session::get('user_id');

		if(DATABASE_TYPE == 'MSSQL')
		{
			$sql = "Select 
						D.PK_DOC
						,D.DOC_SO_KY_HIEU
						,D.DOC_TRICH_YEU
						,D.C_DIRECTION
						,D.C_TYPE
						,D.C_XML_DATA.value('(//item[@id=''doc_ngay_van_ban'']/value/text())[1]', 'varchar(50)') DOC_NGAY_VAN_BAN
					From VIEW_DOC D
					Where D.PK_DOC in (SELECT DISTINCT FK_DOC DOC_ID
										FROM   T_EDOC_MESSAGE
										WHERE  ( C_TO_USER_ID = $v_user_id
												 OR C_FROM_USER_ID = $v_user_id
												)
										UNION ALL
										Select PK_DOC DOC_ID
										From T_EDOC_DOC
										Where FK_USER=$v_user_id
										)";
		}
		elseif(DATABASE_TYPE == 'MYSQL')
		{
			$sql = "Select 
						D.PK_DOC
						,D.DOC_SO_KY_HIEU
						,D.DOC_TRICH_YEU
						,D.C_DIRECTION
						,D.C_TYPE
						,ExtractValue(D.C_XML_DATA,'//item[@id=''doc_ngay_van_ban'']/value') DOC_NGAY_VAN_BAN
					From view_doc D
					Where D.PK_DOC in (SELECT DISTINCT FK_DOC DOC_ID
										FROM   t_edoc_message
										WHERE  ( C_TO_USER_ID = $v_user_id
										OR C_FROM_USER_ID = $v_user_id
										)
										UNION ALL
										Select PK_DOC DOC_ID
										From t_edoc_doc
										Where FK_USER=$v_user_id
									   )";
			 
		}
		if ($p_filter != '')
		{
			$sql .= " And (D.DOC_SO_KY_HIEU like '%$p_filter%' Or D.DOC_TRICH_YEU like '%$p_filter%')"; 
		}
		return $this->db->getAll($sql);
	}

	public function qry_all_doc_user($doc_id)
	{
		if(DATABASE_TYPE == 'MSSQL')
		{
			$stmt = 'Select 
						M.C_TO_USER_ID FK_USER
						,U.C_NAME
						,U.C_LOGIN_NAME
					From T_EDOC_MESSAGE M Left Join T_USER U On M.C_TO_USER_ID=U.PK_USER
					Where M.FK_DOC=?
					And FK_STEP_SEQ Is Null';
		}
		elseif(DATABASE_TYPE == 'MYSQL')
		{
			$stmt = 'Select 
						M.C_TO_USER_ID FK_USER
						,U.C_NAME
						,U.C_LOGIN_NAME
					From t_edoc_message M Left Join t_cores_user U On M.C_TO_USER_ID=U.PK_USER
					Where M.FK_DOC=?
					And FK_STEP_SEQ Is Null';
		}

		$params = array($doc_id);
		return $this->db->getAll($stmt, $params);
	}

	public function do_update_doc_user()
	{
		$v_doc_id = isset($_POST['hdn_item_id']) ? $this->replace_bad_char($_POST['hdn_item_id']) : '0';
		$v_user_id_list = isset($_POST['hdn_user_id_list']) ? $this->replace_bad_char($_POST['hdn_user_id_list']) : '';

		$arr_all_user_id = explode(',', $v_user_id_list);

		//Xoa het du lieu cu
		$stmt = 'Delete From t_edoc_message Where FK_DOC=? And FK_STEP_SEQ Is Null';
		$params = array($v_doc_id);
		$this->db->Execute($stmt, $params);

		//Cap nhat du lieu moi
		foreach ($arr_all_user_id as $v_to_user_id)
		{
			if(DATABASE_TYPE == 'MSSQL')
			{
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
						,?
						,?
						,?
						,getDate()
						)';
			}
			elseif(DATABASE_TYPE == 'MYSQL')
			{
				$stmt = 'Insert Into t_edoc_message
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
						,?
						,?
						,?
						,Now()
						)';
			}
			$params = array(
					Session::get('user_id')
					,Session::get('user_name')
					,$v_to_user_id
					,NULL
					,NULL
					,NULL
					,NULL
					,$v_doc_id
			);
			$this->db->Execute($stmt, $params);
		}

		$this->popup_exec_done();
	}

	public function do_pre_processing_doc()
	{
		$v_allot_by = isset($_POST['hdn_allot_by']) ? $this->replace_bad_char($_POST['hdn_allot_by']) : 0;
		$v_doc_id   = $this->replace_bad_char($_POST['hdn_item_id']);

		$v_step_seq = $this->get_new_seq_val('seq_doc_processing');

		//trinh lanh dao
		if ($v_allot_by == 1)
		{
			$v_allot_user_id    = $this->replace_bad_char($_POST['sel_allot_user']);
			$v_allot_user_name  = $this->replace_bad_char($_POST['hdn_allot_user_name']);
			$v_message_content  = $this->replace_bad_char($_POST['txt_submit_message']);

			$v_action_text =  'Trình lãnh đạo';
			$step = '<step seq="' . $v_step_seq . '" code="submit">';
			$step .= $this->create_single_xml_node('submit_user_id', Session::get('user_id'));
			$step .= $this->create_single_xml_node('submit_user_name', Session::get('user_name'), TRUE);
			$step .= $this->create_single_xml_node('allot_user_id', $v_allot_user_id);
			$step .= $this->create_single_xml_node('allot_user_name', $v_allot_user_name, TRUE);
			$step .= $this->create_single_xml_node('action_text', $v_action_text, TRUE);
			$step .= $this->create_single_xml_node('datetime', $this->getDate());
			$step .= '</step>';
				
			if(DATABASE_TYPE == 'MSSQL')
			{
				$stmt = 'Exec [dbo].[insert_doc_processing_step] ?, N?';
				$params = array($v_doc_id, $step);
				
				$this->db->Execute($stmt, $params);
			}
			elseif(DATABASE_TYPE == 'MYSQL')
			{
				$this->insert_xml_node('t_edoc_doc', 'C_XML_PROCESSING', $step, 'PK_DOC', $v_doc_id);
			}
			
			//End ghi log

			//Tao message phuc vu cong tac nhac nho, canh bao
			if(DATABASE_TYPE == 'MSSQL')
			{
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
			}
			elseif(DATABASE_TYPE == 'MYSQL')
			{
				$stmt = 'Insert Into t_edoc_message
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
						,?
						,?
						,?
						,Now()
						)';
			}
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
		}
		elseif ($v_allot_by == 2)
		{
			//ID phong thu ly chinh
			$v_direct_exec_ou_id    = $this->replace_bad_char($_POST['hdn_direct_exec_ou_id']);
			$v_direct_exec_ou_name  = $this->replace_bad_char($_POST['hdn_direct_exec_ou_name']);

			//Danh sach phong ban phoi hop
			$v_co_exec_ou_id_list = '';

			//Lanh dao phu trach
			$v_monitor_user_id      = '';
			$v_monitor_user_name    = '';

			//Y kien chi dao
			$v_allot_message = '';

			//Thoi han xu ly
			$v_exec_day             = '';
			$v_deadline_exec_date   = '';

			//Lap ho so vu viec
			$v_open_doc_profile = 0;

			//Ghi Log tien trinh xu ly
			$v_step_seq = $this->get_new_seq_val('seq_doc_processing');
			$step = '<step seq="' . $v_step_seq . '" code="allot">';
			$step .= $this->create_single_xml_node('allot_user_id',  Session::get('user_id'));
			$step .= $this->create_single_xml_node('allot_user_name',  Session::get('user_name'), TRUE);
			$step .= $this->create_single_xml_node('action_text', 'Chuyển tiếp văn bản cho cho ' . $v_direct_exec_ou_name  , TRUE);
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
				
			if(DATABASE_TYPE == 'MSSQL')
			{
				$stmt = 'Exec [dbo].[insert_doc_processing_step] ?, N?';
				$params = array($v_doc_id, $step);
				
				$this->db->Execute($stmt, $params);
			}
			elseif(DATABASE_TYPE == 'MYSQL')
			{
				$this->insert_xml_node('t_edoc_doc', 'C_XML_PROCESSING', $step, 'PK_DOC', $v_doc_id);
			}
			

			//2. --Cảnh báo tới Người Phụ trách phân phối văn bản của phòng
			if(DATABASE_TYPE == 'MSSQL')
			{
				$stmt = "Insert Into T_EDOC_MESSAGE 
						(C_TO_USER_ID
						,C_TO_USER_NAME
						, C_DATE_CREATED
						, C_FROM_USER_ID
						, C_FROM_USER_NAME
						, C_MESSAGE_CONTENT
						, C_MESSAGE_TITLE
						, FK_STEP_SEQ
						, FK_DOC)
						Select u.PK_USER, u.C_NAME, getDate(), ?, N?, N?, N?, ?, ?
						FROM T_USER as u CROSS APPLY C_XML_DATA.nodes('/data') t(item)
						Where FK_OU=?
						And item.value('(item[@id=''PHU_TRACH_PHAN_PHOI_VAN_BAN_CUA_PHONG'']/value/text())[1]','varchar(50)')='true'
						";
			}
			elseif(DATABASE_TYPE == 'MYSQL')
			{
				$stmt = "Insert Into t_edoc_message 
						(C_TO_USER_ID
						,C_TO_USER_NAME
						, C_DATE_CREATED
						, C_FROM_USER_ID
						, C_FROM_USER_NAME
						, C_MESSAGE_CONTENT
						, C_MESSAGE_TITLE
						, FK_STEP_SEQ
						, FK_DOC
						)
						Select u.PK_USER, u.C_NAME, Now(), ?, ?, ?, ?, ?, ?
						FROM t_cores_user as u
						Where FK_OU=?
						And ExtractValue(u.C_XML_DATA,'//item[@id=''PHU_TRACH_PHAN_PHOI_VAN_BAN_CUA_PHONG'']/value')='true'
						";
			}
			$params = array(
					Session::get('user_id')
					,Session::get('user_name')
					,$v_allot_message
					,'Chuyển tiếp văn bản cho phòng cho phòng ' . $v_direct_exec_ou_name
					,$v_step_seq
					,$v_doc_id
					,$v_direct_exec_ou_id
			);
			$this->db->Execute($stmt, $params);

		}
		elseif ($v_allot_by == 3)
		{
			//ID Can bo thu ly chinh
			$v_direct_exec_user_id      = $this->replace_bad_char($_POST['hdn_direct_exec_user_id']);
			$v_direct_exec_user_name    = $this->replace_bad_char($_POST['hdn_direct_exec_user_name']);

			//Danh sach CB phoi hop
			$v_co_exec_user_id_list      = '';

			//Lanh dao phu trach
			$v_monitor_user_id      = '';
			$v_monitor_user_name    = '';

			//Y kien chi dao
			$v_allot_message = '';

			//Thoi han xu ly
			$v_exec_day             = '';
			$v_deadline_exec_date   = '';

			//Lap ho so vu viec
			$v_open_doc_profile = 0;

			//Ghi Log tien trinh xu ly
			$v_step_seq = $this->get_new_seq_val('seq_doc_processing');
			$step = '<step seq="' . $v_step_seq . '" code="allot">';
			$step .= $this->create_single_xml_node('allot_user_id',  Session::get('user_id'));
			$step .= $this->create_single_xml_node('allot_user_name',  Session::get('user_name'), TRUE);
			$step .= $this->create_single_xml_node('action_text', 'Chuyển tiếp văn bản cho cho: ' . $v_direct_exec_user_name, TRUE);
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
			if(DATABASE_TYPE == 'MSSQL')
			{
				$stmt = 'Exec [dbo].[insert_doc_processing_step] ?, N?';
				$params = array($v_doc_id, $step);
				
				$this->db->Execute($stmt, $params);
			}
			elseif(DATABASE_TYPE == 'MYSQL')
			{
				$this->insert_xml_node('t_edoc_doc', 'C_XML_PROCESSING', $step, 'PK_DOC', $v_doc_id);
			}
			


			//2. Canh bao toi CB thu ly chinh
			if(DATABASE_TYPE == 'MSSQL')
			{
				$stmt = "Insert Into T_EDOC_MESSAGE 
						(C_TO_USER_ID
						,C_TO_USER_NAME
						, C_DATE_CREATED
						, C_FROM_USER_ID
						, C_FROM_USER_NAME
						, C_MESSAGE_CONTENT
						, C_MESSAGE_TITLE
						, FK_STEP_SEQ
						, FK_DOC
						)Values
						(
						$v_direct_exec_user_id
						, N'$v_direct_exec_user_name'
						, getDate()
						, ?
						, N?
						, N?
						, N?
						, ?
						, ?
						)
						";
			}
			elseif(DATABASE_TYPE == 'MYSQL')
			{
				$stmt = "Insert Into t_edoc_message 
						(C_TO_USER_ID
						,C_TO_USER_NAME
						, C_DATE_CREATED
						, C_FROM_USER_ID
						, C_FROM_USER_NAME
						, C_MESSAGE_CONTENT
						, C_MESSAGE_TITLE
						, FK_STEP_SEQ
						, FK_DOC
						)Values
						(
						$v_direct_exec_user_id
						, '$v_direct_exec_user_name'
						, Now()
						, ?
						, ?
						, ?
						, ?
						, ?
						, ?
						)
						";
			} 
			$params = array(
					Session::get('user_id')
					,Session::get('user_name')
					,$v_allot_message
					,'Chuyển tiếp văn bản cho ' . $v_direct_exec_user_name
					,$v_step_seq
					,$v_doc_id
			);
			$this->db->Execute($stmt, $params);
		}

		$this->popup_exec_done();
	}

	public function view_attachment($file_id)
	{
		$file_id = $this->replace_bad_char($file_id);

		$stmt = 'Select 
					PK_DOC_FILE
					, FK_DOC
					, C_FILE_NAME 
				From t_edoc_doc_file 
				Where PK_DOC_FILE=?';
		$arr_single_file = $this->db->getRow($stmt, array($file_id));

		$v_doc_id = $arr_single_file['FK_DOC'];
		$v_file_name = $arr_single_file['C_FILE_NAME'];

		//Action Log
		$xml_action_log = '<action id="' . uniqid() . '">';
		$xml_action_log .= '<datetime>' . $this->getDate() . '</datetime>';
		$xml_action_log .= '<user_code>' . Session::get('user_code') . '</user_code>';
		$xml_action_log .= '<code>view</code>';
		$xml_action_log .= '<code_text>Xem file đính kèm</code_text>';
		$xml_action_log .= '</action>';

		if(DATABASE_TYPE == 'MSSQL')
		{
			$stmt = "Exec sp_insert_xml_node 'T_EDOC_DOC','C_XML_LOG', 'PK_DOC', ?,?";
			$params = array($v_doc_id, $xml_action_log);
			
			$this->db->Execute($stmt, $params);
		}
		elseif(DATABASE_TYPE == 'MYSQL')
		{
			$this->insert_xml_node('t_edoc_doc', 'C_XML_LOG', $xml_action_log, 'PK_DOC', $v_doc_id);
		}
		
		$v_file_path    = CONST_SITE_DOC_FILE_UPLOAD_DIR . $v_file_name;
		header('location:' . $v_file_path);

	}
}