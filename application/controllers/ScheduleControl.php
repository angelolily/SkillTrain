<?php


class ScheduleControl extends CI_Controller
{
	private $dataArr = [];//操作数据
	private $userArr = [];//用户数据

	function __construct()
	{
		parent::__construct();
		$this->load->service('Schedule');
		$this->load->helper('tool');
		$receiveArr = file_get_contents('php://input');
		$this->OldDataArr = json_decode($receiveArr, true);
	}
	/**
     *
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

//获取排课表
	public function getRow()
	{
		$keys="rows,pages,class_name,course_name,members_name,members_phone";
		$this->hedVerify($keys);
		$result = $this->schedule->getschedule($this->dataArr);
		if (count($result) >= 0) {
			$resulArr = build_resulArr('D000', true, '获取成功', json_encode($result));
			http_data(200, $resulArr, $this);
		} else {
			$resulArr = build_resulArr('D003', false, '获取失败', []);
			http_data(200, $resulArr, $this);
		}
	}

//根据班级ID搜索排课表
    public function getmemberRow()
    {
        $keys="rows,pages,class_id";
        $this->hedVerify($keys);
        $result = $this->schedule->getmembersh($this->dataArr);
        if (count($result) >= 0) {
            $resulArr = build_resulArr('D000', true, '获取成功', json_encode($result));
            http_data(200, $resulArr, $this);
        } else {
            $resulArr = build_resulArr('D003', false, '获取失败', []);
            http_data(200, $resulArr, $this);
        }
    }


	public function modifyRow()
    {
        $keys="schedule_id";
        $this->hedVerify($keys);
//		$this->hedVerify();
        $result = $this->schedule->modifyschedule($this->dataArr, $this->userArr['Mobile']);
        if (count($result) > 0) {
            $resulArr = build_resulArr('D000', true, '修改成功', []);
            http_data(200, $resulArr, $this);
        } else {
            $resulArr = build_resulArr('D003', false, '修改失败', []);
            http_data(200, $resulArr, $this);
        }
    }
}
