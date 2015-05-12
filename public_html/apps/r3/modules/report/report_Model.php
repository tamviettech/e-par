<?php

require_once __DIR__ . '/../record/record_Model.php';

class report_Model extends record_Model
{

    /**
     * @var \ADOConnection 
     */
    public $db;

    function __construct()
    {
        parent::__construct();
    }
    /**
     * lay danh sach don vi
     * @return type
     */
    public function qry_all_unit()
    {
        $v_village_id = session::get('village_id');
        $arr_ret = array();
        $village_condition = '';
        
        if($v_village_id == '0')//neu la can bo cap huyen
        {
            //lấy tên đơn vị cấp huyện
            $filedir = SERVER_ROOT . DS . 'public'. DS .'xml'. DS .'xml_unit_info.xml';
            $dom_unit = simplexml_load_file($filedir);
            $v_full_name = get_xml_value($dom_unit, '//full_name');
            $arr_ret['0'] = $v_full_name;

            //Don vi cap xa
            $arr_ret['-1'] = 'Tất cả đơn vị cáp Xã/Phường';
        }
        else//can bo cap xa(tao dieu kien chi hien thi xa mac dinh)
        {
            $village_condition = " AND PK_OU = '$v_village_id'";
        }
        
        //lay tat ca cac xã/phường trực thuôc
        $sql = "SELECT
                    PK_OU,
                    CONCAT('--- ',C_NAME) AS C_NAME
                  FROM t_cores_ou
                  WHERE C_LEVEL = 3 $village_condition";
        $arr_village = $this->db->GetAssoc($sql);
        return $arr_ret + $arr_village;
    }
    /**
     * Lay danh sach tat ca phong ban
     * @param type $use_cache
     */
    public function qry_all_group()
    {
        $sql = "SELECT
                    G.C_NAME,
                    T.C_GROUP_CODE,
                    FK_VILLAGE_ID
                  FROM (SELECT DISTINCT
                          C_GROUP_CODE,
                          FK_VILLAGE_ID
                        FROM t_r3_bizdone_step_log
                        ORDER BY FK_VILLAGE_ID) T
                    LEFT JOIN t_cores_group G
                      ON T.C_GROUP_CODE = G.C_CODE";
        return $this->db->getAll($sql);
    }
    /**
     * lay du lieu bao cao tong hop tinh hinh giai quyet ho so
     * @param type $v_unit
     * @param type $v_report_type
     * @param type $v_report_time
     * @return type
     */
    public function qry_all_report_data_1($v_unit, $v_report_type,$v_report_time,$begin_date = '',$end_date = '')
    {
        //dieu kien don vi
        $unit_condition = '';
        if($v_unit >= 0)//huyen hoac xa
        {
            $unit_condition = " AND FK_VILLAGE_ID = $v_unit";
        }
        else//tat ca cac xa
        {
            $unit_condition = " AND FK_VILLAGE_ID > 0";
        }
        
        //dieu kien thoi gian luon mac dinh nam hien tai
        $tiepnhan_condition  = " AND YEAR(r.C_RECEIVE_DATE) = '" . DATE('Y') ."'";
        $giaiquyet_condition = " AND YEAR(r.C_CLEAR_DATE) = '" . DATE('Y') ."'";
        $tuchoi_condition    = " AND YEAR(r.C_REJECT_DATE) = '" . DATE('Y') ."'";
        if($v_report_time != 'year')
        {
            if($v_report_time == 'month')//quy hien tai
            {
                $tiepnhan_condition  .= " AND MONTH(r.C_RECEIVE_DATE) = '" . DATE('m') ."'";
                $giaiquyet_condition .= " AND MONTH(r.C_CLEAR_DATE) = '" . DATE('m') ."'";
                $tuchoi_condition    .= " AND MONTH(r.C_REJECT_DATE) = '" . DATE('m') ."'";
            }
            elseif($v_report_time == 'quater')//quy hien tai
            {
                $v_cur_quarter = jwDate::quarterOfYear();//lay quy hien tai
                $v_end_month   = 3 * $v_cur_quarter;
                $v_start_month = $v_end_month - 2;
                
                $tiepnhan_condition  .= " AND MONTH(r.C_RECEIVE_DATE) >= '$v_start_month' AND MONTH(r.C_RECEIVE_DATE) <= '$v_end_month'";
                $giaiquyet_condition .= " AND MONTH(r.C_CLEAR_DATE) >= '$v_start_month' AND MONTH(r.C_CLEAR_DATE) <= '$v_end_month'";
                $tuchoi_condition    .= " AND MONTH(r.C_REJECT_DATE) >= '$v_start_month' AND MONTH(r.C_REJECT_DATE) <= '$v_end_month'";
            }
            elseif($v_report_time == 'time')
            {
                $tiepnhan_condition  = "";
                $giaiquyet_condition = "";
                $tuchoi_condition    = "";

                if($begin_date != '')
                {
                    $begin_date = change_format_date('d-m-Y', 'Y-m-d', $begin_date);

                    $tiepnhan_condition  .= " AND DATEDIFF(r.C_RECEIVE_DATE,'$begin_date') >= 0";
                    $giaiquyet_condition .= " AND DATEDIFF(r.C_CLEAR_DATE,'$begin_date') >= 0";
                    $tuchoi_condition    .= " AND DATEDIFF(r.C_REJECT_DATE,'$begin_date') >= 0";
                }

                if($end_date != '')
                {
                    $end_date = change_format_date('d-m-Y', 'Y-m-d', $end_date);

                    $tiepnhan_condition  .= " AND DATEDIFF(r.C_RECEIVE_DATE,'$end_date') <= 0";
                    $giaiquyet_condition .= " AND DATEDIFF(r.C_CLEAR_DATE,'$end_date') <= 0";
                    $tuchoi_condition    .= " AND DATEDIFF(r.C_REJECT_DATE,'$end_date') <= 0";
                }
            }
        }
        
        //dieu kien group
        $rt_code_column = "";
        $sql_add_column = "";
        if($v_report_type == '0')
        {
            //sql thong tin mac dinh ben trai
            $sql_left = "SELECT
                          C_CODE,
                          C_NAME
                        FROM t_cores_list
                        WHERE FK_LISTTYPE = (SELECT
                                               PK_LISTTYPE
                                             FROM t_cores_listtype
                                             WHERE C_CODE = '"._CONST_DANH_MUC_LINH_VUC."')";
            //cot join
            $column_join     = "C_SPEC_CODE";
            $group_condition = "rt.C_SPEC_CODE";
            
        }
        elseif($v_report_type == 'C_SPEC_CODE')
        {
            //sql thong tin mac dinh ben trai
            $sql_left = "SELECT DISTINCT
                            rt.C_CODE,
                            CONCAT(rt.C_CODE,' - ',rt.C_NAME) AS C_NAME,
                            rt.C_SPEC_CODE,
                            l.C_NAME as C_SPEC_NAME
                          FROM (SELECT
									b.*
								  FROM (SELECT DISTINCT
										  FK_RECORD_TYPE
										FROM t_r3_record
										WHERE (1>0) $unit_condition) a
									LEFT JOIN t_r3_record_type b
									  ON a.FK_RECORD_TYPE = b.PK_RECORD_TYPE
								  WHERE b.C_STATUS = 1) rt LEFT JOIN t_cores_list l
                          ON rt.C_SPEC_CODE = l.C_CODE
                          ORDER BY rt.C_CODE";
            
            $rt_code_column  = ' ,rt.C_CODE AS C_RECORD_TYPE_CODE ';
            $group_condition = "rt.C_CODE";
            $column_join     = "C_RECORD_TYPE_CODE";
            $sql_add_column  = " L.C_SPEC_CODE, L.C_SPEC_NAME, ";
        }
        
        $sql_base = "SELECT
                            rt.C_SPEC_CODE,
                            COUNT(PK_RECORD) AS C_COUNT
                            $rt_code_column
                          FROM t_r3_record r
                            LEFT JOIN t_r3_record_type rt
                              ON r.FK_RECORD_TYPE = rt.PK_RECORD_TYPE
                          WHERE (1>0) $unit_condition AND r.C_DELETED <> 1";
        $sql_group = " GROUP BY $group_condition";
        //tiep nhan
        $sql_tiep_nhan = $sql_base . $tiepnhan_condition . $sql_group;
                          
        //thu ly chua den han
        $sql_thu_ly_chua_den_han = $sql_base . " AND ISNULL(`r`.`C_CLEAR_DATE`) AND NOW() <= r.C_RETURN_DATE" . $sql_group;
        
        //thu ly qua han
        $sql_thu_ly_qua_han = $sql_base . " AND ISNULL(`r`.`C_CLEAR_DATE`) AND NOW() > r.C_RETURN_DATE" . $sql_group;
                                      
        //da giai quyet som han
        $sql_giai_quyet_som_han = $sql_base . " AND r.C_CLEAR_DATE IS NOT NULL 
                                                AND r.C_BIZ_DAYS_EXCEED > 0" . $giaiquyet_condition . $sql_group;

        
        //da giai quyet dung han
        $sql_giai_quyet_dung_han = $sql_base . " AND r.C_CLEAR_DATE IS NOT NULL 
                                                 AND r.C_BIZ_DAYS_EXCEED = 0" . $giaiquyet_condition . $sql_group;
                                          
        //da giai quyet qua han
        $sql_giai_quyet_qua_han = $sql_base . " AND r.C_CLEAR_DATE IS NOT NULL 
                                                AND r.C_BIZ_DAYS_EXCEED < 0" . $giaiquyet_condition . $sql_group;
                                      
        //da giai quyet cho tra
        $sql_cho_tra = $sql_base . " AND r.C_CLEAR_DATE IS NULL" . $giaiquyet_condition . $sql_group;
                              
        //tam dung bo sung (bo sung khong can tinh thoi gian)
       $sql_bo_sung = $sql_base . " AND ISNULL(`r`.`C_CLEAR_DATE`) 
                                    AND ((`r`.`C_PAUSE_DATE` IS NOT NULL) 
                                    AND ISNULL(`r`.`C_UNPAUSE_DATE`)) 
                                    AND C_NEXT_TASK_CODE LIKE '%::BO_SUNG'" . $sql_group;
       
        //tam dung nvtc (ko can tinh thoi gian)
        $sql_nvtc = $sql_base . " AND ISNULL(`r`.`C_CLEAR_DATE`)
                                    AND ((`r`.`C_PAUSE_DATE` IS NOT NULL) AND ISNULL(`r`.`C_UNPAUSE_DATE`))
                                    AND C_NEXT_TASK_CODE NOT LIKE '%::BO_SUNG'" . $sql_group;
                                    
        //tu choi ho so
        $sql_tu_choi = $sql_base . " AND ISNULL(`r`.`C_CLEAR_DATE`)
                                        AND C_REJECTED = 1" . $tuchoi_condition . $sql_group;
        //ho so cong dan rut
        $sql_cong_dan_rut = $sql_base . " AND ISNULL(`r`.`C_CLEAR_DATE`)
                                            AND C_REJECTED = 2" . $tuchoi_condition . $sql_group; 
        //tao sql
        $sql = "SELECT
                    L.C_CODE,
                    L.C_NAME,
                    $sql_add_column
                    COALESCE(TN.C_COUNT,0) AS C_COUNT_TIEP_NHAN,
                    COALESCE(TLCDH.C_COUNT,0) AS C_COUNT_THU_LY_CHUA_DEN_HAN,
                    COALESCE(TLQH.C_COUNT,0) AS C_COUNT_THU_LY_QUA_HAN,
                    COALESCE(GQSH.C_COUNT,0) AS C_COUNT_TRA_SOM_HAN,
                    COALESCE(GQDH.C_COUNT,0) AS C_COUNT_TRA_DUNG_HAN,
                    COALESCE(GQQH.C_COUNT,0) AS C_COUNT_TRA_QUA_HAN,
                    COALESCE(CT.C_COUNT,0) AS C_COUNT_CHO_TRA,
                    COALESCE(BS.C_COUNT,0) AS C_COUNT_BO_SUNG,
                    COALESCE(NVTC.C_COUNT,0) AS C_COUNT_NVTC,
                    COALESCE(TC.C_COUNT,0) AS C_COUNT_TU_CHOI,
                    COALESCE(CDR.C_COUNT,0) AS C_COUNT_CONG_DAN_RUT
                  FROM ($sql_left) L
                LEFT JOIN ($sql_tiep_nhan) TN ON L.C_CODE = TN.$column_join 
                LEFT JOIN ($sql_thu_ly_chua_den_han) TLCDH ON L.C_CODE = TLCDH.$column_join
                LEFT JOIN ($sql_thu_ly_qua_han) TLQH ON L.C_CODE = TLQH.$column_join
                LEFT JOIN ($sql_giai_quyet_som_han) GQSH ON L.C_CODE = GQSH.$column_join
                LEFT JOIN ($sql_giai_quyet_dung_han) GQDH ON L.C_CODE = GQDH.$column_join
                LEFT JOIN ($sql_giai_quyet_qua_han) GQQH ON L.C_CODE = GQQH.$column_join
                LEFT JOIN ($sql_cho_tra) CT ON L.C_CODE = CT.$column_join
                LEFT JOIN ($sql_bo_sung) BS ON L.C_CODE = BS.$column_join
                LEFT JOIN ($sql_nvtc) NVTC ON L.C_CODE = NVTC.$column_join
                LEFT JOIN ($sql_tu_choi) TC ON L.C_CODE = TC.$column_join
                LEFT JOIN ($sql_cong_dan_rut) CDR ON L.C_CODE = CDR.$column_join
                ";
        return $this->db->getAll($sql);
    }
    /**
     * lay du lieu bao cao tong hop tiep nhan ho so
     * @param type $v_unit
     * @param type $v_spec
     * @param type $v_begin_date
     * @param type $v_end_date
     * @return type
     */
    public function qry_all_report_data_2($v_unit, $v_spec,$v_begin_date,$v_end_date)
    {
        //dieu kien don vi
        $unit_condition = '';
        if($v_unit >= 0)//huyen hoac xa
        {
            $unit_condition = " AND FK_VILLAGE_ID = $v_unit";
        }
        else//tat ca cac xa
        {
            $unit_condition = " AND FK_VILLAGE_ID > 0";
        }
        //dieu kien linh vuc
        $spec_condition = '';
        if($v_spec != '')
        {
            $spec_condition = " AND RT.C_SPEC_CODE = '$v_spec'";
        }
        
        //dieu kien thoi gian
        $date_condition = '';
        if($v_begin_date !='')
        {
           $date_condition .= " AND DATEDIFF(C_RECEIVE_DATE,'$v_begin_date') >= 0";
        }
        
        if($v_end_date != '')
        {
           $date_condition .= " AND DATEDIFF(C_RECEIVE_DATE,'$v_end_date') <= 0"; 
        }
        
        $sql = "SELECT
                    RT.C_CODE,
                    CONCAT(RT.C_CODE,' - ',RT.C_NAME) AS C_NAME,
                    COALESCE(R.C_COUNT,0) AS  C_COUNT,
                    RT.C_SPEC_CODE,
                    L.C_NAME AS C_SPEC_NAME
                  FROM (SELECT
							rt.*
						  FROM (SELECT DISTINCT
								  FK_RECORD_TYPE
								FROM t_r3_record
								WHERE (1>0) $unit_condition) l
							LEFT JOIN t_r3_record_type rt
							  ON l.FK_RECORD_TYPE = rt.PK_RECORD_TYPE
						  WHERE rt.C_STATUS = 1) RT
                    LEFT JOIN (SELECT
                                 FK_RECORD_TYPE,
                                 COUNT(PK_RECORD) AS C_COUNT
                               FROM t_r3_record
                               WHERE C_DELETED <> 1 $unit_condition $date_condition
                               GROUP BY FK_RECORD_TYPE) R
                      ON RT.PK_RECORD_TYPE = R.FK_RECORD_TYPE
                      LEFT JOIN t_cores_list L
                      ON RT.C_SPEC_CODE = L.C_CODE
                    Where (1>0) $spec_condition AND COALESCE(R.C_COUNT,0) > 0
                  ORDER BY RT.C_CODE";
        return $this->db->getAll($sql);

    }
    /**
     * bao cao tong hop thu ly ho so
     * @param type $v_unit
     * @param type $v_group
     * @param type $v_begin_date
     * @param type $v_end_date
     * @param type $v_month
     * @param type $v_year
     * @param type $v_quarter
     * @return type
     */
    public function qry_all_report_data_3($v_unit,$v_group,$v_begin_date,$v_end_date,$v_month,$v_year,$v_quarter)
    {
        //lay unit full name
        $dom_unit_info    = simplexml_load_file('public/xml/xml_unit_info.xml');
        $v_unit_full_name = xpath($dom_unit_info, '//full_name', XPATH_STRING);

        //dieu kien don vi
        $unit_condition = '';
        if($v_unit >= 0)//huyen hoac xa
        {
            $unit_condition = " AND FK_VILLAGE_ID = $v_unit";
        }
        else//tat ca cac xa
        {
            $unit_condition = " AND FK_VILLAGE_ID > 0";
        }
        //dieu kien phong ban
        $group_condition = '';
        if($v_group != '')
        {
            $group_condition = " AND C_GROUP_CODE = '$v_group'";
        }
        
        //dieu kien chung
        $condition = '';
        if($v_begin_date != '')//ngay bat dau
        {
            $condition .= " AND DATEDIFF(C_RECEIVE_DATE,'$v_begin_date') >= 0";
        }
        
        if($v_end_date != '')//ngay ket thuc
        {
            $condition .= " AND DATEDIFF(C_RECEIVE_DATE,'$v_end_date') <= 0";
        }
        
        if($v_month != '')//thang
        {
            $condition .= " AND MONTH(C_RECEIVE_DATE) = '$v_month'";
        }
        
        if($v_year != '')//nam
        {
            $condition .= " AND YEAR(C_RECEIVE_DATE) = '$v_year'";
        }
        
        if($v_quarter != '')//quy
        {
            $v_end_month   = 3 * $v_quarter;
            $v_start_month = $v_end_month - 2;
            $condition .= " AND MONTH(C_RECEIVE_DATE) >= '$v_start_month' AND MONTH(C_RECEIVE_DATE) <= '$v_end_month'";
        }
        
        $sql_base = "SELECT
                     C_GROUP_CODE,
                     COUNT(PK_BIZDONE_STEP) AS C_COUNT
                   FROM (SELECT
                            PK_BIZDONE_STEP,
                            C_GROUP_CODE,
                            FK_RECORD,
                            SUM(C_DAYS_EXCEED) AS C_DAYS_EXCEED
                          FROM t_r3_bizdone_step_log
                          GROUP BY C_GROUP_CODE, FK_RECORD) bt
                   LEFT JOIN t_r3_record r
                   ON bt.FK_RECORD = r.PK_RECORD
                   WHERE (1>0) AND r.C_DELETED <> 1 $group_condition ";
        $sql_group = " GROUP BY C_GROUP_CODE";
        //vuot tien do
        $sql_vuot_tien_do = $sql_base . " AND C_DAYS_EXCEED > 0" . $condition . $sql_group;
        //vuot dungs tien do
        $sql_dung_tien_do = $sql_base . " AND C_DAYS_EXCEED = 0" . $condition . $sql_group;
        //cham tien do
        $sql_cham_tien_do = $sql_base . " AND C_DAYS_EXCEED < 0" . $condition . $sql_group;

        $sql = "SELECT
                     L.C_GROUP_CODE,
                     CONCAT('--- ', L.C_NAME) AS C_NAME,
                     L.FK_VILLAGE_ID,
                     CASE 
                         WHEN L.FK_VILLAGE_ID = 0 THEN '$v_unit_full_name'
                         WHEN L.FK_VILLAGE_ID > 0 THEN (SELECT C_NAME FROM t_cores_ou WHERE PK_OU = L.FK_VILLAGE_ID)
                     END AS C_OU_NAME,
                     COALESCE(S.C_COUNT,0) AS C_COUNT_VUOT_TIEN_DO,
                     COALESCE(D.C_COUNT,0) AS C_COUNT_DUNG_TIEN_DO,
                     COALESCE(Q.C_COUNT,0) AS C_COUNT_CHAM_TIEN_DO
                   FROM (SELECT
                           l.C_GROUP_CODE,
                           g.C_NAME,
                           l.FK_VILLAGE_ID
                         FROM (SELECT DISTINCT
                                 C_GROUP_CODE,
                                 FK_VILLAGE_ID
                               FROM t_r3_bizdone_step_log
                               WHERE (1>0) $unit_condition $group_condition) l
                           LEFT JOIN t_cores_group g
                             ON l.C_GROUP_CODE = g.C_CODE) L
                     LEFT JOIN ($sql_vuot_tien_do) S ON L.C_GROUP_CODE = S.C_GROUP_CODE 
                     LEFT JOIN ($sql_dung_tien_do) D ON L.C_GROUP_CODE = D.C_GROUP_CODE
                     LEFT JOIN ($sql_cham_tien_do) Q ON L.C_GROUP_CODE = Q.C_GROUP_CODE
                     ";
        return $this->db->getAll($sql);
    }
    /**
     * lay du lieu bao cao tong hop tra ket qua ho so
     * @param type $v_unit
     * @param type $v_spec
     * @param type $v_begin_date
     * @param type $v_end_date
     * @return type
     */
    public function qry_all_report_data_4($v_unit, $v_spec,$v_begin_date,$v_end_date)
    {
        //dieu kien don vi
        $unit_condition = '';
        if($v_unit >= 0)//huyen hoac xa
        {
            $unit_condition = " AND FK_VILLAGE_ID = $v_unit";
        }
        else//tat ca cac xa
        {
            $unit_condition = " AND FK_VILLAGE_ID > 0";
        }
        //dieu kien linh vuc
        $spec_condition = '';
        if($v_spec != '')
        {
            $spec_condition = " AND RT.C_SPEC_CODE = '$v_spec'";
        }
        
        //dieu kien thoi gian
        $return_date_condition = '';
        $reject_date_condition = '';
        if($v_begin_date !='')
        {
           $return_date_condition .= " AND DATEDIFF(C_CLEAR_DATE,'$v_begin_date') >= 0";
           $reject_date_condition .= " AND DATEDIFF(C_REJECT_DATE,'$v_begin_date') >= 0";
        }
        
        if($v_end_date != '')
        {
           $return_date_condition .= " AND DATEDIFF(C_CLEAR_DATE,'$v_end_date') <= 0"; 
           $reject_date_condition .= " AND DATEDIFF(C_REJECT_DATE,'$v_end_date') <= 0"; 
        }
        
        $sql = "SELECT
                    RT.C_CODE,
                    CONCAT(RT.C_CODE,' - ',RT.C_NAME) AS C_NAME,
                    COALESCE(R.C_COUNT,0) AS C_COUNT_RETURN,
                    COALESCE(RJ.C_COUNT,0) AS C_COUNT_REJECT,
                    RT.C_SPEC_CODE,
                    L.C_NAME AS C_SPEC_NAME
                  FROM (SELECT
							rt.*
						  FROM (SELECT DISTINCT
								  FK_RECORD_TYPE
								FROM t_r3_record
								WHERE (1>0) $unit_condition) l
							LEFT JOIN t_r3_record_type rt
							  ON l.FK_RECORD_TYPE = rt.PK_RECORD_TYPE
						  WHERE rt.C_STATUS = 1) RT
                    LEFT JOIN (SELECT
                                 FK_RECORD_TYPE,
                                 COUNT(PK_RECORD) AS C_COUNT,
                                 C_DELETED
                               FROM t_r3_record
                               WHERE C_DELETED <> 1 
                                        AND C_CLEAR_DATE IS NOT NULL
                                        AND C_REJECTED <> 1
                                        $unit_condition $return_date_condition
                               GROUP BY FK_RECORD_TYPE) R
                      ON RT.PK_RECORD_TYPE = R.FK_RECORD_TYPE
                      LEFT JOIN (SELECT
                                 FK_RECORD_TYPE,
                                 COUNT(PK_RECORD) AS C_COUNT,
                                 C_DELETED
                               FROM t_r3_record
                               WHERE C_DELETED <> 1
                                        AND C_REJECT_DATE IS NOT NULL
                                        AND C_REJECTED = 1
                                        $unit_condition $reject_date_condition
                               GROUP BY FK_RECORD_TYPE) RJ
                      ON RT.PK_RECORD_TYPE = RJ.FK_RECORD_TYPE
                      LEFT JOIN t_cores_list L
                      ON RT.C_SPEC_CODE = L.C_CODE
                    Where (1>0) $spec_condition AND (R.C_COUNT > 0 OR RJ.C_COUNT > 0)
                  ORDER BY RT.C_CODE";
        return $this->db->getAll($sql);

    }
    /**
     * lay du lieu bao cao chi tiet tiep nhan ho so
     * @param type $v_unit
     * @param type $v_spec
     * @param type $v_begin_date
     * @param type $v_end_date
     * @return type
     */
    public function qry_all_report_data_5($v_unit, $v_spec,$v_begin_date,$v_end_date)
    {
        //dieu kien don vi
        $unit_condition = '';
        if($v_unit >= 0)//huyen hoac xa
        {
            $unit_condition = " AND r.FK_VILLAGE_ID = $v_unit";
        }
        else//tat ca cac xa
        {
            $unit_condition = " AND r.FK_VILLAGE_ID > 0";
        }
        //dieu kien linh vuc
        $spec_condition = '';
        if($v_spec != '')
        {
            $spec_condition = " AND rt.C_SPEC_CODE = '$v_spec'";
        }
        
        //dieu kien thoi gian
        $date_condition = '';
        if($v_begin_date !='')
        {
           $date_condition .= " AND DATEDIFF(r.C_RECEIVE_DATE,'$v_begin_date') >= 0";
        }
        
        if($v_end_date != '')
        {
           $date_condition .= " AND DATEDIFF(r.C_RECEIVE_DATE,'$v_end_date') <= 0"; 
        }
        
        $sql = "SELECT
                    L.C_SPEC_CODE,
                    LIST.C_NAME   AS C_SPEC_NAME,
                    L.C_NAME      AS C_RECORD_TYPE_NAME,
                    R.*
                  FROM (SELECT
                          r.PK_RECORD,
                          r.FK_RECORD_TYPE,
                          rt.C_SPEC_CODE,
                          CONCAT('--- ',rt.C_CODE,' - ',rt.C_NAME) AS C_NAME
                        FROM t_r3_record r
                          LEFT JOIN t_r3_record_type rt
                            ON r.FK_RECORD_TYPE = rt.PK_RECORD_TYPE
                        WHERE C_DELETED <> 1 $unit_condition $spec_condition $date_condition
                        ORDER BY rt.C_SPEC_CODE, r.FK_RECORD_TYPE) L
                    LEFT JOIN t_r3_record R
                      ON L.PK_RECORD = R.PK_RECORD
                    LEFT JOIN t_cores_list LIST
                      ON L.C_SPEC_CODE = LIST.C_CODE
                WHERE R.C_DELETED <> 1 AND R.C_DELETED IS NOT NULL";
        return $this->db->getAll($sql);

    }
    /**
     * bao cao tong hop thu ly ho so
     * @param type $v_unit
     * @param type $v_group
     * @param type $v_begin_date
     * @param type $v_end_date
     * @param type $v_month
     * @param type $v_year
     * @param type $v_quarter
     * @return type
     */
    public function qry_all_report_data_6($v_unit,$v_group,$v_begin_date,$v_end_date,$v_month,$v_year,$v_quarter)
    {
        //lay unit full name
        $dom_unit_info    = simplexml_load_file('public/xml/xml_unit_info.xml');
        $v_unit_full_name = xpath($dom_unit_info, '//full_name', XPATH_STRING);

        //dieu kien don vi
        $unit_condition = '';
        if($v_unit >= 0)//huyen hoac xa
        {
            $unit_condition = " AND bt.FK_VILLAGE_ID = $v_unit";
        }
        else//tat ca cac xa
        {
            $unit_condition = " AND bt.FK_VILLAGE_ID > 0";
        }
        //dieu kien phong ban
        $group_condition = '';
        if($v_group != '')
        {
            $group_condition = " AND bt.C_GROUP_CODE = '$v_group'";
        }
        
        //dieu kien chung
        $condition = '';
        if($v_begin_date != '')//ngay bat dau
        {
            $condition .= " AND DATEDIFF(bt.C_FINISH_DATE,'$v_begin_date') >= 0";
        }
        
        if($v_end_date != '')//ngay ket thuc
        {
            $condition .= " AND DATEDIFF(bt.C_FINISH_DATE,'$v_end_date') <= 0";
        }
        
        if($v_month != '')//thang
        {
            $condition .= " AND MONTH(bt.C_FINISH_DATE) = '$v_month'";
        }
        
        if($v_year != '')//nam
        {
            $condition .= " AND YEAR(bt.C_FINISH_DATE) = '$v_year'";
        }
        
        if($v_quarter != '')//quy
        {
            $v_end_month   = 3 * $v_quarter;
            $v_start_month = $v_end_month - 2;
            $condition .= " AND MONTH(bt.C_FINISH_DATE) >= '$v_start_month' AND MONTH(bt.C_FINISH_DATE) <= '$v_end_month'";
        }

        $sql = "SELECT L.*,
                        R.*,
                        CASE
                            WHEN L.C_CUR_VILLAGE_ID = 0 THEN '$v_unit_full_name'
                            ELSE OU.C_NAME
                        END AS C_OU_NAME,
                        L.C_TASK_CODE,
                        (SELECT
                            C_CODE
                          FROM t_r3_record_type
                          WHERE PK_RECORD_TYPE = R.FK_RECORD_TYPE) AS C_RECORD_TYPE_CODE,
                        CASE WHEN L.C_DAYS_EXCEED > 0 THEN L.C_DAYS_EXCEED ELSE '-' END AS C_VUOT_TIEN_DO,
                        CASE WHEN L.C_DAYS_EXCEED = 0 THEN 'x' ELSE '-' END AS C_DUNG_TIEN_DO,
                        CASE WHEN L.C_DAYS_EXCEED < 0 THEN (-1*L.C_DAYS_EXCEED) ELSE '-' END AS C_CHAM_TIEN_DO
                 FROM
                   (SELECT C_GROUP_CODE,
                           FK_RECORD,
                           C_DAYS_EXCEED,
                           C_TASK_CODE,
                           FK_VILLAGE_ID AS C_CUR_VILLAGE_ID,
                           CONCAT('--- ',G.C_NAME) AS C_GROUP_NAME
                    FROM t_r3_bizdone_step_log bt
                    LEFT JOIN t_cores_group G ON bt.C_GROUP_CODE = G.C_CODE
                    WHERE (1>0) $condition $group_condition $unit_condition
                    ORDER BY FK_VILLAGE_ID,
                             C_GROUP_CODE) L
                 LEFT JOIN t_r3_record R ON L.FK_RECORD = R.PK_RECORD
                 LEFT JOIN t_cores_ou OU ON L.C_CUR_VILLAGE_ID = OU.PK_OU
                WHERE R.C_DELETED <> 1 AND R.C_DELETED IS NOT NULL";
        return $this->db->getAll($sql);
    }
    /**
     * lay du lieu bao cao chi tiet tra ket qua
     * @param type $v_unit
     * @param type $v_spec
     * @param type $v_begin_date
     * @param type $v_end_date
     * @return type
     */
    public function qry_all_report_data_7($v_unit, $v_spec,$v_begin_date,$v_end_date)
    {
        //dieu kien don vi
        $unit_condition = '';
        if($v_unit >= 0)//huyen hoac xa
        {
            $unit_condition = " AND r.FK_VILLAGE_ID = $v_unit";
        }
        else//tat ca cac xa
        {
            $unit_condition = " AND r.FK_VILLAGE_ID > 0";
        }
        //dieu kien linh vuc
        $spec_condition = '';
        if($v_spec != '')
        {
            $spec_condition = " AND rt.C_SPEC_CODE = '$v_spec'";
        }
        
        //dieu kien thoi gian
        $date_condition = '';
        if($v_begin_date !='')
        {
           $date_condition .= " AND DATEDIFF(r.C_CLEAR_DATE,'$v_begin_date') >= 0";
        }
        
        if($v_end_date != '')
        {
           $date_condition .= " AND DATEDIFF(r.C_CLEAR_DATE,'$v_end_date') <= 0"; 
        }
        
        $sql = "SELECT
                    L.C_SPEC_CODE,
                    LIST.C_NAME   AS C_SPEC_NAME,
                    L.C_NAME      AS C_RECORD_TYPE_NAME,
                    R.*,
                    CASE WHEN C_BIZ_DAYS_EXCEED > 0 THEN C_BIZ_DAYS_EXCEED ELSE '-' END AS C_TRA_SOM_HAN,
                    CASE WHEN C_BIZ_DAYS_EXCEED = 0 THEN C_BIZ_DAYS_EXCEED ELSE '-' END AS C_TRA_DUNG_HAN,
                    CASE WHEN C_BIZ_DAYS_EXCEED < 0 THEN (-1*C_BIZ_DAYS_EXCEED) ELSE '-' END AS C_TRA_QUA_HAN
                  FROM (SELECT
                          r.PK_RECORD,
                          r.FK_RECORD_TYPE,
                          rt.C_SPEC_CODE,
                          CONCAT('--- ',rt.C_CODE,' - ',rt.C_NAME) AS C_NAME
                        FROM t_r3_record r
                          LEFT JOIN t_r3_record_type rt
                            ON r.FK_RECORD_TYPE = rt.PK_RECORD_TYPE
                        WHERE C_DELETED <> 1 AND C_CLEAR_DATE IS NOT NULL  
                        $unit_condition $spec_condition $date_condition
                        ORDER BY rt.C_SPEC_CODE, r.FK_RECORD_TYPE) L
                    LEFT JOIN t_r3_record R
                      ON L.PK_RECORD = R.PK_RECORD
                    LEFT JOIN t_cores_list LIST
                      ON L.C_SPEC_CODE = LIST.C_CODE WHERE R.C_DELETED <> 1 AND R.C_DELETED IS NOT NULL";
        return $this->db->getAll($sql);

    }
    /**
     * bao cao chi tiet ho so xu ly cham tien do
     * @param type $v_unit
     * @param type $v_group
     * @param type $v_begin_date
     * @param type $v_end_date
     * @param type $v_month
     * @param type $v_year
     * @param type $v_quarter
     * @return type
     */
    public function qry_all_report_data_8($v_unit,$v_group,$v_begin_date,$v_end_date,$v_month,$v_year,$v_quarter)
    {
        //lay unit full name
        $dom_unit_info    = simplexml_load_file('public/xml/xml_unit_info.xml');
        $v_unit_full_name = xpath($dom_unit_info, '//full_name', XPATH_STRING);

        //dieu kien don vi
        $unit_condition = '';
        if($v_unit >= 0)//huyen hoac xa
        {
            $unit_condition = " AND bt.FK_VILLAGE_ID = $v_unit";
        }
        else//tat ca cac xa
        {
            $unit_condition = " AND bt.FK_VILLAGE_ID > 0";
        }
        //dieu kien phong ban
        $group_condition = '';
        if($v_group != '')
        {
            $group_condition = " AND bt.C_GROUP_CODE = '$v_group'";
        }
        
        //dieu kien chung
        $condition = '';
        if($v_begin_date != '')//ngay bat dau
        {
            $condition .= " AND DATEDIFF(bt.C_FINISH_DATE,'$v_begin_date') >= 0";
        }
        
        if($v_end_date != '')//ngay ket thuc
        {
            $condition .= " AND DATEDIFF(bt.C_FINISH_DATE,'$v_end_date') <= 0";
        }
        
        if($v_month != '')//thang
        {
            $condition .= " AND MONTH(bt.C_FINISH_DATE) = '$v_month'";
        }
        
        if($v_year != '')//nam
        {
            $condition .= " AND YEAR(bt.C_FINISH_DATE) = '$v_year'";
        }
        
        if($v_quarter != '')//quy
        {
            $v_end_month   = 3 * $v_quarter;
            $v_start_month = $v_end_month - 2;
            $condition .= " AND MONTH(bt.C_FINISH_DATE) >= '$v_start_month' AND MONTH(bt.C_FINISH_DATE) <= '$v_end_month'";
        }

        $sql = "SELECT L.*,
                        R.*,
                        CASE
                            WHEN L.C_CUR_VILLAGE_ID = 0 THEN '$v_unit_full_name'
                            ELSE OU.C_NAME
                        END AS C_OU_NAME,
                        (-1*L.C_DAYS_EXCEED) AS C_CHAM_TIEN_DO
                 FROM
                   (SELECT C_GROUP_CODE,
                           FK_RECORD,
                           C_DAYS_EXCEED,
                           FK_VILLAGE_ID AS C_CUR_VILLAGE_ID,
                           CONCAT('--- ',G.C_NAME) AS C_GROUP_NAME
                    FROM t_r3_bizdone_step_log bt
                    LEFT JOIN t_cores_group G ON bt.C_GROUP_CODE = G.C_CODE
                    WHERE bt.C_DAYS_EXCEED < 0
                    $condition $group_condition $unit_condition
                    ORDER BY FK_VILLAGE_ID,
                             C_GROUP_CODE) L
                 LEFT JOIN t_r3_record R ON L.FK_RECORD = R.PK_RECORD
                 LEFT JOIN t_cores_ou OU ON L.C_CUR_VILLAGE_ID = OU.PK_OU
                WHERE R.C_DELETED <> 1 AND R.C_DELETED IS NOT NULL";
        return $this->db->getAll($sql);
    }
    /**
     * lay du lieu bao cao chi tiet hs qua han tra ket qua
     * @param type $v_unit
     * @param type $v_spec
     * @param type $v_begin_date
     * @param type $v_end_date
     * @return type
     */
    public function qry_all_report_data_9($v_unit, $v_spec,$v_begin_date,$v_end_date)
    {
        //dieu kien don vi
        $unit_condition = '';
        if($v_unit >= 0)//huyen hoac xa
        {
            $unit_condition = " AND r.FK_VILLAGE_ID = $v_unit";
        }
        else//tat ca cac xa
        {
            $unit_condition = " AND r.FK_VILLAGE_ID > 0";
        }
        //dieu kien linh vuc
        $spec_condition = '';
        if($v_spec != '')
        {
            $spec_condition = " AND rt.C_SPEC_CODE = '$v_spec'";
        }
        
        //dieu kien thoi gian
        $date_condition = '';
        if($v_begin_date !='')
        {
           $date_condition .= " AND DATEDIFF(r.C_RECEIVE_DATE,'$v_begin_date') >= 0";
        }
        
        if($v_end_date != '')
        {
           $date_condition .= " AND DATEDIFF(r.C_RECEIVE_DATE,'$v_end_date') <= 0"; 
        }
        
        $sql = "SELECT
                    L.C_SPEC_CODE,
                    LIST.C_NAME   AS C_SPEC_NAME,
                    L.C_NAME      AS C_RECORD_TYPE_NAME,
                    R.*,
                    L.C_COUNT_QUA_HAN
                  FROM (SELECT
                          r.PK_RECORD,
                          r.FK_RECORD_TYPE,
                          r.C_COUNT_QUA_HAN,
                          rt.C_SPEC_CODE,
                          CONCAT('--- ',rt.C_CODE,' - ',rt.C_NAME) AS C_NAME
                        FROM ((SELECT PK_RECORD,
                                        FK_RECORD_TYPE,
                                        FK_VILLAGE_ID,
                                        C_RECEIVE_DATE,
                                        (-1*C_BIZ_DAYS_EXCEED) AS C_COUNT_QUA_HAN
                                 FROM t_r3_record
                                 WHERE C_DELETED <> 1
                                   AND C_BIZ_DAYS_EXCEED < 0)
                                UNION
                                (SELECT PK_RECORD,
                                      FK_RECORD_TYPE,
                                      FK_VILLAGE_ID,
                                      C_RECEIVE_DATE,
                                     (SELECT COUNT(*)
                                      FROM t_cores_calendar
                                      WHERE C_OFF=0
                                       AND DATEDIFF(C_DATE, NOW())<=0
                                       AND DATEDIFF(C_DATE, C_RETURN_DATE)>0) AS C_COUNT_QUA_HAN
                                   FROM t_r3_record
                                   WHERE C_DELETED <> 1
                                     AND C_CLEAR_DATE IS NULL
                                     AND C_BIZ_DONE_DATE IS NULL
                                     AND C_BIZ_DAYS_EXCEED IS NULL
                                     AND NOW() > C_RETURN_DATE)) r
                          LEFT JOIN t_r3_record_type rt
                            ON r.FK_RECORD_TYPE = rt.PK_RECORD_TYPE
                        WHERE (1>0) 
                        $unit_condition $spec_condition $date_condition
                        ORDER BY rt.C_SPEC_CODE, r.FK_RECORD_TYPE) L
                    LEFT JOIN t_r3_record R
                      ON L.PK_RECORD = R.PK_RECORD
                    LEFT JOIN t_cores_list LIST
                      ON L.C_SPEC_CODE = LIST.C_CODE WHERE R.C_DELETED <> 1 AND R.C_DELETED IS NOT NULL";
        return $this->db->getAll($sql);
    }
    /**
     * Báo cáo tổng hợp phí lệ phí
     * @param type $v_unit
     * @param type $v_spec
     * @param type $v_begin_date_yyyymmdd
     * @param type $v_end_date_yyyymmdd
     * @return type
     */
    public function qry_all_report_data_10($v_unit, $v_spec,$v_begin_date_yyyymmdd, $v_end_date_yyyymmdd)
    {
        //dieu kien don vi
        $unit_condition = '';
        if($v_unit >= 0)//huyen hoac xa
        {
            $unit_condition = " AND R.FK_VILLAGE_ID = $v_unit";
        }
        else//tat ca cac xa
        {
            $unit_condition = " AND R.FK_VILLAGE_ID > 0";
        }
        //dieu kien linh vuc
        $spec_condition = '';
        if($v_spec != '')
        {
            $spec_condition = " AND RT.C_SPEC_CODE = '$v_spec'";
        }
        
        $date_condition = '';
        //ngay thang
        if($v_begin_date_yyyymmdd != '')
        {
            $date_condition = " AND  DATEDIFF('$v_begin_date_yyyymmdd',a.C_CHARGE_DATE)<=0";
        }
        
        if($v_end_date_yyyymmdd != '')
        {
            $date_condition .= " AND DATEDIFF('$v_end_date_yyyymmdd',a.C_CHARGE_DATE)>=0";
        }
        
        
        $stmt            = "Select
                                    a.C_SPEC_CODE
                                  , L.C_NAME        AS C_SPEC_NAME
                                  , COUNT(*) C_TOTAL_RECORD
                                  , SUM(a.C_FINAL_FEE) AS C_TOTAL_FEE
                                  , SUM(a.C_COST) As C_TOTAL_COST
                                  , (SUM(a.C_FINAL_FEE) + SUM(a.C_COST)) AS C_SUM
                                  , a.C_CHARGE_DATE
                                  , a.C_XML_DATA
                              From
                              (
                                  Select
                                        RF.FK_RECORD
                                      , RF.C_FINAL_FEE
                                      , RF.C_COST
                                      , R.FK_RECORD_TYPE
                                      , RT.C_SPEC_CODE
                                      , R.C_XML_DATA 
                                      , ExtractValue(R.C_XML_PROCESSING, '//step[contains(@code,''" . _CONST_XML_RTT_DELIM . _CONST_THU_PHI_ROLE . "'')][last()]/datetime[last()]') AS C_CHARGE_DATE
                                  From t_r3_record_fee RF Left join t_r3_record R On RF.FK_RECORD = R.PK_RECORD
                                    Left join t_r3_record_type RT On R.FK_RECORD_TYPE = RT.PK_RECORD_TYPE
                                    Where (1>0) AND R.C_DELETED <> 1 $unit_condition $spec_condition
                              ) a
                              LEFT JOIN t_cores_list L
                                ON a.C_SPEC_CODE = L.C_CODE
                            Where a.C_SPEC_CODE IS NOT NULL $date_condition
                            Group by C_SPEC_CODE";
        $arr_report_data = $this->db->getAll($stmt);
        return $arr_report_data;
    }
    /**
     * Báo cáo tổng hợp phí lệ phí theo TTHC
     * @param type $v_unit
     * @param type $v_spec
     * @param type $v_begin_date_yyyymmdd
     * @param type $v_end_date_yyyymmdd
     * @return type
     */
    public function qry_all_report_data_11($v_record_type,$v_unit, $v_spec,$v_begin_date_yyyymmdd, $v_end_date_yyyymmdd)
    {
        //dieu kien don vi
        $unit_condition = '';
        if($v_unit >= 0)//huyen hoac xa
        {
            $unit_condition = " AND R.FK_VILLAGE_ID = $v_unit";
        }
        else//tat ca cac xa
        {
            $unit_condition = " AND R.FK_VILLAGE_ID > 0";
        }
        //dieu kien linh vuc
        $spec_condition = '';
        if($v_spec != '')
        {
            $spec_condition = " AND RT.C_SPEC_CODE = '$v_spec'";
        }
        //record type condition 
        $record_type_condition = '';
        if($v_record_type != '')
        {
            $record_type_condition .= "AND RT.PK_RECORD_TYPE = $v_record_type";
        }
        //ngay thang
        $date_condition = '';
        if($v_begin_date_yyyymmdd != '')
        {
            $date_condition = " AND  DATEDIFF('$v_begin_date_yyyymmdd',a.C_CHARGE_DATE)<=0";
        }
        
        if($v_end_date_yyyymmdd != '')
        {
            $date_condition .= " AND DATEDIFF('$v_end_date_yyyymmdd',a.C_CHARGE_DATE)>=0";
        }
        
        
        $stmt            = "Select
                                    a.C_SPEC_CODE
                                  , L.C_NAME        AS C_SPEC_NAME
                                  , a.C_RECORD_TYPE_CODE
                                  , CONCAT(a.C_RECORD_TYPE_CODE,' - ',a.C_RECORD_TYPE_NAME) AS C_RECORD_TYPE_NAME
                                  , COUNT(*) C_TOTAL_RECORD
                                  , SUM(a.C_FINAL_FEE) AS C_TOTAL_FEE
                                  , SUM(a.C_COST) As C_TOTAL_COST
                                  , (SUM(a.C_FINAL_FEE) + SUM(a.C_COST)) AS C_SUM
                                  , a.C_CHARGE_DATE
                                  , a.C_XML_DATA
                              From
                              (
                                  Select
                                        RF.FK_RECORD
                                      , RF.C_FINAL_FEE
                                      , RF.C_COST
                                      , R.FK_RECORD_TYPE
                                      , RT.C_SPEC_CODE
                                      , RT.C_CODE AS C_RECORD_TYPE_CODE
                                      , RT.C_NAME AS C_RECORD_TYPE_NAME
                                      , R.C_XML_DATA 
                                      , ExtractValue(R.C_XML_PROCESSING, '//step[contains(@code,''" . _CONST_XML_RTT_DELIM . _CONST_THU_PHI_ROLE . "'')][last()]/datetime[last()]') AS C_CHARGE_DATE
                                  From t_r3_record_fee RF Left join t_r3_record R On RF.FK_RECORD = R.PK_RECORD
                                    Left join t_r3_record_type RT On R.FK_RECORD_TYPE = RT.PK_RECORD_TYPE
                                    Where (1>0) AND R.C_DELETED <> 1 $unit_condition $spec_condition $record_type_condition
                              ) a
                              LEFT JOIN t_cores_list L
                                ON a.C_SPEC_CODE = L.C_CODE
                            Where a.C_SPEC_CODE IS NOT NULL $date_condition
                            GROUP BY a.FK_RECORD_TYPE
                            ORDER BY a.C_SPEC_CODE, a.FK_RECORD_TYPE";
        $arr_report_data = $this->db->getAll($stmt);
        return $arr_report_data;
    }
    /**
     * Báo cáo chi tiết phí lệ phí
     * @param type $v_unit
     * @param type $v_spec
     * @param type $v_begin_date_yyyymmdd
     * @param type $v_end_date_yyyymmdd
     * @return type
     */
    public function qry_all_report_data_12($v_record_type,$v_unit,$v_spec, $v_begin_date_yyyymmdd, $v_end_date_yyyymmdd)
    {
        
        $date_condition = '';
        $record_type_condition = '';
        //ngay thang
        if($v_begin_date_yyyymmdd != '')
        {
            $date_condition = " AND DATEDIFF('$v_begin_date_yyyymmdd',C_CHARGE_DATE)<=0";
        }
        
        if($v_end_date_yyyymmdd != '')
        {
            $date_condition .= " And DATEDIFF('$v_end_date_yyyymmdd',C_CHARGE_DATE)>=0 ";
        }
        
        if(is_numeric($v_record_type))
        {
            $record_type_condition = " AND R.FK_RECORD_TYPE = $v_record_type";
        }
        //dk đơn vị
        $unit_condition = '';
        if($v_unit >= 0)//huyen hoac xa
        {
            $unit_condition = " AND R.FK_VILLAGE_ID = $v_unit";
        }
        //Lay danh sach Linh vuc
        $arr_all_spec = $this->assoc_list_get_all_by_listtype_code(_CONST_DANH_MUC_LINH_VUC, CONST_USE_ADODB_CACHE_FOR_REPORT);
        
        $sql          = '';
        foreach ($arr_all_spec as $code => $name)
        {
            if ($v_spec && ($v_spec != $code))
            {
                continue;
            }
            $sql .= ($sql != '') ? ' Union All ' : '';
            $sql .= "Select
                            C_SPEC_CODE
                          , C_SPEC_NAME
                          , C_RECORD_NO
                          , PK_RECORD_TYPE
                          , C_RECORD_TYPE_NAME
                          , C_CITIZEN_NAME
                          , C_FINAL_FEE
                          , C_COST
                          , (C_FINAL_FEE+C_COST) As C_SUM
                          , C_FEE_DESCRIPTION
                          , DATE_FORMAT(C_CHARGE_DATE, '%d-%m-%Y %H:%i:%s') C_CHARGE_DATE
                          , C_XML_DATA
                    From (
                            Select
                                  RT.C_SPEC_CODE
                                , '$name' AS C_SPEC_NAME
                                , R.C_RECORD_NO
                                , RT.PK_RECORD_TYPE
                                , RT.C_NAME AS C_RECORD_TYPE_NAME
                                , R.C_CITIZEN_NAME
                                , RF.C_FINAL_FEE
                                , RF.C_COST
                                , RF.C_FEE_DESCRIPTION
                                , R.C_XML_DATA
                                , ExtractValue(R.C_XML_PROCESSING, '//step[contains(@code,''" . _CONST_XML_RTT_DELIM . _CONST_THU_PHI_ROLE . "'')][last()]/datetime[last()]') AS C_CHARGE_DATE
                            From t_r3_record_fee RF Left join t_r3_record R On RF.FK_RECORD = R.PK_RECORD
                                  Left join t_r3_record_type RT On R.FK_RECORD_TYPE = RT.PK_RECORD_TYPE
                            Where R.C_DELETED <> 1 AND RT.C_SPEC_CODE = '$code'
                                $unit_condition $record_type_condition
                          ) $code
                    Where (1>0) $date_condition";
        }

        return $this->db->GetAll($sql);
    }
      /**
     * Lấy danh sách Role của một user
     * @param string $user_code
     * @return array
     */
    public function qry_all_user_role($user_code)
    {
        if (DATABASE_TYPE == 'MSSQL')
        {
            $stmt = "Select distinct right(C_TASK_CODE, PATINDEX ( '%[A-Z0-9_][" . _CONST_XML_RTT_DELIM . "]%' , reverse(C_TASK_CODE))) C_ROLE
                    From t_r3_user_task  Where C_USER_LOGIN_NAME=?";
        }
        elseif (DATABASE_TYPE == 'MYSQL')
        {
            $stmt = "Select distinct Right(C_TASK_CODE, LOCATE('" . _CONST_XML_RTT_DELIM . "', REVERSE(C_TASK_CODE)) - 1) C_ROLE
                    From t_r3_user_task Where C_USER_LOGIN_NAME=?";
        }
        $params = array($user_code);

        if (DEBUG_MODE < 10)
        {
            $this->db->debug = 0;
        }
        $ret             = $this->db->getCol($stmt, $params);
        $this->db->debug = DEBUG_MODE;
        return $ret;
    }
    
