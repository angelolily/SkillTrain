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
        $base_url='https://'.$_SERVER['HTTP_HOST'].substr($_SERVER['PHP_SELF'],0,strrpos($_SERVER['PHP_SELF'],'/index.php')+1);
        $field="a.school_time,a.home_time,a.sign_time,a.signup_time,a.class_romm,a.teacher_name,b.class_name,c.course_name,c.course_describe,c.course_status,c.course_cover";
        $sql="select {$field} from schedule as a left join class_group as b on a.class_id=b.class_id left join course as c on a.course_id=c.course_id where a.members_id='{$val['members_id']}' order by a.school_time asc";
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
        $base_url='https://'.$_SERVER['HTTP_HOST'].substr($_SERVER['PHP_SELF'],0,strrpos($_SERVER['PHP_SELF'],'/index.php')+1);
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
    public function getNote($val)
    {
        $base_url='https://'.$_SERVER['HTTP_HOST'].substr($_SERVER['PHP_SELF'],0,strrpos($_SERVER['PHP_SELF'],'/index.php')+1);
        $field="c.sign_statue,c.sign_created_time,c.sign_id,b.course_name,b.course_describe,b.course_status,b.course_cover,a.course_graphic,a.course_video,a.course_num";
        $sql="select {$field} from course_attach as a left join course as b on a.course_id=b.course_id left join sign_up as c on b.course_id=c.sign_competition_id where c.members_id='{$val['members_id']}' and c.sign_statue!='未付款' order by c.sign_created_time desc";
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
        $base_url='https://'.$_SERVER['HTTP_HOST'].substr($_SERVER['PHP_SELF'],0,strrpos($_SERVER['PHP_SELF'],'/index.php')+1);
        $field="*";
        $rk="members_id".$val['members_id'];
        $allData=$this->Sys_Model->table_seleRow($field,"message",$val,array(),array(),$rk);
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
            if($allData[0]['sign_time']=="" || !$allData[0]['sign_time']){
                $this->Sys_Model->table_updateRow("schedule",array("sign_time"=>date('Y-m-d H:i:s')),$val);
                $allData=0;
            }else if($allData[0]['sign_time']!=""&&($allData[0]['signup_time']=="" || !$allData[0]['sign_time'])){
                $this->Sys_Model->table_updateRow("schedule",array("signup_time"=>date('Y-m-d H:i:s')),$val);
                $allData=0;
            }else if($allData[0]['sign_time']!=""&&$allData[0]['signup_time']!=""){
                $allData=1;
            }
            return $allData;
        }else{
            return 2;
        }
    }
    public function updateInfo($val)
    {   $where['members_id']=$val['members_id'];
        $update=$val['info'];
        $allData=$this->Sys_Model->table_updateRow("members",$update,$where);
        return $allData;
    }
    public function send($val)
    {
        $add_arr=$val['send_arr'];
        for($i=0;$i<count($add_arr);$i++){
            $add_arr[$i]['created_time']=date('Y-m-d H:i:s');
        }
        $allData=$this->Sys_Model->table_addRow("message",$add_arr,2);
        return $allData;
    }
    public function updatePwd($val)
    {   $where['members_id']=$val['members_id'];
        $update['members_pwd']=$this->encryption->encrypt($val['members_pwd']);
        $allData=$this->Sys_Model->table_seleRow("*","members",$where);
        if($this->encryption->decrypt($allData[0]['members_pwd'])== $val['members_oldpwd']){
            $allData=$this->Sys_Model->table_updateRow("members",$update,$where);
            return $allData;
        }else{
            return [];
        }
    }
}







