<?php


class LoginControl extends CI_Controller
{
	private $dataArr = [];//操作数据

	function __construct()
	{
		parent::__construct();
		$this->load->service('Login');
		$this->load->helper('tool');
	}
	/**
	 * Notes:用户登录
	 * User: ljx
	 * DateTime: 2021/1/22 14:41A
	 */
	public function loginRow()
	{
		$dataArr=[];
		$receiveArr = file_get_contents('php://input');
		if ($receiveArr) {
			$dataArr = json_decode($receiveArr, true);
		}
		$result = $this->login->Verifylogin($dataArr);
		if ($result['data']!="") {
			$resulArr = build_resulArr('D000', true, '登录成功',json_encode($result['data']));
			http_data(200, $resulArr, $this);
		} else {
			if($result['code']=="密码错误"){
				$resulArr = build_resulArr('D001', true, '密码错误',[]);
				http_data(200, $resulArr, $this);
			}
			if($result['code']=="用户被停用"){
				$resulArr = build_resulArr('D002', true, '用户被停用',[]);
				http_data(200, $resulArr, $this);
			}
			if($result['code']=="用户不存在"){
				$resulArr = build_resulArr('D003', false, '用户不存在', []);
				http_data(200, $resulArr, $this);
			}

		}

	}


}