    public function qry_all_record_type_with_spec_code()
    {
        $sql = 'Select PK_RECORD_TYPE, C_SPEC_CODE, C_CODE, C_NAME From t_r3_record_type Where C_STATUS>0 Order By C_ORDER';
        if (CONST_USE_ADODB_CACHE_FOR_REPORT)
        {
            return $this->db->CacheGetAll($sql);
        }

        return $this->db->GetAll($sql);
    }

//    public function qry_all_report_data_6($period, $arr_all_spec, $group_code = '')
//    {
//        $v_village_id = Session::get('village_id');
//        $arr_all_report_data = Array();
//        $v_report_subtitle   = '[Kỳ báo cáo]';
//        $condition_permission_record_type = '';
//        //du lieu 
//        $year       = intval(get_request_var('year'));
//        $month      = intval(get_request_var('month'));
//        $v_begin_date = jwDate::ddmmyyyy_to_yyyymmdd(get_request_var('begin_date',''));
//        $v_end_date   = jwDate::ddmmyyyy_to_yyyymmdd(get_request_var('end_date',''));
//        
//         // Kiem tra linh vuc duoc phan quyen xem bao cao
//        $arr_record_type_permisssion_id = $this->_qry_all_record_type_id_permission();
//        if(sizeof($arr_record_type_permisssion_id) > 0)
//        {
//            $v_list_record_type_id = implode(', ', $arr_record_type_permisssion_id);
//            $condition_permission_record_type .= " AND FK_RECORD_TYPE in($v_list_record_type_id) ";
//        }
//        
//        $condition = '';
//        
////        //condition
////        if($group_code != '')
////        {
////            $condition = " And ExtractValue(C_XML_PROCESSING,'//next_task[last()]/@group_code') = '$group_code'";
////        }
//            
//        
//        switch ($period)
//        {
//            case 'year':
//                //khai bao condition
//                $mssql_condition = $condition;
//                $mysql_condition = $condition;
//                
//                //Bao cap tiep nhan theo nam
//                if($year != '' && $year != null)
//                {
//                    $the_date = $year . '-01-' . '01';
//                    
//                    $mssql_condition = $mssql_condition . " AND DATEDIFF(year, '$the_date', R.C_RECEIVE_DATE)=0";
//                    $mysql_condition = $mysql_condition . " AND YEAR(R.C_RECEIVE_DATE) = $year";
//                }
//                
//
//                $sql = '';
//                $i   = 0;
//                foreach ($arr_all_spec as $code => $name)
//                {
//                    $sql .= ($i > 0) ? ' Union All ' : '';
//                    if ($this->is_mssql())
//                    {
//                        $sql .= "SELECT
//                                    '$code' AS C_SPEC_CODE
//                                    ,'$name' as C_SPEC_NAME
//                                    ,(
//                                      SELECT COUNT(*)
//                                      FROM view_record R LEFT JOIN t_r3_record_type RT ON R.FK_RECORD_TYPE=RT.PK_RECORD_TYPE
//                                      WHERE (1>0) $mssql_condition $condition_permission_record_type AND RT.C_SPEC_CODE='$code'
//                                     ) AS C_COUNT";
//                    }
//                    elseif ($this->is_mysql())
//                    {
//                        $sql .= "SELECT
//                                    '$code' AS C_SPEC_CODE
//                                    ,'$name' as C_SPEC_NAME
//                                    ,(
//                                      SELECT COUNT(*)
//                                      FROM view_record R LEFT JOIN t_r3_record_type RT ON R.FK_RECORD_TYPE=RT.PK_RECORD_TYPE
//                                      WHERE (1>0) $mysql_condition $condition_permission_record_type AND RT.C_SPEC_CODE='$code' And R.FK_VILLAGE_ID = $v_village_id
//                                     ) AS C_COUNT";
//                    }
//
//                    $i++;
//                }
//                
//                $v_year = get_request_var('year');
//                $v_report_subtitle = '';
//                if($v_year != '')
//                {
//                    $v_report_subtitle = 'Năm ' . get_request_var('year');
//                }
//                
//                //Nếu là năm quá khứ thì cache
//                if (CONST_USE_ADODB_CACHE_FOR_REPORT && $year < Date('Y'))
//                {
//                    $arr_all_report_data = $this->db->cacheGetAll($sql);
//                }
//                else
//                {
//                    $arr_all_report_data = $this->db->getAll($sql);
//                }
//
//                break;
//
//            case 'month':
//                
//                //khai bao condition
//                $mssql_condition = $condition;
//                $mysql_condition = $condition;
//                
//                //Bao cap tiep nhan theo nam
//                if($year != '' && $year != null && $month != '' && $month != null)
//                {
//                    $the_date = $year . '-' . $month . '-' . '01';
//                    
//                    $mssql_condition = $mssql_condition . " AND DATEDIFF(month, '$the_date', R.C_RECEIVE_DATE)=0";
//                    $mysql_condition = $mysql_condition . " AND YEAR(R.C_RECEIVE_DATE)=$year AND MONTH(R.C_RECEIVE_DATE)=$month";
//                }
//
//                $sql = '';
//                $i   = 0;
//                foreach ($arr_all_spec as $code => $name)
//                {
//                    $sql .= ($i > 0) ? ' Union All ' : '';
//                    if ($this->is_mssql())
//                    {
//                        $sql .= "SELECT
//                                    '$code' AS SPEC_CODE
//                                    ,'$name' as C_SPEC_NAME
//                                    ,(SELECT COUNT(*)
//                                      FROM view_record R LEFT JOIN t_r3_record_type RT ON R.FK_RECORD_TYPE=RT.PK_RECORD_TYPE
//                                      WHERE (1>0) $mssql_condition $condition_permission_record_type AND RT.C_SPEC_CODE='$code'
//                                     ) AS C_COUNT";
//                    }
//                    elseif ($this->is_mysql())
//                    {
//                        $sql .= "SELECT
//                                    '$code' AS SPEC_CODE
//                                    ,'$name' as C_SPEC_NAME
//                                    ,(SELECT COUNT(*)
//                                      FROM view_record R LEFT JOIN t_r3_record_type RT ON R.FK_RECORD_TYPE=RT.PK_RECORD_TYPE
//                                      WHERE (1>0) $mysql_condition $condition_permission_record_type AND RT.C_SPEC_CODE='$code' And R.FK_VILLAGE_ID = $v_village_id
//                                     ) AS C_COUNT";
//                    }
//                    $i++;
//                }
//                $v_report_subtitle = 'Tháng ' . $month . ' năm ' . $year;
//
//                //Nếu là tháng quá khứ thì dùng cache
//                if (CONST_USE_ADODB_CACHE_FOR_REPORT && $month < Date('m') && $year <= Date('Y'))
//                {
//                    $arr_all_report_data = $this->db->CachegetAll($sql);
//                }
//                else
//                {
//                    $arr_all_report_data = $this->db->getAll($sql);
//                }
//
//                break;
//
//            case 'week':
//                //khai bao condition   
//                $mssql_condition = $condition;
//                $mysql_condition = $condition;
//                
//                //Bao cap tiep nhan theo nam
//                if($v_begin_date != '' && $v_begin_date != null && $v_end_date != '' && $v_end_date != null)
//                {
//                    $the_date = $year . '-' . $month . '-' . '01';
//                    
//                    $mssql_condition = $mssql_condition . " AND DATEDIFF(day, '$v_begin_date', R.C_RECEIVE_DATE)>=0
//                                                            AND DATEDIFF(day, '$v_end_date', R.C_RECEIVE_DATE)<=0";
//                    
//                    $mysql_condition = $mysql_condition . " AND DATEDIFF('$v_begin_date',R.C_RECEIVE_DATE)<=0
//                                                            AND DATEDIFF('$v_end_date',R.C_RECEIVE_DATE)>=0";
//                }
//                
//                
//                $sql = '';
//                $i   = 0;
//                foreach ($arr_all_spec as $code => $name)
//                {
//                    $sql .= ($i > 0) ? ' Union All ' : '';
//                    if ($this->is_mssql())
//                    {
//                        $sql .= "SELECT
//                                    '$code' AS SPEC_CODE
//                                    ,'$name' as C_SPEC_NAME
//                                    ,(SELECT COUNT(*)
//                                      FROM view_record R LEFT JOIN t_r3_record_type RT ON R.FK_RECORD_TYPE=RT.PK_RECORD_TYPE
//                                      WHERE (1>0) $mssql_condition AND RT.C_SPEC_CODE='$code'
//                                     ) AS C_COUNT";
//                    }
//                    elseif ($this->is_mysql())
//                    {
//                        $sql .= "SELECT
//                                    '$code' AS SPEC_CODE
//                                    ,'$name' as C_SPEC_NAME
//                                    ,(SELECT COUNT(*)
//                                      FROM view_record R LEFT JOIN t_r3_record_type RT ON R.FK_RECORD_TYPE=RT.PK_RECORD_TYPE
//                                      WHERE (1>0) $mysql_condition $condition_permission_record_type AND RT.C_SPEC_CODE='$code' And R.FK_VILLAGE_ID = $v_village_id
//                                     ) AS C_COUNT";
//                    }
//                    $i++;
//                }
//
//                $v_report_subtitle = 'Tuần từ ' . get_request_var('begin_date') . ' đến ' . get_request_var('end_date');
//
//                //Dung Cache nếu là tuần quá khứ
//                if ($v_end_date < 1)
//                {
//                    
//                }
//                $arr_all_report_data = $this->db->getAll($sql);
//                break;
//
//            case 'date':
//            default:
//                $date = jwDate::ddmmyyyy_to_yyyymmdd(get_request_var('date'));
//
//                $sql = '';
//                $i   = 0;
//                foreach ($arr_all_spec as $code => $name)
//                {
//                    $sql .= ($i > 0) ? ' Union All ' : '';
//                    if ($this->is_mssql())
//                    {
//                        $sql .= "SELECT
//                                    '$code' AS SPEC_CODE
//                                    ,'$name' as C_SPEC_NAME
//                                    ,(SELECT COUNT(*)
//                                      FROM view_record R LEFT JOIN t_r3_record_type RT ON R.FK_RECORD_TYPE=RT.PK_RECORD_TYPE
//                                      WHERE DATEDIFF(day, '$date', R.C_RECEIVE_DATE)=0
//                                        AND RT.C_SPEC_CODE='$code'
//                                     ) AS C_COUNT";
//                    }
//                    elseif ($this->is_mysql())
//                    {
//                        $sql .= "SELECT
//                                    '$code' AS SPEC_CODE
//                                    ,'$name' as C_SPEC_NAME
//                                    ,(SELECT COUNT(*)
//                                      FROM view_record R LEFT JOIN t_r3_record_type RT ON R.FK_RECORD_TYPE=RT.PK_RECORD_TYPE
//                                      WHERE DATEDIFF('$date',R.C_RECEIVE_DATE)=0
//                                        AND RT.C_SPEC_CODE='$code' $condition_permission_record_type And R.FK_VILLAGE_ID = $v_village_id
//                                     ) AS C_COUNT";
//                    }
//
//                    $i++;
//                }
//                $v_report_subtitle = 'Ngày ' . get_request_var('date');
//
//                //Cache nếu là ngày quá khứ
//                if ($date < 1)
//                {
//                    
//                }
//                $arr_all_report_data = $this->db->getAll($sql);
//
//                break;
//        }
//
//        $ret['report_subtitle']     = $v_report_subtitle;
//        $ret['arr_all_report_data'] = $arr_all_report_data;
//
//        return $ret;
//    }

