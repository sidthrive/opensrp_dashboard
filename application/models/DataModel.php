<?php
defined('BASEPATH') OR exit('No direct script access allowed');
 
/**
 * @name: Login model
 * @author: Imron Rosdiana
 */
class DataModel extends CI_Model
{
 
    function __construct() {
        parent::__construct();
        $this->load->model('AnalyticsTableModel','Table');
        $this->load->model('LocationModel','loc');
    }
 
    public function getThisWeekForms(){
        $analyticsDB = $this->load->database('analytics', TRUE);
        $now    = date("Y-m-d");
        $table_default = $this->Table->getTable('bidan');

        $temp_res = [];
        $res = [];

		$ts = strtotime($now);
		$year = date('o', $ts);
		$week = date('W', $ts);
		for($i = 1; $i <= 7; $i++) {
		    $ts = strtotime($year.'W'.$week.$i);
		    $date = date("Y-m-d", $ts);
		    $temp_res[$date] = 0;
		    $res[date("l",strtotime($date))] = 0;
			if($i==1) $startdate = $date;
			if($i==7) $enddate = $date;
		}

		foreach ($table_default as $table=>$legend){
			$query = $analyticsDB->query("SELECT userid,DATE(clientVersionSubmissionDate) as submissiondate, count(*) as counts from ".$table." where (userid='".$this->session->userdata('username')."') and (DATE(clientVersionSubmissionDate) >= '".$startdate."' and DATE(clientVersionSubmissionDate) <= '".$enddate."') group by DATE(clientVersionSubmissionDate)");
			foreach ($query->result() as $datas){
				if(array_key_exists($datas->submissiondate, $temp_res)){
					$res[date("l",strtotime($datas->submissiondate))] += $datas->counts;
				}
			}
		}

		return $res;
    }

