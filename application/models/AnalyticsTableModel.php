<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class AnalyticsTableModel extends CI_Model{

    function __construct() {
        parent::__construct();
    }
    
    private $table =[
        'bidan'=> [
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
            'kartu_anc_close'=>'Penutupan ANC',
            'kartu_anc_edit'=>'Edit ANC',
            'kartu_anc_visit_edit'=>'Edit Kunjungan ANC',
            'kartu_anc_visit_integrasi'=>'Kunjungan ANC Integrasi',
            'kartu_anc_visit_labTest'=>'Kunjungan ANC Labtest',
            'kartu_ibu_close'=>'Penutupan Ibu',
            'kartu_ibu_edit'=>'Edit Ibu',
            'kartu_pnc_close'=>'Penutupan PNC',
            'kartu_pnc_edit'=>'Edit PNC',
            'kohort_anak_tutup'=>'Penutupan Anak',
            'kohort_bayi_edit'=>'Edit Bayi',
            'kohort_bayi_immunization'=>'Imunisasi Bayi',
            'kohort_kb_close'=>'Penutupan KB',
            'kohort_kb_edit'=>'Edit KB',
            'kohort_kb_pelayanan'=>'Pendaftaran KB',
            'kohort_kb_update'=>'Kunjungan KB'],
        'gizi'=>[
            'registrasi_gizi'=>'Registrasi Gizi',
            'kunjungan_gizi'=>'Kunjungan Gizi',
            'close_form'=>'Penutupan Anak'],
        'vaksinator'=>[
            'registrasi_jurim'=>'Registrasi Vaksinator',
            'hb0_visit'=>'HB0',
            'bcg_visit'=>'BCG',
            'polio1_visit'=>'POLIO 1',
            'hb1_visit'=>'DPTHB 1',
            'polio2_visit'=>'POLIO 2',
            'dpt_hb2_visit'=>'DPTHB 2',
            'polio3_visit'=>'POLIO 3',
            'hb3_visit'=>'DPTHB 3',
            'polio4_visit'=>'POLIO 4',
            'campak_visit'=>'CAMPAK',
            'ipv_visit'=>'IPV',
            'dpthb_lanjutan_visit'=>'DPTHB Lanjutan',
            'campak_lanjutan_visit'=>'Campak Lanjutan',
            'close_form'=>'Penutuan Anak']];
    
    public function getTable($fhw){
        return $this->table[$fhw];
    }
    public function getTableName($fhw){
        $ret = [];
        foreach ($this->table[$fhw] as $table=>$tname){
            array_push($ret, $table);
        }
        return $ret;
    }
    public function getTableIndex($fhw){
        $i = 0;
        $ret = [];
        foreach ($this->table[$fhw] as $table=>$tname){
            $ret[$table] = $i;
            $i++;
        }
        return $ret;
    }
}