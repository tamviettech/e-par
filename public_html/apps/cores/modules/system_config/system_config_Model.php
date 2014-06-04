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
<?php
defined('DS') or die('no direct access');

class system_config_Model extends Model{
    function __construct()
    {
        parent::__construct();
    }
    
    function update_options()
    {
        $xml = (get_post_var('XmlData', '', false));
        $controller = get_post_var('controller');
        if($xml == '')
        {
            $this->exec_done($controller);
        }
        $dbkey = SYSTEM_CONFIG;
        if(DATABASE_TYPE == 'MSSQL')
        {
            $stmt = "
                If(Exists(Select PK_OPTION From t_cores_option Where C_OPTION_KEY = ?))
                    Update t_cores_option Set C_OPTION_VALUE = ? where C_OPTION_KEY = ?
                Else
                    Insert Into t_cores_option(C_OPTION_KEY, C_OPTION_VALUE) Values(?, ?)
                ";
            $params = array(
                $dbkey
                ,$xml, $dbkey
                ,$dbkey, $xml
            );
            $this->db->Execute($stmt, $params);
        }
        else if(DATABASE_TYPE == 'MYSQL')
        {
            $stmt = "Select COUNT(PK_OPTION) From t_cores_option Where C_OPTION_KEY = ?";
            $v_count = $this->db->getOne($stmt,array($dbkey));
            if($v_count > 0)
            {
                $stmt = "Update t_cores_option Set C_OPTION_VALUE = ? where C_OPTION_KEY = ?";
                $this->db->Execute($stmt, array($xml,$dbkey));
            }
            else 
            {
                $stmt = "Insert Into t_cores_option(C_OPTION_KEY, C_OPTION_VALUE) Values(?, ?)";
                $this->db->Execute($stmt, array($dbkey,$xml));
            }
        }
        
        if($this->db->errorNo() == 0)
        {
            $this->exec_done($controller);
        }
        else{
            $this->exec_fail($controller, __('update failed'));
        }
    }
    
    function qry_all_system_config_item()
    {
        $stmt = "Select C_OPTION_VALUE From t_cores_option Where C_OPTION_KEY = ?";
        $params = array('SYSTEM_CONFIG');
        return xml_add_declaration($this->db->getOne($stmt, $params));
    }
}