<?php 
/**
// File name   : 
// Version     : 1.0.0.1
// Begin       : 2012-12-01
// Last Update : 2010-12-25
// Author      : TamViet Technology, Ha Noi, Viet Nam. http://www.tamviettech.vn
// License     : GNU-LGPL v3 (http://www.gnu.org/copyleft/lesser.html)
// -------------------------------------------------------------------
//Copyright (C) 2012-2013  TamViet Technology, Ha Noi, Viet Nam. http://www.tamviettech.vn

// E-PAR is free software: you can redistribute it and/or modify it
// under the terms of the GNU Lesser General Public License as
// published by the Free Software Foundation, either version 3 of the
// License, or (at your option) any later version.
//
// E-PAR is distributed in the hope that it will be useful, but
// WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
// See the GNU Lesser General Public License for more details.
//
// See LICENSE.TXT file for more information.
*/
if (!defined('SERVER_ROOT')) exit('No direct script access allowed');

class blacklist_Model extends Model {

    function __construct()
    {
        parent::__construct();
    }
    
    public function qry_all_record_type_option()
    {
        if (DATABASE_TYPE == 'MSSQL')
        {
            $stmt = "Select RT.C_CODE, (RT.C_CODE + ' - ' + RT.C_NAME) as C_NAME From t_r3_record_type RT Where RT.C_STATUS > 0 Order By RT.C_CODE";
        }
        elseif (DATABASE_TYPE == 'MYSQL')
        {
            $stmt = "Select RT.C_CODE, Concat(RT.C_CODE, ' - ',  RT.C_NAME) as C_NAME From t_r3_record_type RT Where RT.C_STATUS > 0 Order By RT.C_CODE";
        }
        
        return $this->db->getAssoc($stmt);
    }
    
    public function qry_all_rule()
    {
        $stmt = 'Select 	
                    PK_RULE, 
                	C_RECORD_TYPE_CODE, 
                	C_NAME, 
                	C_OWNER_NAME, 
                	C_RULE_CONTENT, 
                    C_ORDER,
                	C_STATUS, 
                	C_BEGIN_DATE, 
                	C_END_DATE, 
                	C_ACTION,'
                    . $this->build_convert_date_query('C_BEGIN_DATE',103) . ' as C_BEGIN_DATE_DDMMYYYY, '
                    . $this->build_convert_date_query('C_END_DATE',103) . ' as C_END_DATE_DDMMYYYY '
        	. 'From t_r3_blacklist_rule
              Order By C_ORDER';
        $params = array();
        return $this->db->getAll($stmt, $params);
    }
    
    public function qry_single_rule($v_rule_id)
    {
        if ($v_rule_id > 0)
        {
            $stmt = 'Select 
                     	PK_RULE, 
                    	C_RECORD_TYPE_CODE, 
                    	C_NAME, 
                    	C_OWNER_NAME, 
                    	C_RULE_CONTENT, 
                        C_ORDER,
                    	C_STATUS, 
                    	C_BEGIN_DATE, 
                    	C_END_DATE, 
                    	C_ACTION                	 
                	FROM t_r3_blacklist_rule 
                    Where PK_RULE=?';
            $params = array($v_rule_id);
            
            return $this->db->getRow($stmt, $params);
        }
        else
        {
            return array('C_ORDER' => $this->get_max('t_r3_blacklist_rule', 'C_ORDER') + 1);
        }
    }
    
    public function do_update_rule()
    {
        $v_rule_id            = get_post_var('hdn_item_id',0);
        $v_name               = get_post_var('txt_name');
        $v_record_type_code   = get_post_var('sel_record_type',NULL);
        $v_begin_date         = get_post_var('txt_begin_date',date('d-m-Y'));
        $v_end_date           = get_post_var('txt_end_date','31-12-2099');
        $v_order              = get_post_var('txt_order',1);
        $v_status             = get_post_var('chk_status',0);
        $v_owner_name         = get_post_var('txt_owner_name');
        
        $arr_xml_tag          = get_post_var('txt_xml_tag',array());
        $arr_operator         = get_post_var('hdn_operator',array());
        $arr_xml_value        = get_post_var('txt_xml_value',array());
        
        $v_begin_date         = jwDate::ddmmyyyy_to_yyyymmdd($v_begin_date);
        $v_end_date           = jwDate::ddmmyyyy_to_yyyymmdd($v_end_date);
        
        //C_RULE_CONTENT
        $arr_rule_content = array();
        for ($i=0, $n=count($arr_xml_tag); $i<$n; $i++)
        {
            if (trim($arr_xml_tag[$i] != ''))
            {
                $arr_rule_content[]= array(
                                        'tag'         => $arr_xml_tag[$i],
                                        'operator'    => $arr_operator[$i],
                                        'value'       => $arr_xml_value[$i],
                                    );
            }
        }
        $v_rule_content = json_encode($arr_rule_content);
        $v_action = 0;
        
        if ($v_rule_id > 0)
        {
            $stmt = 'Update t_r3_blacklist_rule Set
                    	C_RECORD_TYPE_CODE = ?,
                    	C_NAME             = ?,
                    	C_OWNER_NAME       = ?,
                    	C_RULE_CONTENT     = ?,
                    	C_ORDER            = ?,
                    	C_STATUS           = ?,
                    	C_BEGIN_DATE       = ?,
                    	C_END_DATE         = ?,
                    	C_ACTION           = ?
                	Where
                	    PK_RULE            = ?';
            $params = array(
                        $v_record_type_code, 
                        $v_name, 
                        $v_owner_name,
                        $v_rule_content,
                        $v_order,
                        $v_status, 
                        $v_begin_date, 
                        $v_end_date,
                        $v_action,
                        $v_rule_id
                    );
            $this->db->Execute($stmt, $params);
        }
        else
        {
            $stmt = "INSERT INTO t_r3_blacklist_rule( 
                    	C_RECORD_TYPE_CODE, 
                    	C_NAME, 
                    	C_OWNER_NAME, 
                    	C_RULE_CONTENT, 
                    	C_ORDER, 
                    	C_STATUS, 
                    	C_BEGIN_DATE, 
                    	C_END_DATE, 
                    	C_ACTION
                    	)
                	VALUES(
                    	?, 
                    	?, 
                    	?, 
                    	?, 
                    	?, 
                    	?, 
                    	?, 
                    	?, 
                    	?
                	)";
            $params = array($v_record_type_code, $v_name, $v_owner_name, $v_rule_content,$v_order, $v_status, $v_begin_date, $v_end_date,$v_action);
            
            $this->db->Execute($stmt, $params);
            $v_rule_id = $this->get_last_inserted_id('t_r3_blacklist_rule');
        }
        
        $this->ReOrder('t_r3_blacklist_rule', 'PK_RULE', 'C_ORDER', $v_rule_id, -1);
        unset($_POST);
        $this->exec_done($this->goback_url);
    }
    
    public function do_delete_rule()
    {
        $v_item_id_list = get_post_var('hdn_item_id_list','');
        if ($v_item_id_list != '')
        {
            $sql = "Delete From t_r3_blacklist_rule Where PK_RULE In ($v_item_id_list)";
            $this->db->Execute($sql);
        }
        
        $this->exec_done($this->goback_url);
    }
}