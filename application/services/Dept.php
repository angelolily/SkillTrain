<?php


/**
 * Class Dept ’部门类
 */
class Dept extends HTY_service
{
	/**
	 * Dept constructor.
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Sys_Model');
		$this->load->helper('tool');
		$this->load->helper('redis');

	}


	/**
	 * Notes:新增部门数据
	 * User: angelo
	 * DateTime: 2020/12/25 14:16
	 * @param array $indData 部门信息
	 * @param $by /添加人员
	 * @return mixed
	 */
	public function addData($indData = [], $by)
	{

		$indData['DeptId'] = uniqid("HTY", 4);//生成唯一部门ID
		$indData['CREATED_BY'] = $by;
		$indData['CREATED_TIME'] = date('Y-m-d H:i');

		$result = $this->Sys_Model->table_addRow("base_dept", $indData, 1);


		return $result;


	}

	/**
	 * Notes: 获取部门信息
	 * User: angelo
	 * DateTime: 2020/12/25 14:16
	 * @param array $searchWhere ‘查询条件
	 * @return array|mixed
	 */
	public function getDept($searchWhere = [])
	{
		if($searchWhere['DataScope']){
			$where = [];
			$like = [];
			if($searchWhere['DeptName'] != '')
			{
				$like['DeptName'] = $searchWhere['DeptName'];
			}
			if($searchWhere['Status'] != ''){
				$where['Status'] = $searchWhere['Status'];
			}
			$where['DelFlag']='1';
			if($searchWhere['DataScope']==1){
				$deptArr = $this->Sys_Model->table_seleRow('DeptId,ParentId,DeptName,DeptNum,Leader,Phone,Email,Status,DelFlag,Display,DeptIcon,province,city,area', "base_dept", $where, $like);
				if (count($deptArr) > 0) {
					$resultDept = $this->getDeptTree('0', $deptArr);
					if (count($resultDept)==0){
						return $deptArr;
					}else{
						return $resultDept;
					}
				}else{
					return $deptArr;
				}
			}else{
//				if($searchWhere['DataScope']==3){
//
//				}
					$wherein=explode(',',$searchWhere['powerdept']);
					$whereinfield='DeptId';
					$deptArr = $this->Sys_Model->table_seleRow('DeptId,ParentId,DeptName,DeptNum,Leader,Phone,Email,Status,DelFlag,Display,DeptIcon,province,city,area', "base_dept", $where, $like,$wherein,$whereinfield);
 					if (count($deptArr) > 0) {
						$resultDept = $this->getDeptTree($wherein[0], $deptArr);
						if(count($wherein)>1){
							$deptfirst = $this->Sys_Model->table_seleRow('DeptId,ParentId,DeptName,DeptNum,Leader,Phone,Email,Status,DelFlag,Display,DeptIcon,province,city,area', "base_dept", array('DeptId'=>$wherein[0]), $like);
							if($deptfirst){
								$deptfirst[0]['children']=$resultDept;//将搜到的儿子放进该部门下
							}else{
								return $resultDept;
							}

						}
						if (count($resultDept)==0){
							return $deptArr;
						}else{
							return $deptfirst;
						}
					}else{
						return $deptArr;
					}
			}
		}else{
			return $deptArr=[];
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
				if( $c ){

					$dp['children'] = $c;
				}
				$tree[] = $dp;
			}
		}
		return $tree;

	}
