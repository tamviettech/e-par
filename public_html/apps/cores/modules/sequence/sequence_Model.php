<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed');

class sequence_Model extends Model {

    function __construct()
    {
        parent::__construct();
    }

    public function next_val($seq_name)
    {
        $seq_name = $this->replace_bad_char($seq_name);
        $this->db->debug=0;
        return $this->get_max($seq_name,'SEQVAL') + 1;
        //return $this->get_new_seq_val($seq_name);
    }
}