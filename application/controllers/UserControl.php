<?php


class UserControl extends CI_Controller
{
	private $dataArr = [];//操作数据
	private $userArr = [];//用户数据

	function __construct()
	{
		parent::__construct();
		$this->load->service('Usermanage');
		$this->load->helper('tool');
		$receiveArr = file_get_contents('php://input');
		$this->OldDataArr = json_decode($receiveArr, true);



	}



	/**
	 * Notes:前置验证，将用户信息与数据分离
	 * User: lchangelo
	 * DateTime: 2020/12/24 14:39
	 */
	private function hedVerify($keys="")
	{

		if ($this->OldDataArr) {
			if (count($this->OldDataArr) > 0) {
				if($keys!="")
				{
					$errorKey=existsArrayKey($keys,$this->OldDataArr);
					if($errorKey=="")
					{
						$this->userArr['Mobile'] = $this->OldDataArr['phone'];
					}
					else
					{
						$resulArr = build_resulArr('S003', false, '参数缺失', []);
						http_data(200, $resulArr, $this);
					}
				}
				$this->dataArr = bykey_reitem($this->OldDataArr, 'phone');
				$this->dataArr = bykey_reitem($this->dataArr, 'timestamp');
				$this->dataArr = bykey_reitem($this->dataArr, 'signature');
			} else {
				$resulArr = build_resulArr('S002', false, '无接收', []);
				http_data(200, $resulArr, $this);
			}
		} else {
			$resulArr = build_resulArr('S002', false, '无接收', []);
			http_data(200, $resulArr, $this);

		}
	}


	/**
	 * Notes:部门新增记录
	 * User: lchangelo
	 * DateTime: 2020/12/24 14:41
	 */
	public function newRow()
	{
		$keys="UserDept,Mobile,UserName,UserEmail,UserPassword,Sex,UserStatus,Birthday,UserRole,Remark,UserPost,IsAdmin";
		$this->hedVerify($keys);
		$resultNum = $this->usermanage->addData($this->dataArr, $this->userArr['Mobile']);
		if (count($resultNum) > 0) {
			$resulArr = build_resulArr('D000', true, '插入成功', []);
			http_data(200, $resulArr, $this);
		} else {
			$resulArr = build_resulArr('D002', false, '手机号重复', []);
			http_data(200, $resulArr, $this);
		}


	}


	/**
	 * Notes:获取用户信息
	 * User: angelo
	 * DateTime: 2020/12/25 10:01
	 */

	public function getRow()
	{
		$keys="Mobile,UserName,UserStatus,DeptId,pages,rows,begin,end,DataScope,powerdept";
		$this->hedVerify($keys);
        $this->hedVerify();
		$result = $this->usermanage->getUser($this->dataArr,$this->userArr['Mobile']);
		if (count($result) >= 0) {
			$resulArr = build_resulArr('D000', true, '获取成功', json_encode($result));
			http_data(200, $resulArr, $this);
		} else {
			$resulArr = build_resulArr('D003', false, '获取失败', []);
			http_data(200, $resulArr, $this);
		}
	}


	public function delRow()
	{
		$keys="Userid,UserDept";
		$this->hedVerify($keys);
//		$this->hedVerify();
		$result = $this->usermanage->delUser($this->dataArr);
		if (count($result) > 0) {
			$resulArr = build_resulArr('D000', true, '删除成功', []);
			http_data(200, $resulArr, $this);
		} else {
			$resulArr = build_resulArr('D003', false, '删除失败', []);
			http_data(200, $resulArr, $this);
		}

	}
	public function modifyRow()
	{
		$keys="UserDept,Mobile,UserName,UserEmail,Sex,UserStatus,Birthday,UserRole,Remark,UserPost,IsAdmin,Userid";
		$this->hedVerify($keys);
//		$this->hedVerify();
		$result = $this->usermanage->modifyUser($this->dataArr, $this->userArr['Mobile']);
		if (count($result) > 0) {
			$resulArr = build_resulArr('D000', true, '修改成功', []);
			http_data(200, $resulArr, $this);
		} else {
			$resulArr = build_resulArr('D003', false, '修改失败', []);
			http_data(200, $resulArr, $this);
		}

	}
	//重置密码
	public function resetRow()
	{
		$keys="Userid";
		$this->hedVerify($keys);
//		$this->hedVerify();
		$result = $this->usermanage->resetPassword($this->dataArr, $this->userArr['Mobile']);
		if (count($result) > 0) {
			$resulArr = build_resulArr('D000', true, '重置密码成功',[]);
			http_data(200, $resulArr, $this);
		} else {
			$resulArr = build_resulArr('D003', false, '重置密码失败', []);
			http_data(200, $resulArr, $this);
		}


	}

}