    public function getdatabyform($dusun,$start,$end){
    	$analyticsDB = $this->load->database('analytics', TRUE);

    	$desa = $this->loc->getDesaFromDusun($dusun);
    	$dusuns = $this->loc->getOneDusunTypo($desa,$dusun);
    	$location = $this->createWhere($dusuns,"dusun");
    	
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
        //retrieve the tables name
        $tables = array();
        foreach ($query->result() as $table){
            if(array_key_exists($table->Tables_in_analytics, $table_default)){
                $tables[$table->Tables_in_analytics]=$table_default[$table->Tables_in_analytics];
            }
        }
        
        
        //make result array from the tables name
        $result_data = array();
        foreach ($table_default as $table=>$legend){
        	$result_data[$legend] = 0;
        }
        foreach ($table_default as $table=>$legend){
            if($table=="kartu_anc_registration"||$table=="kartu_ibu_registration"||$table=="kohort_kb_registration"||$table=="kartu_anc_registration_oa"||$table=="kartu_pnc_regitration_oa"||$table=="kohort_bayi_registration_oa"||$table=="kartu_ibu_edit"){
                $query = $analyticsDB->query("SELECT userid, submissiondate,dusun,count(*) as counts from ".$table." where ($location) and $table.submissiondate>='$start' and $table.submissiondate<='$end' group by userid");
                $result_data[$legend]         += $query->row()?$query->row()->counts:0;
            }
            elseif($table=="kartu_anc_visit"||$table=="kohort_bayi_registration"||$table=="kartu_anc_close"||$table=="kartu_anc_edit"||$table=="kartu_anc_visit_edit"||$table=="kartu_anc_visit_integrasi"||$table=="kartu_anc_visit_labTest"||$table=="kartu_ibu_close"||$table=="kartu_pnc_close"){
                $query = $analyticsDB->query("SELECT $table.userid, $table.kiId, $table.submissiondate,count(*) as counts from $table left join kartu_ibu_registration on kartu_ibu_registration.kiId=$table.kiId where (".$this->fixLocation($location,"kartu_ibu_registration").") and $table.submissiondate>='$start' and $table.submissiondate<='$end' group by userid");
                $result_data[$legend]         += $query->row()?$query->row()->counts:0;
            }
            elseif($table=="kartu_anc_rencana_persalinan"||$table=="kartu_pnc_dokumentasi_persalinan"||$table=="kartu_pnc_edit"||$table=="kohort_bayi_edit"){
                $query = $analyticsDB->query("SELECT $table.userid, $table.motherId, $table.submissiondate,count(*) as counts from $table left join kartu_anc_registration on kartu_anc_registration.motherId=$table.motherId left join kartu_ibu_registration on kartu_ibu_registration.kiId=kartu_anc_registration.kiId where (".$this->fixLocation($location,"kartu_ibu_registration").") and $table.submissiondate>='$start' and $table.submissiondate<='$end' group by userid");
                $result_data[$legend]         += $query->row()?$query->row()->counts:0;
            }
            elseif($table=="kartu_pnc_visit"){
                $query = $analyticsDB->query("SELECT $table.userid, $table.kiId, $table.submissiondate,count(*) as counts from $table inner join kartu_anc_registration on kartu_anc_registration.motherId=$table.motherId where (".$this->fixLocation($location,"kartu_anc_registration").") and $table.submissiondate>='$start' and $table.submissiondate<='$end' group by userid");
                $result_data[$legend]         += $query->row()?$query->row()->counts:0;
                $query = $analyticsDB->query("SELECT $table.userid, $table.kiId, $table.submissiondate,count(*) as counts from $table inner join kartu_anc_registration_oa on kartu_anc_registration_oa.motherId=$table.motherId where (".$this->fixLocation($location,"kartu_anc_registration_oa").") and $table.submissiondate>='$start' and $table.submissiondate<='$end' group by userid");
                $result_data[$legend]         += $query->row()?$query->row()->counts:0;
            }
            elseif($table=="kohort_bayi_kunjungan"||$table=="kohort_bayi_neonatal_period"||$table=="kohort_anak_tutup"||$table=="kohort_bayi_immunization"){
            	$query = $analyticsDB->query("SELECT $table.userid, $table.childId, $table.submissiondate,count(*) as counts from $table inner join kartu_pnc_dokumentasi_persalinan on kartu_pnc_dokumentasi_persalinan.childId=$table.childId inner join kartu_anc_registration on kartu_anc_registration.motherId=kartu_pnc_dokumentasi_persalinan.motherId where (".$this->fixLocation($location,"kartu_anc_registration").") and $table.submissiondate>='$start' and $table.submissiondate<='$end' group by userid");
                $result_data[$legend]         += $query->row()?$query->row()->counts:0;
                $query = $analyticsDB->query("SELECT $table.userid, $table.childId, $table.submissiondate,count(*) as counts from $table inner join kartu_pnc_dokumentasi_persalinan on kartu_pnc_dokumentasi_persalinan.childId=$table.childId inner join kartu_anc_registration_oa on kartu_anc_registration_oa.motherId=kartu_pnc_dokumentasi_persalinan.motherId where (".$this->fixLocation($location,"kartu_anc_registration_oa").") and $table.submissiondate>='$start' and $table.submissiondate<='$end' group by userid");
                $result_data[$legend]         += $query->row()?$query->row()->counts:0;
            	$query = $analyticsDB->query("SELECT $table.userid, $table.childId, $table.submissiondate,count(*) as counts from $table inner join kohort_bayi_registration_oa on kohort_bayi_registration_oa.childId=$table.childId where (".$this->fixLocation($location,"kohort_bayi_registration_oa").") and $table.submissiondate>='$start' and $table.submissiondate<='$end' group by userid");
                $result_data[$legend]         += $query->row()?$query->row()->counts:0;
                $query = $analyticsDB->query("SELECT $table.userid, $table.childId, $table.submissiondate,count(*) as counts from $table inner join kohort_bayi_registration on kohort_bayi_registration.childId=$table.childId inner join kartu_ibu_registration on kartu_ibu_registration.kiId=kohort_bayi_registration.kiId where (".$this->fixLocation($location,"kartu_ibu_registration").") and $table.submissiondate>='$start' and $table.submissiondate<='$end' group by userid");
                $result_data[$legend]         += $query->row()?$query->row()->counts:0;
            }
        }
        return $result_data;

    }

