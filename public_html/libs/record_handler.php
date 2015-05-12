<?php

/**
 * Sử dụng gần giống active record
 */
class record_handler extends Model
{

    /**
     * @var \ADOConnection
     */
    public $db;
    protected $_where;

    function __construct()
    {
        parent::__construct();
    }

    protected function get_query()
    {
        
    }

}