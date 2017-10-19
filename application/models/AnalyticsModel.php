<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class AnalyticsModel extends CI_Model{

    function __construct() {
        parent::__construct();
    }
    
    public function getCountPerDay($kecamatan="",$mode=""){
        if($mode!=""){
            return self::getCountPerMode($kecamatan,$mode);
        }
        date_default_timezone_set("Asia/Makassar"); 
        $analyticsDB = $this->load->database('analytics', TRUE);
        $query  = $analyticsDB->query("SHOW TABLES FROM analytics");
        
        //retrieve the tables name
        $tables = array();
        foreach ($query->result() as $table){
            if($table->Tables_in_analytics[0]=='v'){
                continue;
            }else array_push($tables, $table->Tables_in_analytics);
        }
        
       if($kecamatan=='Sengkol'){
            $users = ['user8'=>'Ketara','user9'=>'Sengkol','user10'=>'Sengkol','user11'=>'Kawo','user12'=>'Tanak Awu','user13'=>'Pengembur','user14'=>'Segala Anyar'];
        }elseif($kecamatan=='Janapria'){
            $users = ['user1'=>'Lekor','user2'=>'Saba','user3'=>'Pendem','user4'=>'Setuta','user5'=>'Jango','user6'=>'Janapria'];
        }else{
            return;
        }
        
        //make result array from the tables name
        $result_data = array();
        foreach ($users as $user=>$desa){
            $data = array();
            for($i=1;$i<=30;$i++){
                $day     = 30-$i;
                $date    = date("Y-m-d",  strtotime("-".$day." days"));
                $data[$date] = 0;
            }
            $result_data[$desa] = $data;
        }
        
        
        //retrieve all the columns in the table
        $columns = array();
        foreach ($tables as $table){
            $query = $analyticsDB->query("SHOW COLUMNS FROM ".$table);
            foreach ($query->result() as $column){
                array_push($columns, $column->Field);
            }
            
            //query tha data
            if($kecamatan=='Sengkol'){
                $query = $analyticsDB->query("SELECT userid,DATE(clientVersionSubmissionDate) as submissiondate,count(*) as counts from ".$table." where (userid='user8' or userid='user9' or userid='user10' or userid='user11' or userid='user12' or userid='user13' or userid='user14') and (submissiondate >= '".date("Y-m-d",strtotime("-30 days"))."' and submissiondate <= '".date("Y-m-d")."') group by userid, submissiondate");
            }
            elseif($kecamatan=='Janapria'){
                $query = $analyticsDB->query("SELECT userid,DATE(clientVersionSubmissionDate) as submissiondate,count(*) as counts from ".$table." where (userid='user1' or userid='user2' or userid='user3' or userid='user4' or userid='user5' or userid='user6') and (submissiondate >= '".date("Y-m-d",strtotime("-30 days"))."' and submissiondate <= '".date("Y-m-d")."') group by userid, submissiondate");
            }
            else{
                $query = $analyticsDB->query("SELECT userid,DATE(clientVersionSubmissionDate) as submissiondate,count(*) as counts from ".$table." where (submissiondate >= '2015-06-24' and submissiondate <= '2015-07-24') group by userid, submissiondate");
            }
            foreach ($query->result() as $datas){
                if(array_key_exists($datas->userid, $users)){
                    $data_count                  = $result_data[$users[$datas->userid]];
                    if(array_key_exists($datas->submissiondate, $data_count)){
                        $data_count[$datas->submissiondate] +=$datas->counts;
                    }
                    $result_data[$users[$datas->userid]] = $data_count;
                }
                
            }
        }
        
        return $result_data;
    }
    
    public function getCountPerDayDrill($kecamatan="",$mode="",$range=""){
        if($mode!=""){
            return self::getCountPerMode($kecamatan,$mode);
        }
        date_default_timezone_set("Asia/Makassar"); 
        $analyticsDB = $this->load->database('analytics', TRUE);
        $query  = $analyticsDB->query("SHOW TABLES FROM analytics");
        
        //retrieve the tables name
        $tables = array();
        foreach ($query->result() as $table){
            if($table->Tables_in_analytics[0]=='v'){
                continue;
            }else array_push($tables, $table->Tables_in_analytics);
        }
        
       if($kecamatan=='Sengkol'){
            $users = ['user8'=>'Ketara','user9'=>'Sengkol','user10'=>'Sengkol','user11'=>'Kawo','user12'=>'Tanak Awu','user13'=>'Pengembur','user14'=>'Segala Anyar'];
        }elseif($kecamatan=='Janapria'){
            $users = ['user1'=>'Lekor','user2'=>'Saba','user3'=>'Pendem','user4'=>'Setuta','user5'=>'Jango','user6'=>'Janapria'];
        }else{
            return;
        }
        
        //make result array from the tables name
        $result_data = array();
        if($range!=""){
            foreach ($users as $user=>$desa){
                $begin = new DateTime($range[0]);
                $end = new DateTime($range[1]);
                $data = array();
                for($i=$begin;$begin<=$end;$i->modify('+1 day')){
                    $date    = $i->format("Y-m-d");
                    $data[$date] = 0;
                }
                $result_data[$desa] = $data;
            }
        }else{
            foreach ($users as $user=>$desa){
                $data = array();
                for($i=1;$i<=30;$i++){
                    $day     = 30-$i;
                    $date    = date("Y-m-d",  strtotime("-".$day." days"));
                    $data[$date] = 0;
                }
                $result_data[$desa] = $data;
            }
        }
        
        
        //retrieve all the columns in the table
        $columns = array();
        foreach ($tables as $table){
            $query = $analyticsDB->query("SHOW COLUMNS FROM ".$table);
            foreach ($query->result() as $column){
                array_push($columns, $column->Field);
            }
            
            //query tha data
            if($range!=""){
                if($kecamatan=='Sengkol'){
                    $query = $analyticsDB->query("SELECT userid,DATE(clientVersionSubmissionDate) as submissiondate,count(*) as counts from ".$table." where (userid='user8' or userid='user9' or userid='user10' or userid='user11' or userid='user12' or userid='user13' or userid='user14') and (DATE(clientVersionSubmissionDate) >= '".$range[0]."' and DATE(clientVersionSubmissionDate) <= '".$range[1]."') group by userid, DATE(clientVersionSubmissionDate)");
                }
                elseif($kecamatan=='Janapria'){
                    $query = $analyticsDB->query("SELECT userid,DATE(clientVersionSubmissionDate) as submissiondate,count(*) as counts from ".$table." where (userid='user1' or userid='user2' or userid='user3' or userid='user4' or userid='user5' or userid='user6') and (DATE(clientVersionSubmissionDate) >= '".$range[0]."' and DATE(clientVersionSubmissionDate) <= '".$range[1]."') group by userid, DATE(clientVersionSubmissionDate)");
                }
            }else{
                if($kecamatan=='Sengkol'){
                    $query = $analyticsDB->query("SELECT userid,DATE(clientVersionSubmissionDate) as submissiondate,count(*) as counts from ".$table." where (userid='user8' or userid='user9' or userid='user10' or userid='user11' or userid='user12' or userid='user13' or userid='user14') and (DATE(clientVersionSubmissionDate) >= '".date("Y-m-d",strtotime("-30 days"))."' and DATE(clientVersionSubmissionDate) <= '".date("Y-m-d")."') group by userid, DATE(clientVersionSubmissionDate)");
                }
                elseif($kecamatan=='Janapria'){
                    $query = $analyticsDB->query("SELECT userid,DATE(clientVersionSubmissionDate) as submissiondate,count(*) as counts from ".$table." where (userid='user1' or userid='user2' or userid='user3' or userid='user4' or userid='user5' or userid='user6') and (DATE(clientVersionSubmissionDate) >= '".date("Y-m-d",strtotime("-30 days"))."' and DATE(clientVersionSubmissionDate) <= '".date("Y-m-d")."') group by userid, DATE(clientVersionSubmissionDate)");
                }
            }
            
            foreach ($query->result() as $datas){
                if(array_key_exists($datas->userid, $users)){
                    $data_count                  = $result_data[$users[$datas->userid]];
                    if(array_key_exists($datas->submissiondate, $data_count)){
                        $data_count[$datas->submissiondate] +=$datas->counts;
                    }
                    $result_data[$users[$datas->userid]] = $data_count;
                }
                
            }
        }
//        var_dump($result_data);
        return $result_data;
    }
    
    public function getCountPerDayByVisitDate($kecamatan="",$mode="",$range=""){
        if($mode!=""){
            return self::getCountPerMode($kecamatan,$mode);
        }
        date_default_timezone_set("Asia/Makassar"); 
        $analyticsDB = $this->load->database('analytics', TRUE);
        $query  = $analyticsDB->query("SHOW TABLES FROM analytics");
        $table_default = [
            'kartu_anc_visit'=>'Kunjungan ANC',
            'kartu_pnc_regitration_oa'=>'Registrasi PNC',
            'kartu_pnc_dokumentasi_persalinan'=>'Dokumentasi Persalinan',
            'kartu_pnc_visit'=>'Kunjungan PNC',
            'kohort_bayi_neonatal_period'=>'Kunjungan Neonatal',
            'kohort_bayi_kunjungan'=>'Kunjungan Bayi',
            'kohort_bayi_immunization'=>'Imunisasi Bayi',
            'kohort_kb_pelayanan'=>'Pelayanan KB',
            'kohort_kb_update'=>'Update KB'];
        //retrieve the tables name
        $tables = array();
        foreach ($query->result() as $table){
            if($table->Tables_in_analytics[0]=='v'){
                continue;
            }else {
                if(array_key_exists($table->Tables_in_analytics, $table_default)){
                    array_push($tables, $table->Tables_in_analytics);
                }
                
            }
        }
        
       if($kecamatan=='Sengkol'){
            $users = ['user8'=>'Ketara','user9'=>'Sengkol','user10'=>'Sengkol','user11'=>'Kawo','user12'=>'Tanak Awu','user13'=>'Pengembur','user14'=>'Segala Anyar'];
        }elseif($kecamatan=='Janapria'){
            $users = ['user1'=>'Lekor','user2'=>'Saba','user3'=>'Pendem','user4'=>'Setuta','user5'=>'Jango','user6'=>'Janapria'];
        }else{
            return;
        }
        
        //make result array from the tables name
        $result_data = array();
        if($range!=""){
            foreach ($users as $user=>$desa){
                $begin = new DateTime($range[0]);
                $end = new DateTime($range[1]);
                $data = array();
                for($i=$begin;$begin<=$end;$i->modify('+1 day')){
                    $date    = $i->format("Y-m-d");
                    $data[$date] = 0;
                }
                $result_data[$desa] = $data;
            }
        }else{
            foreach ($users as $user=>$desa){
                $data = array();
                for($i=1;$i<=30;$i++){
                    $day     = 30-$i;
                    $date    = date("Y-m-d",  strtotime("-".$day." days"));
                    $data[$date] = 0;
                }
                $result_data[$desa] = $data;
            }
        }
        
        
        //retrieve all the columns in the table
        $columns = array();
        foreach ($tables as $table){
            //query tha data
            if($range!=""){
                if($table=="kartu_anc_visit"){
                    $query = $analyticsDB->query("SELECT userid, ancDate as visitdate,count(*) as counts from ".$table." where (ancDate >= '".$range[0]."' and ancDate <= '".$range[1]."') group by userid, ancDate");
                }elseif($table=="kartu_pnc_regitration_oa"){
                    $query = $analyticsDB->query("SELECT userid, tanggalLahir as visitdate,count(*) as counts from ".$table." where (tanggalLahir >= '".$range[0]."' and tanggalLahir <= '".$range[1]."') group by userid, tanggalLahir");
                }elseif($table=="kartu_pnc_dokumentasi_persalinan"){
                    $query = $analyticsDB->query("SELECT userid, tanggalLahirAnak as visitdate,count(*) as counts from ".$table." where (tanggalLahirAnak >= '".$range[0]."' and tanggalLahirAnak <= '".$range[1]."') group by userid, tanggalLahirAnak");
                }elseif($table=="kartu_pnc_visit"){
                    $query = $analyticsDB->query("SELECT userid, referenceDate as visitdate,count(*) as counts from ".$table." where (referenceDate >= '".$range[0]."' and referenceDate <= '".$range[1]."') group by userid, referenceDate");
                }elseif($table=="kohort_bayi_kunjungan"){
                    $query = $analyticsDB->query("SELECT userid, tanggalKunjunganBayiPerbulan as visitdate,count(*) as counts from ".$table." where (tanggalKunjunganBayiPerbulan >= '".$range[0]."' and tanggalKunjunganBayiPerbulan <= '".$range[1]."') group by userid, tanggalKunjunganBayiPerbulan");
                }elseif($table=="kohort_kb_pelayanan"&&$table=="kohort_kb_update"){
                    $query = $analyticsDB->query("SELECT userid, tanggalkunjungan as visitdate,count(*) as counts from ".$table." where (tanggalkunjungan >= '".$range[0]."' and tanggalkunjungan <= '".$range[1]."') group by userid, tanggalkunjungan");
                }else{
                    $query = $analyticsDB->query("SELECT userid, submissiondate as visitdate,count(*) as counts from ".$table." where (submissiondate >= '".$range[0]."' and submissiondate <= '".$range[1]."') group by userid, submissiondate");
                }
            }else{
                if($table=="kartu_anc_visit"){
                    $query = $analyticsDB->query("SELECT userid, ancDate as visitdate,count(*) as counts from ".$table." where (ancDate >= '".date("Y-m-d",strtotime("-30 days"))."' and ancDate <= '".date("Y-m-d")."') group by userid, ancDate");
                }elseif($table=="kartu_pnc_regitration_oa"){
                    $query = $analyticsDB->query("SELECT userid, tanggalLahir as visitdate,count(*) as counts from ".$table." where (tanggalLahir >= '".date("Y-m-d",strtotime("-30 days"))."' and tanggalLahir <= '".date("Y-m-d")."') group by userid, tanggalLahir");
                }elseif($table=="kartu_pnc_dokumentasi_persalinan"){
                    $query = $analyticsDB->query("SELECT userid, tanggalLahirAnak as visitdate,count(*) as counts from ".$table." where (tanggalLahirAnak >= '".date("Y-m-d",strtotime("-30 days"))."' and tanggalLahirAnak <= '".date("Y-m-d")."') group by userid, tanggalLahirAnak");
                }elseif($table=="kartu_pnc_visit"){
                    $query = $analyticsDB->query("SELECT userid, referenceDate as visitdate,count(*) as counts from ".$table." where (referenceDate >= '".date("Y-m-d",strtotime("-30 days"))."' and referenceDate <= '".date("Y-m-d")."') group by userid, referenceDate");
                }elseif($table=="kohort_bayi_kunjungan"){
                    $query = $analyticsDB->query("SELECT userid, tanggalKunjunganBayiPerbulan as visitdate,count(*) as counts from ".$table." where (tanggalKunjunganBayiPerbulan >= '".date("Y-m-d",strtotime("-30 days"))."' and tanggalKunjunganBayiPerbulan <= '".date("Y-m-d")."') group by userid, tanggalKunjunganBayiPerbulan");
                }elseif($table=="kohort_kb_pelayanan"&&$table=="kohort_kb_update"){
                    $query = $analyticsDB->query("SELECT userid, tanggalkunjungan as visitdate,count(*) as counts from ".$table." where (tanggalkunjungan >= '".date("Y-m-d",strtotime("-30 days"))."' and tanggalkunjungan <= '".date("Y-m-d")."') group by userid, tanggalkunjungan");
                }else{
                    $query = $analyticsDB->query("SELECT userid, submissiondate as visitdate,count(*) as counts from ".$table." where (submissiondate >= '".date("Y-m-d",strtotime("-30 days"))."' and submissiondate <= '".date("Y-m-d")."') group by userid, submissiondate");
                }
            }
            
            foreach ($query->result() as $datas){
                if(array_key_exists($datas->userid, $users)){
                    $data_count                  = $result_data[$users[$datas->userid]];
                    if(array_key_exists($datas->visitdate, $data_count)){
                        $data_count[$datas->visitdate] +=$datas->counts;
                    }
                    $result_data[$users[$datas->userid]] = $data_count;
                }
                
            }
        }
//        var_dump($result_data);
        return $result_data;
    }
    
    public function getCountPerMode($kecamatan="",$mode="Mingguan"){
        date_default_timezone_set("Asia/Makassar"); 
        $analyticsDB = $this->load->database('analytics', TRUE);
        $query  = $analyticsDB->query("SHOW TABLES FROM analytics");
        
        //retrieve the tables name
        $tables = array();
        foreach ($query->result() as $table){
            if($table->Tables_in_analytics[0]=='v'){
                continue;
            }else array_push($tables, $table->Tables_in_analytics);
        }
        
        if($kecamatan=='Sengkol'){
            $users = ['user8'=>'Ketara','user9'=>'Sengkol','user10'=>'Sengkol','user11'=>'Kawo','user12'=>'Tanak Awu','user13'=>'Pengembur','user14'=>'Segala Anyar'];
        }elseif($kecamatan=='Janapria'){
            $users = ['user1'=>'Lekor','user2'=>'Saba','user3'=>'Pendem','user4'=>'Setuta','user5'=>'Jango','user6'=>'Janapria'];
        }else{
            return;
        }
        
        //make result array from the tables name
        $result_data = array();
        $now    = date("Y-m-d");
        foreach ($users as $user=>$desa){
            $data = array();
            
            if($mode=='Mingguan'){
                $data['thisweek'] = array();
                $data['lastweek'] = array();                       
                $day_temp = array();
                for($i=1;$i<=6;$i++){
                    $days     = 6-$i;
                    $date    = date("Y-m-d",  strtotime((!(date('N', strtotime($now)) >= 6)?"next Saturday ":"")."-".$days." days"));
                    $day_temp[$date] = 0;
                }
                $data['thisweek'] = $day_temp;
                $day_temp = array();
                for($i=1;$i<=6;$i++){
                    $days     = 6-$i;
                    $date    = date("Y-m-d",  strtotime((!(date('N', strtotime($now)) >= 6)?"last Saturday ":"")."-".$days." days"));
                    $day_temp[$date] = 0;
                }
                $data['lastweek'] = $day_temp;
                
            }elseif($mode=='Bulanan'){
                $data['thisyear'] = array();
                $data['lastyear'] = array();
                $this_month = date("n");
                $month  = array();
                for($i=1;$i<=12;$i++){
                    $date   = date("Y-m",strtotime("+".(-$this_month+$i)." months"));
                    $month[$date]   =   0;
                }
                $data['thisyear'] = $month;
                $month  = array();
                for($i=1;$i<=12;$i++){
                    $date   = date("Y-m",strtotime("+".(-$this_month+$i-12)." months"));
                    $month[$date]   =   0;
                }
                $data['lastyear'] = $month;
            }
            $result_data[$desa] = $data;
        }
        
        
        //retrieve all the columns in the table
        $columns = array();
        foreach ($tables as $table){
            $query = $analyticsDB->query("SHOW COLUMNS FROM ".$table);
            foreach ($query->result() as $column){
                array_push($columns, $column->Field);
            }
            
            //query tha data
            if($kecamatan=='Sengkol'){
                if($mode=='Mingguan'){
                    $query = $analyticsDB->query("SELECT userid,DATE(clientVersionSubmissionDate) as submissiondate,count(*) as counts from ".$table." where (userid='user8' or userid='user9' or userid='user10' or userid='user11' or userid='user12' or userid='user13' or userid='user14') and (DATE(clientVersionSubmissionDate) >= '".date("Y-m-d",  strtotime((!(date('N', strtotime($now)) >= 6)?"last Saturday ":"")."-5 days"))."' and DATE(clientVersionSubmissionDate) <= '".date("Y-m-d",  strtotime((!(date('N', strtotime($now)) >= 6)?"next Saturday ":"")))."') group by userid, DATE(clientVersionSubmissionDate)");
                }elseif($mode=='Bulanan'){
                    $query = $analyticsDB->query("SELECT userid,DATE(clientVersionSubmissionDate) as submissiondate,count(*) as counts from ".$table." where (userid='user8' or userid='user9' or userid='user10' or userid='user11' or userid='user12' or userid='user13' or userid='user14') and (DATE(clientVersionSubmissionDate) >= '".date("Y-m",strtotime("+".(-$this_month-11)." months"))."' and DATE(clientVersionSubmissionDate) <= '".date("Y-m",strtotime("+".(12-$this_month)." months"))."') group by userid, DATE(clientVersionSubmissionDate)");
                }
            }
            elseif($kecamatan=='Janapria'){
                if($mode=='Mingguan'){
                    $query = $analyticsDB->query("SELECT userid,DATE(clientVersionSubmissionDate) as submissiondate,count(*) as counts from ".$table." where (userid='user1' or userid='user2' or userid='user3' or userid='user4' or userid='user5' or userid='user6') and (DATE(clientVersionSubmissionDate) >= '".date("Y-m-d",  strtotime((!(date('N', strtotime($now)) >= 6)?"last Saturday ":"")."-5 days"))."' and DATE(clientVersionSubmissionDate) <= '".date("Y-m-d",  strtotime((!(date('N', strtotime($now)) >= 6)?"next Saturday ":"")))."') group by userid, DATE(clientVersionSubmissionDate)");
                }elseif($mode=='Bulanan'){
                    $query = $analyticsDB->query("SELECT userid,DATE(clientVersionSubmissionDate) as submissiondate,count(*) as counts from ".$table." where (userid='user1' or userid='user2' or userid='user3' or userid='user4' or userid='user5' or userid='user6') and (DATE(clientVersionSubmissionDate) >= '".date("Y-m",strtotime("+".(-$this_month-11)." months"))."' and DATE(clientVersionSubmissionDate) <= '".date("Y-m",strtotime("+".(12-$this_month)." months"))."') group by userid, DATE(clientVersionSubmissionDate)");
                }
            }
            else{
                $query = $analyticsDB->query("SELECT userid,DATE(clientVersionSubmissionDate) as submissiondate,count(*) as counts from ".$table." where (DATE(clientVersionSubmissionDate) >= '2015-06-24' and DATE(clientVersionSubmissionDate) <= '2015-07-24') group by userid, DATE(clientVersionSubmissionDate)");
            }
            foreach ($query->result() as $datas){
                if(array_key_exists($datas->userid, $users)){
                    if($mode=='Mingguan'){
                        $week   =   $result_data[$users[$datas->userid]];
                        $thisweek   = $week['thisweek'];
                        $lastweek   = $week['lastweek'];
                        if(array_key_exists($datas->submissiondate, $thisweek)){
                            $thisweek[$datas->submissiondate] +=$datas->counts;
                        }
                        if(array_key_exists($datas->submissiondate, $lastweek)){
                            $lastweek[$datas->submissiondate] +=$datas->counts;
                        }
                        $week['thisweek'] = $thisweek;
                        $week['lastweek'] = $lastweek;
                        $result_data[$users[$datas->userid]] = $week;
                    }elseif($mode=='Bulanan'){
                        $month = $result_data[$users[$datas->userid]];
                        $thisyear = $month['thisyear'];
                        $lastyear = $month['lastyear'];
                        $m = explode('-', $datas->submissiondate);
                        array_pop($m);
                        $datas->submissiondate = implode('-',$m);
                        if(array_key_exists($datas->submissiondate, $thisyear)){
                            $thisyear[$datas->submissiondate] +=$datas->counts;
                        }
                        if(array_key_exists($datas->submissiondate, $lastyear)){
                            $lastyear[$datas->submissiondate] +=$datas->counts;
                        }
                        $month['thisyear'] = $thisyear;
                        $month['lastyear'] = $lastyear;
                        $result_data[$users[$datas->userid]] = $month;
                    }
                }
                
            }
        }
        
        return $result_data;
    }
    
    public function downloadCountPerForm($kecamatan="",$start,$end,$old="no"){
        $start = new DateTime($start);
        $end = $end1 = new DateTime($end);
        $end = $end->modify('+1 day'); 
        $interval = new DateInterval('P1D');
        $daterange = new DatePeriod($start, $interval ,$end);
        
        $this->load->library('PHPExcell');
        if($old=="yes"){
            $analyticsDB = $this->load->database('analytics_old', TRUE);
            $query  = $analyticsDB->query("SHOW TABLES FROM analytics_prot2_old");
        }else{
            $analyticsDB = $this->load->database('analytics', TRUE);
            $query  = $analyticsDB->query("SHOW TABLES FROM analytics");
        }
        
        $table_default = [
            'kartu_ibu_registration'=>'Registrasi Ibu',
            'kohort_kb_registration'=>'Registrasi KB',
            'kartu_anc_registration'=>'Registrasi ANC',
            'kartu_anc_registration_oa'=>'Registrasi ANC',
            'kartu_anc_rencana_persalinan'=>'Rencana Persalinan',
            'kartu_anc_visit'=>'Kunjungan ANC',
            'kartu_pnc_regitration_oa'=>'Registrasi PNC',
            'kartu_pnc_dokumentasi_persalinan'=>'Dokumentasi Persalinan',
            'kartu_pnc_visit'=>'Kunjungan PNC',
            'kohort_bayi_registration'=>'Registrasi Anak',
            'kohort_bayi_registration_oa'=>'Registrasi Anak',
            'kohort_bayi_neonatal_period'=>'Kunjungan Neonatal',
            'kohort_bayi_kunjungan'=>'Kunjungan Bayi',
            'kartu_anc_close'=>'kartu_anc_close',
            'kartu_anc_edit'=>'kartu_anc_edit',
            'kartu_anc_visit_edit'=>'kartu_anc_visit_edit',
            'kartu_anc_visit_integrasi'=>'kartu_anc_visit_integrasi',
            'kartu_anc_visit_labTest'=>'kartu_anc_visit_labTest',
            'kartu_ibu_close'=>'kartu_ibu_close',
            'kartu_ibu_edit'=>'kartu_ibu_edit',
            'kartu_pnc_close'=>'kartu_pnc_close',
            'kartu_pnc_edit'=>'kartu_pnc_edit',
            'kohort_anak_tutup'=>'kohort_anak_tutup',
            'kohort_bayi_edit'=>'kohort_bayi_edit',
            'kohort_bayi_immunization'=>'kohort_bayi_immunization',
            'kohort_kb_close'=>'kohort_kb_close',
            'kohort_kb_edit'=>'kohort_kb_edit',
            'kohort_kb_pelayanan'=>'kohort_kb_pelayanan',
            'kohort_kb_update'=>'kohort_kb_update'];
        $table_name = [
            'Registrasi Ibu',
            'Registrasi KB',
            'Registrasi ANC',
            'Rencana Persalinan',
            'Kunjungan ANC',
            'Registrasi PNC',
            'Dokumentasi Persalinan',
            'Kunjungan PNC',
            'Registrasi Anak',
            'Kunjungan Neonatal',
            'Kunjungan Bayi',
            'kartu_anc_close',
            'kartu_anc_edit',
            'kartu_anc_visit_edit',
            'kartu_anc_visit_integrasi',
            'kartu_anc_visit_labTest',
            'kartu_ibu_close',
            'kartu_ibu_edit',
            'kartu_pnc_close',
            'kartu_pnc_edit',
            'kohort_anak_tutup',
            'kohort_bayi_edit',
            'kohort_bayi_immunization',
            'kohort_kb_close',
            'kohort_kb_edit',
            'kohort_kb_pelayanan',
            'kohort_kb_update'];
        //retrieve the tables name
        $tables = array();
        if($old=="yes"){
            foreach ($query->result() as $table){
                if(array_key_exists($table->Tables_in_analytics_prot2_old, $table_default)){
                    $tables[$table->Tables_in_analytics_prot2_old]=$table_default[$table->Tables_in_analytics_prot2_old];
                }
            }
        }else{
            foreach ($query->result() as $table){
                if(array_key_exists($table->Tables_in_analytics, $table_default)){
                    $tables[$table->Tables_in_analytics]=$table_default[$table->Tables_in_analytics];
                }
            }
        }
        
        if($kecamatan=='Sengkol'){
            $users = ['user8'=>'Ketara','user9'=>'Sengkol','user10'=>'Sengkol','user11'=>'Kawo','user12'=>'Tanak Awu','user13'=>'Pengembur','user14'=>'Segala Anyar'];
        }elseif($kecamatan=='Janapria'){
            $users = ['user1'=>'Lekor','user2'=>'Saba','user3'=>'Pendem','user4'=>'Setuta','user5'=>'Jango','user6'=>'Janapria'];
        }else{
            return;
        }
        
        $result_data = array();
        foreach ($users as $user=>$desa){
            $data = array();
            foreach($daterange as $date){
                $data[$date->format("Y-m-d")] = array();
                foreach ($table_default as $table=>$legend){
                    $table_data = 0;
                    $data[$date->format("Y-m-d")][$legend] = $table_data;
                }
            }
            $result_data[$desa] = $data;
        }
        
        foreach ($tables as $table=>$legend){
            //query tha data
            foreach($daterange as $date){
                if($kecamatan=='Sengkol'){
                    $query = $analyticsDB->query("SELECT userid,DATE(clientVersionSubmissionDate) as submissiondate,count(*) as counts from ".$table." where (userid='user8' or userid='user9' or userid='user10' or userid='user11' or userid='user12' or userid='user13' or userid='user14') AND DATE(clientVersionSubmissionDate) = '".$date->format("Y-m-d")."' group by userid, submissiondate");
                }
                elseif($kecamatan=='Janapria'){
                    $query = $analyticsDB->query("SELECT userid,DATE(clientVersionSubmissionDate) as submissiondate,count(*) as counts from ".$table." where (userid='user1' or userid='user2' or userid='user3' or userid='user4' or userid='user5' or userid='user6') AND DATE(clientVersionSubmissionDate) = '".$date->format("Y-m-d")."' group by userid, submissiondate");
                }
                foreach ($query->result() as $datas){
                    if(array_key_exists($datas->userid, $users)){
                        $data_count                  = $result_data[$users[$datas->userid]][$date->format("Y-m-d")];
                        $data_count[$legend]         += $datas->counts;
                        $result_data[$users[$datas->userid]][$date->format("Y-m-d")] = $data_count;
                    }
                }
            }
            
        }
        
        $fileObject = new PHPExcel();
        $sheetIndex = $fileObject->getIndex(
            $fileObject->getSheetByName('Worksheet')
        );
        $fileObject->removeSheetByIndex($sheetIndex);
        $index = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF'];
        foreach ($result_data as $nama_desa=>$table_data){
            $myWorkSheet = new PHPExcel_Worksheet($fileObject, $nama_desa);
            $fileObject->addSheet($myWorkSheet);
            $fileObject->setActiveSheetIndexByName($nama_desa);
            $fileObject->getActiveSheet()->setCellValue('A1', 'Tanggal');
            $col = 0;
            foreach ($table_name as $name){
                $fileObject->getActiveSheet()->setCellValue($index[++$col].'1', $name);
            }
            $row = 2;
            foreach ($table_data as $date=>$tdata){
                $col = 0;
                $fileObject->getActiveSheet()->setCellValue($index[$col++].$row, $date);
                foreach ($tdata as $tname=>$value){
                    $fileObject->getActiveSheet()->setCellValue($index[$col++].$row, $value);
                }
                $row++;
            }
            $row = 2;
            $fileObject->getActiveSheet()->setCellValue($index[$col+1].'1', 'TOTAL');
            foreach ($table_data as $date=>$tdata){
                $fileObject->getActiveSheet()->setCellValue($index[$col+1].$row, '=SUM('.'B'.$row.':'.$index[$col].$row.')');
                $row++;
            }
            $fileObject->getActiveSheet()->setCellValue('A'.$row, "TOTAL");
            $col = 1;
            foreach ($table_name as $name){
                $fileObject->getActiveSheet()->setCellValue($index[$col].$row, '=SUM('.$index[$col].'2:'.$index[$col].($row-1).')');
                $col++;
            }
        }
        ob_end_clean();
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Dataentryform-'.$kecamatan.'-'.$start->format("Ymd").'-'.$end1->format("Ymd").'.xlsx"'); 
        header('Cache-Control: max-age=0');

        $saveContainer = PHPExcel_IOFactory::createWriter($fileObject,'Excel2007');
        $saveContainer->save('php://output');
        
    }
    
    public function downloadCountPerFormByVisitDate($kecamatan="",$start,$end){
        $start = new DateTime($start);
        $end = $end1 = new DateTime($end);
        $end = $end->modify('+1 day'); 
        $interval = new DateInterval('P1D');
        $daterange = new DatePeriod($start, $interval ,$end);
        
        $this->load->library('PHPExcell');
        $analyticsDB = $this->load->database('analytics', TRUE);
        $query  = $analyticsDB->query("SHOW TABLES FROM analytics");
        
        $table_default = [
            'kartu_anc_visit'=>'Kunjungan ANC',
            'kartu_pnc_regitration_oa'=>'Registrasi PNC',
            'kartu_pnc_dokumentasi_persalinan'=>'Dokumentasi Persalinan',
            'kartu_pnc_visit'=>'Kunjungan PNC',
            'kohort_bayi_neonatal_period'=>'Kunjungan Neonatal',
            'kohort_bayi_kunjungan'=>'Kunjungan Bayi',
            'kohort_bayi_immunization'=>'Imunisasi Bayi',
            'kohort_kb_pelayanan'=>'Pelayanan KB',
            'kohort_kb_update'=>'Update KB'];
        $table_name = [
            'Kunjungan ANC',
            'Registrasi PNC',
            'Dokumentasi Persalinan',
            'Kunjungan PNC',
            'Kunjungan Neonatal',
            'Kunjungan Bayi',
            'Imunisasi Bayi',
            'Pelayanan KB',
            'Update KB'];
        //retrieve the tables name
        $tables = array();
        foreach ($query->result() as $table){
            if(array_key_exists($table->Tables_in_analytics, $table_default)){
                $tables[$table->Tables_in_analytics]=$table_default[$table->Tables_in_analytics];
            }
        }
        
        if($kecamatan=='Sengkol'){
            $users = ['user8'=>'Ketara','user9'=>'Sengkol','user10'=>'Sengkol','user11'=>'Kawo','user12'=>'Tanak Awu','user13'=>'Pengembur','user14'=>'Segala Anyar'];
        }elseif($kecamatan=='Janapria'){
            $users = ['user1'=>'Lekor','user2'=>'Saba','user3'=>'Pendem','user4'=>'Setuta','user5'=>'Jango','user6'=>'Janapria'];
        }else{
            return;
        }
        
        $result_data = array();
        foreach ($users as $user=>$desa){
            $data = array();
            foreach($daterange as $date){
                $data[$date->format("Y-m-d")] = array();
                foreach ($table_default as $table=>$legend){
                    $table_data = 0;
                    $data[$date->format("Y-m-d")][$legend] = $table_data;
                }
            }
            $result_data[$desa] = $data;
        }
        
        foreach ($tables as $table=>$legend){
            //query tha data
            foreach($daterange as $date){
                if($table=="kartu_anc_visit"){
                    $query = $analyticsDB->query("SELECT userid, ancDate,count(*) as counts from ".$table." where ancDate = '".$date->format("Y-m-d")."' group by userid, ancDate");
                }elseif($table=="kartu_pnc_regitration_oa"){
                    $query = $analyticsDB->query("SELECT userid, tanggalLahir,count(*) as counts from ".$table." where tanggalLahir = '".$date->format("Y-m-d")."' group by userid, tanggalLahir");
                }elseif($table=="kartu_pnc_dokumentasi_persalinan"){
                    $query = $analyticsDB->query("SELECT userid, tanggalLahirAnak,count(*) as counts from ".$table." where tanggalLahirAnak = '".$date->format("Y-m-d")."' group by userid, tanggalLahirAnak");
                }elseif($table=="kartu_pnc_visit"){
                    $query = $analyticsDB->query("SELECT userid, referenceDate,count(*) as counts from ".$table." where referenceDate = '".$date->format("Y-m-d")."' group by userid, referenceDate");
                }elseif($table=="kohort_bayi_kunjungan"){
                    $query = $analyticsDB->query("SELECT userid, tanggalKunjunganBayiPerbulan,count(*) as counts from ".$table." where tanggalKunjunganBayiPerbulan = '".$date->format("Y-m-d")."' group by userid, tanggalKunjunganBayiPerbulan");
                }elseif($table=="kohort_kb_pelayanan"&&$table=="kohort_kb_update"){
                    $query = $analyticsDB->query("SELECT userid, tanggalkunjungan,count(*) as counts from ".$table." where tanggalkunjungan = '".$date->format("Y-m-d")."' group by userid, tanggalkunjungan");
                }else{
                    $query = $analyticsDB->query("SELECT userid, submissiondate,count(*) as counts from ".$table." where submissiondate = '".$date->format("Y-m-d")."' group by userid, submissiondate");
                }
                    
                foreach ($query->result() as $datas){
                    if(array_key_exists($datas->userid, $users)){
                        $data_count                  = $result_data[$users[$datas->userid]][$date->format("Y-m-d")];
                        $data_count[$legend]         += $datas->counts;
                        $result_data[$users[$datas->userid]][$date->format("Y-m-d")] = $data_count;
                    }
                }
            }
            
        }
        
        $fileObject = new PHPExcel();
        $sheetIndex = $fileObject->getIndex(
            $fileObject->getSheetByName('Worksheet')
        );
        $fileObject->removeSheetByIndex($sheetIndex);
        $index = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE'];
        foreach ($result_data as $nama_desa=>$table_data){
            $myWorkSheet = new PHPExcel_Worksheet($fileObject, $nama_desa);
            $fileObject->addSheet($myWorkSheet);
            $fileObject->setActiveSheetIndexByName($nama_desa);
            $fileObject->getActiveSheet()->setCellValue('A1', 'Tanggal');
            $col = 0;
            foreach ($table_name as $name){
                $fileObject->getActiveSheet()->setCellValue($index[++$col].'1', $name);
            }
            $row = 2;
            foreach ($table_data as $date=>$tdata){
                $col = 0;
                $fileObject->getActiveSheet()->setCellValue($index[$col++].$row, $date);
                foreach ($tdata as $tname=>$value){
                    $fileObject->getActiveSheet()->setCellValue($index[$col++].$row, $value);
                }
                $row++;
            }
            $fileObject->getActiveSheet()->setCellValue('A'.$row, "TOTAL");
            $col = 1;
            foreach ($table_name as $name){
                $fileObject->getActiveSheet()->setCellValue($index[$col].$row, '=SUM('.$index[$col].'2:'.$index[$col].($row-1).')');
                $col++;
            }
        }
        ob_end_clean();
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Dataentryformbyvisitdate-'.$kecamatan.'-'.$start->format("Ymd").'-'.$end1->format("Ymd").'.xlsx"'); 
        header('Cache-Control: max-age=0');

        $saveContainer = PHPExcel_IOFactory::createWriter($fileObject,'Excel2007');
        $saveContainer->save('php://output');
        
    }
    
    public function getCountPerForm($kecamatan="",$start,$end,$old="no"){
        $end = date("Y-m-d",  strtotime($end." +1 day"));
        if($old=="yes"){
            $analyticsDB = $this->load->database('analytics_old', TRUE);
            $query  = $analyticsDB->query("SHOW TABLES FROM analytics_prot2_old");
        }else{
            $analyticsDB = $this->load->database('analytics', TRUE);
            $query  = $analyticsDB->query("SHOW TABLES FROM analytics");
        }
        $table_default = [
            'kartu_ibu_registration'=>'Registrasi Ibu',
            'kohort_kb_registration'=>'Registrasi KB',
            'kartu_anc_registration'=>'Registrasi ANC',
            'kartu_anc_registration_oa'=>'Registrasi ANC',
            'kartu_anc_rencana_persalinan'=>'Rencana Persalinan',
            'kartu_anc_visit'=>'Kunjungan ANC',
            'kartu_pnc_regitration_oa'=>'Registrasi PNC',
            'kartu_pnc_dokumentasi_persalinan'=>'Dokumentasi Persalinan',
            'kartu_pnc_visit'=>'Kunjungan PNC',
            'kohort_bayi_registration'=>'Registrasi Anak',
            'kohort_bayi_registration_oa'=>'Registrasi Anak',
            'kohort_bayi_neonatal_period'=>'Kunjungan Neonatal',
            'kohort_bayi_kunjungan'=>'Kunjungan Bayi',
            'kartu_anc_close'=>'kartu_anc_close',
            'kartu_anc_edit'=>'kartu_anc_edit',
            'kartu_anc_visit_edit'=>'kartu_anc_visit_edit',
            'kartu_anc_visit_integrasi'=>'kartu_anc_visit_integrasi',
            'kartu_anc_visit_labTest'=>'kartu_anc_visit_labTest',
            'kartu_ibu_close'=>'kartu_ibu_close',
            'kartu_ibu_edit'=>'kartu_ibu_edit',
            'kartu_pnc_close'=>'kartu_pnc_close',
            'kartu_pnc_edit'=>'kartu_pnc_edit',
            'kohort_anak_tutup'=>'kohort_anak_tutup',
            'kohort_bayi_edit'=>'kohort_bayi_edit',
            'kohort_bayi_immunization'=>'kohort_bayi_immunization',
            'kohort_kb_close'=>'kohort_kb_close',
            'kohort_kb_edit'=>'kohort_kb_edit',
            'kohort_kb_pelayanan'=>'kohort_kb_pelayanan',
            'kohort_kb_update'=>'kohort_kb_update'];
        //retrieve the tables name
        $tables = array();
        if($old=="yes"){
            foreach ($query->result() as $table){
                if(array_key_exists($table->Tables_in_analytics_prot2_old, $table_default)){
                    $tables[$table->Tables_in_analytics_prot2_old]=$table_default[$table->Tables_in_analytics_prot2_old];
                }
            }
        }else{
            foreach ($query->result() as $table){
                if(array_key_exists($table->Tables_in_analytics, $table_default)){
                    $tables[$table->Tables_in_analytics]=$table_default[$table->Tables_in_analytics];
                }
            }
        }
        
        
        if($kecamatan=='Sengkol'){
            $users = ['user8'=>'Ketara','user9'=>'Sengkol','user10'=>'Sengkol','user11'=>'Kawo','user12'=>'Tanak Awu','user13'=>'Pengembur','user14'=>'Segala Anyar'];
        }elseif($kecamatan=='Janapria'){
            $users = ['user1'=>'Lekor','user2'=>'Saba','user3'=>'Pendem','user4'=>'Setuta','user5'=>'Jango','user6'=>'Janapria'];
        }else{
            return;
        }
        
        //make result array from the tables name
        $result_data = array();
        foreach ($users as $user=>$desa){
            $data = array();
            foreach ($table_default as $table=>$legend){
                $table_name = 0;
                $data[$legend] = $table_name;
            }
            $result_data[$desa] = $data;
        }

        foreach ($tables as $table=>$legend){
            //query tha data
            if($kecamatan=='Sengkol'){
                $query = $analyticsDB->query("SELECT userid,DATE(clientVersionSubmissionDate) as submissiondate,count(*) as counts from ".$table." where (userid='user8' or userid='user9' or userid='user10' or userid='user11' or userid='user12' or userid='user13' or userid='user14') AND clientVersionSubmissionDate >= '$start' AND clientVersionSubmissionDate <= '$end' group by userid, submissiondate");
            }
            elseif($kecamatan=='Janapria'){
                $query = $analyticsDB->query("SELECT userid,DATE(clientVersionSubmissionDate) as submissiondate,count(*) as counts from ".$table." where (userid='user1' or userid='user2' or userid='user3' or userid='user4' or userid='user5' or userid='user6') AND clientVersionSubmissionDate >= '$start' AND clientVersionSubmissionDate <= '$end' group by userid, submissiondate");
            }
            foreach ($query->result() as $datas){
                if(array_key_exists($datas->userid, $users)){
                    $data_count                  = $result_data[$users[$datas->userid]];
                    $data_count[$legend]         += $datas->counts;
                    $result_data[$users[$datas->userid]] = $data_count;
                }
            }
        }
        
        return $result_data;
    }
    
    public function getCountPerFormByVisitDate($kecamatan="",$start,$end,$old="no"){
        $end = date("Y-m-d",  strtotime($end." +1 day"));
        if($old=="yes"){
            $analyticsDB = $this->load->database('analytics_old', TRUE);
            $query  = $analyticsDB->query("SHOW TABLES FROM analytics_prot2_old");
        }else{
            $analyticsDB = $this->load->database('analytics', TRUE);
            $query  = $analyticsDB->query("SHOW TABLES FROM analytics");
        }
        $table_default = [
            'kartu_anc_visit'=>'Kunjungan ANC',
            'kartu_pnc_regitration_oa'=>'Registrasi PNC',
            'kartu_pnc_dokumentasi_persalinan'=>'Dokumentasi Persalinan',
            'kartu_pnc_visit'=>'Kunjungan PNC',
            'kohort_bayi_neonatal_period'=>'Kunjungan Neonatal',
            'kohort_bayi_kunjungan'=>'Kunjungan Bayi',
            'kohort_bayi_immunization'=>'Imunisasi Bayi',
            'kohort_kb_pelayanan'=>'Pelayanan KB',
            'kohort_kb_update'=>'Update KB'];
        //retrieve the tables name
        $tables = array();
        if($old=="yes"){
            foreach ($query->result() as $table){
                if(array_key_exists($table->Tables_in_analytics_prot2_old, $table_default)){
                    $tables[$table->Tables_in_analytics_prot2_old]=$table_default[$table->Tables_in_analytics_prot2_old];
                }
            }
        }else{
            foreach ($query->result() as $table){
                if(array_key_exists($table->Tables_in_analytics, $table_default)){
                    $tables[$table->Tables_in_analytics]=$table_default[$table->Tables_in_analytics];
                }
            }
        }
        
        
        if($kecamatan=='Sengkol'){
            $users = ['user8'=>'Ketara','user9'=>'Sengkol','user10'=>'Sengkol','user11'=>'Kawo','user12'=>'Tanak Awu','user13'=>'Pengembur','user14'=>'Segala Anyar'];
        }elseif($kecamatan=='Janapria'){
            $users = ['user1'=>'Lekor','user2'=>'Saba','user3'=>'Pendem','user4'=>'Setuta','user5'=>'Jango','user6'=>'Janapria'];
        }else{
            return;
        }
        
        //make result array from the tables name
        $result_data = array();
        foreach ($users as $user=>$desa){
            $data = array();
            foreach ($table_default as $table=>$legend){
                $table_name = 0;
                $data[$legend] = $table_name;
            }
            $result_data[$desa] = $data;
        }

        foreach ($tables as $table=>$legend){
            if($table=="kartu_anc_visit"){
                $query = $analyticsDB->query("SELECT userid, ancDate,count(*) as counts from ".$table." where ancDate >= '$start' AND ancDate < '$end' group by userid, ancDate");
            }elseif($table=="kartu_pnc_regitration_oa"){
                $query = $analyticsDB->query("SELECT userid, tanggalLahir,count(*) as counts from ".$table." where tanggalLahir >= '$start' AND tanggalLahir < '$end' group by userid, tanggalLahir");
            }elseif($table=="kartu_pnc_dokumentasi_persalinan"){
                $query = $analyticsDB->query("SELECT userid, tanggalLahirAnak,count(*) as counts from ".$table." where tanggalLahirAnak >= '$start' AND tanggalLahirAnak < '$end' group by userid, tanggalLahirAnak");
            }elseif($table=="kartu_pnc_visit"){
                $query = $analyticsDB->query("SELECT userid, referenceDate,count(*) as counts from ".$table." where referenceDate >= '$start' AND referenceDate < '$end' group by userid, referenceDate");
            }elseif($table=="kohort_bayi_kunjungan"){
                $query = $analyticsDB->query("SELECT userid, tanggalKunjunganBayiPerbulan,count(*) as counts from ".$table." where tanggalKunjunganBayiPerbulan >= '$start' AND tanggalKunjunganBayiPerbulan < '$end' group by userid, tanggalKunjunganBayiPerbulan");
            }elseif($table=="kohort_kb_pelayanan"&&$table=="kohort_kb_update"){
                $query = $analyticsDB->query("SELECT userid, tanggalkunjungan,count(*) as counts from ".$table." where tanggalkunjungan >= '$start' AND tanggalkunjungan < '$end' group by userid, tanggalkunjungan");
            }else{
                $query = $analyticsDB->query("SELECT userid, submissiondate,count(*) as counts from ".$table." where submissiondate >= '$start' AND submissiondate < '$end' group by userid, submissiondate");
            }
            
            
            foreach ($query->result() as $datas){
                if(array_key_exists($datas->userid, $users)){
                    $data_count                  = $result_data[$users[$datas->userid]];
                    $data_count[$legend]         += $datas->counts;
                    $result_data[$users[$datas->userid]] = $data_count;
                }
            }
        }
        
        return $result_data;
    }
    
    public function getCountPerFormForDrill($desa="",$date=""){
        $analyticsDB = $this->load->database('analytics', TRUE);
        $query  = $analyticsDB->query("SHOW TABLES FROM analytics");
        $table_default = [
            'kartu_ibu_registration'=>'Registrasi Ibu',
            'kohort_kb_registration'=>'Registrasi KB',
            'kartu_anc_registration'=>'Registrasi ANC',
            'kartu_anc_registration_oa'=>'Registrasi ANC',
            'kartu_anc_rencana_persalinan'=>'Rencana Persalinan',
            'kartu_anc_visit'=>'Kunjungan ANC',
            'kartu_pnc_regitration_oa'=>'Registrasi PNC',
            'kartu_pnc_dokumentasi_persalinan'=>'Dokumentasi Persalinan',
            'kartu_pnc_visit'=>'Kunjungan PNC',
            'kohort_bayi_registration'=>'Registrasi Anak',
            'kohort_bayi_registration_oa'=>'Registrasi Anak',
            'kohort_bayi_neonatal_period'=>'Kunjungan Neonatal',
            'kohort_bayi_kunjungan'=>'Kunjungan Bayi',
            'kartu_anc_close'=>'kartu_anc_close',
            'kartu_anc_edit'=>'kartu_anc_edit',
            'kartu_anc_visit_edit'=>'kartu_anc_visit_edit',
            'kartu_anc_visit_integrasi'=>'kartu_anc_visit_integrasi',
            'kartu_anc_visit_labTest'=>'kartu_anc_visit_labTest',
            'kartu_ibu_close'=>'kartu_ibu_close',
            'kartu_ibu_edit'=>'kartu_ibu_edit',
            'kartu_pnc_close'=>'kartu_pnc_close',
            'kartu_pnc_edit'=>'kartu_pnc_edit',
            'kohort_anak_tutup'=>'kohort_anak_tutup',
            'kohort_bayi_edit'=>'kohort_bayi_edit',
            'kohort_bayi_immunization'=>'kohort_bayi_immunization',
            'kohort_kb_close'=>'kohort_kb_close',
            'kohort_kb_edit'=>'kohort_kb_edit',
            'kohort_kb_pelayanan'=>'kohort_kb_pelayanan',
            'kohort_kb_update'=>'kohort_kb_update'];
        $tabindex = [
            'kartu_ibu_registration'=>0,
            'kohort_kb_registration'=>1,
            'kartu_anc_registration'=>2,
            'kartu_anc_registration_oa'=>3,
            'kartu_anc_rencana_persalinan'=>4,
            'kartu_anc_visit'=>5,
            'kartu_pnc_regitration_oa'=>6,
            'kartu_pnc_dokumentasi_persalinan'=>7,
            'kartu_pnc_visit'=>8,
            'kohort_bayi_registration'=>9,
            'kohort_bayi_registration_oa'=>10,
            'kohort_bayi_neonatal_period'=>11,
            'kohort_bayi_kunjungan'=>12,
            'kartu_anc_close'=>13,
            'kartu_anc_edit'=>14,
            'kartu_anc_visit_edit'=>15,
            'kartu_anc_visit_integrasi'=>16,
            'kartu_anc_visit_labTest'=>17,
            'kartu_ibu_close'=>18,
            'kartu_ibu_edit'=>19,
            'kartu_pnc_close'=>20,
            'kartu_pnc_edit'=>21,
            'kohort_anak_tutup'=>22,
            'kohort_bayi_edit'=>23,
            'kohort_bayi_immunization'=>24,
            'kohort_kb_close'=>25,
            'kohort_kb_edit'=>26,
            'kohort_kb_pelayanan'=>27,
            'kohort_kb_update'=>28];
        //retrieve the tables name
        $tables = array();
        foreach ($query->result() as $table){
            if(array_key_exists($table->Tables_in_analytics, $table_default)){
                $tables[$table->Tables_in_analytics]=$table_default[$table->Tables_in_analytics];
            }
        }
        if($desa=="Lekor"){
            $users = ['user1'=>'Lekor'];
        }elseif($desa=="Saba"){
            $users = ['user2'=>'Saba'];
        }elseif($desa=="Pendem"){
            $users = ['user3'=>'Pendem'];
        }elseif($desa=="Setuta"){
            $users = ['user4'=>'Setuta'];
        }elseif($desa=="Jango"){
            $users = ['user5'=>'Jango'];
        }elseif($desa=="Janapria"){
            $users = ['user6'=>'Janapria'];
        }elseif($desa=="Ketara"){
            $users = ['user8'=>'Ketara'];
        }elseif($desa=="Sengkol"){
            $users = ['user9'=>'Sengkol','user10'=>'Sengkol'];
        }elseif($desa=="Kawo"){
            $users = ['user11'=>'Kawo'];
        }elseif($desa=="Tanak_Awu"){
            $users = ['user12'=>'Tanak Awu'];
        }elseif($desa=="Pengembur"){
            $users = ['user13'=>'Pengembur'];
        }elseif($desa=="Segala_Anyar"){
            $users = ['user14'=>'Segala Anyar'];
        }
        //make result array from the tables name
        $result_data = array();
        foreach ($users as $user=>$desa){
            $data = array();
            $data[$date] = array();
            foreach ($table_default as $table=>$table_name){
                $data[$date]["name"] = $date;
                $data[$date]["id"] = $date;
                $data[$date]["data"] = array();
                foreach ($table_default as $td=>$td_name){
                    array_push($data[$date]["data"], array($td_name,0));
                }
            }
            $result_data = $data;
        }
        //var_dump($result_data);
        //retrieve all the columns in the table
        $columns = array();
        foreach ($users as $user=>$desa){
            foreach ($tables as $table=>$legend){
                $query2 = $analyticsDB->query("SHOW COLUMNS FROM ".$table);
                foreach ($query2->result() as $column){
                    array_push($columns, $column->Field);
                }

                //query tha data
                $query3 = $analyticsDB->query("SELECT userid,DATE(clientVersionSubmissionDate) as submissiondate,count(*) as counts from ".$table." where (userid='".$user."') and DATE(clientVersionSubmissionDate)='".$date."' group by userid, DATE(clientVersionSubmissionDate)");
                foreach ($query3->result() as $datas){
                    if(array_key_exists($datas->userid, $users)){
                        $data_count                  = $result_data[$date];
                        if(array_key_exists($table, $table_default)){
                            $data_count["data"][$tabindex[$table]][1]         += $datas->counts;
                        }

                        $result_data[$date] = $data_count;
                    }
                }
            }
        }
        return $result_data;
    }
    
    public function getCountPerFormByVisitDateForDrill($desa="",$date=""){
        $analyticsDB = $this->load->database('analytics', TRUE);
        $query  = $analyticsDB->query("SHOW TABLES FROM analytics");
        $table_default = [
            'kartu_anc_visit'=>'Kunjungan ANC',
            'kartu_pnc_regitration_oa'=>'Registrasi PNC',
            'kartu_pnc_dokumentasi_persalinan'=>'Dokumentasi Persalinan',
            'kartu_pnc_visit'=>'Kunjungan PNC',
            'kohort_bayi_neonatal_period'=>'Kunjungan Neonatal',
            'kohort_bayi_kunjungan'=>'Kunjungan Bayi',
            'kohort_bayi_immunization'=>'Imunisasi Bayi',
            'kohort_kb_pelayanan'=>'Pelayanan KB',
            'kohort_kb_update'=>'Update KB'];
        $tabindex = [
            'kartu_anc_visit'=>0,
            'kartu_pnc_regitration_oa'=>1,
            'kartu_pnc_dokumentasi_persalinan'=>2,
            'kartu_pnc_visit'=>3,
            'kohort_bayi_neonatal_period'=>4,
            'kohort_bayi_kunjungan'=>5,
            'kohort_bayi_immunization'=>6,
            'kohort_kb_pelayanan'=>7,
            'kohort_kb_update'=>8];
        //retrieve the tables name
        $tables = array();
        foreach ($query->result() as $table){
            if(array_key_exists($table->Tables_in_analytics, $table_default)){
                $tables[$table->Tables_in_analytics]=$table_default[$table->Tables_in_analytics];
            }
        }
        if($desa=="Lekor"){
            $users = ['user1'=>'Lekor'];
        }elseif($desa=="Saba"){
            $users = ['user2'=>'Saba'];
        }elseif($desa=="Pendem"){
            $users = ['user3'=>'Pendem'];
        }elseif($desa=="Setuta"){
            $users = ['user4'=>'Setuta'];
        }elseif($desa=="Jango"){
            $users = ['user5'=>'Jango'];
        }elseif($desa=="Janapria"){
            $users = ['user6'=>'Janapria'];
        }elseif($desa=="Ketara"){
            $users = ['user8'=>'Ketara'];
        }elseif($desa=="Sengkol"){
            $users = ['user9'=>'Sengkol','user10'=>'Sengkol'];
        }elseif($desa=="Kawo"){
            $users = ['user11'=>'Kawo'];
        }elseif($desa=="Tanak_Awu"){
            $users = ['user12'=>'Tanak Awu'];
        }elseif($desa=="Pengembur"){
            $users = ['user13'=>'Pengembur'];
        }elseif($desa=="Segala_Anyar"){
            $users = ['user14'=>'Segala Anyar'];
        }
        //make result array from the tables name
        $result_data = array();
        foreach ($users as $user=>$desa){
            $data = array();
            $data[$date] = array();
            foreach ($table_default as $table=>$table_name){
                $data[$date]["name"] = $date;
                $data[$date]["id"] = $date;
                $data[$date]["data"] = array();
                foreach ($table_default as $td=>$td_name){
                    array_push($data[$date]["data"], array($td_name,0));
                }
            }
            $result_data = $data;
        }
        //var_dump($result_data);
        //retrieve all the columns in the table
        $columns = array();
        foreach ($users as $user=>$desa){
            foreach ($tables as $table=>$legend){
                //query tha data
                if($table=="kartu_anc_visit"){
                    $query3 = $analyticsDB->query("SELECT userid, ancDate,count(*) as counts from ".$table." where (userid='".$user."') and ancDate='".$date."' group by userid, ancDate");
                }elseif($table=="kartu_pnc_regitration_oa"){
                    $query3 = $analyticsDB->query("SELECT userid, tanggalLahir,count(*) as counts from ".$table." where (userid='".$user."') and tanggalLahir='".$date."' group by userid, tanggalLahir");
                }elseif($table=="kartu_pnc_dokumentasi_persalinan"){
                    $query3 = $analyticsDB->query("SELECT userid, tanggalLahirAnak,count(*) as counts from ".$table." where (userid='".$user."') and tanggalLahirAnak='".$date."' group by userid, tanggalLahirAnak");
                }elseif($table=="kartu_pnc_visit"){
                    $query3 = $analyticsDB->query("SELECT userid, referenceDate,count(*) as counts from ".$table." where (userid='".$user."') and referenceDate='".$date."' group by userid, referenceDate");
                }elseif($table=="kohort_bayi_kunjungan"){
                    $query3 = $analyticsDB->query("SELECT userid, tanggalKunjunganBayiPerbulan,count(*) as counts from ".$table." where (userid='".$user."') and tanggalKunjunganBayiPerbulan='".$date."' group by userid, tanggalKunjunganBayiPerbulan");
                }elseif($table=="kohort_kb_pelayanan"&&$table=="kohort_kb_update"){
                    $query3 = $analyticsDB->query("SELECT userid, tanggalkunjungan,count(*) as counts from ".$table." where (userid='".$user."') and tanggalkunjungan='".$date."' group by userid, tanggalkunjungan");
                }else{
                    $query3 = $analyticsDB->query("SELECT userid, submissiondate,count(*) as counts from ".$table." where (userid='".$user."') and submissiondate='".$date."' group by userid, submissiondate");
                }
                
                foreach ($query3->result() as $datas){
                    if(array_key_exists($datas->userid, $users)){
                        $data_count                  = $result_data[$date];
                        if(array_key_exists($table, $table_default)){
                            $data_count["data"][$tabindex[$table]][1]         += $datas->counts;
                        }

                        $result_data[$date] = $data_count;
                    }
                }
            }
        }
        return $result_data;
    }
}