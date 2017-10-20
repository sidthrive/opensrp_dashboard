<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Data extends CI_Controller {
	public function __construct() {
        parent::__construct();
        if(empty($this->session->userdata('id_user'))&&$this->session->userdata('admin_valid') == FALSE) {
            $this->session->set_flashdata('flash_data', 'You don\'t have access!');
            $this->session->set_flashdata('url', $this->uri->uri_string);
            redirect('login');
        }
        $this->load->model('DataModel');
        session_write_close();
    }

   	public function getThisWeekForms($value='')
   	{
   		$weeksData = $this->DataModel->getThisWeekForms();
   		$labels = [];
   		$data = [];
   		foreach ($weeksData as $label => $value) {
   			array_push($labels, $label);
   			array_push($data, $value);
   		}
   		echo json_encode(["labels"=>$labels,"data"=>$data]);
   	}

    public function getdatabyform($dusun,$start,$end){
      $dusun = str_replace("_", " ", $dusun);
      $formdata = $this->DataModel->getdatabyform($dusun,$start,$end);
      $labels = [];
      $data = [];
      foreach ($formdata as $label => $value) {
        array_push($labels, $label);
        array_push($data, $value);
      }
      echo json_encode(["labels"=>$labels,"data"=>$data]);
    }
}