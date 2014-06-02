<?php
/**


This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
?>
<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed');

class application_Model extends Model {

    function __construct()
    {
        parent::__construct();
    }

    public function qry_all_application()
    {
        $sql = 'Select *
                From t_cores_application
                Order By C_ORDER';
        return $this->db->getAll($sql);
    }

    public function qry_single_application($app_id)
    {
        if ($app_id > 0)
        {
            $stmt = 'Select
                        PK_APPLICATION
                        ,C_CODE
                        ,C_NAME
                        ,C_XML_PERMIT_FILE_NAME
                        ,C_ORDER
                        ,C_STATUS
                        ,C_XML_DATA
                        ,C_DESCRIPTION
                        ,C_DEFAULT_MODULE
                    From t_cores_application Where PK_APPLICATION=?';
            $this->db->debug = 0;
            return $this->db->getRow($stmt, array($app_id));
        }

        return array(
            'PK_APPLICATION'            => 0
            ,'C_CODE'                   => ''
            ,'C_NAME'                   => ''
            ,'C_XML_PERMIT_FILE_NAME'   => ''
            ,'C_ORDER'                  => $this->get_max('t_cores_application', 'C_ORDER') + 1
            ,'C_STATUS'                 => 1
            ,'C_DESCRIPTION'            => ''
            ,'C_DEFAULT_MODULE'         => ''
            ,'C_XML_DATA'               => '<root></root>'
        );
    }

    public function update_application()
    {
        $v_application_id       = get_post_var('hdn_item_id',0);
        $v_code                 = get_post_var('txt_code');
        $v_name                 = get_post_var('txt_name');
        $v_permit_xml_file_name = get_post_var('txt_xml_file_name');
        $v_order                = get_post_var('txt_order');
        $v_order                = get_post_var('txt_order');
        $v_status               = isset($_POST['chk_status']) ? 1 : 0;
        $v_xml_data             = isset($_POST['XmlData']) ? $_POST['XmlData'] : '<data/>';
        $v_default_module       = get_post_var('txt_default_module');
        $v_description          = get_post_var('txt_description');

        //Kiem tra trung ma
        $stmt = 'Select Count(*) From t_cores_application Where C_CODE=? And PK_APPLICATION <> ?';
        $v_check_code = $this->db->getOne($stmt, array($v_code, $v_application_id));
        if ($v_check_code > 0)
        {
            $this->exec_fail($this->goback_url, 'Mã ứng dụng đã tồn tại!');
            return;
        }

        //Kiem tra trung ten
        $stmt = 'Select Count(*) From t_cores_application Where C_NAME=? And PK_APPLICATION <> ?';
        $v_check_name = $this->db->getOne($stmt, array($v_name, $v_application_id));
        if ($v_check_name > 0)
        {
            $this->exec_fail($this->goback_url, 'Tên ứng dụng đã tồn tại!');
            return;
        }

        if ($v_application_id < 1)
        {
            $stmt = 'Insert Into t_cores_application (
                                    C_CODE
                                    ,C_NAME
                                    ,C_XML_PERMIT_FILE_NAME
                                    ,C_ORDER
                                    ,C_STATUS
                                    ,C_XML_DATA
                                    ,C_DESCRIPTION
                                    ,C_DEFAULT_MODULE
                            ) Values (
                                    ?,
                                    ?,
                                    ?,
                                    ?,
                                    ?,
                                    ?,
                                    ?,
                                    ?
                            )';
            $params = array(
                            $v_code,
                            $v_name,
                            $v_permit_xml_file_name,
                            $v_order,
                            $v_status,
                            $v_xml_data,
                            $v_description,
                            $v_default_module
            );

            $this->db->Execute($stmt, $params);

            $v_application_id = $this->get_last_inserted_id('t_cores_application','PK_APPLICATION');
        }
        else
        {
            $stmt = 'Update t_cores_application Set
                                    C_CODE = ?
                                    ,C_NAME = ?
                                    ,C_XML_PERMIT_FILE_NAME = ?
                                    ,C_ORDER = ?
                                    ,C_STATUS = ?
                                    ,C_XML_DATA = ?
                                    ,C_DESCRIPTION=?
                                    ,C_DEFAULT_MODULE=?
                     Where PK_APPLICATION=?';

            $params = array(
                            $v_code,
                            $v_name,
                            $v_permit_xml_file_name,
                            $v_order,
                            $v_status,
                            $v_xml_data,
                            $v_description,
                            $v_default_module,
                            $v_application_id
            );

            $this->db->Execute($stmt, $params);
        }

        $this->ReOrder('t_cores_application','PK_APPLICATION','C_ORDER', $v_application_id, $v_order);

        $this->exec_done($this->goback_url);
    }

    public function delete_application()
    {
        $v_item_id_list = get_post_var('hdn_item_id_list','');

        if ($v_item_id_list != '')
        {
            $sql = "Delete From t_cores_application Where PK_APPLICATION in ($v_item_id_list)";
            $this->db->Execute($sql);
        }

        $this->exec_done($this->goback_url);
    }
}