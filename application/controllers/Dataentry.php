<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class DataEntry extends CI_Controller{
    public function __construct() {
        parent::__construct();
        if(empty($this->session->userdata('id_user'))&&$this->session->userdata('admin_valid') == FALSE) {
            $this->session->set_flashdata('flash_data', 'You don\'t have access!');
            $this->session->set_flashdata('url', $this->uri->uri_string);
            redirect('login');
        }
        $this->load->model('LocationModel','loc');
        $this->load->model('AnalyticsFhwModel');
        $this->load->model('AnalyticsModel');
        $this->load->model('OnTimeSubmissionModel');
    }
    
    public function index(){
        if($this->session->userdata('level')=="fhw"){
            $this->load->view('header');
            $this->load->view('dataentry/fhw/dataentrysidebar');
            $this->load->view('dataentry/dataentrymainpage');
            $this->load->view('footer');
        }else{
            $this->load->view('header');
            $this->load->view('dataentry/dataentrysidebar');
            $this->load->view('dataentry/dataentrymainpage');
            $this->load->view('footer');
        }
        $this->SiteAnalyticsModel->trackPage($this->uri->rsegment(1),$this->uri->rsegment(2),base_url().$this->uri->uri_string);
    }
    
    public function bidanByForm(){
        $pass['title'] = "Data Entry By Form";
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
            $data['desa']		= $this->session->userdata('location');
            $data['dusuns']      = $this->loc->getDusun($data['desa']);
            $data['dusunTypo']      = $this->loc->getDusunTypo($data['desa']);
            
            $this->load->view("header",$pass);
            $this->load->view("fhw/navigation");
            $this->load->view("fhw/bidanbyform",$data);
            $this->load->view("footer",$pass);
        }else{
            $data['kecamatan']		= $this->uri->segment(3);
            $data['desa']		= str_replace('%20', ' ', $this->uri->segment(4));
            if($this->input->get('start')==null&&$data['desa']==""){
                if($this->input->get('by')==null)$by = "subdate";else $by = $this->input->get('by');
                $now = date("Y-m-d");
                redirect("dataentry/bidanbyform/".$data['kecamatan']."?start=2015-05-01&end=$now&by=$by");
            }else{
                $by = $this->input->get('by');
                $data['start'] = $this->input->get('start');
                $data['end'] = $this->input->get('end');
                $old_data = $this->input->get('old');
            }$data['datemode'] = $by;
            if($by=="subdate") $data['data'] = $this->AnalyticsModel->getCountPerForm($data['kecamatan'],$data['start'],$data['end'],$old_data);
            else $data['data'] = $this->AnalyticsModel->getCountPerFormByVisitDate($data['kecamatan'],$data['start'],$data['end'],$old_data);
            $this->load->view("header");
            $this->load->view("dataentry/dataentrysidebar",$data);
            if($data['desa']==""){
                $this->load->view("dataentry/bidanentryform",$data);
                
            }else{
                $data['data']           = $this->AnalyticsFhwModel->getCountPerForm($data['desa']);
                $this->load->view("dataentry/fhw/bidanentryform",$data);
            }
            $this->load->view("footer");
            
        }
        // $this->SiteAnalyticsModel->trackPage($this->uri->rsegment(1),$this->uri->rsegment(2),base_url().$this->uri->uri_string);
    }
    
    public function downloadbidanByForm(){
        if($this->session->userdata('level')=="fhw"){
            $listdesa = ['user1'=>'Lekor','user2'=>'Saba','user3'=>'Pendem','user4'=>'Setuta','user5'=>'Jango','user6'=>'Janapria','user8'=>'Ketara','user9'=>'Sengkol','user10'=>'Sengkol','user11'=>'Kawo','user12'=>'Tanak Awu','user13'=>'Pengembur','user14'=>'Segala Anyar'];
            $data['desa']		= $listdesa[$this->session->userdata('username')];
            $data['data']                   = $this->AnalyticsFhwModel->getCountPerForm();
            $this->load->view("header");
            $this->load->view("dataentry/fhw/dataentrysidebar");
            $this->load->view("dataentry/fhw/bidanentryform",$data);
            $this->load->view("footer");
        }else{
            $data['start'] = $this->input->post('start');
            $data['end'] = $this->input->post('end');
            $old_data = $this->input->post('old');
            $by = $this->input->post('by');
            $data['kecamatan']		= $this->uri->segment(3);
            $data['desa']		= str_replace('%20', ' ', $this->uri->segment(4));
            if($by=="subdate") $data['data'] = $this->AnalyticsModel->downloadCountPerForm($data['kecamatan'],$data['start'],$data['end'],$old_data);
            else $data['data'] = $this->AnalyticsModel->downloadCountPerFormByVisitDate($data['kecamatan'],$data['start'],$data['end'],$old_data);
        }
        $this->SiteAnalyticsModel->trackPage($this->uri->rsegment(1),$this->uri->rsegment(2),base_url().$this->uri->uri_string);
    }
    
    public function bidanByTanggal(){
        $pass['title'] = "Data Entry By Tanggal";
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
            'highchart/highcharts.js',
            'highchart/modules/data.js',
            'highchart/modules/exporting.js',
            'highchart/modules/drilldown.js',
            'build/js/custom.js'
        ];
        if($this->session->userdata('level')=="fhw"){
            $data['desa']       = $this->session->userdata('location');
            $data['dusuns']      = $this->loc->getDusun($data['desa']);
            $data['dusunTypo']      = $this->loc->getDusunTypo($data['desa']);
            $data['mode']                   = $this->uri->segment(4);
            if($this->input->get('start')==null&&$data['mode']==''){
                $now = date("Y-m-d");
                $start = date("Y-m-d",  strtotime($now."-29 days"));
                redirect("dataentry/bidanbytanggal/?start=$start&end=$now");
            }else{
                $data['start'] = $this->input->get('start');
                $data['end'] = $this->input->get('end');
            }

            $this->load->view("header",$pass);
            $this->load->view("fhw/navigation");
            $this->load->view("fhw/bidanbytanggal",$data);
            $this->load->view("footer",$pass);
        }else{
            $data['kecamatan']		= $this->uri->segment(3);
            $data['mode']                   = $this->uri->segment(4);
            if($data['mode']!="Bulanan"&&$data['mode']!="Mingguan"){
                $data['desa']		= str_replace('%20', ' ', $this->uri->segment(4));
                $data['mode']                   = $this->uri->segment(5);
            }else{
                $data['desa']		= "";
            }
            if($this->input->get('start')==null&&$data['desa']==""&&$data['mode']==''){
                if($this->input->get('by')==null)$by = "subdate";else $by = $this->input->get('by');
                $now = date("Y-m-d");
                $start = date("Y-m-d",  strtotime($now."-29 days"));
                redirect("dataentry/bidanbytanggal/".$data['kecamatan']."?start=$start&end=$now&by=$by");
            }else{
                $by = $this->input->get('by');
                $data['start'] = $this->input->get('start');
                $data['end'] = $this->input->get('end');
            }
            $data['datemode'] = $by;
            if($by=="subdate") $data['data'] = $this->AnalyticsModel->getCountPerDayDrill($data['kecamatan'],$data['mode'],array($data['start'],$data['end']));
            else $data['data'] = $this->AnalyticsModel->getCountPerDayByVisitDate($data['kecamatan'],$data['mode'],array($data['start'],$data['end']));
            $this->load->view("header");
            $this->load->view("dataentry/dataentrysidebar",$data);
            if($data['desa']==""){
                $this->load->view("dataentry/bidanentrytanggal",$data);
                
            }else{
                if($this->input->get('start')==null&&$data['mode']==''){
                    $now = date("Y-m-d");
                    $start = date("Y-m-d",  strtotime($now."-29 days"));
                    redirect("dataentry/bidanbytanggal/".$data['kecamatan']."/".$data['desa']."?start=$start&end=$now");
                }else{
                    $data['start'] = $this->input->get('start');
                    $data['end'] = $this->input->get('end');
                }
                $data['data']           = $this->AnalyticsFhwModel->getCountPerDay($data['desa'],$data['mode'],array($data['start'],$data['end']));
                $this->load->view("dataentry/fhw/bidanentrytanggal",$data);
            }
            $this->load->view("footer");
        }
        //$this->SiteAnalyticsModel->trackPage($this->uri->rsegment(1),$this->uri->rsegment(2),base_url().$this->uri->uri_string);
    }
    
    public function getbidanByFormByVisitDate($desa,$date){
        if($this->session->userdata('level')=="fhw"){
            $data = $this->AnalyticsFhwModel->getCountPerFormForDrill($desa,$date);
        }else{
            $data = $this->AnalyticsModel->getCountPerFormByVisitDateForDrill($desa,$date);
        }
        
        echo json_encode($data);
    }
    
    public function getbidanByForm($desa,$date){
        if($this->session->userdata('level')=="fhw"){
            $data = $this->AnalyticsFhwModel->getCountPerFormForDrill($desa,$date);
        }else{
            $data = $this->AnalyticsModel->getCountPerFormForDrill($desa,$date);
        }
        
        echo json_encode($data);
    }
    public function getfhwbidanByForm($desa,$date){
        $listdesa = ['user1'=>'Lekor','user2'=>'Saba','user3'=>'Pendem','user4'=>'Setuta','user5'=>'Jango','user6'=>'Janapria','user8'=>'Ketara','user9'=>'Sengkol','user10'=>'Sengkol','user11'=>'Kawo','user12'=>'Tanak Awu','user13'=>'Pengembur','user14'=>'Segala Anyar'];
        $data = $this->AnalyticsFhwModel->getCountPerFormForDrill($desa,$date);
        echo json_encode($data);
    }
    
    public function bidanOnTimeSubmission(){
        $pass['title'] = " On Time";
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
            $data['desa']       = $this->session->userdata('location');
            $data['dusuns']      = $this->loc->getDusun($data['desa']);
            $data['dusunTypo']      = $this->loc->getDusunTypo($data['desa']);
            $data['mode']                   = $this->uri->segment(4);
            $data['mode']       = $this->uri->segment(3);
            if($this->input->get('start')==null){
                $now = date("Y-m-d");
                $start = date("Y-m-d",  strtotime($now."-29 days"));
                redirect("dataentry/bidanontimesubmission/".$data['mode']."?start=$start&end=$now");
            }else{
                $data['start'] = $this->input->get('start');
                $data['end'] = $this->input->get('end');
            }
            $pass['title'] = ucwords($data['mode']).$pass['title'];
            $this->load->view("header",$pass);
            $this->load->view("fhw/navigation");
            if($data['mode']=="daily"){
                $this->load->view("fhw/dailyontime",$data);
            }elseif($data['mode']=="weekly"){
                $this->load->view("fhw/weeklyontime",$data);
            }else{
                redirect("dataentry/bidanontimesubmission/daily?start=$start&end=$now");
            }
            $this->load->view("footer",$pass);
        }else{
            $data['mode']		= $this->uri->segment(3);
            if($this->input->get('start')==null){
                $now = date("Y-m-d");
                $start = date("Y-m-d",  strtotime($now."-29 days"));
                redirect("dataentry/bidanontimesubmission/".$data['mode']."?start=$start&end=$now");
            }else{
                $data['start'] = $this->input->get('start');
                $data['end'] = $this->input->get('end');
            }
            $data['data'] = $this->OnTimeSubmissionModel->getOnTimeSubmission($data['mode'],array($data['start'],$data['end']),'bidan');
            $this->load->view("header");
            $this->load->view("dataentry/dataentrysidebar",$data);
            $this->load->view("dataentry/bidanontimesubmission",$data);
            $this->load->view("footer");
        }
        //$this->SiteAnalyticsModel->trackPage($this->uri->rsegment(1),$this->uri->rsegment(2),base_url().$this->uri->uri_string);
    }
}