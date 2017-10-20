<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Laporan extends CI_Controller{
    public function __construct() {
        parent::__construct();
        if(empty($this->session->userdata('id_user'))&&$this->session->userdata('admin_valid') == FALSE) {
            $this->session->set_flashdata('flash_data', 'You don\'t have access!');
            $this->session->set_flashdata('url', $this->uri->uri_string);
            redirect('login');
        }
        $this->load->model('LocationModel','loc');
        //$this->load->model('CakupanModel','ec');
    }
    
    public function index(){
        $this->load->view('header');
        $this->load->view('laporan/laporansidebar');
        $this->load->view('laporan/laporanmainpage');
        $this->load->view('footer');
        $this->SiteAnalyticsModel->trackPage($this->uri->rsegment(1),$this->uri->rsegment(2),base_url().$this->uri->uri_string);
    }
    
    public function cakupanIndikatorPWS(){
        $pass['title'] = "Cakupan PWS";
        $pass['css'] = [
            'vendors/bootstrap/dist/css/bootstrap.min.css',
            'vendors/font-awesome/css/font-awesome.min.css',
            'vendors/nprogress/nprogress.css',
            'vendors/iCheck/skins/flat/green.css',
            'vendors/bootstrap-progressbar/css/bootstrap-progressbar-3.3.4.min.css',
            'vendors/jqvmap/dist/jqvmap.min.css',
            'vendors/bootstrap-daterangepicker/daterangepicker.css',
            'highchart/css/highcharts.css',
            'build/css/custom.css'
        ];
        $pass['js'] = [
            'vendors/jquery/dist/jquery.min.js',
            'vendors/bootstrap/dist/js/bootstrap.min.js',
            'vendors/fastclick/lib/fastclick.js',
            'vendors/nprogress/nprogress.js',
            'vendors/Chart.js/dist/Chart.min.js',
            'vendors/gauge.js/dist/gauge.min.js',
            'vendors/bootstrap-progressbar/bootstrap-progressbar.min.js',
            'vendors/iCheck/icheck.min.js',
            'vendors/skycons/skycons.js',
            'vendors/Flot/jquery.flot.js',
            'vendors/Flot/jquery.flot.pie.js',
            'vendors/Flot/jquery.flot.time.js',
            'vendors/Flot/jquery.flot.stack.js',
            'vendors/Flot/jquery.flot.resize.js',
            'vendors/flot.orderbars/js/jquery.flot.orderBars.js',
            'vendors/flot-spline/js/jquery.flot.spline.min.js',
            'vendors/flot.curvedlines/curvedLines.js',
            'vendors/DateJS/build/date.js',
            'vendors/jqvmap/dist/jquery.vmap.js',
            'vendors/jqvmap/dist/maps/jquery.vmap.world.js',
            'vendors/jqvmap/examples/js/jquery.vmap.sampledata.js',
            'vendors/moment/min/moment.min.js',
            'vendors/bootstrap-daterangepicker/daterangepicker.js',
            'highchart/js/highcharts.js',
            'highchart/js/modules/exporting.js',
            'build/js/custom.js'
        ];
        if($this->input->get('b')==null){
            $bulan_map = [1=>'januari',2=>'februari',3=>'maret',4=>'april',5=>'mei',6=>'juni',7=>'juli',8=>'agustus',9=>'september',10=>'oktober',11=>'november',12=>'desember'];
            $b = date("n");
            $t = date("Y");
            redirect("laporan/cakupanindikatorpws?b=$bulan_map[$b]&t=$t");
        }else{
            $dataXLS['bulan'] = $this->input->get('b');
            $dataXLS['tahun'] = $this->input->get('t');
        }
        
        if($this->session->userdata('level')=="fhw"){
            // $this->load->model('BidanFhwCakupanModel');
            // $dataXLS['xlsForm']=$this->BidanFhwCakupanModel->cakupanBulanIni($dataXLS['bulan'],$dataXLS['tahun']);
        }else{
            $this->load->model('BidanCakupanModel');
            $dataXLS['xlsForm']=$this->BidanCakupanModel->cakupanBulanIni($dataXLS['bulan'],$dataXLS['tahun']);
        }
        

            $data['desa']       = $this->session->userdata('location');
            $data['dusuns']      = $this->loc->getDusun($data['desa']);
            $data['dusunTypo']      = $this->loc->getDusunTypo($data['desa']);

        $this->load->view("header",$pass);
        $this->load->view("fhw/navigation");
        $this->load->view("fhw/cakupanpws",$data);
        $this->load->view("footer",$pass);
        //$this->SiteAnalyticsModel->trackPage($this->uri->rsegment(1),$this->uri->rsegment(2),base_url().$this->uri->uri_string);
    }
    
    public function downloadkartuibu(){
        $this->load->view("header");
        $this->load->view("laporan/laporansidebar");
        if($this->session->userdata('level')=="fhw"){
            $this->load->model('KartuIbuModel');
            $data['ibulist'] = $this->KartuIbuModel->getIbuList();
            $this->load->view("laporan/fhw/downloadkartuibu",$data);
        }else{
            $this->load->view("laporan/downloadkartuibu");
        }
        
        $this->load->view("footer");
        $this->SiteAnalyticsModel->trackPage($this->uri->rsegment(1),$this->uri->rsegment(2),base_url().$this->uri->uri_string);
    }
    
    public function downloadki(){
        $this->load->model('KartuIbuModel');
        $id = $_POST['id'];
        $this->KartuIbuModel->downloadPdf($id);
    }
    
    public function downloadBidanPWS(){
        $pass['title'] = "Download PWS";
        $pass['css'] = [
            'vendors/bootstrap/dist/css/bootstrap.min.css',
            'vendors/font-awesome/css/font-awesome.min.css',
            'vendors/nprogress/nprogress.css',
            'vendors/iCheck/skins/flat/green.css',
            'vendors/bootstrap-progressbar/css/bootstrap-progressbar-3.3.4.min.css',
            'vendors/jqvmap/dist/jqvmap.min.css',
            'vendors/bootstrap-daterangepicker/daterangepicker.css',
            'highchart/css/highcharts.css',
            'build/css/custom.css'
        ];
        $pass['js'] = [
            'vendors/jquery/dist/jquery.min.js',
            'vendors/bootstrap/dist/js/bootstrap.min.js',
            'vendors/fastclick/lib/fastclick.js',
            'vendors/nprogress/nprogress.js',
            'vendors/Chart.js/dist/Chart.min.js',
            'vendors/gauge.js/dist/gauge.min.js',
            'vendors/bootstrap-progressbar/bootstrap-progressbar.min.js',
            'vendors/iCheck/icheck.min.js',
            'vendors/skycons/skycons.js',
            'vendors/Flot/jquery.flot.js',
            'vendors/Flot/jquery.flot.pie.js',
            'vendors/Flot/jquery.flot.time.js',
            'vendors/Flot/jquery.flot.stack.js',
            'vendors/Flot/jquery.flot.resize.js',
            'vendors/flot.orderbars/js/jquery.flot.orderBars.js',
            'vendors/flot-spline/js/jquery.flot.spline.min.js',
            'vendors/flot.curvedlines/curvedLines.js',
            'vendors/DateJS/build/date.js',
            'vendors/jqvmap/dist/jquery.vmap.js',
            'vendors/jqvmap/dist/maps/jquery.vmap.world.js',
            'vendors/jqvmap/examples/js/jquery.vmap.sampledata.js',
            'vendors/moment/min/moment.min.js',
            'vendors/bootstrap-daterangepicker/daterangepicker.js',
            'highchart/js/highcharts.js',
            'highchart/js/modules/exporting.js',
            'build/js/custom.js'
        ];

        if($this->session->userdata('level')=="fhw"){
            // $this->load->view("laporan/fhw/downloadpwsbidan");
        }else{
            // $this->load->view("laporan/downloadpwsbidan");
        }
        
        $data['desa']       = $this->session->userdata('location');
            $data['dusuns']      = $this->loc->getDusun($data['desa']);
            $data['dusunTypo']      = $this->loc->getDusunTypo($data['desa']);

        $this->load->view("header",$pass);
        $this->load->view("fhw/navigation");
        $this->load->view("fhw/downloadpws",$data);
        $this->load->view("footer",$pass);
        //$this->SiteAnalyticsModel->trackPage($this->uri->rsegment(1),$this->uri->rsegment(2),base_url().$this->uri->uri_string);
    }
    
    public function download(){
        if($this->session->userdata('level')=="fhw"){
            $year   = $this->input->post('year');
            $month  = $this->input->post('month');
            $form   = $this->input->post('formtype');
            $this->download_fhw($year,$month,$form);
        }else{
            $this->load->model('PHPExcelModel');
            $this->load->model('BidanNewPwsModel');

            $kec    = $this->input->post('kecamatan');
            $year   = $this->input->post('year');
            $month  = $this->input->post('month');
            $form   = $this->input->post('formtype');

            if($form=="KIA"){
                $this->BidanNewPwsModel->kia($kec,$year,$month,$form);
            }elseif(strpos($form,'bayi')!==false){
                $this->BidanNewPwsModel->bayi($kec, $year, $month, $form);
            }elseif(strpos($form,'balita')!==false){
                $this->BidanNewPwsModel->balita($kec, $year, $month, $form);
            }elseif(strpos($form,'anak')!==false){
                $this->BidanNewPwsModel->anak($kec, $year, $month, $form);
            }elseif($form=="neonatal"){
                $this->BidanNewPwsModel->neonatal($kec,$year,$month,$form);
            }elseif($form=="kb"){
                $this->BidanNewPwsModel->kb($kec,$year,$month,$form);
            }elseif($form=="maternal"){
                $this->BidanNewPwsModel->maternal($kec,$year,$month,$form);
            }
        }
        $this->SiteAnalyticsModel->trackPage($this->uri->rsegment(1),$this->uri->rsegment(2),base_url().$this->uri->uri_string);
    }
    
    private function download_fhw($year,$month,$form){
        $user = $this->session->userdata('username');
        $this->load->model('PHPExcelModel');
        $this->load->model('PWSNewFhwModel');
        if($form=="KIA"){
            $this->PWSNewFhwModel->kia($user,$year,$month,$form);
        }elseif(strpos($form,'bayi')!==false){
            $this->PWSNewFhwModel->bayi($user, $year, $month, $form);
        }elseif(strpos($form,'balita')!==false){
            $this->PWSNewFhwModel->balita($user, $year, $month, $form);
        }elseif(strpos($form,'anak')!==false){
            $this->PWSNewFhwModel->anak($user, $year, $month, $form);
        }elseif($form=="neonatal"){
            $this->PWSNewFhwModel->neonatal($user,$year,$month,$form);
        }elseif($form=="kb"){
            $this->PWSNewFhwModel->kb($user,$year,$month,$form);
        }elseif($form=="maternal"){
            $this->PWSNewFhwModel->maternal($user,$year,$month,$form);
        }
        $this->SiteAnalyticsModel->trackPage($this->uri->rsegment(1),$this->uri->rsegment(2),base_url().$this->uri->uri_string);
    }
}