    /**
     * Lấy danh sách phòng ban có tham gia vào việc thụ lý hồ sơ
     * @param Boolean $use_cache Co sung dung cache khong?
     */
    public function qry_all_exec_group($use_cache = FALSE)
    {
        $stmt   = 'Select Distinct
                    G.C_CODE
                    ,G.C_NAME
                From t_cores_group G Left join t_r3_user_task UT On UT.C_GROUP_CODE = G.C_CODE
                Where UT.C_TASK_CODE LIKE ?
                Order by G.C_NAME';
        $params = array('%' . _CONST_XML_RTT_DELIM . _CONST_PHAN_CONG_ROLE);
        return ($use_cache) ? $this->db->cacheGetAssoc($stmt, $params) : $this->db->getAssoc($stmt, $params);
    }

    
    /**
     * lay tat ca ou
     * @return type array
     */
//    public function qry_all_ou()
//    {
//        $sql = "SELECT DISTINCT
//                    CASE WHEN(FK_OU = -1) THEN -1  ELSE PK_OU END AS PK_OU,
//                    C_NAME
//                  FROM t_cores_ou";
//        
//        return $this->db->getAssoc($sql);
//    }

//    public function qry_all_report_data_3($date)
//    {
//        //Danh sach phong ban chuyen mon
//        $arr_all_exec_group = $this->qry_all_exec_group(CONST_USE_ADODB_CACHE_FOR_REPORT);
//        $v_village_id       = Session::get('village_id');
//
//        //1.Tong so HS tiep nhan trong ngay theo tung phong ban
//        $sql = '';
//        foreach ($arr_all_exec_group as $code => $name)
//        {
//            $sql .= ($sql != '') ? ' Union All ' : '';
//            $sql .= "Select
//                        COUNT(*) C_COUNT
//                      , '$code' AS C_GROUP_CODE
//                      From view_record R
//                      Where ExtractValue(C_XML_PROCESSING, '//step[to_group_code=''$code'' and contains(datetime,''$date'')][last()]/@code[last()]') != ''";
//        }
//        $this->db->SetFetchMode(ADODB_FETCH_ASSOC);
//        if (is_past_date($date) && CONST_USE_ADODB_CACHE_FOR_REPORT)
//        {
//            $arr_count_received_record_by_group = $this->db->CacheGetAll($sql);
//        }
//        else
//        {
//            $arr_count_received_record_by_group = $this->db->getAll($sql);
//        }
//
//        //2. Tong so HS đang xử lý trong ngay theo tung phong ban
//        $sql                                  = "Select
//                    COUNT(*) C_COUNT
//                  , ExtractValue(C_XML_PROCESSING, '//next_task/@group_code') C_GROUP_CODE
//                From view_processing_record
//                Where
//                  (   C_NEXT_TASK_CODE Like '%" . _CONST_XML_RTT_DELIM . _CONST_PHAN_CONG_ROLE . "'
//                      OR C_NEXT_TASK_CODE Like '%" . _CONST_XML_RTT_DELIM . _CONST_THU_LY_ROLE . "'
//                      OR C_NEXT_TASK_CODE Like '%" . _CONST_XML_RTT_DELIM . _CONST_XET_DUYET_ROLE . "'
//                      OR C_NEXT_TASK_CODE Like '%" . _CONST_XML_RTT_DELIM . _CONST_YEU_CAU_THU_LY_LAI_ROLE . "'
//                      OR C_NEXT_TASK_CODE Like '%" . _CONST_XML_RTT_DELIM . _CONST_XET_DUYET_BO_SUNG_ROLE . "'
//                  )
//                And FK_VILLAGE_ID = $v_village_id
//                Group by C_GROUP_CODE";
//        $arr_count_processing_record_by_group = $this->db->getAll($sql);
//
//        //3. Tong so HS đang cham tien do trong ngay theo tung phong ban
//        $sql                               = "Select
//                    COUNT(*) C_COUNT
//                  , ExtractValue(C_XML_PROCESSING, '//next_task/@group_code') C_GROUP_CODE
//                From view_processing_record
//
//                Where C_BIZ_DAYS_EXCEED IS NULL
//                And (DATEDIFF(C_DOING_STEP_DEADLINE_DATE, NOW()) < 0)
//                And (C_NEXT_TASK_CODE IS NOT NULL)
//                And FK_VILLAGE_ID = $v_village_id
//                Group by C_GROUP_CODE";
//        $arr_count_delayed_record_by_group = $this->db->getAll($sql);
//
//        //4. Tong hồ sơ đang quá hạn trong ngày theo tung phong ban
//        $sql                                = "Select
//                    COUNT(*) C_COUNT
//                  , ExtractValue(C_XML_PROCESSING, '//next_task/@group_code') C_GROUP_CODE
//                From view_processing_record
//
//                Where C_BIZ_DAYS_EXCEED Is Null
//                    And ($this->_datediff(C_RETURN_DATE, Now()) < 0)
//                    And FK_VILLAGE_ID = $v_village_id
//                Group by C_GROUP_CODE";
//        $arr_count_overtime_record_by_group = $this->db->getAll($sql);
//
//        //return
//        $ret['arr_count_received_record_by_group']   = $arr_count_received_record_by_group;
//        $ret['arr_count_processing_record_by_group'] = $arr_count_processing_record_by_group;
//        $ret['arr_count_delayed_record_by_group']    = $arr_count_delayed_record_by_group;
//        $ret['arr_count_overtime_record_by_group']   = $arr_count_overtime_record_by_group;
//
//        return $ret;
//    }

