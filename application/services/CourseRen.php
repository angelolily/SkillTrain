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
        $base_url='http://'.$_SERVER['HTTP_HOST'].substr($_SERVER['PHP_SELF'],0,strrpos($_SERVER['PHP_SELF'],'/index.php')+1);
        $field="a.school_time,a.home_time,a.sign_time,a.signup_time,a.class_romm,a.teacher_name,b.class_name,c.course_name,c.course_describe,c.course_status,c.course_cover";
        $sql="select {$field} from schedule as a left join class_group as b on a.class_id=b.class_id left join course as c on b.course_id=c.course_id where a.members_id='{$val['members_id']}' order by a.school_time asc";
        $allData=$this->Sys_Model->execute_sql($sql);
        for($i=0;$i<count($allData);$i++){
            if(array_key_exists('course_cover',$allData[$i])){
                $allData[$i]['course_cover'] = $base_url.'public/coursecover/'.$allData[$i]['course_cover'];
            }
            if(array_key_exists('course_graphic',$allData[$i])){
                $allData[$i]['course_graphic'] = $base_url.'public/coursegraphic/'.$allData[$i]['course_graphic'];
            }
        }
            return $allData;
    }
    public function getTeacherSchedule($val)
    {
        $base_url='http://'.$_SERVER['HTTP_HOST'].substr($_SERVER['PHP_SELF'],0,strrpos($_SERVER['PHP_SELF'],'/index.php')+1);
        $field="a.class_num,a.class_id,a.course_id,a.school_time,a.home_time,a.sign_time,a.signup_time,a.class_romm,a.teacher_name,b.class_name,c.course_name,c.course_describe,c.course_status,c.course_cover";
        $sql="select {$field} from schedule as a left join class_group as b on a.class_id=b.class_id left join course as c on b.course_id=c.course_id where a.teacher_id='{$val['teacher_id']}' order by a.school_time asc";
        $allData=$this->Sys_Model->execute_sql($sql);
        for($i=0;$i<count($allData);$i++){
            if(array_key_exists('course_cover',$allData[$i])){
                $allData[$i]['course_cover'] = $base_url.'public/coursecover/'.$allData[$i]['course_cover'];
            }
            if(array_key_exists('course_graphic',$allData[$i])){
                $allData[$i]['course_graphic'] = $base_url.'public/coursegraphic/'.$allData[$i]['course_graphic'];
            }
        }
        return $allData;
    }
    public function getCourse($val)
    {
        $base_url='http://'.$_SERVER['HTTP_HOST'].substr($_SERVER['PHP_SELF'],0,strrpos($_SERVER['PHP_SELF'],'/index.php')+1);
        $field="a.sign_statue,a.sign_created_time,a.sign_id,b.course_name,b.course_describe,b.course_status,b.course_cover,c.course_graphic,c.course_video,c.course_num";
        $sql="select {$field} from sign_up as a left join course as b on a.sign_competition_id=b.course_id left join course_attach as c on b.course_id=c.course_id where a.members_id='{$val['members_id']}' and a.sign_statue!='未付款' order by a.sign_created_time desc";
        $allData=$this->Sys_Model->execute_sql($sql);
        for($i=0;$i<count($allData);$i++){
            if(array_key_exists('course_cover',$allData[$i])){
                $allData[$i]['course_cover'] = $base_url.'public/coursecover/'.$allData[$i]['course_cover'];
            }
            if(array_key_exists('course_graphic',$allData[$i])){
                $allData[$i]['course_graphic'] = $base_url.'public/coursegraphic/'.$allData[$i]['course_graphic'];
            }
        }
        return $allData;
    }
    public function getOrder($val)
    {
        $base_url='http://'.$_SERVER['HTTP_HOST'].substr($_SERVER['PHP_SELF'],0,strrpos($_SERVER['PHP_SELF'],'/index.php')+1);
        $field="a.sign_statue,a.sign_created_time,a.sign_id,b.course_name,b.course_describe,b.course_status,b.course_cover,c.order_statue";
        $sql="select {$field} from sign_up as a left join course as b on a.sign_competition_id=b.course_id left join `order` as c on a.sign_order_id=c.order_autoid where a.members_id='{$val['members_id']}' order by a.sign_created_time desc";
        $allData=$this->Sys_Model->execute_sql($sql);
        for($i=0;$i<count($allData);$i++){
            if(array_key_exists('course_cover',$allData[$i])){
                $allData[$i]['course_cover'] = $base_url.'public/coursecover/'.$allData[$i]['course_cover'];
            }
            if(array_key_exists('course_graphic',$allData[$i])){
                $allData[$i]['course_graphic'] = $base_url.'public/coursegraphic/'.$allData[$i]['course_graphic'];
            }
        }
        return $allData;
    }
    public function getMessage($val)
    {
        $base_url='http://'.$_SERVER['HTTP_HOST'].substr($_SERVER['PHP_SELF'],0,strrpos($_SERVER['PHP_SELF'],'/index.php')+1);
        $field="*";
        $rk="members_id".$val['members_id'];
        $allData=$this->Sys_Model->table_seleRow($field,"message",$val,array(),array(),"",$rk);
        for($i=0;$i<count($allData);$i++){
            if(array_key_exists('course_cover',$allData[$i])){
                $allData[$i]['course_cover'] = $base_url.'public/coursecover/'.$allData[$i]['course_cover'];
            }
            if(array_key_exists('course_graphic',$allData[$i])){
                $allData[$i]['course_graphic'] = $base_url.'public/coursegraphic/'.$allData[$i]['course_graphic'];
            }
        }
        return $allData;
    }
    public function updateMessage($val)
    {
        $allData=$this->Sys_Model->table_updateRow("message",array("is_read"=>1),$val);
        return $allData;
    }
    public function sign($val)
    {
        $allData=$this->Sys_Model->table_seleRow("*","schedule",$val);
        if(count($allData)>0){
            if($allData[0]['sign_time']=""){
                $allData=$this->Sys_Model->table_updateRow("schedule",$val,array("sign_time"=>date('Y-m-d H:i:s')));
            }else if($allData[0]['sign_time']!=""&&$allData[0]['signup_time']=""){
                $allData=$this->Sys_Model->table_updateRow("schedule",$val,array("signup_time"=>date('Y-m-d H:i:s')));
            }else if($allData[0]['sign_time']!=""&&$allData[0]['signup_time']!=""){
                $allData=[];
            }
            return $allData;
        }else{
            return $allData;
        }
    }
}







