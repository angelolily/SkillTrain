<?php


class CertControl extends CI_Controller
{
	private $dataArr = [];//操作数据
	private $userArr = [];//用户数据

	function __construct()
	{
		parent::__construct();
		$this->load->service('Cert');
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
		$keys="cert_name";
		$this->hedVerify($keys);
		$resultNum = $this->cert->addData($this->dataArr, $this->userArr['Mobile']);
		if ($resultNum) {
			$resulArr = build_resulArr('D000', true, '插入成功', []);
			http_data(200, $resulArr, $this);
		} else {
			$resulArr = build_resulArr('D002', false, '插入失败', []);
			http_data(200, $resulArr, $this);
		}
	}

//获取
	public function getRow()
	{
        $keys="rows,pages,cert_name,members_name";
        $this->hedVerify($keys);
        $result = $this->cert->getcert($this->dataArr);
        if (count($result) >= 0) {
            $base_url='https://'.$_SERVER['HTTP_HOST'].substr($_SERVER['PHP_SELF'],0,strrpos($_SERVER['PHP_SELF'],'/index.php')+1);
            $url = $base_url.'public/';
            for($i=0;$i<count($result['data']);$i++){
                $img_arr = [];
                $result['data'][$i]['cert_path_show'] = $url.$result['data'][$i]['cert_path'];
                $pass_img_arr = explode(',',$result['data'][$i]['process_data']);
                for($j=0;$j<count($pass_img_arr);$j++){
                    array_push($img_arr,$url.$result['data'][$i]['cert_dir'].'/'.$pass_img_arr[$j]);
                }
                $result['data'][$i]['process_data_show']=$img_arr;
            }
            $resulArr = build_resulArr('D000', true, '获取成功', json_encode($result));
            http_data(200, $resulArr, $this);
        } else {
            $resulArr = build_resulArr('D003', false, '获取失败', []);
            http_data(200, $resulArr, $this);
        }
    }



//修改
	public function modifyRow()
    {
        $keys="cert_id";
        $this->hedVerify($keys);
//		$this->hedVerify();
        $result = $this->cert->modifycert($this->dataArr, $this->userArr['Mobile']);
        if ($result) {
            $resulArr = build_resulArr('D000', true, '修改成功', []);
            http_data(200, $resulArr, $this);
        } else {
            $resulArr = build_resulArr('D003', false, '修改失败', []);
            http_data(200, $resulArr, $this);
        }
    }

    //删除
    public function delRow()
    {
        $keys="cert_id";
        $this->hedVerify($keys);
//		$this->hedVerify();
        $result = $this->cert->delcert($this->dataArr);
        if ($result) {
            $resulArr = build_resulArr('D000', true, '删除成功', []);
            http_data(200, $resulArr, $this);
        } else {
            $resulArr = build_resulArr('D003', false, '删除失败', []);
            http_data(200, $resulArr, $this);
        }
    }

    public function manyimageupload()  //多图片上传
    {
		$result = $this->cert->manyimageupload($this->dataArr);
        if (count($result) > 0) {
            $resulArr = build_resulArr('D000', true, '上传成功', json_encode($result));
            http_data(200, $resulArr, $this);
        } else {
            $resulArr = build_resulArr('D003', false, '上传失败', []);
            http_data(200, $resulArr, $this);
        }
    }

    public function readmaneypic()  //读取目录下多图片
    {
        $keys="process_data";
        $this->hedVerify($keys);
//		$this->hedVerify();
        $result = $this->cert->readmaneypic($this->dataArr);
        if (count($result) > 0) {
            $resulArr = build_resulArr('D000', true, '新增成功', json_encode($result));
            http_data(200, $resulArr, $this);
        } else {
            $resulArr = build_resulArr('D003', false, '新增失败', []);
            http_data(200, $resulArr, $this);
        }

    }

    //人员下拉
    public function membersdata()
    {
        $keys="";
        $this->hedVerify($keys);
//		$this->hedVerify();
        $result = $this->cert->membersdata();
        if ($result) {
            $resulArr = build_resulArr('D000', true, '下拉成功', $result);
            http_data(200, $resulArr, $this);
        } else {
            $resulArr = build_resulArr('D003', false, '下拉失败', []);
            http_data(200, $resulArr, $this);
        }
    }
}