    public function getdatabytanggal($dusun,$start,$end){
    	$analyticsDB = $this->load->database('analytics', TRUE);

    	$desa = $this->loc->getDesaFromDusun($dusun);
    	$dusuns = $this->loc->getOneDusunTypo($desa,$dusun);
    	$location = $this->createWhere($dusuns,"dusun");
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
        //retrieve the tables name
        
        $tables = array();
        foreach ($query->result() as $table){
            if(array_key_exists($table->Tables_in_analytics, $table_default)){
                array_push($tables, $table->Tables_in_analytics);
            }
        }

        $result_data = array();
        $begin = new DateTime($start);
        $_end = new DateTime($end);
        $data = array();
        for($i=$begin;$begin<=$_end;$i->modify('+1 day')){
            $date    = $i->format("Y-m-d");
            $result_data[$date] = 0;
        }

        foreach ($table_default as $table=>$legend){
            if($table=="kartu_anc_registration"||$table=="kartu_ibu_registration"||$table=="kohort_kb_registration"||$table=="kartu_anc_registration_oa"||$table=="kartu_pnc_regitration_oa"||$table=="kohort_bayi_registration_oa"||$table=="kartu_ibu_edit"){
                $query = $analyticsDB->query("SELECT userid, submissiondate,dusun,count(*) as counts from ".$table." where ($location) and $table.submissiondate>='$start' and $table.submissiondate<='$end' group by $table.submissiondate");
                foreach ($query->result() as $d){
                	if(array_key_exists($d->submissiondate, $result_data)){
                		$result_data[$d->submissiondate]         += $d->counts;
                	}
                }
                
            }
            elseif($table=="kartu_anc_visit"||$table=="kohort_bayi_registration"||$table=="kartu_anc_close"||$table=="kartu_anc_edit"||$table=="kartu_anc_visit_edit"||$table=="kartu_anc_visit_integrasi"||$table=="kartu_anc_visit_labTest"||$table=="kartu_ibu_close"||$table=="kartu_pnc_close"){
                $query = $analyticsDB->query("SELECT $table.userid, $table.kiId, $table.submissiondate,count(*) as counts from $table left join kartu_ibu_registration on kartu_ibu_registration.kiId=$table.kiId where (".$this->fixLocation($location,"kartu_ibu_registration").") and $table.submissiondate>='$start' and $table.submissiondate<='$end' group by $table.submissiondate");
                foreach ($query->result() as $d){
                	if(array_key_exists($d->submissiondate, $result_data)){
                		$result_data[$d->submissiondate]         += $d->counts;
                	}
                }
            }
            elseif($table=="kartu_anc_rencana_persalinan"||$table=="kartu_pnc_dokumentasi_persalinan"||$table=="kartu_pnc_edit"||$table=="kohort_bayi_edit"){
                $query = $analyticsDB->query("SELECT $table.userid, $table.motherId, $table.submissiondate,count(*) as counts from $table left join kartu_anc_registration on kartu_anc_registration.motherId=$table.motherId left join kartu_ibu_registration on kartu_ibu_registration.kiId=kartu_anc_registration.kiId where (".$this->fixLocation($location,"kartu_ibu_registration").") and $table.submissiondate>='$start' and $table.submissiondate<='$end' group by $table.submissiondate");
                foreach ($query->result() as $d){
                	if(array_key_exists($d->submissiondate, $result_data)){
                		$result_data[$d->submissiondate]         += $d->counts;
                	}
                }
            }
            elseif($table=="kartu_pnc_visit"){
                $query = $analyticsDB->query("SELECT $table.userid, $table.kiId, $table.submissiondate,count(*) as counts from $table inner join kartu_anc_registration on kartu_anc_registration.motherId=$table.motherId where (".$this->fixLocation($location,"kartu_anc_registration").") and $table.submissiondate>='$start' and $table.submissiondate<='$end' group by $table.submissiondate");
                foreach ($query->result() as $d){
                	if(array_key_exists($d->submissiondate, $result_data)){
                		$result_data[$d->submissiondate]         += $d->counts;
                	}
                }
                $query = $analyticsDB->query("SELECT $table.userid, $table.kiId, $table.submissiondate,count(*) as counts from $table inner join kartu_anc_registration_oa on kartu_anc_registration_oa.motherId=$table.motherId where (".$this->fixLocation($location,"kartu_anc_registration_oa").") and $table.submissiondate>='$start' and $table.submissiondate<='$end' group by $table.submissiondate");
                foreach ($query->result() as $d){
                	if(array_key_exists($d->submissiondate, $result_data)){
                		$result_data[$d->submissiondate]         += $d->counts;
                	}
                }
            }
            elseif($table=="kohort_bayi_kunjungan"||$table=="kohort_bayi_neonatal_period"||$table=="kohort_anak_tutup"||$table=="kohort_bayi_immunization"){
            	$query = $analyticsDB->query("SELECT $table.userid, $table.childId, $table.submissiondate,count(*) as counts from $table inner join kartu_pnc_dokumentasi_persalinan on kartu_pnc_dokumentasi_persalinan.childId=$table.childId inner join kartu_anc_registration on kartu_anc_registration.motherId=kartu_pnc_dokumentasi_persalinan.motherId where (".$this->fixLocation($location,"kartu_anc_registration").") and $table.submissiondate>='$start' and $table.submissiondate<='$end' group by $table.submissiondate");
                foreach ($query->result() as $d){
                	if(array_key_exists($d->submissiondate, $result_data)){
                		$result_data[$d->submissiondate]         += $d->counts;
                	}
                }
                $query = $analyticsDB->query("SELECT $table.userid, $table.childId, $table.submissiondate,count(*) as counts from $table inner join kartu_pnc_dokumentasi_persalinan on kartu_pnc_dokumentasi_persalinan.childId=$table.childId inner join kartu_anc_registration_oa on kartu_anc_registration_oa.motherId=kartu_pnc_dokumentasi_persalinan.motherId where (".$this->fixLocation($location,"kartu_anc_registration_oa").") and $table.submissiondate>='$start' and $table.submissiondate<='$end' group by $table.submissiondate");
                foreach ($query->result() as $d){
                	if(array_key_exists($d->submissiondate, $result_data)){
                		$result_data[$d->submissiondate]         += $d->counts;
                	}
                }
            	$query = $analyticsDB->query("SELECT $table.userid, $table.childId, $table.submissiondate,count(*) as counts from $table inner join kohort_bayi_registration_oa on kohort_bayi_registration_oa.childId=$table.childId where (".$this->fixLocation($location,"kohort_bayi_registration_oa").") and $table.submissiondate>='$start' and $table.submissiondate<='$end' group by $table.submissiondate");
                foreach ($query->result() as $d){
                	if(array_key_exists($d->submissiondate, $result_data)){
                		$result_data[$d->submissiondate]         += $d->counts;
                	}
                }
                $query = $analyticsDB->query("SELECT $table.userid, $table.childId, $table.submissiondate,count(*) as counts from $table inner join kohort_bayi_registration on kohort_bayi_registration.childId=$table.childId inner join kartu_ibu_registration on kartu_ibu_registration.kiId=kohort_bayi_registration.kiId where (".$this->fixLocation($location,"kartu_ibu_registration").") and $table.submissiondate>='$start' and $table.submissiondate<='$end' group by $table.submissiondate");
                foreach ($query->result() as $d){
                	if(array_key_exists($d->submissiondate, $result_data)){
                		$result_data[$d->submissiondate]         += $d->counts;
                	}
                }
            }
        }

    	return $result_data;
    }