    /**
     * Cham tien do
     * @return type
     */
//    public function qry_all_report_data_12()
//    {         
//        $v_village_id = Session::get('village_id');
//        $group_code   = get_request_var('group','');
//        $spec_code    = get_request_var('spec_code','');
//        $group_level  = get_request_var('group_level','');
//            
//        $group_condition = '';
//        $spec_condition = '';
//        
//        //neu co ou
//        if($v_village_id != 0)
//        {
//            $group_condition = " AND FK_VILLAGE_ID = $v_village_id";
//        }
//        else 
//        {
//            if($group_code != '')//ton tai group code
//            {
//                if((int)$group_level == 3)//neu yeu cau bao cao cap xa
//                {
//                    $group_condition = " AND R.FK_VILLAGE_ID = $group_level";
//                }
//                else if((int) $group_level == 1 && $group_code == '0') //can bo cap huyen chi xem bao cao tai huyen
//                {
//                    $group_condition = " AND R.FK_VILLAGE_ID = 0";
//                }
//                else 
//                {
//                    $group_condition = " AND R.PK_RECORD IN (SELECT DISTINCT FK_RECORD FROM t_r3_bizdone_step_log WHERE C_GROUP_CODE = '$group_code')";
//                }
//            }
//        }
//         // Kiem tra linh vuc duoc phan quyen xem bao cao
//        $condition = '';
//        $arr_record_type_permisssion_id = $this->_qry_all_record_type_id_permission();
//        if(sizeof($arr_record_type_permisssion_id) > 0)
//        {
//            $v_list_record_type_id = implode(', ', $arr_record_type_permisssion_id);
//            $condition = " AND R.FK_RECORD_TYPE in($v_list_record_type_id) ";
//        }
//        //neu co spec code
//        if($spec_code != '' && $spec_code != null)
//        {
//            $spec_condition = " AND RT.C_SPEC_CODE = '$spec_code'";
//        }
//        $sql = "SELECT
//                    SL.C_DAYS_EXCEED AS C_DOING_STEP_DAYS_REMAIN,
//                    CASE
//                        WHEN (R.C_REJECTED = 1) THEN 3
//                        WHEN (R.C_REJECTED <> 1
//                              AND (R.C_CLEAR_DATE IS NOT NULL)) THEN 2
//                        ELSE 1
//                    END AS C_ACTIVITY ,
//                    DATE_FORMAT(SL.C_STEP_DEADLINE_DATE, '%d-%m-%Y') AS C_STEP_DEADLINE_DATE ,
//                    SL.C_GROUP_CODE,
//                    (SELECT C_NAME FROM t_cores_group WHERE C_CODE = SL.C_GROUP_CODE) AS C_GROUP_NAME,
//                    R.PK_RECORD,
//                    R.FK_RECORD_TYPE,
//                    R.C_RECORD_NO,
//                    CAST(R.C_RECEIVE_DATE AS CHAR(19)) AS C_RECEIVE_DATE,
//                    CAST(R.C_RETURN_DATE AS CHAR(19)) AS C_RETURN_DATE,
//                    R.C_RETURN_PHONE_NUMBER,
//                    R.C_XML_DATA,
//                    R.C_XML_PROCESSING,
//                    R.C_DELETED,
//                    R.C_CLEAR_DATE,
//                    R.C_XML_WORKFLOW,
//                    R.C_RETURN_EMAIL,
//                    R.C_REJECTED,
//                    R.C_REJECT_REASON,
//                    R.C_CITIZEN_NAME,
//                    R.C_ADVANCE_COST,
//                    R.C_CREATE_BY,
//                    R.C_NEXT_TASK_CODE,
//                    R.C_NEXT_USER_CODE,
//                    R.C_NEXT_CO_USER_CODE,
//                    R.C_LAST_TASK_CODE,
//                    R.C_LAST_USER_CODE,
//                    R.C_BIZ_DAYS_EXCEED,
//                    12 AS TT
//                  FROM t_r3_record R
//                    INNER JOIN (SELECT
//                                  C_GROUP_CODE,
//                                  FK_RECORD,
//                                  C_DAYS_EXCEED,
//                                  C_STEP_DEADLINE_DATE
//                                FROM t_r3_bizdone_step_log
//                                WHERE C_DAYS_EXCEED < 0) SL
//                      ON R.PK_RECORD = SL.FK_RECORD
//                    LEFT JOIN t_r3_record_type RT
//                      ON R.FK_RECORD_TYPE = RT.PK_RECORD_TYPE
//                    Where (1>0) $spec_condition $condition $group_condition
//                  ORDER BY SL.C_GROUP_CODE, R.C_RECEIVE_DATE DESC";
////        $sql          = "SELECT
////                    ExtractValue(C_XML_PROCESSING,'//next_task[last()]/@group_code') AS C_DOING_GROUP_CODE
////                    ,Case When (R.C_REJECTED = 1) Then 3 When (R.C_REJECTED <> 1 And (R.C_CLEAR_DATE Is Not Null)) Then 2 Else 1 End as C_ACTIVITY
////                    , CASE WHEN (DATEDIFF(NOW(), R.C_DOING_STEP_DEADLINE_DATE)>0) THEN (SELECT -1 * (COUNT(*)) FROM view_working_date WD WHERE DATEDIFF(WD.C_DATE, NOW())<=0 AND DATEDIFF(WD.C_DATE, R.C_DOING_STEP_DEADLINE_DATE)>0 ) ELSE (SELECT (COUNT(*)) FROM view_working_date WD WHERE DATEDIFF(WD.C_DATE, NOW())>=0 AND DATEDIFF(WD.C_DATE, R.C_DOING_STEP_DEADLINE_DATE)<0 ) END AS C_DOING_STEP_DAYS_REMAIN
////                    , CASE WHEN (DATEDIFF(NOW(),R.C_RETURN_DATE)>0) THEN (SELECT -1 * (COUNT(*)) FROM view_working_date WD WHERE DATEDIFF(WD.C_DATE, NOW())<=0 AND DATEDIFF(WD.C_DATE, R.C_RETURN_DATE)>0 ) ELSE (SELECT (COUNT(*)) FROM view_working_date WD WHERE DATEDIFF(WD.C_DATE, NOW())>=0 AND DATEDIFF(WD.C_DATE, R.C_RETURN_DATE)<0 ) END AS C_RETURN_DAYS_REMAIN
////                    , R.PK_RECORD
////                    , R.FK_RECORD_TYPE
////                    , R.C_RECORD_NO
////                    , CAST(R.C_RECEIVE_DATE AS CHAR(19)) AS C_RECEIVE_DATE
////                    , CAST(R.C_RETURN_DATE AS CHAR(19)) AS C_RETURN_DATE
////                    , R.C_RETURN_PHONE_NUMBER
////                    , R.C_XML_DATA
////                    , R.C_XML_PROCESSING
////                    , R.C_DELETED
////                    , R.C_CLEAR_DATE
////                    , R.C_XML_WORKFLOW
////                    , R.C_RETURN_EMAIL
////                    , R.C_REJECTED
////                    , R.C_REJECT_REASON
////                    , R.C_CITIZEN_NAME
////                    , R.C_ADVANCE_COST
////                    , R.C_CREATE_BY
////                    , R.C_NEXT_TASK_CODE
////                    , R.C_NEXT_USER_CODE
////                    , R.C_NEXT_CO_USER_CODE
////                    , R.C_LAST_TASK_CODE
////                    , R.C_LAST_USER_CODE
////                    , CAST(R.C_DOING_STEP_BEGIN_DATE AS CHAR(19)) AS C_DOING_STEP_BEGIN_DATE
////                    , R.C_DOING_STEP_DEADLINE_DATE
////                    , R.C_BIZ_DAYS_EXCEED
////                    , a.C_TASK_CODE
////                    , a.C_STEP_TIME
////                    , 8 as TT
////                FROM
////                (
////                    SELECT
////                        RID.`PK_RECORD`
////                      , UT.C_TASK_CODE
////                      , UT.C_STEP_TIME
////                    FROM
////                    (
////                        SELECT
////                            PK_RECORD
////                          , C_NEXT_TASK_CODE
////                          , C_NEXT_USER_CODE
////                          , FK_RECORD_TYPE
////                        FROM t_r3_record
////                        WHERE C_BIZ_DAYS_EXCEED IS NULL
////                            AND C_DELETED <> 1 AND C_CLEAR_DATE IS NULL
////                            AND (DATEDIFF(C_DOING_STEP_DEADLINE_DATE, NOW()) < 0)
////                            AND (C_NEXT_TASK_CODE IS NOT NULL)
////                            AND (C_PAUSE_DATE IS NULL OR (C_PAUSE_DATE IS NOT NULL AND C_UNPAUSE_DATE IS NOT NULL))
////                            $group_condition
////                            $condition
////                    )  RID LEFT JOIN t_r3_user_task UT ON (RID.C_NEXT_TASK_CODE = UT.C_TASK_CODE AND RID.C_NEXT_USER_CODE = UT.C_USER_LOGIN_NAME)
////                    LEFT JOIN t_r3_record_type RT 
////                    ON RID.FK_RECORD_TYPE = RT.PK_RECORD_TYPE 
////                    WHERE (1>0) $spec_condition                        
////                ) a LEFT JOIN view_record R ON a.PK_RECORD=R.PK_RECORD
////                ORDER BY C_DOING_GROUP_CODE, R.C_RECEIVE_DATE DESC
////                ";
//        return $this->db->getAll($sql);
//    }
    /**
     * lay tat ca phong ban chuyen mon theo cau hinh tai bang t_r3_record_meta (key: PHONG_BAN_CHUYEN_MON)
     * @return type
     */
//    public function qry_all_business_unit()
//    {
//        $sql = "SELECT C_CODE,
//                        C_NAME
//                    FROM t_cores_group
//                    WHERE INSTR(
//                                  (SELECT extractValue(C_META_VALUE,'//item')
//                                   FROM t_r3_record_meta
//                                   WHERE FK_RECORD = 0
//                                     AND C_META_KEY = '"._CONST_META_KEY_PHONG_BAN."'), C_CODE) > 0";
//        return $this->db->GetAssoc($sql);
//    }
    /**
     * lay du lieu bao cao so sanh tien do xu ly cac phong ban
     * @param type $v_begin_date
     * @param type $v_end_date
     * @param type $v_group_code
     * @return type
     */
//    public function qry_all_report_data_20($v_begin_date = '',$v_end_date = '',$v_group_code)
//    {
//        $condition_date  = '';
//        $condition_group = '';
//        if($v_begin_date != '')
//        {
//            $v_begin_date = jwDate::ddmmyyyy_to_yyyymmdd($v_begin_date);
//            $condition_date .= " AND '$v_begin_date' <= C_FINISH_DATE";
//        }
//        
//        if($v_end_date != '')
//        {
//            $v_end_date = jwDate::ddmmyyyy_to_yyyymmdd($v_end_date);
//            $condition_date .= " AND '$v_end_date' >= C_FINISH_DATE";
//        }
//        
//        if($v_group_code != '')
//        {
//            $condition_group = " AND C_CODE = '$v_group_code'";
//        }
//        
//        $sql = "SELECT G.C_NAME,
//                        G.C_CODE,
//                        COALESCE(RL.C_TOTAL_RECORD,0) AS C_TOTAL_RECORD,
//                        COALESCE(RL.C_ON_TIME,0) AS C_ON_TIME,
//                        COALESCE(RL.C_DELAYS,0) AS C_DELAYS
//                 FROM
//                   (SELECT C_NAME,
//                           C_CODE
//                    FROM t_cores_group
//                    WHERE INSTR(
//                                  (SELECT extractValue(C_META_VALUE,'//item')
//                                   FROM t_r3_record_meta
//                                   WHERE FK_RECORD = 0
//                                     AND C_META_KEY = '"._CONST_META_KEY_PHONG_BAN."'), C_CODE) > 0
//                                     $condition_group) G
//                 LEFT JOIN
//                   (SELECT R.C_GROUP_CODE,
//                           SUM(R.C_ON_TIME) AS C_ON_TIME,
//                           SUM(R.C_DELAYS) AS C_DELAYS,
//                           COUNT(R.C_GROUP_CODE) AS C_TOTAL_RECORD
//                    FROM
//                      (SELECT C_GROUP_CODE,
//                              CASE WHEN AVG(C_DAYS_EXCEED) >= 0 THEN 1 ELSE 0 END AS C_ON_TIME,
//                              CASE WHEN AVG(C_DAYS_EXCEED) < 0 THEN 1 ELSE 0 END AS C_DELAYS,
//                              FK_RECORD
//                       FROM t_r3_bizdone_step_log
//                       WHERE (1>0) $condition_date
//                       GROUP BY C_GROUP_CODE,
//                                FK_RECORD) R
//                    GROUP BY R.C_GROUP_CODE) RL ON G.C_CODE = RL.C_GROUP_CODE";
//       return $this->db->getAll($sql); 
//    }
    /**
     * Bao cao thu tuc hanh chinh qua han
     */
//    public function qry_all_report_data_13()
//    {
//        $v_village_id = Session::get('village_id');
//        $group_code = get_request_var('group','');
//        $spec_code = get_request_var('spec_code','');
//        $group_level  = get_request_var('group_level','');
//        
//        $group_condition = '';
//        $spec_condition = '';
//        //neu co ou
//        if($v_village_id != 0)
//        {
//            $group_condition = " AND FK_VILLAGE_ID = $v_village_id";
//        }
//        else 
//        {
//            if($group_code != '')//ton tai group code
//            {
//                if((int)$group_level == 3)//neu yeu cau bao cao cap xa
//                {
//                    $group_condition = " AND FK_VILLAGE_ID = $group_level";
//                }
//                else if((int) $group_level == 1 && $group_code == '0') //can bo cap huyen chi xem bao cao tai huyen
//                {
//                    $group_condition = " AND FK_VILLAGE_ID = 0";
//                }
//                else 
//                {
//                    $group_condition = " AND PK_RECORD IN (SELECT DISTINCT FK_RECORD FROM t_r3_bizdone_step_log WHERE C_GROUP_CODE = '$group_code')";
//                }
//            }
//        }
//        // Kiem tra linh vuc duoc phan quyen xem bao cao
//        $condition_permission_record_type = '';
//        $arr_record_type_permisssion_id = $this->_qry_all_record_type_id_permission();
//        if(sizeof($arr_record_type_permisssion_id) > 0)
//        {
//            $v_list_record_type_id = implode(', ', $arr_record_type_permisssion_id);
//            $condition_permission_record_type .= " AND FK_RECORD_TYPE in($v_list_record_type_id) ";
//        }
//        
//        //neu co spec code
//        if($spec_code != '' && $spec_code != null)
//        {
//            $spec_condition = " AND RT.C_SPEC_CODE = '$spec_code'";
//        }
//        
//        $sql          = "SELECT
//                    ExtractValue(C_XML_PROCESSING,'//next_task[last()]/@group_code') AS C_DOING_GROUP_CODE
//                    ,Case When (R.C_REJECTED = 1) Then 3 When (R.C_REJECTED <> 1 And (R.C_CLEAR_DATE Is Not Null)) Then 2 Else 1 End as C_ACTIVITY
//                    , CASE WHEN (DATEDIFF(NOW(), R.C_DOING_STEP_DEADLINE_DATE)>0) THEN (SELECT -1 * (COUNT(*)) FROM view_working_date WD WHERE DATEDIFF(WD.C_DATE, NOW())<=0 AND DATEDIFF(WD.C_DATE, R.C_DOING_STEP_DEADLINE_DATE)>0 ) ELSE (SELECT (COUNT(*)) FROM view_working_date WD WHERE DATEDIFF(WD.C_DATE, NOW())>=0 AND DATEDIFF(WD.C_DATE, R.C_DOING_STEP_DEADLINE_DATE)<0 ) END AS C_DOING_STEP_DAYS_REMAIN
//                    , CASE WHEN (DATEDIFF(NOW(),R.C_RETURN_DATE)>0) THEN (SELECT -1 * (COUNT(*)) FROM view_working_date WD WHERE DATEDIFF(WD.C_DATE, NOW())<=0 AND DATEDIFF(WD.C_DATE, R.C_RETURN_DATE)>0 ) ELSE (SELECT (COUNT(*)) FROM view_working_date WD WHERE DATEDIFF(WD.C_DATE, NOW())>=0 AND DATEDIFF(WD.C_DATE, R.C_RETURN_DATE)<0 ) END AS C_RETURN_DAYS_REMAIN
//                    , R.PK_RECORD
//                    , R.FK_RECORD_TYPE
//                    , R.C_RECORD_NO
//                    , CAST(R.C_RECEIVE_DATE AS CHAR(19)) AS C_RECEIVE_DATE
//                    , CAST(R.C_RETURN_DATE AS CHAR(19)) AS C_RETURN_DATE
//                    , R.C_RETURN_PHONE_NUMBER
//                    , R.C_XML_DATA
//                    , R.C_XML_PROCESSING
//                    , R.C_DELETED
//                    , R.C_CLEAR_DATE
//                    , R.C_XML_WORKFLOW
//                    , R.C_RETURN_EMAIL
//                    , R.C_REJECTED
//                    , R.C_REJECT_REASON
//                    , R.C_CITIZEN_NAME
//                    , R.C_ADVANCE_COST
//                    , R.C_CREATE_BY
//                    , R.C_NEXT_TASK_CODE
//                    , R.C_NEXT_USER_CODE
//                    , R.C_NEXT_CO_USER_CODE
//                    , R.C_LAST_TASK_CODE
//                    , R.C_LAST_USER_CODE
//                    , CAST(R.C_DOING_STEP_BEGIN_DATE AS CHAR(19)) AS C_DOING_STEP_BEGIN_DATE
//                    , R.C_DOING_STEP_DEADLINE_DATE
//                    , R.C_BIZ_DAYS_EXCEED
//                    , a.C_TASK_CODE
//                    , a.C_STEP_TIME
//                    , 8 as TT
//                FROM
//                (
//                    SELECT
//                        RID.`PK_RECORD`
//                      , UT.C_TASK_CODE
//                      , UT.C_STEP_TIME
//                    FROM
//                    (
//                        SELECT
//                            PK_RECORD
//                          , C_NEXT_TASK_CODE
//                          , C_NEXT_USER_CODE
//                          , FK_RECORD_TYPE
//                        From view_processing_record
//                        Where C_IS_PAUSING = 0 
//                            AND C_BIZ_DAYS_EXCEED Is Null
//                            And (C_RETURN_DATE < Now()) 
//                            $group_condition
//                            $condition_permission_record_type
//                    )  RID LEFT JOIN t_r3_user_task UT ON (RID.C_NEXT_TASK_CODE = UT.C_TASK_CODE AND RID.C_NEXT_USER_CODE = UT.C_USER_LOGIN_NAME)
//                    LEFT JOIN t_r3_record_type RT 
//                    ON RID.FK_RECORD_TYPE = RT.PK_RECORD_TYPE 
//                    WHERE (1>0) $spec_condition
//                ) a LEFT JOIN view_record R ON a.PK_RECORD=R.PK_RECORD
//                ORDER BY C_DOING_GROUP_CODE, R.C_RECEIVE_DATE DESC
//                ";
//        return $this->db->getAll($sql);
//    }

