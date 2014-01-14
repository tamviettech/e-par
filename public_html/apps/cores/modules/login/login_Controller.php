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

if (!defined('SERVER_ROOT'))
    exit('No direct script access allowed');

class login_Controller extends Controller
{

    function __construct()
    {
        parent::__construct('cores', 'login');
        $this->model->goback_url = $this->view->get_controller_url();
    }

    public function main()
    {
        $this->dsp_login();
    }

    public function dsp_login()
    {
        session::init();
        if (empty($_SERVER['HTTPS']))
        {
            //header('location:' . 'https://demo.e-par.vn/cores/login/');
        }
        $this->view->render('dsp_login');
    }

    public function do_login()
    {
        session::destroy();
        session::init();
        session_regenerate_id(true);
        $this->model->goback_url = $this->view->get_controller_url();
        $this->model->do_login();
    }

    public function do_logout()
    {
        session::destroy();
        session::init();   
        session_regenerate_id(true);
        #header('location:' . SITE_ROOT);
        $this->dsp_login();
    }

    public function svc_check_session_token($token)
    {
        header('Content-type: application/json');
        echo json_encode(validate_session_token($token));
    }

}