//	public function getDeptTree($pid, $arr, &$tree = [])
//	{
//		foreach ($arr as $key => $dp) {
//			if ($dp['ParentId'] == $pid) {
//				$c=$this->getDeptTree($dp['DeptId'], $arr);
//				if($c){
//					$dp['chirdren']=$c;
//				}
//				$a['chirdren']=$dp;
//			}
//			if($dp['DeptId']==$pid){
//				$a['DeptId']=$dp['DeptId'];
//				$a['ParentId']=$dp['ParentId'];
//			}
//
//		}
//		if($a){
//			$tree[]=$a;
//		}
//		return $tree;
//	}

	/**
	 * 停用部门
	 * @param $pid
	 * @param $arr
	 * @param array $tree
	 * @return array|mixed
	 */
	public function modifyDeptTree($pid, $arr, &$tree = [])
	{
		foreach ($arr as $key => $dp) {
			if ($dp['ParentId'] == $pid) {
				$c = $this->modifyDeptTree($dp['DeptId'], $arr);
				if ($c) {
					foreach ($c as $b){
						$b['Status'] = '1';
						$tree[]= $b;
						$dp['Status'] = '1';
					}

				}
				else{
					$dp['Status'] = '1';
				}
				$tree[] = $dp;
			}
		}
		return $tree;

	}

	/**
	 * 启用部门
	 * @param $pid
	 * @param $arr
	 * @param array $tree
	 * @return array|mixed
	 */
	public function unmodifyDeptTree($pid, $arr, &$tree = [])
	{
		foreach ($arr as $key => $dp) {
			if ($dp['ParentId'] == $pid) {
				$c = $this->unmodifyDeptTree($dp['DeptId'], $arr);
				if ($c) {
					foreach ($c as $b){
						$b['Status'] = '0';
						$tree[]= $b;
						$dp['Status'] = '0';
					}

				}
				else{
					$dp['Status'] = '0';
				}
				$tree[] = $dp;
			}
		}
		return $tree;

	}

	/**
	 * Notes: 删除部门数据
	 * User: angelo
	 * DateTime: 2020/12/25 14:16
	 * @param array $deptId '部门ID
	 * @return mixed
	 */
	public function delDept($deptId = [],$by)
	{

		$deptid = array('DeptId'=>$deptId['DeptId']);
		$values['UPDATED_BY'] = $by;
		$values['UPDATED_TIME'] = date('Y-m-d H:i');
//		$del_sql = "delete from base_dept where DeptId ='" . $deptid . "' or ParentId ='" . $deptid . "'";
//		$restulNum = $this->Sys_Model->execute_sql($del_sql, 2);
		$values['DelFlag']='2';
		$result = $this->Sys_Model->table_updateRow('base_dept', $values, $deptid);

		return $result;

	}

	/**
	 * * Notes: 修改部门数据
	 * User: junxiong
	 * DateTime: 2020/12/25 17:00
	 * @param array $values
	 * @return mixed
	 */
	public function modifyDept($values,$by)
	{
		$values['UPDATED_BY'] = $by;
		$values['UPDATED_TIME'] = date('Y-m-d H:i');
		$restul = $this->Sys_Model->table_seleRow('ParentId', "base_dept", array('DeptId' => $values['DeptId']), $like = array());
		if(isset($values['ParentId']) && $values['ParentId']!=$restul[0]['ParentId']){
			$wherein[0]=$restul[0]['ParentId'];//被移动的父部门
			$wherein[1]=$values['DeptId'];//被移动部门
			$wherein[2]=$values['ParentId'];//移动后父部门
			$change=[];
			$deptArr = $this->Sys_Model->table_seleRow('DeptId,DeptNum', "base_dept", $where=array(), $like = array(),$wherein,$whereinfield='DeptId');
			foreach ($deptArr as $item){
				if($item['DeptId']==$values['DeptId']){
					$num=$item['DeptNum'];
					$item['ParentId']=$values['ParentId'];
					array_push($change,$item);
				}
			}
			foreach ($deptArr as $item) {
					if ($item['DeptId'] == $restul[0]['ParentId']) {
						$item['DeptNum'] = $item['DeptNum'] - $num;
						array_push($change, $item);
					}
					if ($item['DeptId'] == $values['ParentId']) {
						$item['DeptNum'] = $item['DeptNum'] + $num;
						array_push($change, $item);
					}
			}

			$restulNum=$this->Sys_Model->table_updateBatchRow("base_dept", $change, $wherekey='DeptId');
		}else{
			$restulNum = $this->Sys_Model->table_updateRow('base_dept', $values, array('DeptId' => $values['DeptId']));
		}
		return $restulNum;
	}

	/**
	 * * * Notes: 移动部门 (下拉)
	 * User: junxiong
	 * DateTime: 2020/12/25 17:29
	 * @param array $values
	 * @return mixed
	 */
	public function moveDept($values)
	{
		$deptArr = $this->Sys_Model->table_seleRow('ParentId', "base_dept", array('DeptId' => $values['DeptId']), $like = array());//查这个部门的父部门
		$where_parent['DeptId'] = $deptArr[0]['ParentId'];
		$deptArr_sec = $this->Sys_Model->table_seleRow('ParentId', "base_dept", $where_parent, $like = array());//查父部门的父部门
		$where_parent_sec = array('Status' => '0','DelFlag'=>'1');
		$where_parent_sec['ParentId'] = $deptArr_sec[0]['ParentId'];
		$deptArr_th = $this->Sys_Model->table_seleRow('DeptId,DeptName ', "base_dept", $where_parent_sec, $like = array());//查传来部门的同级父部门
		return $deptArr_th;

	}

	/**
	 * * * Notes: 停用启用部门
	 * User: junxiong
	 * DateTime: 2020/12/29 15:08
	 * @param $values
	 * @return mixed
	 */
	public function statusDept($values,$by)
	{

		$deptArr = $this->Sys_Model->table_seleRow('DeptId,ParentId,Status', "base_dept", array('DelFlag'=>'1'), $like=array());
		if (count($deptArr) > 0) {
			if($values['Status']=='1'){//停用
				$resultDept = $this->modifyDeptTree($values['DeptId'], $deptArr);
			}
			else{//启用
				$is_array=$this->Sys_Model->table_seleRow('ParentId,Status', "base_dept", array('DeptId'=>$values['DeptId']), $like=array());
				$is_array=$this->Sys_Model->table_seleRow('Status', "base_dept", array('DeptId'=>$is_array[0]['ParentId']), $like=array());
				if($is_array[0]['Status']=='1'){
					$result=[];
					return $result;
				}
				$resultDept = $this->unmodifyDeptTree($values['DeptId'], $deptArr);
			}

		}
		$values['UPDATED_BY'] = $by;
		$values['UPDATED_TIME'] = date('Y-m-d H:i');
		$resultDept[]=$values;
		$User=[];
		foreach ($resultDept as $item){
			$resultUser['UserDept']=$item['DeptId'];
			if($item['Status']=="0"){//判断部门是否启用
				$resultUser['UserStatus']="1";//启用部门下用户
			}else{
				$resultUser['UserStatus']="0";//停用部门下用户
			}
			array_push($User,$resultUser);
		}
		if($User[0]['UserStatus']=='0'){//如果是停用用户，就将其从服务器中删除
			$wherein= array_column($User, 'UserDept');
			$deptArr = $this->Sys_Model->table_seleRow('Mobile', "base_user", $where=array(), $like = array(),$wherein,$whereinfield='UserDept');
			$deptArr= array_column($deptArr, 'Mobile');
			del_reids_key($deptArr);
		}
		$this->Sys_Model->table_updateBatchRow('base_user', $User, 'UserDept');
		$result_dept=$this->Sys_Model->table_updateBatchRow('base_dept', $resultDept, 'DeptId');
		return $result_dept;
	}
}







