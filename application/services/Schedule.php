<?php


/**
 * Class Post ’岗位类
 */
class Schedule extends HTY_service
{
	/**
	 * Dept constructor.
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Sys_Model');
		$this->load->helper('tool');
		$this->load->helper('qrcode');

	}
//获取
    public function getschedule($searchWhere = []) //查询到排课表
    {
        $like = "";
        if (count($searchWhere) > 0) {
            if ($searchWhere['class_name'] != '') {//班级名称
                $like = $like . " and class_name like  '%{$searchWhere['class_name']}%' ";
            }
            if ($searchWhere['course_name'] != '') {//课程名称
                $like = $like . " and course_name like  '%{$searchWhere['course_name']}%' ";
            }
            if ($searchWhere['members_name'] != '') {//会员名称
                $like = $like . " and members_name like  '%{$searchWhere['members_name']}%' ";
            }
            if ($searchWhere['members_phone'] != '') {//会员手机号
                $like = $like . " and members_phone like  '%{$searchWhere['members_phone']}%' ";
            }
            $pages = $searchWhere['pages'];
            $rows = $searchWhere['rows'];
            $deptTmpArr=$this->get_scheduledata($pages, $rows,$like);
        }
        return $deptTmpArr;
    }

//搜索活动信息页面 分页
    public function get_scheduledata($pages,$rows,$wheredata){
        //Select SQL_CALC_FOUND_ROWS UserId,UserName,base_dept.DeptName,Mobile,Birthday,UserStatus,UserEmail,Sex,Remark,IsAdmin,UserRol,UserPost,base_user.CREATED_TIME from base_user,base_dept where base_user.DeptId = base_dept.DeptId
        $offset=($pages-1)*$rows;//计算偏移量
        $sql_query="Select * from schedule  where  1=1  ";
        $sql_query_where=$sql_query.$wheredata;
        if($wheredata!="")
        {
            $sql_query=$sql_query_where;
        }
        $sql_query_total=$sql_query;
        $sql_query=$sql_query." order by create_time desc limit ".$offset.",".$rows;
        $query = $this->db->query($sql_query);
        $ss=$this->db->last_query();
        $r_total=$this->db->query($sql_query_total)->result_array();
        $row_arr=$query->result_array();
        $result['total']=count($r_total);//获取总行数
        $result["data"] = $row_arr;
        $result["alldata"] = $r_total;
        return $result;
    }

    //获取
    public function getmembersh($searchWhere = []) //查询到排课表
    {
        $like = "";
        if (count($searchWhere) > 0) {
            if ($searchWhere['class_id'] != '') {//班级名称
                $like = $like . " and class_id = '".$searchWhere['class_name']."'";
            }
            $pages = $searchWhere['pages'];
            $rows = $searchWhere['rows'];
            $deptTmpArr=$this->get_scheduledata($pages, $rows,$like);
        }
        return $deptTmpArr;
    }

////搜索活动信息页面 分页
//    public function get_memberdata($pages,$rows,$wheredata){
//        //Select SQL_CALC_FOUND_ROWS UserId,UserName,base_dept.DeptName,Mobile,Birthday,UserStatus,UserEmail,Sex,Remark,IsAdmin,UserRol,UserPost,base_user.CREATED_TIME from base_user,base_dept where base_user.DeptId = base_dept.DeptId
//        $offset=($pages-1)*$rows;//计算偏移量
//        $sql_query="Select * from schedule  where  1=1  ";
//        $sql_query_where=$sql_query.$wheredata;
//        if($wheredata!="")
//        {
//            $sql_query=$sql_query_where;
//        }
//        $sql_query_total=$sql_query;
//        $sql_query=$sql_query." order by create_time desc limit ".$offset.",".$rows;
//        $query = $this->db->query($sql_query);
//        $ss=$this->db->last_query();
//        $r_total=$this->db->query($sql_query_total)->result_array();
//        $row_arr=$query->result_array();
//        $result['total']=count($r_total);//获取总行数
//        $result["data"] = $row_arr;
//        $result["alldata"] = $r_total;
//        return $result;
//    }


//修改排课表
    public function modifyschedule($values,$by)
    {
        $values['update_by'] = $by;
        $values['update_time'] = date('Y-m-d H:i');
        $resluts = $this->Sys_Model->table_updateRow('schedule', $values, array('schedule_id' => $values['schedule_id']));
        return $resluts;
    }

}







