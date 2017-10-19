<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class OnTimeSubmissionModel extends CI_Model{

    function __construct() {
        parent::__construct();
        $this->load->model('AnalyticsTableModel','Table');
    }
    
    public function getOnTimeSubmission($mode="",$range="",$fhw){
        if($mode=="daily"){
            return $this->getDailyOnTime($range,$fhw);
        }else{
            return $this->getWeeklyOnTime($range,$fhw);
        }
    }
    
    public function getDailyOnTime($range="",$fhw){
        $location = $this->loc->getIntLocId($fhw);
        $result_data = array();
        $detail = $this->getOnTimeFormByDate($range,$fhw);
        foreach ($detail as $x=>$d){
            $result_data[$x] = $d;
        }
        return $result_data;
    }
    
    public function getWeeklyOnTime($range="",$fhw){
        $location = $this->loc->getIntLocId($fhw);
        $result_data = array();
        $detail = $this->getOnTimeForm("weekly",$range,$fhw);
        foreach ($detail as $x=>$d){
            $result_data[$x] = $d;
        }
        return $result_data;
    }
    
    public function getDailyOnTimeDesa($location="",$range="",$fhw){
        date_default_timezone_set("Asia/Makassar");
        if($fhw=='bidan'){
            $analyticsDB = $this->load->database('analytics', TRUE);
            $query  = $analyticsDB->query("SHOW TABLES FROM analytics");

            $table_default = $this->Table->getTable($fhw);
            //retrieve the tables name
            $tables = array();
            foreach ($query->result() as $table){
                if($table->Tables_in_analytics[0]=='v'){
                    continue;
                }else {
                    if(array_key_exists($table->Tables_in_analytics, $table_default)){
                        $tables[$table->Tables_in_analytics]=$table_default[$table->Tables_in_analytics];
                    }
                }
            }
        }elseif($fhw=='gizi'){
            $analyticsDB = $this->load->database('gizi', TRUE);
            $query  = $analyticsDB->query("SHOW TABLES FROM opensrp_gizi");

            $table_default = $this->Table->getTable($fhw);
            //retrieve the tables name
            $tables = array();
            foreach ($query->result() as $table){
                if($table->Tables_in_opensrp_gizi[0]=='v'){
                    continue;
                }else {
                    if(array_key_exists($table->Tables_in_opensrp_gizi, $table_default)){
                        $tables[$table->Tables_in_opensrp_gizi]=$table_default[$table->Tables_in_opensrp_gizi];
                    }
                }
            }
        }elseif($fhw=='vaksinator'){
            $analyticsDB = $this->load->database('vaksinator', TRUE);
            $query  = $analyticsDB->query("SHOW TABLES FROM opensrp_vaksinator");

            $table_default = $this->Table->getTable($fhw);
            //retrieve the tables name
            $tables = array();
            foreach ($query->result() as $table){
                if($table->Tables_in_opensrp_vaksinator[0]=='v'){
                    continue;
                }else {
                    if(array_key_exists($table->Tables_in_opensrp_vaksinator, $table_default)){
                        $tables[$table->Tables_in_opensrp_vaksinator]=$table_default[$table->Tables_in_opensrp_vaksinator];
                    }
                }
            }
        }
        
        
        $locations = $this->loc->getLocIdQuery($location);
        
        $ontime = 0;
        $total = 0;
        
        foreach ($tables as $table=>$legend){
            //query tha data
            $query = $analyticsDB->query("SELECT *,DATE(clientVersionSubmissionDate) as dateCreated from ".$table." where ($locations) AND clientVersionSubmissionDate >= '".$range[0]."' AND clientVersionSubmissionDate <= '".$range[1]."'");
            
            foreach ($query->result() as $datas){
                if(array_key_exists($datas->userID, $location)){
                    if($this->isOnTime($table,$datas,$fhw)){
                        $ontime +=1;
                    }
                    $total +=1;
                }
                
            }
        }
        
        if($total==0) return 0;
        else return (float)number_format(100*$ontime/$total,2);
    }
    
    
    private function isOnTime($table,$data,$fhw){
        $clientdate = explode(" ",$data->clientVersionSubmissionDate);
        $cdate = date_create($clientdate[0]);
        if($fhw=='bidan'){
            if($table=='kartu_anc_registration'||$table=='kartu_anc_visit_integrasi'||$table=='kartu_anc_visit_labtest'||$table=='kartu_pnc_visit'){
                $eventdate = explode(" ",$data->referenceDate);
            }elseif($table=='kartu_anc_visit'){
                $eventdate = explode(" ",$data->ancDate);
            }elseif($table=='kartu_ibu_registration'){
                $eventdate = explode(" ",$data->tanggalPeriksa);
            }elseif($table=='kartu_pnc_dokumentasi_persalinan'){
                $eventdate = explode(" ",$data->tanggalLahirAnak);
            }elseif($table=='kohort_kb_pelayanan'||$table=='kohort_kb_update'){
                $eventdate = explode(" ",$data->tanggalkunjungan);
            }elseif($table=='kohort_balita_kunjungan'||$table=='kohort_bayi_kunjungan'){
                $eventdate = explode(" ",$data->tanggalKunjunganBayiPerbulan);
            }else{
                $eventdate = explode(" ",$data->dateCreated);
            }
        }elseif($fhw=='gizi'){
            if($table=='registrasi_gizi'){
                $eventdate = explode(" ",$data->registrationDate);
            }elseif($table=='kunjungan_gizi'){
                $eventdate = explode(" ",$data->tanggalPenimbangan);
            }else{
                $eventdate = explode(" ",$data->dateCreated);
            }
        }elseif($fhw=='vaksinator'){
            if($table=='bcg_visit'){
                $eventdate = explode(" ",$data->bcg);
            }elseif($table=='campak_lanjutan_visit'){
                $eventdate = explode(" ",$data->campak_lanjutan);
            }elseif($table=='campak_visit'){
                $eventdate = explode(" ",$data->imunisasi_campak);
            }elseif($table=='dpthb_lanjutan_visit'){
                $eventdate = explode(" ",$data->dpt_hb_lanjutan);
            }elseif($table=='dpt_hb2_visit'){
                $eventdate = explode(" ",$data->dpt_hb2);
            }elseif($table=='hb0_visit'){
                $eventdate = explode(" ",$data->hb0);
            }elseif($table=='hb1_visit'){
                $eventdate = explode(" ",$data->dpt_hb1);
            }elseif($table=='hb3_visit'){
                $eventdate = explode(" ",$data->dpt_hb3);
            }elseif($table=='ipv_visit'){
                $eventdate = explode(" ",$data->ipv);
            }elseif($table=='polio1_visit'){
                $eventdate = explode(" ",$data->polio1);
            }elseif($table=='polio2_visit'){
                $eventdate = explode(" ",$data->polio2);
            }elseif($table=='polio3_visit'){
                $eventdate = explode(" ",$data->polio3);
            }elseif($table=='polio4_visit'){
                $eventdate = explode(" ",$data->polio4);
            }else{
                $eventdate = explode(" ",$data->dateCreated);
            }
        }
        
        if($eventdate[0]=="-"||$eventdate[0]=="None") return false;
        $evdate = date_create($eventdate[0]);
        $diff = date_diff($cdate,$evdate);
        if($diff->days<=1) return true;
        return false;
    }
    
    private function isWeeklyOnTime($table,$data,$fhw){
        $clientdate = explode(" ",$data->clientVersionSubmissionDate);
        $cdate = date_create($clientdate[0]);
        if($fhw=='bidan'){
            if($table=='kartu_anc_registration'||$table=='kartu_anc_visit_integrasi'||$table=='kartu_anc_visit_labtest'||$table=='kartu_pnc_visit'){
                $eventdate = explode(" ",$data->referenceDate);
            }elseif($table=='kartu_anc_visit'){
                $eventdate = explode(" ",$data->ancDate);
            }elseif($table=='kartu_ibu_registration'){
                $eventdate = explode(" ",$data->tanggalPeriksa);
            }elseif($table=='kartu_pnc_dokumentasi_persalinan'){
                $eventdate = explode(" ",$data->tanggalLahirAnak);
            }elseif($table=='kohort_kb_pelayanan'||$table=='kohort_kb_update'){
                $eventdate = explode(" ",$data->tanggalkunjungan);
            }elseif($table=='kohort_balita_kunjungan'||$table=='kohort_bayi_kunjungan'){
                $eventdate = explode(" ",$data->tanggalKunjunganBayiPerbulan);
            }else{
                $eventdate = explode(" ",$data->dateCreated);
            }
        }elseif($fhw=='gizi'){
            if($table=='registrasi_gizi'){
                $eventdate = explode(" ",$data->registrationDate);
            }elseif($table=='kunjungan_gizi'){
                $eventdate = explode(" ",$data->tanggalPenimbangan);
            }else{
                $eventdate = explode(" ",$data->dateCreated);
            }
        }elseif($fhw=='vaksinator'){
            if($table=='bcg_visit'){
                $eventdate = explode(" ",$data->bcg);
            }elseif($table=='campak_lanjutan_visit'){
                $eventdate = explode(" ",$data->campak_lanjutan);
            }elseif($table=='campak_visit'){
                $eventdate = explode(" ",$data->imunisasi_campak);
            }elseif($table=='dpthb_lanjutan_visit'){
                $eventdate = explode(" ",$data->dpt_hb_lanjutan);
            }elseif($table=='dpt_hb2_visit'){
                $eventdate = explode(" ",$data->dpt_hb2);
            }elseif($table=='hb0_visit'){
                $eventdate = explode(" ",$data->hb0);
            }elseif($table=='hb1_visit'){
                $eventdate = explode(" ",$data->dpt_hb1);
            }elseif($table=='hb3_visit'){
                $eventdate = explode(" ",$data->dpt_hb3);
            }elseif($table=='ipv_visit'){
                $eventdate = explode(" ",$data->ipv);
            }elseif($table=='polio1_visit'){
                $eventdate = explode(" ",$data->polio1);
            }elseif($table=='polio2_visit'){
                $eventdate = explode(" ",$data->polio2);
            }elseif($table=='polio3_visit'){
                $eventdate = explode(" ",$data->polio3);
            }elseif($table=='polio4_visit'){
                $eventdate = explode(" ",$data->polio4);
            }else{
                $eventdate = explode(" ",$data->dateCreated);
            }
        }
        
        if($eventdate[0]=="-"||$eventdate[0]=="None") return false;
        $evdate = date_create($eventdate[0]);
        $diff = date_diff($cdate,$evdate);
        if($diff->days<=7) return true;
        return false;
    }
    
    public function getWeeklyOnTimeDesa($location="",$range="",$fhw){
        date_default_timezone_set("Asia/Makassar"); 
        if($fhw=='bidan'){
            $analyticsDB = $this->load->database('analytics', TRUE);
            $query  = $analyticsDB->query("SHOW TABLES FROM analytics");

            $table_default = $this->Table->getTable($fhw);
            //retrieve the tables name
            $tables = array();
            foreach ($query->result() as $table){
                if($table->Tables_in_analytics[0]=='v'){
                    continue;
                }else {
                    if(array_key_exists($table->Tables_in_analytics, $table_default)){
                        $tables[$table->Tables_in_analytics]=$table_default[$table->Tables_in_analytics];
                    }
                }
            }
        }elseif($fhw=='gizi'){
            $analyticsDB = $this->load->database('gizi', TRUE);
            $query  = $analyticsDB->query("SHOW TABLES FROM opensrp_gizi");

            $table_default = $this->Table->getTable($fhw);
            //retrieve the tables name
            $tables = array();
            foreach ($query->result() as $table){
                if($table->Tables_in_opensrp_gizi[0]=='v'){
                    continue;
                }else {
                    if(array_key_exists($table->Tables_in_opensrp_gizi, $table_default)){
                        $tables[$table->Tables_in_opensrp_gizi]=$table_default[$table->Tables_in_opensrp_gizi];
                    }
                }
            }
        }elseif($fhw=='vaksinator'){
            $analyticsDB = $this->load->database('vaksinator', TRUE);
            $query  = $analyticsDB->query("SHOW TABLES FROM opensrp_vaksinator");

            $table_default = $this->Table->getTable($fhw);
            //retrieve the tables name
            $tables = array();
            foreach ($query->result() as $table){
                if($table->Tables_in_opensrp_vaksinator[0]=='v'){
                    continue;
                }else {
                    if(array_key_exists($table->Tables_in_opensrp_vaksinator, $table_default)){
                        $tables[$table->Tables_in_opensrp_vaksinator]=$table_default[$table->Tables_in_opensrp_vaksinator];
                    }
                }
            }
        }
        
        $locations = $this->loc->getLocIdQuery($location);
        
        $ontime = 0;
        $total = 0;
        
        foreach ($tables as $table=>$legend){
            //query tha data
            $query = $analyticsDB->query("SELECT *,DATE(clientVersionSubmissionDate) as dateCreated from ".$table." where ($locations) AND clientVersionSubmissionDate >= '".$range[0]."' AND clientVersionSubmissionDate <= '".$range[1]."'");
            
            foreach ($query->result() as $datas){
                if(array_key_exists($datas->userID, $location)){
                    if($this->isWeeklyOnTime($table,$datas,$fhw)){
                        $ontime +=1;
                    }
                    $total +=1;
                }
                
            }
        }
        
        if($total==0) return 0;
        else return (float)number_format(100*$ontime/$total,2);
    }
    
    
    
    //buat desa
    public function getOnTimeForm($mode="",$range="",$fhw){
        date_default_timezone_set("Asia/Makassar"); 
        if($fhw=='bidan'){
            $analyticsDB = $this->load->database('analytics', TRUE);
            $query  = $analyticsDB->query("SHOW TABLES FROM analytics");

            $table_default = $this->Table->getTable($fhw);
            //retrieve the tables name
            $tables = array();
            foreach ($query->result() as $table){
                if($table->Tables_in_analytics[0]=='v'){
                    continue;
                }else {
                    if(array_key_exists($table->Tables_in_analytics, $table_default)){
                        $tables[$table->Tables_in_analytics]=$table_default[$table->Tables_in_analytics];
                    }
                }
            }
        }elseif($fhw=='gizi'){
            $analyticsDB = $this->load->database('gizi', TRUE);
            $query  = $analyticsDB->query("SHOW TABLES FROM opensrp_gizi");

            $table_default = $this->Table->getTable($fhw);
            //retrieve the tables name
            $tables = array();
            foreach ($query->result() as $table){
                if($table->Tables_in_opensrp_gizi[0]=='v'){
                    continue;
                }else {
                    if(array_key_exists($table->Tables_in_opensrp_gizi, $table_default)){
                        $tables[$table->Tables_in_opensrp_gizi]=$table_default[$table->Tables_in_opensrp_gizi];
                    }
                }
            }
        }elseif($fhw=='vaksinator'){
            $analyticsDB = $this->load->database('vaksinator', TRUE);
            $query  = $analyticsDB->query("SHOW TABLES FROM opensrp_vaksinator");

            $table_default = $this->Table->getTable($fhw);
            //retrieve the tables name
            $tables = array();
            foreach ($query->result() as $table){
                if($table->Tables_in_opensrp_vaksinator[0]=='v'){
                    continue;
                }else {
                    if(array_key_exists($table->Tables_in_opensrp_vaksinator, $table_default)){
                        $tables[$table->Tables_in_opensrp_vaksinator]=$table_default[$table->Tables_in_opensrp_vaksinator];
                    }
                }
            }
        }
        
        $locId = $this->loc->getIntLocId($fhw);$location = $this->loc->getLocIdQuery($locId);
        //make result array from the tables name
        $result_data = array();
        $result_data['daily'] = array();
        foreach ($locId as $user=>$desa){
            $result_data['daily'][$desa] = 0;
            $data = array();
            foreach ($table_default as $t=>$tn){
                $data[$tn] = 0;
            }
            $result_data[$desa] = $data;
        }
        
        $deno = $result_data;
        foreach ($tables as $table=>$legend){
            //query tha data
            $query = $analyticsDB->query("SELECT *,DATE(clientVersionSubmissionDate) as dateCreated from ".$table." where ($location) AND DATE(clientVersionSubmissionDate) >= '".$range[0]."' AND DATE(clientVersionSubmissionDate) <= '".$range[1]."'");
            
            foreach ($query->result() as $datas){
                if(array_key_exists($datas->userID, $locId)){
                    $data_count                  = $result_data[$locId[$datas->userID]];
                    $data_count2                 = $deno[$locId[$datas->userID]];
                    if($mode=='daily'){
                        if($this->isOnTime($table,$datas,$fhw)){
                            $result_data['daily'][$locId[$datas->userID]] +=1;
                            $data_count[$table_default[$table]] +=1;
                        }
                    }else{
                        if($this->isWeeklyOnTime($table,$datas,$fhw)){
                            $result_data['daily'][$locId[$datas->userID]] +=1;
                            $data_count[$table_default[$table]] +=1;
                        }
                    }
                    
                    $data_count2[$table_default[$table]] +=1;
                    $deno['daily'][$locId[$datas->userID]] +=1;
                    $result_data[$locId[$datas->userID]] = $data_count;
                    $deno[$locId[$datas->userID]] = $data_count2;
                }
                
            }
        }
        
        foreach ($result_data as $desa=>$res){
            foreach ($res as $date=>$value){
                if($deno[$desa][$date]==0)continue;
                $result_data[$desa][$date] = (float)number_format(100*$result_data[$desa][$date]/$deno[$desa][$date],2);
            }
        }
        return $result_data;
    }
    
    public function getOnTimeFormByDate($range="",$fhw){
        date_default_timezone_set("Asia/Makassar"); 
        $end = new DateTime($range[1]);
        $end = $end->modify( '+1 day' );
        $period = new DatePeriod(
             new DateTime($range[0]),
             new DateInterval('P1D'),
             $end
        );
        
        if($fhw=='bidan'){
            $analyticsDB = $this->load->database('analytics', TRUE);
            $query  = $analyticsDB->query("SHOW TABLES FROM analytics");

            $table_default = $this->Table->getTable($fhw);
            //retrieve the tables name
            $tables = array();
            foreach ($query->result() as $table){
                if($table->Tables_in_analytics[0]=='v'){
                    continue;
                }else {
                    if(array_key_exists($table->Tables_in_analytics, $table_default)){
                        $tables[$table->Tables_in_analytics]=$table_default[$table->Tables_in_analytics];
                    }
                }
            }
        }elseif($fhw=='gizi'){
            $analyticsDB = $this->load->database('gizi', TRUE);
            $query  = $analyticsDB->query("SHOW TABLES FROM opensrp_gizi");

            $table_default = $this->Table->getTable($fhw);
            //retrieve the tables name
            $tables = array();
            foreach ($query->result() as $table){
                if($table->Tables_in_opensrp_gizi[0]=='v'){
                    continue;
                }else {
                    if(array_key_exists($table->Tables_in_opensrp_gizi, $table_default)){
                        $tables[$table->Tables_in_opensrp_gizi]=$table_default[$table->Tables_in_opensrp_gizi];
                    }
                }
            }
        }elseif($fhw=='vaksinator'){
            $analyticsDB = $this->load->database('vaksinator', TRUE);
            $query  = $analyticsDB->query("SHOW TABLES FROM opensrp_vaksinator");

            $table_default = $this->Table->getTable($fhw);
            //retrieve the tables name
            $tables = array();
            foreach ($query->result() as $table){
                if($table->Tables_in_opensrp_vaksinator[0]=='v'){
                    continue;
                }else {
                    if(array_key_exists($table->Tables_in_opensrp_vaksinator, $table_default)){
                        $tables[$table->Tables_in_opensrp_vaksinator]=$table_default[$table->Tables_in_opensrp_vaksinator];
                    }
                }
            }
        }
        
        $locId = $this->loc->getIntLocId($fhw);$location = $this->loc->getLocIdQuery($locId);
        //make result array from the tables name
        $result_data = array();
        $result_data['daily'] = array();
        foreach ($locId as $user=>$desa){
            $result_data['daily'][$desa] = 0;
            foreach (iterator_to_array($period) as $date){
                $result_data[$desa][$date->format("Y-m-d")] = 0;
            }
        }
        
        $deno = $result_data;
        foreach ($tables as $table=>$legend){
            //query tha data
            $query = $analyticsDB->query("SELECT *,DATE(clientVersionSubmissionDate) as dateCreated from ".$table." where ($location) AND DATE(clientVersionSubmissionDate) >= '".$range[0]."' AND DATE(clientVersionSubmissionDate) <= '".$range[1]."'");
            
            foreach ($query->result() as $datas){
                if(array_key_exists($datas->userID, $locId)){
                    if($this->isOnTime($table,$datas,$fhw)){
                        $result_data['daily'][$locId[$datas->userID]] +=1;
                        $result_data[$locId[$datas->userID]][$datas->dateCreated] +=1;
                    }
                    
                    $deno['daily'][$locId[$datas->userID]] +=1;
                    $deno[$locId[$datas->userID]][$datas->dateCreated] +=1;
                }
                
            }
        }
        
        foreach ($result_data as $desa=>$res){
            foreach ($res as $date=>$value){
                if($deno[$desa][$date]==0)continue;
                $result_data[$desa][$date] = (float)number_format(100*$result_data[$desa][$date]/$deno[$desa][$date],2);
            }
        }
        return $result_data;
    }
    
    public function getOnTimeFormDesa($desa,$range,$fhw){
        date_default_timezone_set("Asia/Makassar"); 
        if($fhw=='bidan'){
            $analyticsDB = $this->load->database('analytics', TRUE);
            $query  = $analyticsDB->query("SHOW TABLES FROM analytics");

            $table_default = $this->Table->getTable($fhw);
            //retrieve the tables name
            $tables = array();
            foreach ($query->result() as $table){
                if($table->Tables_in_analytics[0]=='v'){
                    continue;
                }else {
                    if(array_key_exists($table->Tables_in_analytics, $table_default)){
                        $tables[$table->Tables_in_analytics]=$table_default[$table->Tables_in_analytics];
                    }
                }
            }
        }elseif($fhw=='gizi'){
            $analyticsDB = $this->load->database('gizi', TRUE);
            $query  = $analyticsDB->query("SHOW TABLES FROM opensrp_gizi");

            $table_default = $this->Table->getTable($fhw);
            //retrieve the tables name
            $tables = array();
            foreach ($query->result() as $table){
                if($table->Tables_in_opensrp_gizi[0]=='v'){
                    continue;
                }else {
                    if(array_key_exists($table->Tables_in_opensrp_gizi, $table_default)){
                        $tables[$table->Tables_in_opensrp_gizi]=$table_default[$table->Tables_in_opensrp_gizi];
                    }
                }
            }
        }elseif($fhw=='vaksinator'){
            $analyticsDB = $this->load->database('vaksinator', TRUE);
            $query  = $analyticsDB->query("SHOW TABLES FROM opensrp_vaksinator");

            $table_default = $this->Table->getTable($fhw);
            //retrieve the tables name
            $tables = array();
            foreach ($query->result() as $table){
                if($table->Tables_in_opensrp_vaksinator[0]=='v'){
                    continue;
                }else {
                    if(array_key_exists($table->Tables_in_opensrp_vaksinator, $table_default)){
                        $tables[$table->Tables_in_opensrp_vaksinator]=$table_default[$table->Tables_in_opensrp_vaksinator];
                    }
                }
            }
        }
        $locId = $this->loc->getLocIdAndDesabyDesa($desa);
        $location = $this->loc->getLocIdQuery($locId);
        //make result array from the tables name
        $result_data = array();
        foreach ($locId as $user=>$desa){
            $data = array();
            foreach ($table_default as $t=>$tn){
                $data[$tn] = 0;
            }
            $result_data = $data;
        }
        $deno = $result_data;
        
        $result_data = array();
        foreach ($locId as $user=>$desa){
            $data = array();
            foreach ($table_default as $t=>$tn){
                $data[$tn] = 0;
            }
            $result_data['daily'] = $data;
            $result_data['weekly'] = $data;
        }
        
        foreach ($tables as $table=>$legend){
            //query tha data
            $query = $analyticsDB->query("SELECT *,DATE(clientVersionSubmissionDate) as dateCreated from ".$table." where ($location) AND DATE(clientVersionSubmissionDate) >= '".$range[0]."' AND DATE(clientVersionSubmissionDate) <= '".$range[1]."'");
            
            foreach ($query->result() as $datas){
                if(array_key_exists($datas->userID, $locId)){
                    $data_daily                  = $result_data['daily'];
                    $data_weekly                 = $result_data['weekly'];
                    $data_denum                  = $deno;
                    
                    if($this->isOnTime($table,$datas,$fhw)){
                        $data_daily[$table_default[$table]] +=1;
                    }
                    if($this->isWeeklyOnTime($table,$datas,$fhw)){
                        $data_weekly[$table_default[$table]] +=1;
                    }
                    
                    $data_denum[$table_default[$table]] +=1;
                    $result_data['daily'] = $data_daily;
                    $result_data['weekly'] = $data_weekly;
                    $deno = $data_denum;
                }
                
            }
        }
        
        foreach ($result_data as $mode=>$res){
            foreach ($res as $form=>$value){
                if($deno[$form]==0)continue;
                $result_data[$mode][$form] = (float)number_format(100*$result_data[$mode][$form]/$deno[$form],2);
            }
        }
        
        return $result_data;
    }
    
}