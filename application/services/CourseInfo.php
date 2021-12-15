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
    public function get_sign_model($data){
        $where = array("sign_relevancy_id"=>$data['moedl_id']);
        $field = "sign_control_column as column,sign_control_title as title,sign_control_tip as tip,sign_index_style as style,sign_is_require as require,";
        return $this->Sys_Model->table_seleRow($field,'sign_index',$where);
    }
}