    /**
     * Bao cao thu tuc hanh chinh dang bo sung
     */
//    public function qry_all_report_data_14()
//    {
//        $v_village_id = Session::get('village_id');
//        $spec_code = get_request_var('spec_code','');
//        
//        $spec_condition = '';
//                //neu co spec code
//        if($spec_code != '' && $spec_code != null)
//        {
//            $spec_condition = " AND RT.C_SPEC_CODE = '$spec_code'";
//        }
//         // Kiem tra linh vuc duoc phan quyen xem bao cao
//        $condition_permission_record_type = '';
//        $arr_record_type_permisssion_id = $this->_qry_all_record_type_id_permission();
//        if(sizeof($arr_record_type_permisssion_id) > 0)
//        {
//            $v_list_record_type_id = implode(', ', $arr_record_type_permisssion_id);
//            $condition_permission_record_type .= " AND FK_RECORD_TYPE in($v_list_record_type_id) ";
//        }
//        
//        $sql          = "SELECT
//                    ExtractValue(C_XML_PROCESSING,'//next_task[last()]/@group_code') AS C_DOING_GROUP_CODE
//                    ,Case When (R.C_REJECTED = 1) Then 3 When (R.C_REJECTED <> 1 And (R.C_CLEAR_DATE Is Not Null)) Then 2 Else 1 End as C_ACTIVITY
//                    , CASE WHEN (DATEDIFF(NOW(), R.C_DOING_STEP_DEADLINE_DATE)>0) THEN (SELECT -1 * (COUNT(*)) FROM view_working_date WD WHERE DATEDIFF(WD.C_DATE, NOW())<=0 AND DATEDIFF(WD.C_DATE, R.C_DOING_STEP_DEADLINE_DATE)>0 ) ELSE (SELECT (COUNT(*)) FROM view_working_date WD WHERE DATEDIFF(WD.C_DATE, NOW())>=0 AND DATEDIFF(WD.C_DATE, R.C_DOING_STEP_DEADLINE_DATE)<0 ) END AS C_DOING_STEP_DAYS_REMAIN
//                    , CASE WHEN (DATEDIFF(NOW(),R.C_RETURN_DATE)>0) THEN (SELECT -1 * (COUNT(*)) FROM view_working_date WD WHERE DATEDIFF(WD.C_DATE, NOW())<=0 AND DATEDIFF(WD.C_DATE, R.C_RETURN_DATE)>0 ) ELSE (SELECT (COUNT(*)) FROM view_working_date WD WHERE DATEDIFF(WD.C_DATE, NOW())>=0 AND DATEDIFF(WD.C_DATE, R.C_RETURN_DATE)<0 ) END AS C_RETURN_DAYS_REMAIN
//                    , R.PK_RECORD
//                    , R.FK_RECORD_TYPE
//                    , R.C_RECORD_NO
//                    , CAST(R.C_RECEIVE_DATE AS CHAR(19)) AS C_RECEIVE_DATE
//                    , CAST(R.C_RETURN_DATE AS CHAR(19)) AS C_RETURN_DATE
//                    , R.C_RETURN_PHONE_NUMBER
//                    , R.C_XML_DATA
//                    , R.C_XML_PROCESSING
//                    , R.C_DELETED
//                    , R.C_CLEAR_DATE
//                    , R.C_XML_WORKFLOW
//                    , R.C_RETURN_EMAIL
//                    , R.C_REJECTED
//                    , R.C_REJECT_REASON
//                    , R.C_CITIZEN_NAME
//                    , R.C_ADVANCE_COST
//                    , R.C_CREATE_BY
//                    , R.C_NEXT_TASK_CODE
//                    , R.C_NEXT_USER_CODE
//                    , R.C_NEXT_CO_USER_CODE
//                    , R.C_LAST_TASK_CODE
//                    , R.C_LAST_USER_CODE
//                    , CAST(R.C_DOING_STEP_BEGIN_DATE AS CHAR(19)) AS C_DOING_STEP_BEGIN_DATE
//                    , R.C_DOING_STEP_DEADLINE_DATE
//                    , R.C_BIZ_DAYS_EXCEED
//                    , a.C_TASK_CODE
//                    , a.C_STEP_TIME
//                    , 8 as TT
//                    , CAST(DATE_FORMAT(ExtractValue(C_XML_PROCESSING, '//step[contains(@code,''" . _CONST_XML_RTT_DELIM . _CONST_THONG_BAO_BO_SUNG_ROLE . "'')][last()]/datetime'),'%d-%m-%Y %H:%i:%s') AS CHAR ) as C_ANNOUNCE_DATE
//                FROM
//                (
//                    SELECT
//                        RID.`PK_RECORD`
//                      , UT.C_TASK_CODE
//                      , UT.C_STEP_TIME
//                    FROM
//                    (
//                        SELECT
//                            PK_RECORD
//                          , C_NEXT_TASK_CODE
//                          , C_NEXT_USER_CODE
//                          , FK_RECORD_TYPE
//                         From view_processing_record
//                        Where C_NEXT_TASK_CODE like '%" . _CONST_XML_RTT_DELIM . _CONST_BO_SUNG_ROLE . "' And FK_VILLAGE_ID = $v_village_id
//                            $condition_permission_record_type
//                    )  RID LEFT JOIN t_r3_user_task UT ON (RID.C_NEXT_TASK_CODE = UT.C_TASK_CODE AND RID.C_NEXT_USER_CODE = UT.C_USER_LOGIN_NAME)
//                    LEFT JOIN t_r3_record_type RT 
//                    ON RID.FK_RECORD_TYPE = RT.PK_RECORD_TYPE 
//                    WHERE (1>0) $spec_condition
//                ) a LEFT JOIN view_record R ON a.PK_RECORD=R.PK_RECORD
//                ORDER BY R.C_RECEIVE_DATE DESC
//                ";
//        return $this->db->getAll($sql);
//    }

