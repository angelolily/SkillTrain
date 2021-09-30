<?php


class CoursewareControl extends CI_Controller
{
	private $dataArr = [];//操作数据
	private $userArr = [];//用户数据

	function __construct()
	{
		parent::__construct();
		$this->load->service('Courseware');
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
	 * Notes:新增记录
	 * User: ljx
	 *
	 */
	public function newRow()
	{
		$keys="course_name,course_num,course_id";
		$this->hedVerify($keys);
		$resultNum = $this->courseware->addData($this->dataArr, $this->userArr['Mobile']);
		if (count($resultNum )> 0) {
			$resulArr = build_resulArr('D000', true, '插入成功', []);
			http_data(200, $resulArr, $this);
		} else {
			$resulArr = build_resulArr('D002', false, '插入失败', []);
			http_data(200, $resulArr, $this);
		}
	}
//上传详情图
    public function Uploaddetail()
    {
        $result = $this->courseware->imageuploaddetail($this->dataArr);
        if (count($result )> 0) {
            $resulArr = build_resulArr('D000', true, '插入成功', json_encode($result));
            http_data(200, $resulArr, $this);
        } else {
            $resulArr = build_resulArr('D002', false, '插入失败', []);
            http_data(200, $resulArr, $this);
        }
    }
//显示详情图
    public function finddetail()
    {
        $this->hedVerify();//前置验证
        $result = $this->courseware->getimagedetail($this->dataArr);
        if (count($result)> 0) {
            $resulArr = build_resulArr('D000', true, '显示成功', json_encode($result));
            http_data(200, $resulArr, $this);
        } else {
            $resulArr = build_resulArr('D002', false, '显示失败', []);
            http_data(200, $resulArr, $this);
        }
    }


//获取
	public function getRow()
	{
		$keys="rows,pages,course_id";
		$this->hedVerify($keys);
		$result = $this->courseware->getcourseware($this->dataArr);
		if (count($result) >= 0) {
			$resulArr = build_resulArr('D000', true, '获取成功', json_encode($result));
			http_data(200, $resulArr, $this);
		} else {
			$resulArr = build_resulArr('D003', false, '获取失败', []);
			http_data(200, $resulArr, $this);
		}
	}
//删除
	public function delRow()
	{
		$keys="course_id,course_num";
		$this->hedVerify($keys);
//		$this->hedVerify();
		$result = $this->courseware->delcourseware($this->dataArr);
		if (count($result) > 0) {
			$resulArr = build_resulArr('D000', true, '删除成功', []);
			http_data(200, $resulArr, $this);
		} else {
			$resulArr = build_resulArr('D003', false, '删除失败', []);
			http_data(200, $resulArr, $this);
		}
	}
//修改
	public function modifyRow()
    {
        $keys="course_num,course_id";
        $this->hedVerify($keys);
//		$this->hedVerify();
        $result = $this->courseware->modifycourseware($this->dataArr, $this->userArr['Mobile']);
        if (count($result) > 0) {
            $resulArr = build_resulArr('D000', true, '修改成功', []);
            http_data(200, $resulArr, $this);
        } else {
            $resulArr = build_resulArr('D003', false, '修改失败', []);
            http_data(200, $resulArr, $this);
        }
    }
}
