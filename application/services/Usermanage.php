<?php


/**
 * Class Usermanage ’用户管理类
 */
class Usermanage extends HTY_service
{
	/**
	 * Dept constructor.
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Sys_Model');
		$this->load->helper('tool');
		$this->load->library('encryption');

	}
	/**
	 * 部门人数加一
	 * @param $pid
	 * @param $arr
	 * @param array $tree
	 * @return array|mixed
	 */
	public function modifyDeptTree($pid, $arr, &$tree = [])
	{
		foreach ($arr as $key => $dp) {
			if ($dp['DeptId'] == $pid) {
				$dp['DeptNum'] = $dp['DeptNum']+1;
				$tree[] = $dp;
				if($dp['ParentId']!="0"){
					$dp['DeptNum'] = $dp['DeptNum']-1;//恢复
					$c=$this->modifyDeptTree($dp['ParentId'], $arr);
					foreach ($c as $b){
						$tree[] = $b;
					}
				}
			}
		}
		return $tree;
	}

	/**
	 * 部门人数减一
	 * @param $pid
	 * @param $arr
	 * @param array $tree
	 * @return array|mixed
	 */
	public function unmodifyDeptTree($pid, $arr, &$tree = [])
	{
		foreach ($arr as $key => $dp) {
			if ($dp['DeptId'] == $pid) {
				$dp['DeptNum'] = $dp['DeptNum']-1;
				$tree[] = $dp;
				if($dp['ParentId']!="0"){
					$dp['DeptNum'] = $dp['DeptNum']+1;//恢复
					$c=$this->unmodifyDeptTree($dp['ParentId'], $arr);
					foreach ($c as $b){
						$tree[] = $b;
					}
				}
			}
		}
		return $tree;

	}

	/**
	 * Notes:新增用户数据
	 * User: angelo
	 * DateTime: 2021/1/11 14:42
	 * @param array $indData 用户信息
	 * @param $by /添加人员
	 * @return mixed
	 */
	public function addData($indData = [], $by)
	{
		$indData['Userid'] = uniqid("HTY", 4);//生成唯一用户ID
		$indData['CREATED_BY'] = $by;
		$indData['CREATED_TIME'] = date('Y-m-d H:i');
		$postname=$this->Sys_Model->table_seleRow('Userid',"base_user",array('Mobile'=>$indData['Mobile']), $like=array());
		if ($postname){
			$result = [];
			return $result;
		}else{
			$indData['UserPassword']=$this->encryption->encrypt($indData['UserPassword']);//加密
			$result = $this->Sys_Model->table_addRow("base_user", $indData, 1);
			$deptArr = $this->Sys_Model->table_seleRow('DeptId,ParentId,DeptNum', "base_dept", array('DelFlag'=>'1'), $like=array());
			$addonetenth=$this->modifyDeptTree($indData['UserDept'], $deptArr);//新部门人数加一
			if($addonetenth){
				$this->Sys_Model->table_updateBatchRow('base_dept', $addonetenth, 'DeptId');
			}
			return $result;
		}
	}

	/**
	 * Notes: 递归获取部门层级数组
	 * User: angelo
	 * DateTime: 2020/12/25 14:16
	 * @param $pid '父ID
	 * @param $arr 'tree数组
	 * @param array $tree
	 * @return array|mixed
	 */
	public function getDeptTree($pid , $arr, &$tree = [])
	{

		foreach ( $arr as $key => $dp ){
			if( $dp['ParentId'] == $pid ){
				$c= $this->getDeptTree( $dp['DeptId'] ,$arr );
				foreach($c as $b){

					$tree[] = $b;
				}
				$tree[] = $dp;
			}
		}
		return $tree;

	}



	//搜索用户页面 分页
	public function get_userdata($pages,$rows,$wheredata,$likedata){
		//Select SQL_CALC_FOUND_ROWS UserId,UserName,base_dept.DeptName,Mobile,Birthday,UserStatus,UserEmail,Sex,Remark,IsAdmin,UserRol,UserPost,base_user.CREATED_TIME from base_user,base_dept where base_user.DeptId = base_dept.DeptId
		$offset=($pages-1)*$rows;//计算偏移量
		$field='Select Userid,UserName,UserRole,base_dept.DeptName,base_user.Mobile,Birthday,UserStatus,UserEmail,Sex,UserDept,base_user.Remark,IsAdmin,UserPost,base_user.CREATED_TIME,base_post.PostName';
		$sql_query=$field." from (base_user left join base_dept on base_user.UserDept = base_dept.DeptId) left join base_post on base_user.UserPost=base_post.PostId ";
		if($wheredata!=""){
			$sql_query_where=$field." from base_user,base_dept,base_post where base_user.UserDept = base_dept.DeptId and base_user.UserPost=base_post.PostId".$wheredata;
		}
		if($likedata!=""){//like不为空
			$sql_query=$sql_query_where." ".$likedata;
		}elseif($wheredata!="")
		{
			$sql_query=$sql_query_where;
		}
		$sql_query_total=$sql_query;
		$sql_query=$sql_query." order by base_user.UserDept limit ".$offset.",".$rows;

 		$query = $this->db->query($sql_query);
		$ss=$this->db->last_query();
		$r_total=$this->db->query($sql_query_total)->result_array();
		$row_arr=$query->result_array();
		$result['total']=count($r_total);//获取总行数
		$result["data"] = $row_arr;
		return $result;
	}

