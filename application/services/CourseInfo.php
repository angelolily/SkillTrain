<?php
class CourseInfo extends HTY_service{
    public function __construct(){
        parent::__construct();
        $this->load->helper('tool');
        $this->load->model('Sys_Model');
    }
    public function get_course_info($field,$where,$rk){
        return $this->Sys_Model->table_seleRow($field,'course',$where,[],[],"",[],$rk);
    }
    public function get_index_banner(){
        $where = array("advertType"=>1);
        $field = "advertImagePath as banner_url,advertSkipPath as banner_page";
        return $this->Sys_Model->table_seleRow($field,'advert',$where);
    }
}