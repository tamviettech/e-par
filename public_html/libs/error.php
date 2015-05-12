<?php

class Error extends Controller {

    function __construct() {
        parent::__construct('cores','error');        
        $this->view->msg = "This page doesnt exist!";
    }
    
    public function main(){
        echo $this->view->msg;
    }

}