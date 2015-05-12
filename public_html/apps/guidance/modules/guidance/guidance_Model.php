 <?php 
if (!defined('SERVER_ROOT')) exit('No direct script access allowed');

class guidance_Model extends Model {

	function __construct() {
		parent::__construct();
	}
        /**
         * 
         * Lấy tất cả các lĩnh vưc
         */
        public function qry_all_linh_vuc()
        {
            $stml = "SELECT
                            PK_LIST,
                            C_NAME,
                            C_CODE
                          from t_cores_list
                          where FK_LISTTYPE = (select
                                                 PK_LISTTYPE
                                               from t_cores_listtype
                                               where C_CODE = 'DANH_MUC_LINH_VUC'
                                                   and C_STATUS = 1)
                                and C_STATUS =1
                                and C_CODE <> 'PD'
                                order by C_ORDER ASC
                            ";
            return $this->db->getAll($stml);
        }
        /*
         * Lay danh sach cac thu tục theo mã lĩnh vực
         */
        public function qry_all_list_thu_tuc($v_id,$v_keyword = '')
        {
            
               if(isset($_REQUEST['page']) && intval($_REQUEST['page']) > 1)
                {
                    $v_start = (intval($_REQUEST['page']) - 1) * _CONTS_LIMIT_GUIDANCE_LIST ;
                    $v_end   =  _CONTS_LIMIT_GUIDANCE_LIST; 
                }
                else
                {
                    $v_start = 0;
                    $v_end   = _CONTS_LIMIT_GUIDANCE_LIST;
                }
                if($v_id > 0)
                {
                    $sql = "SELECT
                                C_CODE
                              from t_cores_list
                              where C_STATUS = 1
                                  and PK_LIST = $v_id ";

            $v_code =  $this->db->GetOne($sql);
            $sql = '';
            if(trim($v_keyword))
            {
                
                 $sql  = "  and (C_CODE = '$v_keyword'
                                 OR C_NAME like '%$v_keyword%')";
            }
            $stmt = "select
                          (select C_NAME from t_cores_list where C_CODE ='$v_code') as C_NAME_LINH_VUC,
                            PK_RECORD_TYPE,
                            C_CODE,
                            C_NAME,
                            case when C_SCOPE  = 3 then 'Thủ tục cấp huyện'
                                when C_SCOPE   = 1 then 'Thủ tục liên thông xã huyện'
                                when C_SCOPE   = 2 then 'Thủ tục liên thông huyện xã'
                                else 'Thủ tục cấp xã'
                                end as C_SCOPE
                            ,
                            (select
                               count(PK_RECORD_TYPE)
                             from t_r3_record_type rt
                             where C_SPEC_CODE = '$v_code'
                                 and C_STATUS = 1 $sql) as C_TOTAL
                          from t_r3_record_type rt
                          where C_SPEC_CODE = '$v_code'
                              and C_STATUS = 1
                              $sql
                          order by C_ORDER ASC
                          limit $v_start,$v_end
                            ";
            
               if($v_code != NULL)
               {
                
                   return $this->db->GetAll($stmt);
               }
            }
            
        }
        /**
         * Lấy Thông tin  chi tiết thủ tục theo mã thủ tục
         */
        public function qry_single_guidance($v_id)
        {
            $sql = "select
                        C_SPEC_CODE
                      from t_r3_record_type rt
                      where PK_RECORD_TYPE = $v_id
                    ";
            $v_code = $this->db->GetOne($sql);
            
            $stmt = "select
                        (select
                                  PK_LIST
                            from t_cores_list
                            where C_CODE =?)as PK_LIST,
                        (select
                               C_NAME
                           from t_cores_list
                           where C_CODE =?) AS C_NAME_THU_TUC,
                        C_NAME,
                        C_XML_DATA
                    from t_r3_record_type rt
                    where
                        PK_RECORD_TYPE = ?
                        and C_STATUS = 1
                        ";
           return  $this->db->GetRow($stmt,array($v_code,$v_code,$v_id));
        }
        
}
