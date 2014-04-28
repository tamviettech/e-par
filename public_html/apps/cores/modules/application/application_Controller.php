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

class application_Controller extends Controller {

    function __construct()
    {
        deny_bad_http_referer();
        
        parent::__construct('cores', 'application');
        $this->view->template->show_left_side_bar = FALSE;

        //Kiem tra dang nhap
        session::check_login();
    }

    function main()
    {
        //Kiem tra quyen
        (Session::get('is_admin') == 1) Or die($this->access_denied());

        $this->dsp_all_application();
    }

    function dsp_all_application()
    {
        //Kiem tra quyen
        (Session::get('is_admin') == 1) Or die($this->access_denied());

        $VIEW_DATA['arr_all_application'] = $this->model->qry_all_application();
        $this->view->render('dsp_all_application', $VIEW_DATA);
    }

    public function dsp_single_application($app_id)
    {
        //Kiem tra quyen
        (Session::get('is_admin') == 1) Or die($this->access_denied());

        $this->view->template->show_left_side_bar =FALSE;

        if (!( preg_match( '/^\d*$/', trim($app_id)) == 1 ))
        {
            $app_id = 0;
        }
        $VIEW_DATA['arr_single_application'] = $this->model->qry_single_application($app_id);

        $this->view->render('dsp_single_application', $VIEW_DATA);
    }

    public function update_application()
    {
        //Kiem tra quyen
        (Session::get('is_admin') == 1) Or die($this->access_denied());

        $this->model->goback_url = $this->view->get_controller_url() . 'dsp_all_application/';
        $this->model->update_application();
    }

    public function dsp_all_file_xml()
    {
        $this->view->render('dsp_all_file_xml');
    }

    public function delete_application()
    {
        //Kiem tra quyen
        (Session::get('is_admin') == 1) Or die($this->access_denied());

       $this->model->goback_url = $this->view->get_controller_url() . 'dsp_all_application/';
       $this->model->delete_application();
    }

    public function dsp_application_permit($app_id)
    {
        //Kiem tra quyen
        (Session::get('is_admin') == 1) Or die($this->access_denied());
        
        $this->view->template->show_left_side_bar =FALSE;

        if (!( preg_match( '/^\d*$/', trim($app_id)) == 1 ))
        {
            $app_id = 0;
        }
        $VIEW_DATA['arr_single_application'] = $this->model->qry_single_application($app_id);

        $this->view->render('dsp_application_permit', $VIEW_DATA);
    }

}