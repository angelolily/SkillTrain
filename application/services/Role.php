<?php


/**
 * Class Role ’角色类
 */
class Role extends HTY_service
{
	/**
	 * Role constructor.
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Sys_Model');
		$this->load->helper('tool');

	}


	/**
	 * Notes:新增角色
	 * User: angelo
	 * DateTime: 2021/1/25 10:40
	 * @param array $indData 角色信息
	 * @param $by /添加角色的人员电话
	 * @return mixed
	 */
	public function addData($indData=[], $by)
	{

		$indData['RoleId']=uniqid("HTY",4);//生成唯一角色ID
		$indData['CREATED_BY']=$by;
		$indData['CREATED_TIME']=date('Y-m-d H:i');
		$postname=$this->Sys_Model->table_seleRow('RoleId',"base_role",array('Name'=>$indData['Name']), $like=array());
		if($postname){
			$result=[];
			return $result;
		}
		$result=$this->Sys_Model->table_addRow("base_role",$indData,1);
		return $result;
	}

	/**
	 * Notes: 获取角色信息
	 * User: angelo
	 * DateTime: 2021/1/25 14:16
	 * @param array $searchWhere ‘查询条件
	 * @return array|mixed
	 */
	public function getRole($searchWhere=[])
	{
		$where="where 1=1 ";
		$like="";
		$curr=$searchWhere['pages'];
		$limit=$searchWhere['rows'];
		if($searchWhere['Name']!="")
		{
			$like=" and Name like '%{$searchWhere['Name']}%'";
		}
		if($searchWhere['Status']!="")
		{
			$where=$where." and Status='{$searchWhere['Status']}'";
		}
		if($searchWhere['RoleId']!="")
		{
			$where=$where." and RoleId='{$searchWhere['RoleId']}'";
		}
		if($searchWhere['begin'] != '' and $searchWhere['end'] != ''){

			$where=$where." and CREATED_TIME between '".$searchWhere['begin']."' and '".$searchWhere['end']."'";
		}
		$items=$this->get_roledata($curr,$limit,$where,$like);
		return $items;


	}
	/**
	 * Notes: 删除角色数据
	 * User: ljx
	 * DateTime: 2021/1/27 9：50
	 * @param array $postId  岗位ID
	 * @return mixed
	 */
	public function delRole($RoleId = [])
	{
		$restulNum = $this->Sys_Model->table_del("base_role", $RoleId);
		return $restulNum;
	}

	/**
	 * * Notes: 修改角色数据
	 * User: junxiong
	 * DateTime: 2020/12/25 17:00
	 * @param array $values
	 * @return mixed
	 */
	public function modifyRole($values,$by)
	{
		$values['UPDATED_BY'] = $by;
		$values['UPDATED_TIME'] = date('Y-m-d H:i');
		$postname=$this->Sys_Model->table_seleRow('RoleId',"base_role",array('Name'=>$values['Name']), $like=array());
		if ($postname){
			$restulNum = [];
			if($postname[0]['RoleId']==$values['RoleId']){
				$restulNum = $this->Sys_Model->table_updateRow('base_role', $values, array('RoleId' => $values['RoleId']));
				return $restulNum;
			}
			return $restulNum;
		}else{

			$restulNum = $this->Sys_Model->table_updateRow('base_role', $values, array('RoleId' => $values['RoleId']));
			return $restulNum;
		}

	}

	/**
	 * * Notes: 修改角色数据
	 * User: junxiong
	 * DateTime: 2020/12/25 17:00
	 * @param array $values
	 * @return mixed
	 */
	public function distriData($values)
	{
 		$updata['RoleId']=$values['RoleId'];
		if ($values['DataScope']=="1"){
			$updata['DataScope']=$values['DataScope'];
			$where['DelFlag']='1';
			$deptArr = $this->Sys_Model->table_seleRow('DeptId,ParentId,DeptName', "base_dept", $where, $like=array());
			$uname=$deptArr[0]['DeptId'];
				for($i=1;$i<count($deptArr);$i++){
					$uname=$uname.",".$deptArr[$i]['DeptId'];
				}
			$updata['showdata']=$uname;
			$resuls=$this->Sys_Model->table_updateRow('base_role', $updata,array('RoleId' => $updata['RoleId']));
			return $resuls;
		}
		if ($values['DataScope']=="2"){
			$updata['DataScope']=$values['DataScope'];
			$updata['showdata']=$values['showdata'];
			$resuls=$this->Sys_Model->table_updateRow('base_role', $updata,array('RoleId' => $updata['RoleId']));
			return $resuls;
		}
		$updata['DataScope']=$values['DataScope'];
		$updata['showdata']="";
		$resuls=$this->Sys_Model->table_updateRow('base_role', $updata,array('RoleId' => $updata['RoleId']));
		return $resuls;
	}




	//搜索角色页面 分页
	public function get_roledata($pages,$rows,$wheredata,$likedata){

		$offset=($pages-1)*$rows;//计算偏移量
		$field='SQL_CALC_FOUND_ROWS RoleId,Status,Name,DataScope,Remark,CREATED_TIME,showmenu,showdata';
		$sql_query="Select ".$field." from base_role ";
		if($wheredata!=""){
			$sql_query=$sql_query.$wheredata;
		}
		if($likedata!=""){
			$sql_query=$sql_query." ".$likedata;
		}
		$sql_query_total=$sql_query;
		$sql_query=$sql_query." order by CREATED_TIME desc limit ".$offset.",".$rows;
		$query = $this->db->query($sql_query);
		$ss=$this->db->last_query();
		$r_total=$this->db->query($sql_query_total)->result_array();
		$row_arr=$query->result_array();
		$result['total']=count($r_total);//获取总行数
		$result["data"] = $row_arr;
		return $result;
	}
}



