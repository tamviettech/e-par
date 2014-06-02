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

class calendar_Model extends Model {

    function __construct()
    {
        parent::__construct();
    }

    private function _qry_all_date_of_year($year,$filter=-1)
    {
        //Ngay trong tuan 7 => thu bay; 1 => Chu nhat; 2 => Thu 2; 3 => Thu 3; ...

        //So ngay cua nam: 365 ? 366
        $v_count_days_of_year = jwDate::dateDiff(1,1,$year,31,12,$year);
        $v_begin_date_string = $year. '-01-01';

        if (DATABASE_TYPE == 'MSSQL')
        {
            $sql = 'Select
                       b.C_DATE_YYMMDD
                       ,b.C_DATE_DDMMYY
                    From (
                        Select
                            a.C_DATE C_DATE_YYMMDD
                            , Convert(varchar(20),a.C_DATE,103) as C_DATE_DDMMYY
                            , DAYOFWEEK(a.C_DATE) C_WEEK_DAY
                        From (';
            $sql .= "Select '$v_begin_date_string' AS C_DATE";
            for ($i=1; $i<=$v_count_days_of_year; $i++)
            {
                $sql .= " Union Select DateAdd(day,$i, '$v_begin_date_string') AS C_DATE";
            }
        }
        elseif (DATABASE_TYPE == 'MYSQL')
        {
            $sql = "Select
                       b.C_DATE_YYMMDD
                       ,b.C_DATE_DDMMYY
                    From (
                        Select
                            a.C_DATE C_DATE_YYMMDD
                            , DATE_FORMAT(a.C_DATE,'%d-%m-%Y') AS C_DATE_DDMMYY
                            , DAYOFWEEK(a.C_DATE) C_WEEK_DAY
                        From (";
            $sql .= "Select '$v_begin_date_string' AS C_DATE";
            for ($i=1; $i<=$v_count_days_of_year; $i++)
            {
                $sql .= "\n Union Select AddDate('$v_begin_date_string', INTERVAL $i DAY) AS C_DATE";
            }
        }

        $sql .= ') a ) b ';

        $arr_default_date_off_ddmm = explode(',', _CONST_DEFAULT_DATE_OFF);
        $arr_all_dateoff_yyyymmdd = array();
        foreach ($arr_default_date_off_ddmm as $day_and_month)
        {
            $v_full_date_off_ddmmyyyy = str_replace('/', '-',trim($day_and_month)) . '-' .$year;
            $arr_all_dateoff_yyyymmdd[] = jwDate::ddmmyyyy_to_yyyymmdd($v_full_date_off_ddmmyyyy);
        }

        if ($filter == 0) //Chi lay ngay nghi
        {
            $sql .= ' Where b.C_WEEK_DAY In ( '. replace_bad_char(_CONST_DEFAULT_DW_OFF) . ')';
            foreach ($arr_all_dateoff_yyyymmdd as $date_off)
            {
                $sql .= " Or Datediff(b.C_DATE_YYMMDD, '$date_off')= 0";
            }
        }
        elseif ($filter == 1) //Chi lay ngay lam viec
        {
            $sql .= ' Where b.C_WEEK_DAY Not In ( '. replace_bad_char(_CONST_DEFAULT_DW_OFF) . ')';
            foreach ($arr_all_dateoff_yyyymmdd as $date_off)
            {
                $sql .= " And Datediff(b.C_DATE_YYMMDD, '$date_off') != 0";
            }
        }

        //return $this->db->CacheGetAssoc($v_count_days_of_year * 86400, $sql);
        return $this->db->GetAssoc($sql);
    }

    /**
     * Lay danh sach ngay nghi trong 1 nam
     * @param Int $year
     */
    public function qry_all_date_off($year)
    {
        //Kiem tra da khoi tao du lieu chua?
        if ($this->db->getOne('Select count(*) From t_cores_calendar Where C_YEAR=?', array($year)) > 0)
        {
            if (DATABASE_TYPE == 'MSSQL')
            {
                $stmt = 'Select C_DATE as C_DATE_YYMMDD
                            , Convert(VarChar(10), C_DATE, 103) C_DATE_DDMMYY
                        From t_cores_calendar
                        Where C_YEAR=? And C_OFF=1
                        Order By C_DATE Asc';
            }
            elseif (DATABASE_TYPE == 'MYSQL')
            {
                $stmt = "Select C_DATE as C_DATE_YYMMDD
                            , DATE_FORMAT(C_DATE,'%d-%m-%Y') AS C_DATE_DDMMYY
                        From t_cores_calendar
                        Where C_YEAR=? And C_OFF=1
                        Order By C_DATE Asc";
            }

            $params = array($year);
            return $this->db->getAssoc($stmt, $params);
        }
        else
        {
            return $this->_qry_all_date_of_year($year, 0);
        }
    }

    /**
     * Lay danh sach ngay lam viec trong mot nam
     * @param Int $year
     */
    public function qry_all_date_working($year)
    {
       //Kiem tra da khoi tao du lieu chua?
        if ($this->db->getOne('Select count(*) From t_cores_calendar Where C_YEAR=?', array($year)) > 0)
        {
            if (DATABASE_TYPE == 'MSSQL')
            {
                $stmt = 'Select C_DATE as C_DATE_YYMMDD
                            , Convert(VarChar(10), C_DATE, 103) C_DATE_DDMMYY
                        From t_cores_calendar
                        Where C_YEAR=? And C_OFF=0
                        Order By C_DATE Asc';
                $params = array($year);
            }
            elseif  (DATABASE_TYPE == 'MYSQL')
            {
                $stmt = "Select C_DATE as C_DATE_YYMMDD
                            , DATE_FORMAT(C_DATE,'%d-%m-%Y') AS C_DATE_DDMMYY
                        From t_cores_calendar
                        Where C_YEAR=? And C_OFF=0
                        Order By C_DATE Asc";
            }

            $params = array($year);
            return $this->db->getAssoc($stmt, $params);
        }
        else
        {
            return $this->_qry_all_date_of_year($year, 1);
        }//end if
    }

    public function update_calendar()
    {
        if (!isset($_POST['sel_year']))
        {
            $this->exec_done($this->goback_url);
            return;
        }

        $v_year         = $this->replace_bad_char($_POST['sel_year']);
        $arr_date_off   = explode(',', $this->replace_bad_char($_POST['hdn_date_off']));
        $arr_date_work  = explode(',', $this->replace_bad_char($_POST['hdn_date_work']));
        
        //var_dump($arr_date_off);
        //var_dump($arr_date_work);

        //$ok = TRUE;
        //$this->db->BeginTrans();

        //Delete
        $this->db->Execute('Delete From t_cores_calendar Where C_YEAR=?', array($v_year));

        //Insert date off
        foreach ($arr_date_off as $date)
        {
            $this->db->Execute('Insert Into t_cores_calendar(C_YEAR, C_DATE, C_OFF) Values(?,?,1)', array($v_year, $date));
        }
        //Insert date working
        foreach ($arr_date_work as $date)
        {
            $this->db->Execute('Insert Into t_cores_calendar(C_YEAR, C_DATE, C_OFF) Values(?,?,0)', array($v_year, $date));
        }

        unset($_POST);
        $this->exec_done($this->goback_url);
    }


}