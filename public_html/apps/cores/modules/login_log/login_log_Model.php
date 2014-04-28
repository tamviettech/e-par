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

class login_log_Model extends Model {

    function __construct()
    {
        parent::__construct();
    }

    public function login_log()
    {
        
        //Loc theo ten, ma
        $v_filter = get_post_var('txt_filter');

        page_calc($v_start, $v_end);

        $condition_query = '';
        if ($v_filter !== '')
        {
            $condition_query = " And (C_LOGIN_NAME Like '%$v_filter%')";
        }

        //Dem tong ban ghi
        $sql_count_record = "Select Count(*) t_cores_user_login_log From t_cores_listtype LT Where (1 > 0) $condition_query";
        
        $sql = 'Select *
                From t_cores_application
                Order By C_ORDER';
        return $this->db->getAll($sql);
    }
