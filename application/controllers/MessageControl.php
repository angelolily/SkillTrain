<?php

/**
 * @OA\Info(
 *  title="职技通",
 *  version="1.0.0"
 * )
 *
 */
class MessageControl extends CI_Controller
{


	private $dataArr = [];//操作数据

	function __construct()
	{
		parent::__construct();
		$this->load->helper('tool');
        $this->load->service('MsgService');
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
     * Notes:获取消息数据
     * User: lchangelo
     * DateTime: 2021/09/28 15:39
     */
    public function  getMessageinfo(){
        $keys="class_id,members_name,pages,rows";
        $this->hedVerify($keys);
        $result = $this->msgservice->getMessage($this->dataArr);
        if (count($result) >= 0) {
            $resulArr = build_resulArr('D000', true, '获取成功', json_encode($result));
            http_data(200, $resulArr, $this);
        } else {
            $resulArr = build_resulArr('D003', false, '获取失败', []);
            http_data(200, $resulArr, $this);
        }

    }

        /**
     * Notes:新增消息
     * User: lchangelo
     * DateTime: 2021/09/28 15:39
     * Modify:lchangelo 将签名串从data中摘除
     * DateTime: 2021/10/18 16:08
     */
    public function  addMessageinfo(){
        //$keys="class_id,members_id,members_openid,members_name,message_title,message_center";
        $ins_data=[];
        $temdata=[];
        $receiveArr = file_get_contents('php://input');

        $data_arr=json_decode($receiveArr,true);

        if(count($data_arr)>0)
        {
            $phone=$data_arr['phone'];
            foreach ($data_arr['data'] as $rows){
                $temdata['created_by']=$phone;
                $temdata['created_time']=date("Y-m-d H:i:s");
                $temdata['class_id']=$rows['class_id'];
                $temdata['members_id']=$rows['members_id'];
                $temdata['members_openid']=$rows['members_openid'];
                $temdata['members_name']=$rows['members_name'];
                $temdata['message_title']=$rows['message_title'];
                $temdata['message_center']=$rows['message_center'];
                array_push($ins_data,$temdata);
            }
            $result = $this->msgservice->addMessage($ins_data);
        }


        if ($result == 1) {
            $resulArr = build_resulArr('MA000', true, '新增成功', json_encode($result));
            http_data(200, $resulArr, $this);
        } elseif ($result == 2) {
            $resulArr = build_resulArr('MA001', false, '新增失败，部分成功', []);
            http_data(200, $resulArr, $this);
        }elseif($result == 3){
            $resulArr = build_resulArr('MA002', false, '新增失败', []);
            http_data(200, $resulArr, $this);
        }

    }


    /**
     * Notes:获取考勤主表
     * User: lchangelo
     * DateTime: 2021/09/29 15:39
     */
    public function getAttendanceRow()
    {
        $keys="rows,pages,class_name,course_name,members_name,members_phone";
        $this->hedVerify($keys);
        $result = $this->msgservice->getAttendance($this->dataArr);
        if (count($result) >= 0) {
            $resulArr = build_resulArr('D000', true, '获取成功', json_encode($result));
            http_data(200, $resulArr, $this);
        } else {
            $resulArr = build_resulArr('D003', false, '获取失败', []);
            http_data(200, $resulArr, $this);
        }
    }

    /**
     * Notes:获取考勤明细表
     * User: lchangelo
     * DateTime: 2021/09/29 15:39
     */
    public function getAttDetailRow()
    {
        $keys="rows,pages,class_id,course_id,members_id";
        $this->hedVerify($keys);
        $result = $this->msgservice->getAttendDetail($this->dataArr);
        if (count($result) >= 0) {
            $resulArr = build_resulArr('D000', true, '获取成功', json_encode($result));
            http_data(200, $resulArr, $this);
        } else {
            $resulArr = build_resulArr('D003', false, '获取失败', []);
            http_data(200, $resulArr, $this);
        }
    }

    /**
     * Notes:获取课程考勤二维码
     * User: lchangelo
     * DateTime: 2021/09/29 15:39
     */

    public function getCrouseQR()
    {
        $keys="qrtext";
        $this->hedVerify($keys);
        $filenamne=time().rand(1111,9999);
        $qrsave="./public/qrclass/".$filenamne.".png";
        buildQr($this->dataArr['qrtext'],$qrsave);
        if(file_exists($qrsave)){
            $base_url='https://'.$_SERVER['HTTP_HOST'].substr($_SERVER['PHP_SELF'],0,strrpos($_SERVER['PHP_SELF'],'/index.php')+1);
            $result['qrpath']=$base_url."/public/qrclass/".$filenamne.".png";
            $resulArr = build_resulArr('D000', true, '生成成功成功', json_encode($result));
            http_data(200, $resulArr, $this);
        }
        else {
            $resulArr = build_resulArr('D003', false, '获取失败', []);
            http_data(200, $resulArr, $this);
        }
    }
}
