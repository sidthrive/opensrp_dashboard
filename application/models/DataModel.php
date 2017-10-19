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