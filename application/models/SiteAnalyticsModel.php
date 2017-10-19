<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class SiteAnalyticsModel extends CI_Model{
    
    private $page = array(
        'login'=>array('index'=>'Login'),
        'welcome'=>array('page'=>'Home Dashboard','logout'=>'Logout'),
        'berita'=>array('show'=>'Lihat Berita','post'=>'Atur Berita','setpost'=>'Simpan Berita'),
        'dataentry'=>array('index'=>'Dataentry Dashboard','bidanbyform'=>'Bidan By Form','bidanbytanggal'=>'Bidan By Tanggal','gizibyform'=>'Gizi By Form','gizibytanggal'=>'Gizi By Tanggal','vaksinatorbyform'=>'Vaksinator By Form','vaksinatorbytanggal'=>'Vaksinator By Tanggal'),
        'laporan'=>array('index'=>'Laporan Dashboard','cakupanindikatorpws'=>'Cakupan Bidan','downloadbidanpws'=>'Bidan PWS Menu','download'=>'Download PWS Bidan','download_fhw'=>'Download PWS Bidan(FHW)','cakupangizi'=>'Cakupan Gizi','downloadgizipws'=>'Gizi PWS Menu','downloadpwsgizi'=>'Download PWS Gizi','cakupanpwsvaksinator'=>'Cakupan Vaksinator','downloadvaksinatorpws'=>'Vaksinator PWS Menu','downloadpwsvaksinator'=>'Download PWS Vaksinator'),
        'hhhscore'=>array('index'=>'HHHScore Dashboard','headscore'=>'Head Score','bidantrimester1'=>'Hand Score - QCI (tm1)','bidantrimester2'=>'Hand Score - QCI (tm2)','bidantrimester3'=>'Hand Score - QCI (tm3)','standar'=>'Hand Score - Cakupan Pelayanan','heartscore'=>'Heart Score'));
    
    function __construct() {
        parent::__construct();
    }
    
    public function trackPage($tab,$page,$url){
        date_default_timezone_set("Asia/Makassar");
        $now = date("Y-m-d H:i:s");
        $sitedb = $this->load->database('site_analytics', TRUE);
        $sitedb->query("INSERT INTO page_views (username,level,tipe,tab,page,url,timestamp) VALUES ("
                ."'".$this->session->userdata('username')."',"
                ."'".$this->session->userdata('level')."',"
                ."'".$this->session->userdata('tipe')."',"
                ."'".$tab."',"
                ."'".$page."',"
                ."'".$url."',"
                ."'".$now. "')");
    }
    
    public function getPageViewsForGraph($params=array()){
        $sitedb = $this->load->database('site_analytics', TRUE);
        $user = $this->db->query("SELECT * FROM users WHERE level!='super' AND level!='master' ORDER BY level")->result();
        $result_data = array();
        $tanggal = array();
        foreach ($user as $u){
            $data = array();
            for($i=1;$i<=30;$i++){
                $day     = 30-$i;
                $date    = date("Y-m-d",  strtotime("-".$day." days"));
                $data[$date] = 0;
            }
            $tanggal = $data;
            $result_data[$u->username] = $data;
        }
        reset($tanggal);
        $start = key($tanggal);
        end($tanggal);
        $end = key($tanggal);
        $params['date_range'] = array($start,$end);
        $data = $this->getPageViews($params);
        foreach ($data as $d){
            if(array_key_exists($d->username,$result_data)){
                $result_data[$d->username][$d->date] +=1;
            }
        }
        
        return $result_data;
    }
    
    public function getPageViewsForDrill($user,$date){
        $params['user'] = $user;
        $params['date'] = $date;
        $views = $this->getPageViews($params);
        
        $result_data = array();
        $data = array();
        $data[$date] = array();
        
        $data[$date]["name"] = $date;
        $data[$date]["id"] = $date;
        $data[$date]["data"] = array();
        $tabindex = array();
        $index = 0;
        foreach ($this->page as $tab=>$pages){
            foreach ($pages as $page=>$name){
                array_push($data[$date]["data"], array($name,0));
                $tabindex[$tab.$page] = $index++;
            }
        }
        foreach ($views as $view){
            if(array_key_exists($view->tab, $this->page)){
                if(array_key_exists($view->page, $this->page[$view->tab])){
                    $data[$date]["data"][$tabindex[$view->tab.$view->page]][1] +=1;
                }
            }
        }
        
        return $data;
    }


    public function getPageViews($params=array()){
        $sitedb = $this->load->database('site_analytics', TRUE);
        if(isset($params['user']))$user = "AND username='".$params['user']."'";else $user="";
        if(isset($params['level']))$level = "AND level='".$params['level']."'";else $level="";
        if(isset($params['tipe']))$tipe = "AND tipe='".$params['tipe']."'";else $tipe="";
        if(isset($params['date']))$date = "AND DATE(timestamp)='".$params['date']."'";else $date="";
        if(isset($params['date_range'])&&$date=="")$date = "AND DATE(timestamp)>='".$params['date_range'][0]."' AND DATE(timestamp)<='".$params['date_range'][1]."'";
        return $sitedb->query("SELECT *,DATE(timestamp) as date FROM page_views WHERE 1 $user$level$tipe$date")->result();
    }
    
}