    /**
     * Bao cao thu tuc hanh chinh bi tu choi
     */
//    public function qry_all_report_data_15($begin_date_yyyymmdd = '', $end_date_yyyymmdd = '',$group_code = '', $spec_code= '')
//    {
//        $v_village_id    = Session::get('village_id');
//        $spec_condition  = '';
//        $date_condition  = '';
//        
//        //neu co ou
////        if($group_code != '')
////        {
////            $group_condition = " And ExtractValue(C_XML_PROCESSING,'//next_task[last()]/@group_code') = '$group_code'";
////        }
//        //neu co spec code
//        if($spec_code != '' && $spec_code != null)
//        {
//            $spec_condition = " AND RT.C_SPEC_CODE = '$spec_code'";
//        }
//        //ngay thang
//        if($begin_date_yyyymmdd != '')
//        {
//            $date_condition = " And (DATEDIFF('$begin_date_yyyymmdd',C_REJECTED_DATE) <=0)";
//        }
//        
//        if($end_date_yyyymmdd != '')
//        {
//            $date_condition .= " And (DATEDIFF('$end_date_yyyymmdd', C_REJECTED_DATE) >=0)";
//        }
//         // Kiem tra linh vuc duoc phan quyen xem bao cao
//        $condition_permission_record_type = '';
//        $arr_record_type_permisssion_id = $this->_qry_all_record_type_id_permission();
//        if(sizeof($arr_record_type_permisssion_id) > 0)
//        {
//            $v_list_record_type_id = implode(', ', $arr_record_type_permisssion_id);
//            $condition_permission_record_type .= " AND FK_RECORD_TYPE in($v_list_record_type_id) ";
//        }
//        $sql          = "SELECT
//                    ExtractValue(C_XML_PROCESSING,'//next_task[last()]/@group_code') AS C_DOING_GROUP_CODE
//                    ,Case When (R.C_REJECTED = 1) Then 3 When (R.C_REJECTED <> 1 And (R.C_CLEAR_DATE Is Not Null)) Then 2 Else 1 End as C_ACTIVITY
//                    , CASE WHEN (DATEDIFF(NOW(), R.C_DOING_STEP_DEADLINE_DATE)>0) THEN (SELECT -1 * (COUNT(*)) FROM view_working_date WD WHERE DATEDIFF(WD.C_DATE, NOW())<=0 AND DATEDIFF(WD.C_DATE, R.C_DOING_STEP_DEADLINE_DATE)>0 ) ELSE (SELECT (COUNT(*)) FROM view_working_date WD WHERE DATEDIFF(WD.C_DATE, NOW())>=0 AND DATEDIFF(WD.C_DATE, R.C_DOING_STEP_DEADLINE_DATE)<0 ) END AS C_DOING_STEP_DAYS_REMAIN
//                    , CASE WHEN (DATEDIFF(NOW(),R.C_RETURN_DATE)>0) THEN (SELECT -1 * (COUNT(*)) FROM view_working_date WD WHERE DATEDIFF(WD.C_DATE, NOW())<=0 AND DATEDIFF(WD.C_DATE, R.C_RETURN_DATE)>0 ) ELSE (SELECT (COUNT(*)) FROM view_working_date WD WHERE DATEDIFF(WD.C_DATE, NOW())>=0 AND DATEDIFF(WD.C_DATE, R.C_RETURN_DATE)<0 ) END AS C_RETURN_DAYS_REMAIN
//                    , R.PK_RECORD
//                    , R.FK_RECORD_TYPE
//                    , R.C_RECORD_NO
//                    , CAST(R.C_RECEIVE_DATE AS CHAR(19)) AS C_RECEIVE_DATE
//                    , CAST(DATE_FORMAT(R.C_RECEIVE_DATE,'%d-%m-%Y %H:%i:%s') AS CHAR) AS C_RECEIVE_DATE_DDMMYYYY
//                    , CAST(R.C_RETURN_DATE AS CHAR(19)) AS C_RETURN_DATE
//                    , CAST(DATE_FORMAT(R.C_RETURN_DATE,'%d-%m-%Y %H:%i:%s') AS CHAR) AS C_RETURN_DATE_DDMMYYYY
//                    , R.C_RETURN_PHONE_NUMBER
//                    , R.C_XML_DATA
//                    , R.C_XML_PROCESSING
//                    , R.C_DELETED
//                    , R.C_CLEAR_DATE
//                    , R.C_XML_WORKFLOW
//                    , R.C_RETURN_EMAIL
//                    , R.C_REJECTED
//                    , R.C_REJECT_REASON
//                    , R.C_CITIZEN_NAME
//                    , R.C_ADVANCE_COST
//                    , R.C_CREATE_BY
//                    , R.C_NEXT_TASK_CODE
//                    , R.C_NEXT_USER_CODE
//                    , R.C_NEXT_CO_USER_CODE
//                    , R.C_LAST_TASK_CODE
//                    , R.C_LAST_USER_CODE
//                    , CAST(R.C_DOING_STEP_BEGIN_DATE AS CHAR(19)) AS C_DOING_STEP_BEGIN_DATE
//                    , R.C_DOING_STEP_DEADLINE_DATE
//                    , R.C_BIZ_DAYS_EXCEED
//                    , a.C_TASK_CODE
//                    , a.C_STEP_TIME
//                    , 8 as TT
//                    , CAST(DATE_FORMAT(C_REJECTED_DATE,'%d-%m-%Y %H:%i:%s') AS CHAR) AS C_REJECTED_DATE_DDMMYYYY
//                FROM
//                (
//                    SELECT
//                        RID.`PK_RECORD`
//                      , UT.C_TASK_CODE
//                      , UT.C_STEP_TIME
//                      , C_REJECTED_DATE
//                    FROM
//                    (
//                        SELECT
//                            PK_RECORD
//                          , C_NEXT_TASK_CODE
//                          , C_NEXT_USER_CODE
//                          , CAST(DATE_FORMAT(ExtractValue(C_XML_PROCESSING, '//step[@code=''REJECT''][last()]/datetime'),'%Y-%m-%d %H:%i:%s') AS CHAR) AS C_REJECTED_DATE
//                          , FK_RECORD_TYPE
//                        From view_record
//                        Where C_REJECTED=1 And FK_VILLAGE_ID = $v_village_id 
//                            $condition_permission_record_type
//                    )  RID LEFT JOIN t_r3_user_task UT ON (RID.C_NEXT_TASK_CODE = UT.C_TASK_CODE AND RID.C_NEXT_USER_CODE = UT.C_USER_LOGIN_NAME)
//                    LEFT JOIN t_r3_record_type RT 
//                    ON RID.FK_RECORD_TYPE = RT.PK_RECORD_TYPE 
//                    WHERE (1>0) $spec_condition
//                ) a LEFT JOIN view_record R ON a.PK_RECORD=R.PK_RECORD
//                Where (1>0) $date_condition
//                ORDER BY R.C_RECEIVE_DATE DESC
//                ";
//        return $this->db->getAll($sql);
//    }

//    public function qry_all_report_data_16($v_spec_code, $v_record_type_id, $v_begin_date_yyyymmdd, $v_end_date_yyyymmdd,$group_code='',$group_level)
//    {
//        $v_village_id = Session::get('village_id');
//         //neu co ou
//        $group_condition = '';
//        //neu co ou
//        if($v_village_id != 0) //neu la nguoi o xa
//        {
//            $group_condition = " AND FK_VILLAGE_ID = $v_village_id";
//        }
//        else //neu la nguoi o huyen
//        {
//            
//            if($group_code != '')//ton tai group code
//            {
//                if((int)$group_level == 3)//neu yeu cau bao cao cap xa
//                {
//                    $group_condition = " AND FK_VILLAGE_ID = $group_code";
//                }
//                else if((int) $group_level == 1 && $group_code == '0') //can bo cap huyen chi xem bao cao tai huyen
//                {
//                    $group_condition = " AND FK_VILLAGE_ID = 0";
//                }
//                else 
//                {
//                    $group_condition = " AND PK_RECORD IN (SELECT DISTINCT FK_RECORD FROM t_r3_bizdone_step_log WHERE C_GROUP_CODE = '$group_code')";
//                }
//            }
//        }
//        
//        $arr_report_filter = Array();
//        $sql               = "Select
//                    C_CITIZEN_NAME
//                    ,FK_RECORD_TYPE
//                    ,C_RECORD_NO
//                    ,C_RECEIVE_DATE
//                    ,C_RETURN_DATE
//                    ,CAST(DATE_FORMAT(ExtractValue(C_XML_PROCESSING, '//step[@code=''REJECT''][last()]/datetime'),'%d-%m-%Y %H:%i:%s') AS CHAR(19)) AS C_REJECTED_DATE_DDMMYYYY
//                    ,C_CLEAR_DATE
//                    ,C_XML_DATA
//              From view_record
//              Where 1>0 
//              $group_condition";
//        if ($v_spec_code != '')
//        {
//            //Danh sach linh vuc
//            $arr_all_spec = $this->assoc_list_get_all_by_listtype_code(_CONST_DANH_MUC_LINH_VUC, CONST_USE_ADODB_CACHE_FOR_REPORT);
//            array_push($arr_report_filter, array('Lĩnh vực: ', $arr_all_spec[$v_spec_code]));
//
//            if ($v_record_type_id > 0)
//            {
//                $sql .= " And FK_RECORD_TYPE=$v_record_type_id";
//
//                //Danh sach loai thu tuc
//                $arr_all_record_type = $this->db->cacheGetAssoc("Select PK_RECORD_TYPE, C_CODE, C_NAME From t_r3_record_type");
//                array_push($arr_report_filter, array('Loại hồ sơ: ', $arr_all_record_type[$v_record_type_id]['C_CODE'] . ' - ' . $arr_all_record_type[$v_record_type_id]['C_NAME']));
//            }
//            else
//            {
//                if (CONST_USE_ADODB_CACHE_FOR_REPORT)
//                {
//                    $v_record_type_id_list = implode(',', $this->db->CacheGetCol("Select PK_RECORD_TYPE From t_r3_record_type RT Where C_SPEC_CODE='$v_spec_code'"));
//                }
//                else
//                {
//                    $v_record_type_id_list = implode(',', $this->db->getCol("Select PK_RECORD_TYPE From t_r3_record_type RT Where C_SPEC_CODE='$v_spec_code'"));
//                }
//                $sql .= " And FK_RECORD_TYPE In ($v_record_type_id_list)";
//            }
//        }
//
//        if ($v_begin_date_yyyymmdd != '')
//        {
//            $sql .= " And (DATEDIFF('$v_begin_date_yyyymmdd',C_RECEIVE_DATE) <=0)";
//
//            array_push($arr_report_filter, array('Tiếp nhận từ ngày: ', $v_begin_date_yyyymmdd));
//        }
//        if ($v_end_date_yyyymmdd != '')
//        {
//            $sql .= " And (DATEDIFF('$v_end_date_yyyymmdd', C_RECEIVE_DATE) >=0)";
//            array_push($arr_report_filter, array('đến ngày: ', $v_end_date_yyyymmdd));
//        }
//         // Kiem tra linh vuc duoc phan quyen xem bao cao
//        $condition_permission_record_type = '';
//        $arr_record_type_permisssion_id = $this->_qry_all_record_type_id_permission();
//        if(sizeof($arr_record_type_permisssion_id) > 0)
//        {
//            $v_list_record_type_id = implode(', ', $arr_record_type_permisssion_id);
//            $condition_permission_record_type .= " AND FK_RECORD_TYPE in($v_list_record_type_id) ";
//        }
//        $sql .= $condition_permission_record_type;
//        
//        $ret['report_data']   = $this->db->getAll($sql);
//        $ret['report_filter'] = $arr_report_filter;
//        return $ret;
//    }

