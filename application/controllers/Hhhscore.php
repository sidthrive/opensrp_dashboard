<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class HHHScore extends CI_Controller{
    public function __construct() {
        parent::__construct();
        if(empty($this->session->userdata('id_user'))&&$this->session->userdata('admin_valid') == FALSE) {
            $this->session->set_flashdata('flash_data', 'You don\'t have access!');
            $this->session->set_flashdata('url', $this->uri->uri_string);
            redirect('login');
        }
        $this->load->model('UjianModel');
        $this->load->model('LocationModel','loc');
    }
    
    public function index(){
        
        $this->load->view('header');
        $this->load->view('hhhscore/hhhsidebar');
        $this->load->view('hhhscore/standarisasimainpage');
        $this->load->view('footer');
        $this->SiteAnalyticsModel->trackPage($this->uri->rsegment(1),$this->uri->rsegment(2),base_url().$this->uri->uri_string);
    }
    public function headscore(){
        $UjianDB = $this->load->database('ujian', TRUE);
        if($this->session->userdata('level')=="fhw"){
            $data['mode']  = $this->uri->segment(3);
            if($data['mode']=='hasil'){
                $this->load->view('header');
                $this->load->view('hhhscore/hhhsidebar');
                $user = $UjianDB->query("SELECT * FROM user WHERE username='".$this->session->userdata('username')."'")->result();
                $id = array();
                foreach($user as $u){
                    $tes_id = $UjianDB->query("SELECT * FROM tes WHERE id_user=$u->id")->result();
                    foreach($tes_id as $t){
                        array_push($id, $t->id);
                    }
                }
                $data['hasil'] = $this->UjianModel->getHasil($id);
                $this->load->view('ujian/hasillist',$data);
                $this->load->view('footer');
            }elseif($data['mode']=='do'){
                $data['id_user'] = $UjianDB->query("SELECT id FROM user WHERE username='".$this->session->userdata('username')."'")->row()->id;
                $data['jadwal'] = $UjianDB->query("SELECT * FROM tes WHERE id_user='".$data['id_user']."' AND id_jenis=3 AND aktif='yes'")->row();
                if(empty($data['jadwal'])){
                    $data['token'] = $this->UjianModel->setJadwalTesBidan();
                }else{
                    $data['on_going'] = $this->UjianModel->getOnGoing($data['jadwal']->id,true);
                    if(empty($data['on_going'])){
                        $data['token'] = $data['jadwal']->token;
                    }else{
                        $data['jadwal'] = $this->UjianModel->getJadwal($data['on_going']->id_tes);
                        $data['token'] = $data['jadwal']->token;
                    }
                }
                $this->load->view('header');
                $this->load->view('hhhscore/hhhsidebar');
                $this->load->view('hhhscore/do_headscore',$data);
                $this->load->view('footer');
            }elseif($data['mode']=='lihat'){
                $id = $this->uri->segment(4);
                $data['hasil'] = $this->UjianModel->getReport($id);
                
                $this->load->view('header');
                $this->load->view('hhhscore/hhhsidebar');
                $this->load->view('ujian/hasil',$data);
                $this->load->view('footer');
            }else{
                $data['id_user'] = $UjianDB->query("SELECT id FROM user WHERE username='".$this->session->userdata('username')."'")->row()->id;
                $data['jadwal'] = $UjianDB->query("SELECT * FROM tes WHERE id_user='".$data['id_user']."' AND id_jenis=3 AND aktif='yes'")->row();
                $this->load->view('header');
                $this->load->view('hhhscore/hhhsidebar');
                $this->load->view('hhhscore/headscore',$data);
                $this->load->view('footer');
            }
        }else{
            $this->load->model('UjianModel');
            $this->load->view('header');
            $this->load->view('hhhscore/hhhsidebar');
            $data['mode']  = $this->uri->segment(3);
            if($data['mode']=='lihat'){
                $id = $this->uri->segment(4);
                $data['hasil'] = $this->UjianModel->getReport($id);
                $this->load->view('ujian/hasil',$data);
            }elseif($data['mode']=='cari'){
                $q = $this->input->get('q');
                $user = $UjianDB->query("SELECT * FROM user WHERE nama_lengkap LIKE '%$q%' order by id")->result();
                $id = array();
                foreach($user as $u){
                    $tes_id = $UjianDB->query("SELECT * FROM tes WHERE id_user=$u->id")->result();
                    foreach($tes_id as $t){
                        array_push($id, $t->id);
                    }
                }
                if(empty($id)){
                    $id = -1;
                }
                $data['hasil'] = $this->UjianModel->getHasil($id);
                $this->load->view('ujian/hasillist',$data);
            }else{
                $data['hasil'] = $this->UjianModel->getHasil("all");
                $this->load->view('ujian/hasillist',$data);
            }
            $this->load->view('footer');
        }
        $this->SiteAnalyticsModel->trackPage($this->uri->rsegment(1),$this->uri->rsegment(2),base_url().$this->uri->uri_string);
    }
    
    public function handscore(){
        if($this->session->userdata('level')=="fhw"){
            $dataXLS['kec'] = $this->session->userdata('location');
            if($this->input->get('start')==null){
                $s = date("Y-m-d",  strtotime(date("Y-m-d").' -3 months'));
                $e = date("Y-m-d");
                redirect("hhhscore/handscore?start=$s&end=$e");
            }else{
                $dataXLS['start'] = $this->input->get('start');
                $dataXLS['end'] = $this->input->get('end');
            }
            $this->load->model('HhhscoreModel');

            $dataXLS['xlsForm']=$this->HhhscoreModel->handScoreBulanIni($dataXLS['kec']);

            $this->load->view('header');
            $this->load->view('hhhscore/fhw/hhhsidebar');
            $this->load->view('hhhscore/handscore',$dataXLS);
            $this->load->view('footer');
        }else{
            if($this->input->get('start')==null){
                $s = date("Y-m-d",  strtotime(date("Y-m-d").' -3 months'));
                $e = date("Y-m-d");
                redirect("hhhscore/handscore?start=$s&end=$e");
            }else{
                $dataXLS['start'] = $this->input->get('start');
                $dataXLS['end'] = $this->input->get('end');
            }
            $this->load->model('HhhscoreModel');

            $dataXLS['xlsForm']=$this->HhhscoreModel->handScoreBulanIni($dataXLS['start'],$dataXLS['end']);

            $data['location'] = $this->loc->getAllLoc('bidan');
            $this->load->view('header');
            $this->load->view('hhhscore/hhhsidebar',$data);
            $this->load->view('hhhscore/handscore',$dataXLS);
            $this->load->view('footer');
        }
        
        $this->SiteAnalyticsModel->trackPage($this->uri->rsegment(1),$this->uri->rsegment(2),base_url().$this->uri->uri_string);
    }
    
    public function heartscore(){
        if($this->session->userdata('level')=="fhw"){
            $dataXLS['kec'] = $this->session->userdata('location');
            if($this->input->get('start')==null){
                $s = date("Y-m-d",  strtotime(date("Y-m-d").' -3 months'));
                $e = date("Y-m-d");
                redirect("hhhscore/heartscore?start=$s&end=$e");
            }else{
                $dataXLS['start'] = $this->input->get('start');
                $dataXLS['end'] = $this->input->get('end');
            }
            $this->load->model('HhhscoreModel');

            $dataXLS['xlsForm']=$this->HhhscoreModel->heartScoreBulanIni($dataXLS['kec']);

            $this->load->view('header');
            $this->load->view('hhhscore/fhw/hhhsidebar');
            $this->load->view('hhhscore/heartscore',$dataXLS);
            $this->load->view('footer');
        }else{
            if($this->input->get('start')==null){
                $s = date("Y-m-d",  strtotime(date("Y-m-d").' -3 months'));
                $e = date("Y-m-d");
                redirect("hhhscore/heartscore?start=$s&end=$e");
            }else{
                $dataXLS['start'] = $this->input->get('start');
                $dataXLS['end'] = $this->input->get('end');
            }
            $this->load->model('HhhscoreModel');

            $dataXLS['xlsForm']=$this->HhhscoreModel->heartScoreBulanIni();

            $data['location'] = $this->loc->getAllLoc('bidan');
            $this->load->view('header');
            $this->load->view('hhhscore/hhhsidebar',$data);
            $this->load->view('hhhscore/heartscore',$dataXLS);
            $this->load->view('footer');
        }
        
        $this->SiteAnalyticsModel->trackPage($this->uri->rsegment(1),$this->uri->rsegment(2),base_url().$this->uri->uri_string);
    }
    
    public function bidanTrimester1(){
        if($this->input->get('b')==null){
            $bulan_map = [1=>'januari',2=>'februari',3=>'maret',4=>'april',5=>'mei',6=>'juni',7=>'juli',8=>'agustus',9=>'september',10=>'oktober',11=>'november',12=>'desember'];
            $b = date("n");
            $t = date("Y");
            redirect("hhhscore/bidantrimester1?b=$bulan_map[$b]&t=$t");
        }else{
            $dataXLS['bulan'] = $this->input->get('b');
            $dataXLS['tahun'] = $this->input->get('t');
        }
        $this->load->model('BidanCakupanModel');
        
        $dataXLS['xlsForm']=$this->BidanCakupanModel->trimester1BulanIni($dataXLS['bulan'],$dataXLS['tahun']);
        
        $this->load->view("header");
        $this->load->view("hhhscore/hhhsidebar");
        $this->load->view("hhhscore/trimester1",$dataXLS,false);
        $this->load->view("footer");
        $this->SiteAnalyticsModel->trackPage($this->uri->rsegment(1),$this->uri->rsegment(2),base_url().$this->uri->uri_string);
    }
    public function bidanTrimester2(){
        if($this->input->get('b')==null){
            $bulan_map = [1=>'januari',2=>'februari',3=>'maret',4=>'april',5=>'mei',6=>'juni',7=>'juli',8=>'agustus',9=>'september',10=>'oktober',11=>'november',12=>'desember'];
            $b = date("n");
            $t = date("Y");
            redirect("hhhscore/bidantrimester2?b=$bulan_map[$b]&t=$t");
        }else{
            $dataXLS['bulan'] = $this->input->get('b');
            $dataXLS['tahun'] = $this->input->get('t');
        }
        $this->load->model('BidanCakupanModel');
        
        $dataXLS['xlsForm']=$this->BidanCakupanModel->trimester2BulanIni($dataXLS['bulan'],$dataXLS['tahun']);
        
        $this->load->view("header");
        $this->load->view("hhhscore/hhhsidebar");
        $this->load->view("hhhscore/trimester2",$dataXLS,false);
        $this->load->view("footer");
        $this->SiteAnalyticsModel->trackPage($this->uri->rsegment(1),$this->uri->rsegment(2),base_url().$this->uri->uri_string);
    }
    public function bidanTrimester3(){
        if($this->input->get('b')==null){
            $bulan_map = [1=>'januari',2=>'februari',3=>'maret',4=>'april',5=>'mei',6=>'juni',7=>'juli',8=>'agustus',9=>'september',10=>'oktober',11=>'november',12=>'desember'];
            $b = date("n");
            $t = date("Y");
            redirect("hhhscore/bidantrimester3?b=$bulan_map[$b]&t=$t");
        }else{
            $dataXLS['bulan'] = $this->input->get('b');
            $dataXLS['tahun'] = $this->input->get('t');
        }
        $this->load->model('BidanCakupanModel');
        
        $dataXLS['xlsForm']=$this->BidanCakupanModel->trimester3BulanIni($dataXLS['bulan'],$dataXLS['tahun']);
        
        $this->load->view("header");
        $this->load->view("hhhscore/hhhsidebar");
        $this->load->view("hhhscore/trimester3",$dataXLS,false);
        $this->load->view("footer");
        $this->SiteAnalyticsModel->trackPage($this->uri->rsegment(1),$this->uri->rsegment(2),base_url().$this->uri->uri_string);
    }
    public function standar(){
        if($this->input->get('b')==null){
            $bulan_map = [1=>'januari',2=>'februari',3=>'maret',4=>'april',5=>'mei',6=>'juni',7=>'juli',8=>'agustus',9=>'september',10=>'oktober',11=>'november',12=>'desember'];
            $b = date("n");
            $t = date("Y");
            redirect("hhhscore/standar?b=$bulan_map[$b]&t=$t");
        }else{
            $dataXLS['bulan'] = $this->input->get('b');
            $dataXLS['tahun'] = $this->input->get('t');
        }
        $this->load->model('BidanCakupanModel');
       
        $dataXLS['xlsForm']=$this->BidanCakupanModel->cakupanHHHBulanIni($dataXLS['bulan'],$dataXLS['tahun']);
        
        $this->load->view('header');
        $this->load->view('hhhscore/hhhsidebar');
        $this->load->view('hhhscore/standar',$dataXLS);
        $this->load->view('footer');
        $this->SiteAnalyticsModel->trackPage($this->uri->rsegment(1),$this->uri->rsegment(2),base_url().$this->uri->uri_string);
    }
//    public function heartscore(){
//        if($this->input->get('b')==null){
//            $bulan_map = [1=>'januari',2=>'februari',3=>'maret',4=>'april',5=>'mei',6=>'juni',7=>'juli',8=>'agustus',9=>'september',10=>'oktober',11=>'november',12=>'desember'];
//            $b = date("n");
//            $t = date("Y");
//            redirect("hhhscore/heartscore?b=$bulan_map[$b]&t=$t");
//        }else{
//            $dataXLS['bulan'] = $this->input->get('b');
//            $dataXLS['tahun'] = $this->input->get('t');
//        }
//        $this->load->model('BidanCakupanModel');
//        
//        $dataXLS['xlsForm']=$this->BidanCakupanModel->heartScoreBulanIni($dataXLS['bulan'],$dataXLS['tahun']);
//        
//        $this->load->view('header');
//        $this->load->view('hhhscore/hhhsidebar');
//        $this->load->view('hhhscore/heartscore',$dataXLS);
//        $this->load->view('footer');
//        $this->SiteAnalyticsModel->trackPage($this->uri->rsegment(1),$this->uri->rsegment(2),base_url().$this->uri->uri_string);
//    }
    
}