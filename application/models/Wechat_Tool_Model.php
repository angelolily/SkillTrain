<?php
/**
 * Created by PhpStorm.
 * User: lchangelo
 * Date: 2018/8/7
 * Time: 10:30
 * 微信工具类：一些微信的操作放在这里
 */
defined('BASEPATH') OR exit('No direct script access allowed');
require_once 'vendor/autoload.php';
use EasyWeChat\Factory;
use EasyWeChat\Kernel\Messages\NewsItem;


class Wechat_Tool_Model extends CI_Model
{
    private $appid = "wx5d276a1e3d25bce5";
    private $secret = "530ba6273fa62b9dcb10658f2231b6b7";
//    private $appid = "wx93cb0390799f1959";
//    private $secret = "11e0966b745047b1b9debe3573d88dc1";
    private $app;
    function __construct()
    {
        parent::__construct();
        $config = [
            'app_id' => $this->appid,
            'secret' => $this->secret,
            'token' => "HTYZJT",
            'aes_key'=>"HedSISn1uzmcRzImKqw0ksUK8yS2CTjkjQhSIP4yvsh",
            // 指定 API 调用返回结果的类型：array(default)/collection/object/raw/自定义类名
            'response_type' => 'array',

            'log' => [
                'default' => 'dev', // 默认使用的 channel，生产环境可以改为下面的 prod
                'channels' => [
                    // 测试环境
                    'dev' => [
                        'driver' => 'single',
                        'path' => 'public/easywechat.log',
                        'level' => 'debug',
                    ]
                ],
            ],
        ];
        $this->load->database('default');
        $this->app = Factory::officialAccount($config);
    }

    //查询记录
    public function table_seleRow($field,$taname,$wheredata=array(),$likedata=array()){

        $this->db->select($field);
        if(count($wheredata)>0){
            $this->db->where($wheredata);//判断需不需where要查询
        }
        if(count($likedata)>0){
            $this->db->like($likedata);//判断需不需要like查询
        }
        $query = $this->db->get($taname);

        $ss=$this->db->last_query();

        $rows_arr=$query->result_array();

        return $rows_arr;

    }
    //get获取JSON
    public function getJson($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        return json_decode($output, true);
    }

    /**
     *等比例缩放函数（以保存新图片的方式实现）
     * @param string $picName 被缩放的处理图片源
     * @param string $savePath 保存路径
     * @param int $maxx 缩放后图片的最大宽度
     * @param int $maxy 缩放后图片的最大高度
     * @param string $pre 缩放后图片的前缀名
     * @return $string 返回后的图片名称（） 如a.jpg->s.jpg
     *
     **/
    protected function scaleImg($picName,$savePath, $maxx = 85, $maxy = 85)
    {
        $info = getimageSize($picName);//获取图片的基本信息
        $w = $info[0];//获取宽度
        $h = $info[1];//获取高度

        if($w<=$maxx&&$h<=$maxy){
            return $picName;
        }
        //获取图片的类型并为此创建对应图片资源
        switch ($info[2]) {
            case 1://gif
                $im = imagecreatefromgif($picName);
                break;
            case 2://jpg
                $im = imagecreatefromjpeg($picName);
                break;
            case 3://png
                $im = imagecreatefrompng($picName);
                break;
            default:
                die("图像类型错误");
        }
        //计算缩放比例
        if (($maxx / $w) > ($maxy / $h)) {
            $b = $maxy / $h;
        } else {
            $b = $maxx / $w;
        }
        //计算出缩放后的尺寸
        $nw = floor($w * $b);
        $nh = floor($h * $b);
        //创建一个新的图像源（目标图像）
        $nim = imagecreatetruecolor($nw, $nh);

        //透明背景变黑处理
        //2.上色
        $color=imagecolorallocate($nim,255,255,255);
        //3.设置透明
        imagecolortransparent($nim,$color);
        imagefill($nim,0,0,$color);


        //执行等比缩放
        imagecopyresampled($nim, $im, 0, 0, 0, 0, $nw, $nh, $w, $h);
        //输出图像（根据源图像的类型，输出为对应的类型）
        $picInfo = pathinfo($picName);//解析源图像的名字和路径信息
        //$savePath = $savePath. "/" .date("Ymd")."/".$this->pre . $picInfo["basename"];
        switch ($info[2]) {
            case 1:
                imagegif($nim, $savePath);
                break;
            case 2:
                imagejpeg($nim, $savePath);
                break;
            case 3:
                imagepng($nim, $savePath);
                break;

        }
        //释放图片资源
        imagedestroy($im);
        imagedestroy($nim);
        //返回结果
        return $savePath;
    }

