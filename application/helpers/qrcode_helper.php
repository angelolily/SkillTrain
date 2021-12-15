<?php
header('content-type:text/html;charset=utf-8');
//配置APPID、APPSECRET

function getCode($path,$name,$userId){
    $APPID ="wx61088edf470bc1f4";
    $APPSECRET = "542a4b5583a35cce1361e60751d22e67";


//获取access_token
    $access_token ="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$APPID&secret=$APPSECRET";
//缓存access_token
    session_start();
    $_SESSION['access_token'] ="";
    $_SESSION['expires_in'] = 0;
    $ACCESS_TOKEN ="";
    if(!isset($_SESSION['access_token']) || (isset($_SESSION['expires_in']) && time() >$_SESSION['expires_in']))
    {

        $json = httpRequest($access_token );
        $json = json_decode($json,true);
        // var_dump($json);
        $_SESSION['access_token'] =$json['access_token'];
        $_SESSION['expires_in'] = time()+7200;
        $ACCESS_TOKEN =$json["access_token"];
    }
    else{

        $ACCESS_TOKEN = $_SESSION["access_token"];
    }
//构建请求二维码参数
//path是扫描二维码跳转的小程序路径，可以带参数?id=xxx
//width是二维码宽度
    $qcode ="https://api.weixin.qq.com/cgi-bin/wxaapp/createwxaqrcode?access_token=$ACCESS_TOKEN";
    $param = json_encode(array("path"=>"$path?$name=$userId","width"=> 150));

//POST参数
    $result = httpRequest($qcode,$param,"POST");
//生成二维码
    $name=time().rand(1111,9999).".png";
    $filepath="./public/qrcode/".$name;
    file_put_contents($filepath,$result);
    $base_url='https://'.$_SERVER['HTTP_HOST'].substr($_SERVER['PHP_SELF'],0,strrpos($_SERVER['PHP_SELF'],'/index.php')+1);
    $filepath= $base_url."public/qrcode/".$name;
//    $base64_image ="data:image/jpeg;base64,".base64_encode($result );
    return $filepath;
}


//把请求发送到微信服务器换取二维码
function httpRequest($url,$data='',$method='GET'){
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL,$url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($curl, CURLOPT_USERAGENT,$_SERVER['HTTP_USER_AGENT']);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
    if($method=='POST')
    {
        curl_setopt($curl, CURLOPT_POST, 1);
        if ($data !='')
        {
            curl_setopt($curl, CURLOPT_POSTFIELDS,$data);
        }
    }
    curl_setopt($curl, CURLOPT_TIMEOUT, 30);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($curl);
    curl_close($curl);
    return $result;
}

?>