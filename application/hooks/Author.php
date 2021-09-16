<?php


/**
 * Class Author
 * api验证授权钩子
 */
class Author
{
	private static $CI;

	function __construct()
	{
		self::$CI = &get_instance();  //获取CI对象
		self::$CI->load->helper('redis');
		self::$CI->load->helper('tool');


	}


	private static function buildsign($parameter = [])
	{
		$tmp = "";
		$signStr = "";
		reset($parameter);
		if (count($parameter) > 0) {
			while (list($k, $v) = each($parameter)) {


				if (is_array($v)) {

					$parameterStr = json_encode($v, true);

					for ($i = 0; $i < strlen($parameterStr); $i++) {//遍历字符串追加给数组
						$tmp = $tmp . $parameterStr[$i];
					}

					$tmp = "$k" . $tmp;
				} else {
					$tmp = "$k" . "$v";
				}
				$signStr .= $tmp;
			}

			$signStr = sha1($signStr);//全部拼接key后，得到sha1
			$signStr = strtoupper($signStr);//全部转换为大写


		}

		return $signStr;


	}


	public static function verifica()
	{
		$ss = $_SERVER['REQUEST_METHOD'];
		if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
			http_data(200, [], self::$CI);
		}
//		$class = self::$CI->router->fetch_class();
//		if ($class != "UploadControl") {//所有下载的板块
//
//			$resulArr = $dataArr = $receiveArr = [];//返回信息,解码json数据，接收信息
//			$statue = 0;//http状态码
//			$selfSignStr = "";
//			$receiveArr = file_get_contents('php://input');
//			$dataArr = json_decode($receiveArr, true);
//			if ($dataArr) {
//				if (array_key_exists("signature", $dataArr) && array_key_exists("timestamp", $dataArr)) {
//					$nowTime = time();//获取当前时间
//					$intervalTime = $nowTime - $dataArr['timestamp'];
//					$signatureStr = $dataArr['signature'];
//					//判断接口时间是否超时`
//					if ($intervalTime < 3600) {
//						//隔离重放攻击
//						if (!(RedisGet($signatureStr))) {
//							$dataArr = bykey_reitem($dataArr, "signature");
//							$selfSignStr = self::buildsign($dataArr);
//							if ($selfSignStr != $signatureStr) {
//								$statue = 401;
//								$resulInfo['success'] = false;
//								$resulInfo['msg'] = "签名错误";
//							} else {
//								RedisSet($signatureStr, "1");//验证通过记录签名值，防止重放
//								$dataArr = json_decode($receiveArr, true);
//								if ($class != "LoginControl") {
//									$userdata = get_reids_key($dataArr['phone']);
//									if ($userdata) {
//										if ($userdata != "1") {
//											$statue = 401;
//											$resulInfo['success'] = false;
//											$resulInfo['msg'] = "用户状态异常";
//										}
//									} else {
//										$statue = 401;
//										$resulInfo['success'] = false;
//										$resulInfo['msg'] = "用户状态异常";
//									}
//									if ($statue != 0) {
//
//										http_data($statue, $resulInfo, self::$CI);
//									}
//
//
//								}
//
//							}
//						} else {
//							$statue = 401;
//							$resulInfo['success'] = false;
//							$resulInfo['msg'] = "重复签名";
//						}
//
//
//					} else {
//						$statue = 401;
//						$resulInfo['success'] = false;
//						$resulInfo['msg'] = "接口调用超时";
//
//					}
//				} else {
//					$statue = 400;
//					$resulInfo['success'] = false;
//					$resulInfo['msg'] = "获取参数错误";
//				}
//			} else {
//				$statue = 400;
//				$resulInfo['success'] = false;
//				$resulInfo['msg'] = "获取参数错误";
//			}
//			if ($statue != 0) {
//				http_data($statue, $resulInfo, self::$CI);
//			}
//		}
//		$ss = $_SERVER['REQUEST_METHOD'];
//		if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
//			http_data(200, [], self::$CI);
//		}
//		$class = self::$CI->router->fetch_class();
//		if ($class != "UploadControl") {//所有下载的板块
//
//			$resulArr = $dataArr = $receiveArr = [];//返回信息,解码json数据，接收信息
//			$statue = 0;//http状态码
//			$selfSignStr = "";
//			$receiveArr = file_get_contents('php://input');
//			$dataArr = json_decode($receiveArr, true);
//			if ($dataArr) {
//				if (array_key_exists("signature", $dataArr) && array_key_exists("timestamp", $dataArr)) {
//					$nowTime = time();//获取当前时间
//					$intervalTime = $nowTime - $dataArr['timestamp'];
//					$signatureStr = $dataArr['signature'];
//					//判断接口时间是否超时`
//					if ($intervalTime < 3600) {
//						//隔离重放攻击
//						if (!(RedisGet($signatureStr))) {
//							$dataArr = bykey_reitem($dataArr, "signature");
//							$selfSignStr = self::buildsign($dataArr);
//							if ($selfSignStr != $signatureStr) {
//								$statue = 401;
//								$resulInfo['success'] = false;
//								$resulInfo['msg'] = "签名错误";
//							} else {
//								RedisSet($signatureStr, "1");//验证通过记录签名值，防止重放
//								$dataArr = json_decode($receiveArr, true);
//								if ($class != "LoginControl") {
//									$userdata = get_reids_key($dataArr['phone']);
//									if ($userdata) {
//										if ($userdata != "1") {
//											$statue = 401;
//											$resulInfo['success'] = false;
//											$resulInfo['msg'] = "用户状态异常";
//										}
//									} else {
//										$statue = 401;
//										$resulInfo['success'] = false;
//										$resulInfo['msg'] = "用户状态异常";
//									}
//									if ($statue != 0) {
//
//										http_data($statue, $resulInfo, self::$CI);
//									}
//
//
//								}
//
//							}
//						} else {
//							$statue = 401;
//							$resulInfo['success'] = false;
//							$resulInfo['msg'] = "重复签名";
//						}
//
//
//					} else {
//						$statue = 401;
//						$resulInfo['success'] = false;
//						$resulInfo['msg'] = "接口调用超时";
//
//					}
//				} else {
//					$statue = 400;
//					$resulInfo['success'] = false;
//					$resulInfo['msg'] = "获取参数错误";
//				}
//			} else {
//				$statue = 400;
//				$resulInfo['success'] = false;
//				$resulInfo['msg'] = "获取参数错误";
//			}
//			if ($statue != 0) {
//				http_data($statue, $resulInfo, self::$CI);
//			}
//		}
	}
}