    //获取微信token
    public function getToken(){


        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appid&secret=$this->secret";
        $token = $this->getJson($url);

        if(array_key_exists("errcode", $token)){
            $assdata['data']='';
            $assdata[ "errorCode"]="user-error";
            $assdata[ "ErrorMessage"]=$token['errmsg'];
            $assdata[ "Success"]=false;
            $assdata[ "Status_Code"]=601;
            header("HTTP/1.1 201 Created");
            header("Content-type: application/json");
            log_message("error",$token['errmsg']);

            return false;

        }
        else{
            return $token["access_token"];
        }

    }

    //获取二维码图片
    public function getwechartQR($ticket,$qrimg_path){


        $url = $this->app->qrcode->url($ticket);
        $content = file_get_contents($url); // 得到二进制图片内容

        file_put_contents($qrimg_path, $content); // 写入文件

        if(file_exists($qrimg_path)){


            $this->scaleImg($qrimg_path,$qrimg_path);

        }


    }


    //生成微信二维码
   public function build_qrcode($arg,$type="",$qr_path){
        $file_result=0;


        if($type=="temporary"){

            $qrresult = $this->app->qrcode->temporary($arg, 24 * 24 * 3600);

        }else{
            $qrresult = $this->app->qrcode->forever($arg);
        }

        if(count($qrresult)>0){

            $url = $this->app->qrcode->url($qrresult['ticket']);
            $content = file_get_contents($url);
            if(!(file_put_contents($qr_path, $content))){

                $file_result=1;
            }
            else{
                if(file_exists($qr_path)){


                    $this->scaleImg($qr_path,$qr_path);

                }
            }

        }
        else{
            $file_result=2;
        }

        return $file_result;
    }

    //查询报告状态 向服务器发送消息
    public function send_Report_Statue(){

        try{
            $this->app->server->push(function ($message) {

                if(array_key_exists("MsgType",$message)){
                    if($message['MsgType']=='event'){
                        $rpoid=$message['EventKey'];
                        $keyArray = explode("_", $rpoid);//判断是不是扫码课程二维码进入，获取课程id
                        if (count($keyArray) != 1){
                            $rpoid=$keyArray[1];
                        }

                        if($rpoid){

                            $projinfo=$this->table_seleRow('course_name,course_describe,course_cover','course',array('course_id'=>$rpoid));
                            if(count($projinfo)>0){
                                $items = [
                                    new NewsItem([
                                        'title'       => "欢迎报名!".$projinfo['course_name'],
                                        'description' => $projinfo['course_describe'],
                                        'url'         => $this->config->item('gzhMsgpath')."/".$rpoid,
                                        'image'       => $this->config->item('localpath')."/public/coursecover/".$projinfo['course_cover'],
                                        // ...
                                    ]),
                                ];
                                $news = new News($items);
                                return $news;

                            }
                            else{
                                return "福建沃顿健康管理，欢迎您！";
                            }

                        }
                        else{
                            return "福建沃顿健康管理，欢迎您！";
                        }


                    } else{
                        return "福建沃顿健康管理，欢迎您！";
                    }
                }else{
                    return null;
                }



            });
            ob_clean();
            $response = $this->app->server->serve();
            $response->send();
        }
        catch (Exception $ex){

        }




    }

    //模版消息
    public function send_template_msg($msg=array()){

        $result=false;
        if(count($msg)>0){
            $result=$this->app->template_message->send($msg);
            if(count($result)>0){
                if($result['errcode']==0){
                    $result=true;
                }
            }
        }

        return $result;


    }







}