	/**
	 * Notes: 获取用户信息或者刷新
	 * User: junxiong
	 * DateTime: 2021/1/11 15:04
	 * @param array $searchWhere ‘查询条件
	 * @return array|mixed
	 */
	public function getUser($searchWhere = [],$by)
	{
		if($searchWhere['DataScope']) {
			$where = "";
			$like = "";
			$curr = $searchWhere['pages'];
			$limit = $searchWhere['rows'];
			if ($searchWhere['UserName'] != "") {
				$like = " and UserName like '%{$searchWhere['UserName']}%'";
			}
			if ($searchWhere['Mobile'] != "") {
				$like = " and base_user.Mobile like '%{$searchWhere['Mobile']}%'";
			}
			if ($searchWhere['DeptId'] != "")//是搜索
			{
				if ($searchWhere['DataScope'] != "" && $searchWhere['DataScope'] != 1) {
					if($searchWhere['DataScope']==5){
						$where = $where . " and base_user.Mobile in('{$by}')";
					}
					$where = $where . " and  UserDept in('{$searchWhere['DeptId']}')";
				}else{
					$where = $where . " and  UserDept in('{$searchWhere['DeptId']}')";
				}
			}else{//刷新
				if ($searchWhere['DataScope'] != "" && $searchWhere['DataScope'] != 1) {
					if($searchWhere['DataScope']==5){
						$where = $where . " and base_user.Mobile in('{$by }')";
					}
					$all=explode(',',$searchWhere['powerdept']);
					$DeptId = "'" . $all[0] . "'";
					for ($i = 1; $i < count($all); $i++) {
						$DeptId = $DeptId . ",'" . $all[$i] . "'";
					}
					$where = $where . " and UserDept in({$DeptId})";
				}
			}
			if($searchWhere['UserStatus']!="")
			{
				$where=$where." and UserStatus='{$searchWhere['UserStatus']}'";
			}
			if($searchWhere['begin'] != '' and $searchWhere['end'] != ''){

				$where=$where." and base_user.CREATED_TIME between '".$searchWhere['begin']."' and '".$searchWhere['end']."'";
			}
			$items=$this->get_userdata($curr,$limit,$where,$like);
			$result=[];
			if(count($items['data'])>0){//记得优化一下
				foreach ($items['data'] as $item){
					if ($item['UserRole']!=""){
						$roleid=explode(",",$item['UserRole']);
						$role="'".$roleid[0]."'";
						for($i=1;$i<count($roleid);$i++) {
							$role = $role . ",'" . $roleid[$i] . "'";
						}
						$sql="select Name from base_role where RoleId in ({$role}) ";
						$a=$this->Sys_Model->execute_sql($sql, 1);
						$a = array_column($a, 'Name');
						$str=implode(',',$a);
						$item['Name']=$str;
						array_push($result,$item);
					}else{
						array_push($result,$item);
					}
				}
			}
			$items['data']=$result;
			return $items;
		}else{
			return $result=[];
		}

	}
	/**
	 * Notes: 批量删除用户数据
	 * User: angelo
	 * DateTime: 2021/1/19 11:49
	 * @param array $Userid '一个用户ID或多个
	 * @return mixed
	 */
	public function delUser($values)
	{
		if(count($values)>=1){
			$Userid = explode(',',$values['Userid']);
			$uname="'".$Userid[0]."'";
			for($i=1;$i<count($Userid);$i++){
				$uname=$uname.",'".$Userid[$i]."'";
			}
			$the_uname ="Userid in(".$uname.")";
			$del_sql = "delete from base_user where ".$the_uname;
		}
		else{
			$restulNum=[];
			return $restulNum;
		}
		$restulNum = $this->Sys_Model->execute_sql($del_sql, 2);
		$DeptId = explode(',',$values['UserDept']);
		$deptArr = $this->Sys_Model->table_seleRow('DeptId,ParentId,DeptNum', "base_dept", array('DelFlag'=>'1'), $like=array());
		foreach ($DeptId as $row){
			$Minusone=$this->unmodifyDeptTree($row, $deptArr);//原部门人数减一
			if($Minusone){
				$this->Sys_Model->table_updateBatchRow('base_dept', $Minusone, 'DeptId');
			}
		}


		return $restulNum;
	}

