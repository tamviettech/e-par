<?php
/**
Copyright (C) 2012 Tam Viet Tech. All rights reserved.

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

defined('SERVER_ROOT') or die();

/**
 * @package logistic
 * @author Tam Viet <ltbinh@gmail.com>
 * @author Duong Tuan Anh <goat91@gmail.com>
 */
class logistic_Controller extends Controller
{

    /**
     *
     * @var \logistic_Model 
     */
    public $model;

    /**
     *
     * @var View 
     */
    public $view;

    function __construct()
    {
        parent::__construct('r3', 'logistic');
        $this->view->template->show_left_side_bar = false;
        Session::init();
        Session::get('user_id') or die('Bạn cần đăng nhập');
        check_permission('THEO_DOI_NGUOI_DUNG', 'R3') or die('Bạn không có quyền thực hiện chức năng này');
    }

    function main()
    {
        $this->view->render('dsp_main');
    }

    function dsp_new_actions()
    {
        $VIEW_DATA['arr_new_actions'] = $this->model->qry_new_actions();
        $this->view->render('dsp_new_actions', $VIEW_DATA);
    }

}