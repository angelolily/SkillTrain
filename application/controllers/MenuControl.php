<?php


class MenuControl extends CI_Controller
{
	private $dataArr = [];//操作数据
	private $userArr = [];//用户数据

	function __construct()
	{
		parent::__construct();
		$this->load->service('Menu');
		$this->load->helper('tool');

	}


	public function newRow()
	{

		$receiveArr = file_get_contents('php://input');
		$DataArr = json_decode($receiveArr, true);
		$userData=$DataArr['phone'];
		$DataArr = bykey_reitem($DataArr, 'timestamp');
		$DataArr = bykey_reitem($DataArr, 'signature');
		$DataArr = bykey_reitem($DataArr, 'username');
		$DataArr = bykey_reitem($DataArr, 'phone');

		$keys="menuname,parentid,title,icon,path,component";
		$errorKey=existsArrayKey($keys,$DataArr);
		if($errorKey=="")
		{

			$resultNum = $this->menu->addMenu($DataArr, $userData);
			if ($resultNum > 0) {
				$resulArr = build_resulArr('M000', true, '插入成功', []);
				http_data(200, $resulArr, $this);
			} else {
				$resulArr = build_resulArr('M002', false, '插入失败', []);
				http_data(200, $resulArr, $this);
			}
		}
		else
		{
			$resulArr = build_resulArr('M001', false, $errorKey.'这些参数未传', []);
			http_data(200, $resulArr, $this);
		}

	}

	public function delRow()
	{

		$receiveArr = file_get_contents('php://input');
		$DataArr = json_decode($receiveArr, true);
		$DataArr = bykey_reitem($DataArr, 'timestamp');
		$DataArr = bykey_reitem($DataArr, 'signature');
		$DataArr = bykey_reitem($DataArr, 'phone');

		$keys="menuid";
		$errorKey=existsArrayKey($keys,$DataArr);
		if($errorKey=="")
		{
			$result = $this->menu->delMenu($DataArr['menuid']);
			if (count($result) > 0) {
				$resulArr = build_resulArr('M000', true, '删除成功',[] );
				http_data(200, $resulArr, $this);
			} else {
				$resulArr = build_resulArr('M003', false, '删除失败', []);
				http_data(200, $resulArr, $this);
			}
		}
		else
		{
			$resulArr = build_resulArr('M001', false, $errorKey.'这些参数未传', []);
			http_data(200, $resulArr, $this);
		}



	}

	public function updateRow()
	{

		$receiveArr = file_get_contents('php://input');
		$DataArr = json_decode($receiveArr, true);
		$userData=$DataArr['phone'];
		$DataArr = bykey_reitem($DataArr, 'timestamp');
		$DataArr = bykey_reitem($DataArr, 'signature');
		$DataArr = bykey_reitem($DataArr, 'username');
		$DataArr = bykey_reitem($DataArr, 'phone');

		$keys="menuid,menuname,parentid,title,icon,path";
		$errorKey=existsArrayKey($keys,$DataArr);
		if($errorKey=="")
		{
			$result = $this->menu->updateMenu($DataArr['menuid'],$DataArr,$userData);
			if ($result > 0) {
				$resulArr = build_resulArr('M000', true, '修改成功',[] );
				http_data(200, $resulArr, $this);
			} else {
				$resulArr = build_resulArr('M003', false, '修改失败', []);
				http_data(200, $resulArr, $this);
			}

		}
		else {
			$resulArr = build_resulArr('M001', false, $errorKey.'这些参数未传', []);
			http_data(200, $resulArr, $this);
		}


	}

	public function getRow()
	{

		$receiveArr = file_get_contents('php://input');
		$DataArr = json_decode($receiveArr, true);

		$DataArr = bykey_reitem($DataArr, 'timestamp');
		$DataArr = bykey_reitem($DataArr, 'signature');
		//$DataArr = bykey_reitem($DataArr, 'filpMenuId');


		$result = $this->menu->getMenu($DataArr['filpMenuId']);
		if (count($result) > 0) {
//			$strResult=strtolower(json_encode($result));
			$resulArr = build_resulArr('M000', true, '获取成功', json_encode($result,true));
			http_data(200, $resulArr, $this);
		} else {
			$resulArr = build_resulArr('M004', false, '获取失败', []);
			http_data(200, $resulArr, $this);
		}




	}

	public function allRefresh(){
		$receiveArr = file_get_contents('php://input');
		$DataArr = json_decode($receiveArr, true);
		$userData=$DataArr['phone'];
		$DataArr = bykey_reitem($DataArr, 'timestamp');
		$DataArr = bykey_reitem($DataArr, 'signature');
		$DataArr = bykey_reitem($DataArr, 'username');
		$DataArr = bykey_reitem($DataArr, 'phone');

		$keys="treeData";
		$errorKey=existsArrayKey($keys,$DataArr);
		if($errorKey=="")
		{

			$result = $this->menu->changeLevel($DataArr['treeData']);
			if ($result > 0) {
				$resulArr = build_resulArr('M000', true, '修改成功',[] );
				http_data(200, $resulArr, $this);
			} else {
				$resulArr = build_resulArr('M003', false, '修改失败', []);
				http_data(200, $resulArr, $this);
			}

		}
		else {
			$resulArr = build_resulArr('M001', false, $errorKey.'这些参数未传', []);
			http_data(200, $resulArr, $this);
		}


	}




}
