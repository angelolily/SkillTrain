<?php


/**
 * Class
 */
class CourseRen extends HTY_service
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Sys_Model');
        $this->load->helper('tool');
        $this->load->library('encryption');
    }
    public function getSchedule($val)
    {
        $field="a.school_time,a.home_time,a.sign_time,a.signup_time,a.class_romm,a.teacher,b.class_name,c.course_name,c.course_describe,c.course_status,c.course_cover";
        $sql="select {$field} from schedule as a left join class_group as b on a.class_id=b.class_id left join course as c on b.course_id=c.course_id where a.members_id='{$val['members_id']}' order by a.school_time asc";
        $allData=$this->Sys_Model->execute_sql($sql);
        return $allData;
    }
    public function getCourse($val)
    {
        $field="a.sign_statue,a.sign_created_time,a.sign_id,b.course_name,b.course_describe,b.course_status,b.course_cover,c.course_graphic,c.course_video,c.course_num";
        $sql="select {$field} from sign_up as a left join course as b on a.sign_competition_id=b.course_id left join course_attach as c on b.course_id=c.course_id where a.members_id='{$val['members_id']}' and a.sign_statue!='未付款' order by a.sign_created_time desc";
        $allData=$this->Sys_Model->execute_sql($sql);
        return $allData;
    }
    public function getMessage($val)
    {
        $field="*";
        $rk="members_id".$val['members_id'];
        $allData=$this->Sys_Model->table_seleRow($field,"message",$val,array(),array(),"",$rk);
        return $allData;
    }
    public function updateMessage($val)
    {
        $allData=$this->Sys_Model->table_updateRow("message",array("is_read"=>1),$val);
        return $allData;
    }
}