    public function getCountPerFormForDrill($dusun="",$date=""){
        $dusun = implode(" ", explode('_', $dusun));

    	$desa = $this->loc->getDesaFromDusun($dusun);
    	$dusuns = $this->loc->getOneDusunTypo($desa,$dusun);
    	$location = $this->createWhere($dusuns,"dusun");

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
        
        if($this->session->userdata('level')=="fhw"){
            $username = $this->session->userdata('username');
        }else{
            foreach ($this->dusun as $user=>$list){
                if(array_search($dusun,$list)){
                    $username = $user;
                    break;
                }
            }
        }
        
        $listdusun = $dusuns;
        $namadusun = array();
        foreach ($listdusun as $x=>$n){
            if($n==$dusun){
                $namadusun[$x]=$dusun;
            }
        }
        
        
        
        //make result array from the tables name
        $result_data = array();
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
        
        foreach ($table_default as $table=>$legend){
            if($table=="kartu_anc_registration"||$table=="kartu_ibu_registration"||$table=="kohort_kb_registration"||$table=="kartu_anc_registration_oa"||$table=="kartu_pnc_regitration_oa"||$table=="kohort_bayi_registration_oa"||$table=="kartu_ibu_edit"){
                $query = $analyticsDB->query("SELECT userid, submissiondate,dusun,count(*) as counts from ".$table." where ($location) and $table.submissiondate='$date' group by userid");
                if($query->num_rows()>0){
                	$result_data[$query->row()->submissiondate]["data"][$tabindex[$table]][1]         += $query->row()->counts;
                }
                
            }
            elseif($table=="kartu_anc_visit"||$table=="kohort_bayi_registration"||$table=="kartu_anc_close"||$table=="kartu_anc_edit"||$table=="kartu_anc_visit_edit"||$table=="kartu_anc_visit_integrasi"||$table=="kartu_anc_visit_labTest"||$table=="kartu_ibu_close"||$table=="kartu_pnc_close"){
                $query = $analyticsDB->query("SELECT $table.userid, $table.kiId, $table.submissiondate,count(*) as counts from $table left join kartu_ibu_registration on kartu_ibu_registration.kiId=$table.kiId where (".$this->fixLocation($location,"kartu_ibu_registration").") and $table.submissiondate='$date' group by userid");
                if($query->num_rows()>0){
                	$result_data[$query->row()->submissiondate]["data"][$tabindex[$table]][1]         += $query->row()->counts;
                }
            }
            elseif($table=="kartu_anc_rencana_persalinan"||$table=="kartu_pnc_dokumentasi_persalinan"||$table=="kartu_pnc_edit"||$table=="kohort_bayi_edit"){
                $query = $analyticsDB->query("SELECT $table.userid, $table.motherId, $table.submissiondate,count(*) as counts from $table left join kartu_anc_registration on kartu_anc_registration.motherId=$table.motherId left join kartu_ibu_registration on kartu_ibu_registration.kiId=kartu_anc_registration.kiId where (".$this->fixLocation($location,"kartu_ibu_registration").") and $table.submissiondate='$date' group by userid");
                if($query->num_rows()>0){
                	$result_data[$query->row()->submissiondate]["data"][$tabindex[$table]][1]         += $query->row()->counts;
                }
            }
            elseif($table=="kartu_pnc_visit"){
                $query = $analyticsDB->query("SELECT $table.userid, $table.kiId, $table.submissiondate,count(*) as counts from $table inner join kartu_anc_registration on kartu_anc_registration.motherId=$table.motherId where (".$this->fixLocation($location,"kartu_anc_registration").") and $table.submissiondate='$date' group by userid");
                if($query->num_rows()>0){
                	$result_data[$query->row()->submissiondate]["data"][$tabindex[$table]][1]         += $query->row()->counts;
                }
                $query = $analyticsDB->query("SELECT $table.userid, $table.kiId, $table.submissiondate,count(*) as counts from $table inner join kartu_anc_registration_oa on kartu_anc_registration_oa.motherId=$table.motherId where (".$this->fixLocation($location,"kartu_anc_registration_oa").") and $table.submissiondate='$date' group by userid");
                if($query->num_rows()>0){
                	$result_data[$query->row()->submissiondate]["data"][$tabindex[$table]][1]         += $query->row()->counts;
                }
            }
            elseif($table=="kohort_bayi_kunjungan"||$table=="kohort_bayi_neonatal_period"||$table=="kohort_anak_tutup"||$table=="kohort_bayi_immunization"){
            	$query = $analyticsDB->query("SELECT $table.userid, $table.childId, $table.submissiondate,count(*) as counts from $table inner join kartu_pnc_dokumentasi_persalinan on kartu_pnc_dokumentasi_persalinan.childId=$table.childId inner join kartu_anc_registration on kartu_anc_registration.motherId=kartu_pnc_dokumentasi_persalinan.motherId where (".$this->fixLocation($location,"kartu_anc_registration").") and $table.submissiondate='$date' group by userid");
                if($query->num_rows()>0){
                	$result_data[$query->row()->submissiondate]["data"][$tabindex[$table]][1]         += $query->row()->counts;
                }
                $query = $analyticsDB->query("SELECT $table.userid, $table.childId, $table.submissiondate,count(*) as counts from $table inner join kartu_pnc_dokumentasi_persalinan on kartu_pnc_dokumentasi_persalinan.childId=$table.childId inner join kartu_anc_registration_oa on kartu_anc_registration_oa.motherId=kartu_pnc_dokumentasi_persalinan.motherId where (".$this->fixLocation($location,"kartu_anc_registration_oa").") and $table.submissiondate='$date' group by userid");
                if($query->num_rows()>0){
                	$result_data[$query->row()->submissiondate]["data"][$tabindex[$table]][1]         += $query->row()->counts;
                }
            	$query = $analyticsDB->query("SELECT $table.userid, $table.childId, $table.submissiondate,count(*) as counts from $table inner join kohort_bayi_registration_oa on kohort_bayi_registration_oa.childId=$table.childId where (".$this->fixLocation($location,"kohort_bayi_registration_oa").") and $table.submissiondate='$date' group by userid");
                if($query->num_rows()>0){
                	$result_data[$query->row()->submissiondate]["data"][$tabindex[$table]][1]         += $query->row()->counts;
                }
                $query = $analyticsDB->query("SELECT $table.userid, $table.childId, $table.submissiondate,count(*) as counts from $table inner join kohort_bayi_registration on kohort_bayi_registration.childId=$table.childId inner join kartu_ibu_registration on kartu_ibu_registration.kiId=kohort_bayi_registration.kiId where (".$this->fixLocation($location,"kartu_ibu_registration").") and $table.submissiondate='$date' group by userid");
                if($query->num_rows()>0){
                	$result_data[$query->row()->submissiondate]["data"][$tabindex[$table]][1]         += $query->row()->counts;
                }
            }
        }
        
        return $result_data;
    }

    private function createWhere($list,$column){
    	$ret = "";
    	$i = 1;
    	foreach ($list as $loc=>$id){
            $ret .= $column." LIKE '$loc'";
            if($i != count($list)) $ret .= " OR ";
            $i++;
        }
        return $ret;
    }

    private function fixLocation($string,$tabName){
    	$str = explode(" ", $string);
        return str_replace($str[0], $tabName.".".$str[0], $string);
    }
}