    public function qry_all_spec()
    {
        $arr_record_type_permission = array();
        $sql = "Select PK_LIST,C_CODE
                    From t_cores_list
                    Where FK_LISTTYPE = (SELECT
                                           PK_LISTTYPE
                                         From t_cores_listtype
                                         Where C_CODE = ?
                                             And C_STATUS = 1)
                        AND C_STATUS = 1";
        $arr_list     = $this->db->GetAll($sql,array(_CONST_DANH_MUC_LINH_VUC));
        
        if(sizeof($arr_list) > 0)
        {
            for($i=0;$i<sizeof($arr_list);$i++)
            {
                $v_list_code        = $arr_list[$i]['C_CODE'];
                $v_list_id          = $arr_list[$i]['PK_LIST'];
                 
                if(check_permission('BAOCAO_'.$v_list_code, 'r3'))
                {
                    $arr_record_type_permission = array_merge((array)$v_list_id,$arr_record_type_permission);
                }
            }
        }
        $condition = '';
        if(sizeof($arr_record_type_permission) >0) 
        {
            $v_list_id = implode(', ', $arr_record_type_permission);
            $condition = " and ls.PK_LIST in($v_list_id) ";
        }
       
        return $this->db->GetAssoc("
            Select ls.C_CODE, ls.C_NAME From t_cores_list ls
            Inner Join t_cores_listtype lt
                On ls.FK_LISTTYPE = lt.PK_LISTTYPE
            Where lt.C_CODE='DANH_MUC_LINH_VUC'
            $condition
        ");
    }

    
    /**
     * lay nam tu dong
     * @return type
     */
    public function get_year()
    {
        $sql = "SELECT
                    MAX(YEAR(C_RECEIVE_DATE)) AS C_MAX_YEAR,
                    MIN(YEAR(C_RECEIVE_DATE)) AS C_MIN_YEAR
                  FROM t_r3_record";
        return $this->db->getRow($sql);
    }
}