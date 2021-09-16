<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//require_once 'vendor/autoload.php';
//use PhpOffice\PhpSpreadsheet\Spreadsheet;
//use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

//关联数组删除key
function bykey_reitem($arr, $key){
	if(!array_key_exists($key, $arr)){
		return $arr;
	}
	$keys = array_keys($arr);
	$index = array_search($key, $keys);
	if($index !== FALSE){
		array_splice($arr, $index, 1);
	}
	return $arr;

}

//图片转换base64
function fileToBase64($file){
    $base64_file = '';
    if(file_exists($file)){
        $mime_type= mime_content_type($file);
        $base64_data = base64_encode(file_get_contents($file));
        $base64_file = 'data:'.$mime_type.';base64,'.$base64_data;
    }
    return $base64_file;
}

//单独文件类型，保存base64文件
function base64_file_content_type($base64_image_content,$path,$name,$type="jpg"){
    $new_file = $path;
    $new_file = $new_file.'/'.$name.".{$type}";
    if (file_put_contents($new_file, base64_decode($base64_image_content))){
        return  $new_file;
    }else{
        return "";
    }
}
function batch_import_excel($file){
    $spreadsheet = PhpOffice\PhpSpreadsheet\IOFactory::load($file);
    $sheetData = $spreadsheet->getActiveSheet()->toArray();
    return $sheetData;

}

function build_resulArr($code,$success,$msg,$data)
{
	$resulArr['code']=$code;
	$resulArr['success'] = $success;
	$resulArr['msg'] = $msg;
	$resulArr['data'] =$data;

	return $resulArr;

}
function arrayGbkToUtf8($val=[])
{
	$result=[];
	foreach ($val as $row)
	{
		$row=iconv("GBK","UTF-8",$row);
		array_push($result,$row);
	}
	return $result;
}

function http_data($statue,$HttpData=[],$CI)
{
	$CI->output
		->set_header('access-control-allow-headers: Accept,Authorization,Cache-Control,Content-Type,DNT,If-Modified-Since,Keep-Alive,Origin,User-Agent,X-Mx-ReqToken,X-Requested-With,transformrequest')
		->set_header('access-control-allow-methods: GET, POST, PUT, DELETE, HEAD, OPTIONS')
		->set_header('access-control-allow-credentials: true')
		->set_header('access-control-allow-origin: *')
		->set_header('X-Powered-By: WAF/2.0')
		->set_status_header($statue)
		->set_content_type('application/json', 'utf-8')
		->set_output(json_encode($HttpData))
		->_display();
	exit;
}
function existsArrayKey($keys,$arr=[])
{

	$arrkeys=[];
	$errorKeys="";
	$arrkeys=explode(",",$keys);
	foreach ($arrkeys as $row)
	{
		if(!(array_key_exists($row,$arr))){

			$errorKeys.=",".$row;

		}
	}

	return $errorKeys;

}
function build_resultArr($code,$success,$status_code,$msg=null,$data=[])
{
    $resultArr['ErrorCode']=$code;
    $resultArr['Success'] = $success;
    $resultArr['Status_Code'] = $status_code;
    $resultArr['ErrorMessage'] = $msg;
    $resultArr['Data'] =$data;

    return $resultArr;

}

function get_random_tool($length,$s_key=''){
    $str = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h',
        'i', 'j', 'k', 'l','m', 'n', 'o', 'p', 'q', 'r', 's',
        't', 'u', 'v', 'w', 'x', 'y','z', 'A', 'B', 'C', 'D',
        'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L','M', 'N', 'O',
        'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y','Z',
        '0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
    $base_psw = '';
    $aim_len = $length;
    if($s_key !== ''){
        $aim_len = $length - strlen($s_key);
        $base_psw = $s_key;
    }
    $keys = array_rand($str, $aim_len);
    $password = '';
    for($i = 0; $i < $aim_len; $i++){
        $password .= $str[$keys[$i]];
    }
    return $base_psw.$password;
}
function imageSize($source,$destination){
    $image = imagecreatefromstring(file_get_contents($source)); // 加载资源
    if (isset($image) && is_resource($image)) {
        imagejpeg($image, $destination, 40);   // 根据$quality比例进行
        imagedestroy($image);
    }
}
