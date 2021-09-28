<?php


/**
 * Class Post ’岗位类
 */
class Member extends HTY_service
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

	/**
	 * Notes: 获取报名人员信息
	 * User: angelo
	 * DateTime: 2020/12/25 14:16
	 * @param array $searchWhere ‘查询条件
	 * @return array|mixed
	 */
	public function getMember($searchWhere = [])
	{
            $like = "";
            if (count($searchWhere) > 0) {
                if($searchWhere['members_name']!="")
                {
                    $like=" and members_name like '%{$searchWhere['members_name']}%'";
                }
                if($searchWhere['members_phone']!="")
                {
                    $like=" and members_phone like '%{$searchWhere['members_phone']}%'";
                }
                $pages = $searchWhere['pages'];
                $rows = $searchWhere['rows'];
                $deptTmpArr=$this->get_Memberdata($pages, $rows,$where="",$like);
                $fill=[];
                foreach ($deptTmpArr['data'] as $item){
                    $totalcommdity=$this->Sys_Model->table_sumRow("order",array('members_id'=> $item['members_id'],'order_statue'=>"已完成"),$sum="order_price",$name="totalcommdity");
                    $item['totalcommdity']=$totalcommdity[0]['totalcommdity'];//商品消费金额
                    $totalcourse=$this->Sys_Model->table_sumRow("order",array('members_id'=> $item['members_openid'],'order_statue'=>"进行中",'order_type'=>"培训"),$sum="order_price",$name="totalcourse");
                    $item['totalcourse']=$totalcourse[0]['totalcourse'];//课程消费金额
                    $totalac1=$this->Sys_Model->table_sumRow("order",array('members_id'=> $item['members_openid'],'order_statue'=>"进行中",'order_type'=>"活动"),$sum="order_price",$name="totalac");
                    $totalac2=$this->Sys_Model->table_sumRow("order",array('members_id'=> $item['members_openid'],'order_statue'=>"已结束",'order_type'=>"活动"),$sum="order_price",$name="totalac");
                    $item['totalac']=$totalac1[0]['totalac']+$totalac2[0]['totalac'];//活动消费金额
                    $totalcom=$this->Sys_Model->table_sumRow("order",array('members_id'=> $item['members_openid'],'order_statue'=>"进行中",'order_type'=>"比赛"),$sum="order_price",$name="totalcom");
                    $item['totalcom']=$totalcom[0]['totalcom'];//比赛消费金额
                    $commdity=$this->Sys_Model->table_seleRow('order_id',"order",array('members_id'=> $item['members_openid'],'order_statue'=>"进行中",'order_type'=>"培训"), $like=array());
                    $item['countcourse']=count($commdity);//参加课程次数
                    $commdity1=$this->Sys_Model->table_seleRow('order_id',"order",array('members_id'=> $item['members_openid'],'order_statue'=>"进行中",'order_type'=>"活动"), $like=array());
                    $commdity2=$this->Sys_Model->table_seleRow('order_id',"order",array('members_id'=> $item['members_openid'],'order_statue'=>"已结束",'order_type'=>"活动"), $like=array());
                    $item['countac']=count($commdity1)+count($commdity2);//参加活动次数
                    $commdity=$this->Sys_Model->table_seleRow('order_id',"order",array('members_id'=> $item['members_openid'],'order_statue'=>"进行中",'order_type'=>"比赛"), $like=array());
                    $item['countcom']=count($commdity);//参加比赛次数
                    array_push($fill,$item);
                }
            }
        $result=[];
        $result['data']=$fill;
        $result['total']=$deptTmpArr['total'];
        $result["alldata"] = $deptTmpArr["alldata"];
        return  $result;
	}

//搜索报名信息页面 分页
public function get_Memberdata($pages,$rows,$wheredata,$likedata){
    //Select SQL_CALC_FOUND_ROWS UserId,UserName,base_dept.DeptName,Mobile,Birthday,UserStatus,UserEmail,Sex,Remark,IsAdmin,UserRol,UserPost,base_user.CREATED_TIME from base_user,base_dept where base_user.DeptId = base_dept.DeptId
    $offset=($pages-1)*$rows;//计算偏移量
    $sql_query="Select DISTINCT *  from members   where 1=1 ";
    $sql_query_where=$sql_query.$wheredata;
    if($wheredata!="")
    {
        $sql_query=$sql_query_where;
    }
    if($likedata!=""){//like不为空
        $sql_query=$sql_query_where." ".$likedata;
    }
    $sql_query_total=$sql_query;
    $sql_query=$sql_query." order by members_created_time desc limit ".$offset.",".$rows;
    $query = $this->db->query($sql_query);
    $ss=$this->db->last_query();
    $r_total=$this->db->query($sql_query_total)->result_array();
    $row_arr=$query->result_array();
    $result['total']=count($r_total);//获取总行数
    $result["data"] = $row_arr;
    $result["alldata"] = $r_total;
    return $result;
}
}







