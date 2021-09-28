<?php


class CourseControl extends CI_Controller
{
	private $dataArr = [];//操作数据
	private $userArr = [];//用户数据

	function __construct()
	{
		parent::__construct();
		$this->load->service('Course');
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
		$keys="course_name,course_describe,course_type,course_graphic,course_cover,course_ishome,course_sex_limit,course_limitup,course_limitdown,course_number_limit,course_beginDate,course_endDate,course_signBegin,course_signEnd,course_signPrice,course_signIntegral";
		$this->hedVerify($keys);
		$resultNum = $this->course->addData($this->dataArr, $this->userArr['Mobile']);
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
        $result = $this->course->imageuploaddetail($this->dataArr);
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
        $result = $this->course->getimagedetail($this->dataArr);
        if (count($result)> 0) {
            $resulArr = build_resulArr('D000', true, '显示成功', json_encode($result));
            http_data(200, $resulArr, $this);
        } else {
            $resulArr = build_resulArr('D002', false, '显示失败', []);
            http_data(200, $resulArr, $this);
        }
    }
//上传封面图
    public function Uploadcover()
    {
        $result = $this->course->imageuploadcover($this->dataArr);
        if (count($result )> 0) {
            $resulArr = build_resulArr('D000', true, '导入成功', json_encode($result));
            http_data(200, $resulArr, $this);
        } else {
            $resulArr = build_resulArr('D002', false, '导入失败', []);
            http_data(200, $resulArr, $this);
        }
    }
//显示封面图
    public function findcover()
    {
        $this->hedVerify();//前置验证
        $result = $this->course->getimagecover($this->dataArr);
        if (count($result)> 0) {
            $resulArr = build_resulArr('D000', true, '显示成功', json_encode($result));
            http_data(200, $resulArr, $this);
        } else {
            $resulArr = build_resulArr('D002', false, '显示失败', []);
            http_data(200, $resulArr, $this);
        }
    }
    //活动下拉
    public function showRow()
    {
        $this->hedVerify();//前置验证
        $result = $this->course->showcourse($this->dataArr);
        if (count($result) > 0) {
            $resulArr = build_resulArr('D000', true, '显示成功', json_encode($result));
            http_data(200, $resulArr, $this);
        } else {
            $resulArr = build_resulArr('D003', false, '显示失败', []);
            http_data(200, $resulArr, $this);
        }
    }
//获取
	public function getRow()
	{
		$keys="rows,pages,course_id";
		$this->hedVerify($keys);
		$result = $this->course->getcourse($this->dataArr);
		if (count($result) >= 0) {
			$resulArr = build_resulArr('D000', true, '获取成功', json_encode($result));
			http_data(200, $resulArr, $this);
		} else {
			$resulArr = build_resulArr('D003', false, '获取失败', []);
			http_data(200, $resulArr, $this);
		}


	}

//    public function publishRow()
//    {
//        $keys="course_id";
//        $this->hedVerify($keys);
//        $result = $this->course->publishaa($this->dataArr);
//        if (count($result) >= 0) {
//            $resulArr = build_resulArr('D000', true, '发布成功', []);
//            http_data(200, $resulArr, $this);
//        } else {
//            $resulArr = build_resulArr('D003', false, '发布失败', []);
//            http_data(200, $resulArr, $this);
//        }
//
//
//    }

    public function finallyRow()
    {
        $keys="course_id";
        $this->hedVerify($keys);
        $result = $this->course->finallycourse($this->dataArr);
        if (count($result) >= 0) {
            $resulArr = build_resulArr('D000', true, '结束成功', []);
            http_data(200, $resulArr, $this);
        } else {
            $resulArr = build_resulArr('D003', false, '结束失败', []);
            http_data(200, $resulArr, $this);
        }


    }


	public function delRow()
	{
		$keys="course_id";
		$this->hedVerify($keys);
//		$this->hedVerify();
		$result = $this->course->delcourse($this->dataArr);
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
        $keys="course_id,course_name,course_describe,course_type,course_graphic,course_cover,course_ishome,course_sex_limit,course_limitup,course_limitdown,course_number_limit,course_beginDate,course_endDate,course_signBegin,course_signEnd,course_signPrice,course_signIntegral";
        $this->hedVerify($keys);
//		$this->hedVerify();
        $result = $this->course->modifycourse($this->dataArr, $this->userArr['Mobile']);
        if (count($result) > 0) {
            $resulArr = build_resulArr('D000', true, '修改成功', []);
            http_data(200, $resulArr, $this);
        } else {
            $resulArr = build_resulArr('D003', false, '修改失败', []);
            http_data(200, $resulArr, $this);
        }
    }

//    public function lowactivity()
//    {
//        $keys="course_id";
//        $this->hedVerify($keys);
////		$this->hedVerify();
//        $result = $this->course->finallycommodity($this->dataArr);
//        if (count($result) > 0) {
//            $resulArr = build_resulArr('D000', true, '修改成功', []);
//            http_data(200, $resulArr, $this);
//        } else {
//            $resulArr = build_resulArr('D003', false, '修改失败', []);
//            http_data(200, $resulArr, $this);
//        }
//    }




}
