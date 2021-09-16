<?php


/**
 * Class
 */
class Login extends HTY_service
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
		$this->load->helper('redis');

	}
	public  function v2k($array)//把值转换成字符串，并把这个字符串做为键名，再用array_merge()合并
    {
    	$newArr=array();
    	foreach($array as $v){
			$newArr[md5($v)]=$v;//$newArr[menu的MD5]=menu
    	}
    	return $newArr;
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

	/**
	 * Notes:登陆
	 * User: angelo
	 * DateTime: 2020/12/25 14:16
	 * @param array $indData 部门信息
	 * @param $by /添加人员
	 * @return mixed
	 */
	public function Verifylogin($indData = [])
	{

			$resulArr=[];
			$userDataArr = $this->Sys_Model->table_seleRow('UserStatus,UserName,Mobile,UserPassword,UserRole,Sex,IsAdmin,UserDept,UserPost,Avatar',
				'base_user', array('Mobile' => $indData['Mobile']));
			if (count($userDataArr) > 0) {
				if ($userDataArr[0]['UserStatus'] == '1') {
					$pwd = $this->encryption->decrypt($userDataArr[0]['UserPassword']);
					if ($indData['UserPassword'] == $pwd) {
						$resulArr['code'] = "登录成功";
						if($userDataArr[0]['IsAdmin']=="1"){//超级管理员直接登录成功
							set_reids_key($userDataArr[0]['Mobile'],$userDataArr[0]['UserStatus']);
							$resulArr['data'] = $userDataArr[0];
							$resulArr['data']['filpMenuId']="";
							return $resulArr;
						}
						if($userDataArr[0]['UserRole']!=""){
							$deptall=[];
							$deptArr = $this->Sys_Model->table_seleRow('DeptId,ParentId', "base_dept", array('DelFlag'=>'1'), $like=array());
							$owndept[0]=$userDataArr[0]['UserDept'];//自身部门ID
							$deptall=$this->getDeptTree($owndept[0], $deptArr);
							if($deptall){//有儿子外加自己存进去
								foreach ($deptall as $item){
									array_push($owndept,$item['DeptId']);
								}
								$deptall=join(',',$owndept);
							}else{//没儿子就存自己
								$deptall=join(',',$owndept);
							}
							$disRole = explode(',',$userDataArr[0]['UserRole']);
							$DataScope=[];
							$DataScope['2']=[];
							$DataScope['3']=[];
							$DataScope['4']=[];
							$DataScope['5']=[];
							$DataScope['showdata']=[];
							$userDataArr[0]['filpMenuId']=[];
							foreach ($disRole as $item){//搜索用户所有的角色的信息
								$itemdata=$this->Sys_Model->table_seleRow('showmenu,showdata,Name,DataScope,Remark',
									'base_role', array('RoleId' => $item,'Status'=>'1'));
								$first=explode(',',$itemdata[0]['showmenu']);
								if($first!=$userDataArr[0]['filpMenuId']){//合并角色表字段“showmenu”的部门
									$second=explode(',',$itemdata[0]['showmenu']);
									$userDataArr[0]['filpMenuId']=array_flip(array_flip(array_merge($first,$second)));
								}else{
									$userDataArr[0]['filpMenuId']=$first;
								}
								if($itemdata){
//									if($itemdata[0]['DataScope']=='1'){
//										if($DataScope['1']==""){
//											$DataScope['1']=explode(',',$itemdata[0]['showmenu']);
//										}else{
//											$arr1=$DataScope['1'];
//											$arr2=explode(',',$itemdata[0]['showmenu']);
//											$DataScope['1']=array_flip(array_flip(array_merge($arr1,$arr2)));
//
//										}
//									}
									if($itemdata[0]['DataScope']=='2'){
										if($DataScope['2']==""){
											$DataScope['2']=explode(',',$itemdata[0]['showmenu']);
											$DataScope['showdata']=explode(',',$itemdata[0]['showdata']);
										}else{
											$arr1=$DataScope['2'];
											$arr2=explode(',',$itemdata[0]['showmenu']);
											$arr3=$DataScope['showdata'];
											$arr4=explode(',',$itemdata[0]['showdata']);
											$DataScope['2']=array_flip(array_flip(array_merge($arr1,$arr2)));
											$DataScope['showdata']=array_flip(array_flip(array_merge($arr3,$arr4)));
										}
									}
									if($itemdata[0]['DataScope']=='3'){
										if($DataScope['3']==""){
											$DataScope['3']=explode(',',$itemdata[0]['showmenu']);
										}else{
											$arr1=$DataScope['3'];
											$arr2=explode(',',$itemdata[0]['showmenu']);
											$DataScope['3']=array_flip(array_flip(array_merge($arr1,$arr2)));

										}
									}
									if($itemdata[0]['DataScope']=='4'){
										if($DataScope['4']==""){
											$DataScope['4']=explode(',',$itemdata[0]['showmenu']);
										}else{
											$arr1=$DataScope['4'];
											$arr2=explode(',',$itemdata[0]['showmenu']);
											$DataScope['4']=array_flip(array_flip(array_merge($arr1,$arr2)));

										}
									}
									if($itemdata[0]['DataScope']=='5'){
										if($DataScope['5']==""){
											$DataScope['5']=explode(',',$itemdata[0]['showmenu']);
										}else{
											$arr1=$DataScope['5'];
											$arr2=explode(',',$itemdata[0]['showmenu']);
											$DataScope['5']=array_flip(array_flip(array_merge($arr1,$arr2)));

										}
									}
								}
							}
							if($DataScope){
								$restultdata=[];
								if($DataScope['5']){
									foreach ($DataScope['5'] as $item){
										$restultdata[$item]=5;
										$restultdata['powerdept'][5]=$owndept[0];
									}
								}
								if($DataScope['4']){
									foreach ($DataScope['4'] as $item){
										$restultdata[$item]=4;
										$restultdata['powerdept'][4]=$owndept[0];
									}
								}
								if($DataScope['3']){
									foreach ($DataScope['3'] as $item){
										$restultdata[$item]=3;
										$restultdata['powerdept'][3]=$deptall;
									}
								}
								if($DataScope['2']){
									foreach ($DataScope['2'] as $item){
										$restultdata[$item]=2;
										$restultdata['powerdept'][2]=join(",",$DataScope['showdata']);
									}
								}
								$userDataArr[0]['menudept']= $restultdata;
							}
						}
						set_reids_key($userDataArr[0]['Mobile'],$userDataArr[0]['UserStatus']);
						$userDataArr[0]['filpMenuId']=join(',',$userDataArr[0]['filpMenuId']);
						$resulArr['data'] = $userDataArr[0];
					}else{
						$resulArr['code'] = "密码错误";
						$resulArr['data'] = "";
					}

				} else {
					$resulArr['code'] = "用户被停用";
					$resulArr['data'] = "";
				}

			} else {
				$resulArr['data'] = "";
				$resulArr['code'] = "用户不存在";
			}
			return $resulArr;

		}

}







