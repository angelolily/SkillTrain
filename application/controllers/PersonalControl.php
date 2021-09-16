<?php


class PersonalControl extends CI_Controller
{
	private $dataArr = [];//操作数据
	private $userArr = [];//用户数据

	function __construct()
	{
		parent::__construct();
		$this->load->service('Personal');
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
	 * Notes:获取用户个人信息
	 * User: ljx
	 * DateTime: 2020/1/7 9:51
	 */
	public function getRow()
	{
		$keys="Mobile";
		$this->hedVerify($keys);
		$result = $this->personal->getPersonal($this->dataArr,$this->userArr['Mobile']);
		if (count($result) >0) {
			$resulArr = build_resulArr('D000', true, '获取成功', json_encode($result['data'][0]));
			http_data(200, $resulArr, $this);
		} else {
			$resulArr = build_resulArr('D003', false, '获取失败', []);
			http_data(200, $resulArr, $this);
		}


	}

	/**
	 ** Notes:修改用户密码
	 * User: ljx
	 * DateTime: 2020/1/7 16:51
	 */
	public function modifyRow()
	{
		$keys="Mobile,UserPassword,newPassword";
		$this->hedVerify($keys);
		$result = $this->personal->modifyPersonal($this->dataArr,$this->userArr['Mobile']);
		if (count($result) > 0) {
			$resulArr = build_resulArr('D000', true, '密码修改成功', []);
			http_data(200, $resulArr, $this);
		} else {
			$resulArr = build_resulArr('D003', false, '旧密码错误', []);
			http_data(200, $resulArr, $this);
		}
	}


	/**
	 ** Notes:上传头像
	 * User: ljx
	 * DateTime: 2020/1/7 16:51
	 */
	public function headPortraitRow()
	{
		$keys="Mobile,image";
		$this->hedVerify($keys);
		$result = $this->personal->headPortrait($this->dataArr,$this->userArr['Mobile']);
		if (count($result) > 0) {
			$resulArr = build_resulArr('D000', true, '显示成功', $result);
			http_data(200, $resulArr, $this);
		} else {
			$resulArr = build_resulArr('D003', false, '显示失败', []);
			http_data(200, $resulArr, $this);
		}


	}


}
