<?php
/**
Copyright (C) 2012 Tam Viet Tech.

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