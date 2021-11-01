<?php


/**
 * Class 消息管理
 */
class MsgService extends HTY_service
{
    /**
     * Dept constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Sys_Model');
        $this->load->helper('tool');

    }
    //获取消息数据
    public function getMessage($searchWhere = [])
    {
        $like = [];
        $where = [];
        $data=[];
        if (count($searchWhere) > 0) {

            if ($searchWhere['class_id'] != '') {//班级id
                $where['class_id']=$searchWhere['class_id'];
            }
            if ($searchWhere['members_name'] != '') {//会员名称名称
                $where['members_name']=$searchWhere['members_name'];
            }

            $pages = $searchWhere['pages'];
            $rows = $searchWhere['rows'];
            $offset = ($pages - 1) * $rows;//计算偏移量
            $arr_total=$this->Sys_Model->table_seleRow("*","message",$where,$like);

            if(count($arr_total)>0){
                $deptTmpArr['total'] = count($arr_total);//获取总行数
                $data = $this->Sys_Model->table_seleRow_limit("*","message",$where,$like,$rows,$offset);
                $deptTmpArr['data']=$data;
            }
            else{
                $deptTmpArr['total']=[];
                $deptTmpArr['data']=[];
            }





        }
        return $deptTmpArr;
    }

    //新增消息数据
    public function addMessage($data){

        $rowscount=$this->Sys_Model->table_addRow("message",$data,2);
        if($rowscount==count($data)){
            return 1;
        }elseif ($rowscount>0){
            return 2;//部分插入成功
        }
        else{
            return 3;
        }



    }



    //获取考勤主表
    public function getAttendance($searchWhere = [])
    {
        $like = [];
        $countFinal=[];
        if (count($searchWhere) > 0) {
            if ($searchWhere['class_name'] != '') {//班级名称
                $like['class_name']=$searchWhere['class_name'];
            }
            if ($searchWhere['course_name'] != '') {//课程名称
                $like['course_name']=$searchWhere['course_name'];
            }
            if ($searchWhere['members_name'] != '') {//会员名称
                $like['members_name']=$searchWhere['members_name'];
            }
            if ($searchWhere['members_phone'] != '') {//会员手机号
                $like['members_phone']=$searchWhere['members_phone'];
            }

            //第一步取出所有考勤记录,按会员手机号分组
            $pages = $searchWhere['pages'];
            $rows = $searchWhere['rows'];
            $offset=($pages-1)*$rows;//计算偏移量
            $sel_total=$this->Sys_Model->table_seleRow("*","schedule1",[],$like,[],"",['course_id','members_phone']);//求总记录数
            $sel_rows=$this->Sys_Model->table_seleRow_limit("*,count(class_num) as xa","schedule1",[],$like,$rows,$offset,null,null,[],"",['course_id','members_phone']);


            //第二步计算考勤时间
            if(count($sel_rows)>0){

                foreach ($sel_rows as $row){

                    $searchSignTime="select SUM(sa) as sa1,SUM(sb) as sb1 from (
                                     SELECT
	                                  CASE WHEN sign_time > school_time OR sign_time = '' THEN 1 ELSE 0 END AS sa,
                                      CASE WHEN signup_time < home_time OR signup_time = '' THEN 1 ELSE 0 END AS sb 
                                     From schedule1
                                     where members_phone ='".$row['members_phone']."' and course_id='".$row['course_id']."') as A";
                    $signcount=$this->Sys_Model->execute_sql($searchSignTime);

                    if(count($signcount)>0){

                        $row['late']=$signcount[0]['sa1'];//迟到次数
                        $row['leave']=$signcount[0]['sb1'];//早退次数
                        $row['Attendance']=(($row['late']+$row['leave'])/($row['xa']*2))*100;//考勤率


                    }
                    else{
                        $row['late']="";
                        $row['leave']="";
                        $row['Attendance']="";
                    }


                    array_push($countFinal,$row);

                }



            }

        }
        $result=[];
        $result['data']=$countFinal;
        $result['total']=count($sel_total);
        return  $result;
    }

    //获取考勤明细
    public function getAttendDetail($searchWhere = [])
    {

        $where = [];
        $data=[];
        if (count($searchWhere) > 0) {

            $where['class_id']=$searchWhere['class_id'];
            $where['course_id']=$searchWhere['course_id'];
            $where['members_id']=$searchWhere['members_id'];

            $pages = $searchWhere['pages'];
            $rows = $searchWhere['rows'];
            $offset = ($pages - 1) * $rows;//计算偏移量
            $arr_total=$this->Sys_Model->table_seleRow_limit("*","schedule",$where,[]);

            if(count($arr_total)>0){
                $deptTmpArr['total'] = count($arr_total);//获取总行数
                $data = $this->Sys_Model->table_seleRow_limit("*","schedule",$where,[],$rows,$offset);
                $deptTmpArr['data']=$data;
            }
            else{
                $deptTmpArr['total']=[];
                $deptTmpArr['data']=[];
            }
        }
        return $deptTmpArr;
    }
}







