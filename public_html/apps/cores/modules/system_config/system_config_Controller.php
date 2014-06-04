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

class system_config_Controller extends Controller
{

    function __construct()
    {
        parent::__construct('cores', 'system_config');
        Session::init();
        //Kiem tra dang nhap
        session::check_login();
        Session::get('is_admin') or $this->access_denied();
    }

    function main()
    {
        $data['xml_data'] = $this->model->qry_all_system_config_item();
        
        $this->view->render('dsp_main', $data);
    }
    
    function update_options()
    {
        $this->model->update_options();
    }

}

?>
