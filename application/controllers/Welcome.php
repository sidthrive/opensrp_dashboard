<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {
	public function __construct() {
        parent::__construct();
        if(empty($this->session->userdata('id_user'))&&$this->session->userdata('admin_valid') == FALSE) {
            $this->session->set_flashdata('flash_data', 'You don\'t have access!');
            $this->session->set_flashdata('url', $this->uri->uri_string);
            redirect('login');
        }
    }
	public function index()
	{
		redirect('welcome/overview');
	}

	public function overview()
	{
		$pass['title'] = "Beranda";
		$pass['css'] = [
			'vendors/bootstrap/dist/css/bootstrap.min.css',
			'vendors/font-awesome/css/font-awesome.min.css',
			'vendors/nprogress/nprogress.css',
			'vendors/iCheck/skins/flat/green.css',
			'vendors/bootstrap-progressbar/css/bootstrap-progressbar-3.3.4.min.css',
			'vendors/jqvmap/dist/jqvmap.min.css',
			'vendors/bootstrap-daterangepicker/daterangepicker.css',
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
			'build/js/custom.js'
		];
		$this->load->view('header',$pass);
		if($this->session->userdata('level')=="fhw"){
			$this->load->view('fhw/navigation');
			$this->load->view('fhw/overview');
		}else{

		}
		
		$this->load->view('footer',$pass);
	}

	public function logout() {
        $this->session->sess_destroy();
        //$this->SiteAnalyticsModel->trackPage($this->uri->rsegment(1),$this->uri->rsegment(2),base_url().$this->uri->uri_string);
        redirect('login');
    }
}
