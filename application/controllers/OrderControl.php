<?php


class OrderControl extends CI_Controller
{
	private $dataArr = [];//操作数据
	private $userArr = [];//用户数据

	function __construct()
	{
		parent::__construct();
		$this->load->service('Order');
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
	 * Notes:获取订单信息
	 * User: angelo
	 * DateTime: 2020/12/25 10:01
	 */

	public function getRow()
	{
		$keys="pages,rows,order_customer_name,order_capid,DataScope,order_deptid,powerdept,order_statue";
		$this->hedVerify($keys);
		$result = $this->order->getOrder($this->dataArr);
		if (count($result) >= 0) {
			$resulArr = build_resulArr('D000', true, '获取成功', json_encode($result));
			http_data(200, $resulArr, $this);
		} else {
			$resulArr = build_resulArr('D003', false, '获取失败', []);
			http_data(200, $resulArr, $this);
		}
	}
    public function getallRow()
    {
        $keys="pages,rows,order_customer_phone,order_id,DataScope,order_product,powerdept,order_refund_flag";
        $this->hedVerify($keys);
        $result = $this->order->getallOrder($this->dataArr);
        if (count($result) >= 0) {
            $resulArr = build_resulArr('D000', true, '获取成功', json_encode($result));
            http_data(200, $resulArr, $this);
        } else {
            $resulArr = build_resulArr('D003', false, '获取失败', []);
            http_data(200, $resulArr, $this);
        }
    }
    public function getactivityRow()
    {
        $keys="pages,rows,order_id,DataScope,order_product,powerdept,order_statue";
        $this->hedVerify($keys);
        $result = $this->order->getactivityOrder($this->dataArr);
        if (count($result) >= 0) {
            $resulArr = build_resulArr('D000', true, '获取成功', json_encode($result));
            http_data(200, $resulArr, $this);
        } else {
            $resulArr = build_resulArr('D003', false, '获取失败', []);
            http_data(200, $resulArr, $this);
        }
    }
    public function getcourseRow()
    {
        $keys="pages,rows,order_id,DataScope,order_product,powerdept,order_statue";
        $this->hedVerify($keys);
        $result = $this->order->getcourseOrder($this->dataArr);
        if (count($result) >= 0) {
            $resulArr = build_resulArr('D000', true, '获取成功', json_encode($result));
            http_data(200, $resulArr, $this);
        } else {
            $resulArr = build_resulArr('D003', false, '获取失败', []);
            http_data(200, $resulArr, $this);
        }
    }
    public function getdetialOrder()
    {
        $keys="pages,rows,order_id";
        $this->hedVerify($keys);
        $result = $this->order->getdetialOrder($this->dataArr);
        if (count($result) >= 0) {
            $resulArr = build_resulArr('D000', true, '获取成功', json_encode($result));
            http_data(200, $resulArr, $this);
        } else {
            $resulArr = build_resulArr('D003', false, '获取失败', []);
            http_data(200, $resulArr, $this);
        }
    }

	public function yesornoRow()//同意，不同意
	{
		$keys="order_id,ID,order_capid,order_deptid,members_id,order_customer_name,order_customer_phone,order_refund_rate";
		$this->hedVerify($keys);
//		$this->hedVerify();
		$result = $this->order->modifyyesorno($this->dataArr, $this->userArr['Mobile']);
		if (count($result) > 0) {
			$resulArr = build_resulArr('D000', true, '修改成功', []);
			http_data(200, $resulArr, $this);
		} else {
			$resulArr = build_resulArr('D003', false, '修改失败', []);
			http_data(200, $resulArr, $this);
		}
	}

    public function modifypriceRow()//修改价格
    {
        $keys="order_id,order_price";
        $this->hedVerify($keys);
//		$this->hedVerify();
        $result = $this->order->modifyprice($this->dataArr, $this->userArr['Mobile']);
        if (count($result) > 0) {
            $resulArr = build_resulArr('D000', true, '修改成功', []);
            http_data(200, $resulArr, $this);
        } else {
            $resulArr = build_resulArr('D003', false, '修改失败', []);
            http_data(200, $resulArr, $this);
        }
    }

    public function modifystatuRow()//修改订单状态
    {
        $keys="order_id";
        $this->hedVerify($keys);
//		$this->hedVerify();
        $result = $this->order->modifystatu($this->dataArr, $this->userArr['Mobile']);
        if (count($result) > 0) {
            $resulArr = build_resulArr('D000', true, '修改成功', []);
            http_data(200, $resulArr, $this);
        } else {
            $resulArr = build_resulArr('D003', false, '修改失败', []);
            http_data(200, $resulArr, $this);
        }
    }

	public function paymentRow() //支付
	{
		$keys="ID";
		$this->hedVerify($keys);
		$result = $this->order->payment($this->dataArr, $this->userArr['Mobile']);
		if (count($result) > 0) {
			$resulArr = build_resulArr('D000', true, '支付成功', []);
			http_data(200, $resulArr, $this);
		} else {
			$resulArr = build_resulArr('D003', false, '支付失败', []);
			http_data(200, $resulArr, $this);
		}
	}

	public function shippedRow() //卖家已发货
	{
		$keys="order_id";
		$this->hedVerify($keys);
//		$this->hedVerify();
		$result = $this->order->shipped($this->dataArr, $this->userArr['Mobile']);
		if (count($result) > 0) {
			$resulArr = build_resulArr('D000', true, '已发货', []);
			http_data(200, $resulArr, $this);
		} else {
			$resulArr = build_resulArr('D003', false, '修改失败', []);
			http_data(200, $resulArr, $this);
		}

	}

    public function receivedgoodRow() //确认收货
    {
        $keys="order_id";
        $this->hedVerify($keys);
//		$this->hedVerify();
        $result = $this->order->receivedgood($this->dataArr, $this->userArr['Mobile']);
        if (count($result) > 0) {
            $resulArr = build_resulArr('D000', true, '已确认收货', []);
            http_data(200, $resulArr, $this);
        } else {
            $resulArr = build_resulArr('D003', false, '不能确认收货', []);
            http_data(200, $resulArr, $this);
        }
    }

    public function aftersalesRow()  //申请售后
    {
        $keys="order_id,orderitem_id";
        $this->hedVerify($keys);
//		$this->hedVerify();
        $result = $this->order->aftersales($this->dataArr);
        if (count($result) > 0) {
            $resulArr = build_resulArr('D000', true, '申请成功', []);
            http_data(200, $resulArr, $this);
        } else {
            $resulArr = build_resulArr('D003', false, '申请失败', []);
            http_data(200, $resulArr, $this);
        }

    }

    public function manyimageupload()  //申请售后时，多图片上传
    {
//        $keys="order_id";
//        $this->hedVerify($keys);
//		$this->hedVerify();
        $result = $this->order->manyimageupload($this->dataArr);
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
        $keys="orderitem_image_refused";
        $this->hedVerify($keys);
//		$this->hedVerify();
        $result = $this->order->readmaneypic($this->dataArr);
        if (count($result) > 0) {
            $resulArr = build_resulArr('D000', true, '新增成功', json_encode($result));
            http_data(200, $resulArr, $this);
        } else {
            $resulArr = build_resulArr('D003', false, '新增失败', []);
            http_data(200, $resulArr, $this);
        }

    }

    public function selleragree()   //卖家是否同意
    {
        $keys="order_id,orderitem_id";
        $this->hedVerify($keys);
//		$this->hedVerify();
        $result = $this->order->selleragree($this->dataArr, $this->userArr['Mobile']);
        if (count($result) > 0) {
            $resulArr = build_resulArr('D000', true, '修改成功', []);
            http_data(200, $resulArr, $this);
        } else {
            $resulArr = build_resulArr('D003', false, '修改失败', []);
            http_data(200, $resulArr, $this);
        }

    }

    public function aftersalessuccess()   //售后结束，只有状态4可以点
    {
        $keys="order_id";
        $this->hedVerify($keys);
//		$this->hedVerify();
        $result = $this->order->aftersalessuccess($this->dataArr, $this->userArr['Mobile']);
        if (count($result) > 0) {
            $resulArr = build_resulArr('D000', true, '售后成功', []);
            http_data(200, $resulArr, $this);
        } else {
            $resulArr = build_resulArr('D003', false, '售后失败', []);
            http_data(200, $resulArr, $this);
        }

    }


}
