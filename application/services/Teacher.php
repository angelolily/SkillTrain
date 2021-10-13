<?php


/**
 * Class Post ’岗位类
 */
class Teacher extends HTY_service
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
//新增教师
	public function addData($indData = [], $by)
	{
		$indData['created_by'] = $by;
		$indData['created_time'] = date('Y-m-d H:i');
		$indData['teacher_pwd'] = "7cd114fc8432fa2af245048f84d5a29c27c16de0d5ecea8d2fb1a458146b4e3c2444476ae2e293678ebfe671dded74091b8f1faf2322bec4bee0a84a554c24d6J7PnTSCVfGHIkHBTWMqB41MzOytDXaJ1Cj1C0hi3NUM=";
		$postname=$this->Sys_Model->table_seleRow('teahcer_id',"teacher",array('teacher_phone'=>$indData['teacher_phone']), $like=array());
		if ($postname){
			$results = [];
		    return $results;
	}else{
            $result=$this->Sys_Model->table_addRow("teacher", $indData, 1);
            return $result;
		}
	}

//获取搜索教师
    public function getteacher($searchWhere = []) //查询到课程表
    {
            $where = "";
            if (count($searchWhere) > 0) {
                if ($searchWhere['teacher_phone'] != '') {
                    $where = $where . " and teacher_phone like '%{$searchWhere['teacher_phone']}%')";
                }
                if ($searchWhere['teacher_name'] != '') {
                    $where = $where . " and teacher_name like '%{$searchWhere['teacher_name']}%')";
                }
                if ($searchWhere['course_name'] != '') {
                    $where = $where . " and schedule.course_name like '%{$searchWhere['course_name']}%')";
                }
                if ($searchWhere['class_name'] != '') {
                    $where = $where . " and schedule.class_name like '%{$searchWhere['class_name']}%')";
                }
                $pages = $searchWhere['pages'];
                $rows = $searchWhere['rows'];
                $deptTmpArr=$this->get_teacherdata($pages, $rows,$where);
            }
        return $deptTmpArr;
    }

//搜索活动信息页面 分页
    public function get_teacherdata($pages,$rows,$wheredata){
        //Select SQL_CALC_FOUND_ROWS UserId,UserName,base_dept.DeptName,Mobile,Birthday,UserStatus,UserEmail,Sex,Remark,IsAdmin,UserRol,UserPost,base_user.CREATED_TIME from base_user,base_dept where base_user.DeptId = base_dept.DeptId
        $offset=($pages-1)*$rows;//计算偏移量
        $sql_query="Select teacher.*,schedule.class_name,schedule.course_name from teacher left join schedule on teacher.teacher_id=schedule.teacher_id where  1=1  ";
        $sql_query_where=$sql_query.$wheredata;
        if($wheredata!="")
        {
            $sql_query=$sql_query_where;
        }
        $sql_query_total=$sql_query;
        $sql_query=$sql_query." order by teacher.created_time desc limit ".$offset.",".$rows;
        $query = $this->db->query($sql_query);
        $ss=$this->db->last_query();
        $r_total=$this->db->query($sql_query_total)->result_array();
        $row_arr=$query->result_array();
        $result['total']=count($r_total);//获取总行数
        $result["data"] = $row_arr;
        $result["alldata"] = $r_total;
        return $result;
    }


//修改
	public function modifyteacher($values,$by)
    {
        $values['updated_by'] = $by;
        $values['updated_time'] = date('Y-m-d H:i');
        $postname = $this->Sys_Model->table_seleRow('teahcer_id', "teahcer", array('teahcer_phone' => $values['teahcer_phone']), $like = array());
        $resluts=[];
        if ($postname) {
            if ($postname[0]['teahcer_id'] == $values['teahcer_id']) {
                $resluts=$this->Sys_Model->table_updateRow('teahcer', $values, array('teahcer_id' => $values['teahcer_id']));
            }
            return $resluts;
        }
        $resluts=$this->Sys_Model->table_updateRow('teahcer', $values, array('teahcer_id' => $values['teahcer_id']));
        return $resluts;
    }

//审核 修改状态
    public function teacherstatus($values,$by)
    {
        $values['updated_by'] = $by;
        $values['updated_time'] = date('Y-m-d H:i');
        $resluts=$this->Sys_Model->table_updateRow('teahcer', $values, array('teahcer_id' => $values['teahcer_id']));
        return $resluts;
    }




}