	/**
	 * * Notes: 修改用户数据
	 * User: junxiong
	 * DateTime: 2021/1/19 10:10
	 * @param array $values
	 * @return mixed
	 */
	public function modifyUser($values,$by)
	{
		$values['UPDATED_BY'] = $by;
		$values['UPDATED_TIME'] = date('Y-m-d H:i');
		$postname=$this->Sys_Model->table_seleRow('Userid',"base_user",array('Mobile'=>$values['Mobile']), $like=array());
		if ($postname){//如果存在前端传来的手机号即进行判断
			$restulNum = [];
			if(!isset($values['UserDept'])){//是否传DeptID 如果传了说明 不是修改状态的接口 没传deptid进
				$DataArr = bykey_reitem($values, 'phone');//删除phone这个key
				if($values['UserStatus']=='1'){
					$deptArr = $this->Sys_Model->table_seleRow('UserDept', "base_user", array('Userid' => $values['Userid']), $like = array());
					$deptArr = $this->Sys_Model->table_seleRow('Status', "base_dept", array('DeptId' => $deptArr[0]['UserDept']), $like = array());
					if( $deptArr[0]['Status']=="1"){//部门被停用无法启用用户
						return $restulNum;
					}
				}
				$restulNum = $this->Sys_Model->table_updateRow('base_user', $DataArr, array('Userid' => $values['Userid']));
				if($values['UserStatus']=='0'){//删除服务器的key
					$deptArr = $this->Sys_Model->table_seleRow('Mobile', "base_user", array('Userid' => $values['Userid']), $like = array());
					$deptArr= array_column($deptArr, 'Mobile');
					del_reids_key($deptArr);
				}
				return $restulNum;
			}
			$DeptId=$this->Sys_Model->table_seleRow('Userid,UserDept',"base_user",array('Userid'=>$values['Userid']), $like=array());//查找被修改的用户的部门ID
			if($postname[0]['Userid']==$values['Userid']){//若前端传来的用户ID跟查询到的用户ID一致则进行修改
				$restulNum = $this->Sys_Model->table_updateRow('base_user', $values, array('Userid' => $values['Userid']));
				if($DeptId[0]['UserDept']==$values['UserDept']){//若前端传来的部门ID与查询到的部门ID一致 则直接返回结果
					return $restulNum;
				}
				$deptArr = $this->Sys_Model->table_seleRow('DeptId,ParentId,DeptNum', "base_dept", array('DelFlag'=>'1'), $like=array());
				$Minusone=$this->unmodifyDeptTree($DeptId[0]['UserDept'], $deptArr);//原部门人数减一
				if($Minusone){
					$this->Sys_Model->table_updateBatchRow('base_dept', $Minusone, 'DeptId');
				}
				$deptArr = $this->Sys_Model->table_seleRow('DeptId,ParentId,DeptNum', "base_dept", array('DelFlag'=>'1'), $like=array());
				$addonetenth=$this->modifyDeptTree($values['UserDept'], $deptArr);//新部门人数加一
				if($addonetenth){
					$this->Sys_Model->table_updateBatchRow('base_dept', $addonetenth, 'DeptId');
				}
				return $restulNum;
			}
			return $restulNum;
		}else{
			$restulNum = $this->Sys_Model->table_updateRow('base_user', $values, array('Userid' => $values['Userid']));
			$DeptId=$this->Sys_Model->table_seleRow('Userid,UserDept',"base_user",array('Userid'=>$values['Userid']), $like=array());//查找被修改的用户的部门ID
			if($DeptId[0]['UserDept']==$values['UserDept']){
				return $restulNum;
			}
			$deptArr = $this->Sys_Model->table_seleRow('DeptId,ParentId,DeptNum', "base_dept", array('DelFlag'=>'1'), $like=array());
			$Minusone=$this->unmodifyDeptTree($DeptId[0]['UserDept'], $deptArr);//原部门人数减一
			if($Minusone){
				$this->Sys_Model->table_updateBatchRow('base_dept', $Minusone, 'DeptId');
			}
			$deptArr = $this->Sys_Model->table_seleRow('DeptId,ParentId,DeptNum', "base_dept", array('DelFlag'=>'1'), $like=array());
			$addonetenth=$this->modifyDeptTree($values['UserDept'], $deptArr);//新部门人数加一
			if($addonetenth){
				$this->Sys_Model->table_updateBatchRow('base_dept', $addonetenth, 'DeptId');
			}
			return $restulNum;

		}
	}
	/**
	 * * Notes: 重置用户密码
	 * User: junxiong
	 * DateTime: 2021/1/19 11:57
	 * @param array $values
	 * @return mixed
	 */
	public function resetPassword($values,$by){
		$values['UPDATED_BY'] = $by;
		$values['UPDATED_TIME'] = date('Y-m-d H:i');
		if(count($values)>0){
			$Originalpassword="123456";
			$values['UserPassword']=$this->encryption->encrypt($Originalpassword);
			$restulNum = $this->Sys_Model->table_updateRow('base_user', $values, array('Userid' => $values['Userid']));
			return $restulNum;
		}
		$restulNum=[];
		return $restulNum;
	}
}







