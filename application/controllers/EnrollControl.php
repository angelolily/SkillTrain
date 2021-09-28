<?php
class EnrollControl extends CI_Controller{
    private $receive_data;
    public function __construct(){
        parent::__construct();
        $this->load->helper('tool');
        $this->load->helper('qrcode');
        $this->load->service('Enroll');
        $receive = file_get_contents('php://input');
        $this->receive_data = json_decode($receive, true);
    }
    public function upload_img(){
        $file = $_FILES['file'];
        $type = $this->input->post('type');
        $dir_name = $this->input->post('dir');
        $file_name = time().rand(100,999).$this->input->post('name');
        $path = './public/enroll/'.$dir_name;
        $tamp_arr = explode('.', $file['name']);
        $ex_name = '.' . $tamp_arr[count($tamp_arr)-1];
        if(is_dir($path) or mkdir($path)){
            $file_tmp = $file['tmp_name'];
            $save_path = $path . "/" . $file_name . $ex_name;
            if(file_exists($save_path)){
                $file_name = time().rand(100,999).$this->input->post('name');
                $save_path = $path . "/" . $file_name . $ex_name;
            }
            $ab_url = 'D:\\phpstudy_pro\\WWW\\Hanfu-World\\public\\enroll\\'.$dir_name.'\\'.$file_name.$ex_name;
            $size = $file['size']/10240;
            if($size>900){
                imageSize($file_tmp,$ab_url);
                $move_result = TRUE;
            }else{
                $move_result = move_uploaded_file($file_tmp, $save_path);
            }
            if(!$move_result){
                $resultArr = build_resultArr('UI002', FALSE, 0,'存储照片失败', null );
                http_data(200, $resultArr, $this);
            }
            $resultArr = build_resultArr('UI000', TRUE, 0,'存储照片成功', null );
            http_data(200, $resultArr, $this);
        }else{
            $resultArr = build_resultArr('UI001', FALSE, 0,'打开目录失败', null );
            http_data(200, $resultArr, $this);
        }

    }
    public function update_img_dir(){
        $type = $this->receive_data['type'];
        if($type === 'image'){
            // 修改图片为宣传照时
            $res = $this->enroll->update_image_dir($this->receive_data);
            $sign_info = $this->enroll->get_sign_info($this->receive_data);
            if(!$sign_info){
                $resultArr = build_resultArr('UIR001', FALSE, 0,'获取值出错', null );
                http_data(200, $resultArr, $this);
            }
            $this->receive_data['DeptId'] = $sign_info[0]['DeptId'];
            $this->receive_data['sign_name'] = $sign_info[0]['sign_name'];
            $this->receive_data['competition_id'] = $sign_info[0]['sign_competition_id'];
            $this->enroll->update_picture_dir_vote($this->receive_data);
        }else{
            $res = $this->enroll->update_picture_dir($this->receive_data);
        }
        if(!$res){
            $resultArr = build_resultArr('UIR002', FALSE, 0,'更新文件出错，请重新上传', null );
            http_data(200, $resultArr, $this);
        }

        $resultArr = build_resultArr('UIR000', TRUE, 0,'更新文件成功', null );
        http_data(200, $resultArr, $this);
    }
    public function submit(){
        $res_num = $this->enroll->check_enroll_num($this->receive_data);
        if(!$res_num){
            $resultArr = build_resultArr('SUB001', FALSE, 0,'获取余额出错', null );
            http_data(200, $resultArr, $this);
        }
        $enroll_num = (int)$res_num[0]['amount'];
        if($enroll_num<=0){
            $resultArr = build_resultArr('SUB001', FALSE, 0,'报名人数已达上限', null );
            http_data(200, $resultArr, $this);
        }
        $new_num = $enroll_num - 1;
        $new_date = array(
            'spec_id' => $this->receive_data['spec_id'],
            'new_num' => $new_num
        );
        $res_update = $this->enroll->update_enroll_num($new_date);
        if(!$res_update){
            $resultArr = build_resultArr('SUB002', FALSE, 0,'更新赛区报名人数失败', null );
            http_data(200, $resultArr, $this);
        }
        $this->receive_data['order_pay_statue'] = $this->receive_data['sign_statue'];
        if($this->receive_data['sign_statue'] === '进行中'){
            $this->receive_data['order_pay_statue'] = '成功报名';
        }
        if(!isset($this->receive_data['form']['sign_phone'])){
            $this->receive_data['form']['sign_phone'] = '';
        }
        $res_order = $this->enroll->set_order($this->receive_data);
        if(!$res_order){
            $resultArr = build_resultArr('SUB004', FALSE, 0,'存储订单信息失败', null );
            http_data(200, $resultArr, $this);
        }
        $this->receive_data['last_order_id'] = $res_order;
        $res_enroll = $this->enroll->set_enroll($this->receive_data);
        if(!$res_enroll){
            $resultArr = build_resultArr('SUB003', FALSE, 0,'存储报名表失败', null );
            http_data(200, $resultArr, $this);
        }
        $resultArr = build_resultArr('UI000', TRUE, 0,'报名成功', null );
        http_data(200, $resultArr, $this);
    }
    public function edit_from(){
        $res_sign = $this->enroll->edit_from_sign($this->receive_data);
        if(!$res_sign){
            $resultArr = build_resultArr('EF001', FALSE, 0,'修改值出错', null );
            http_data(200, $resultArr, $this);
        }
        $sign_info = $this->enroll->get_sign_info($this->receive_data);
        if(!$sign_info){
            $resultArr = build_resultArr('EF002', FALSE, 0,'获取值出错', null );
            http_data(200, $resultArr, $this);
        }
        $this->receive_data['DeptId'] = $sign_info[0]['DeptId'];
        $this->receive_data['sign_name'] = $sign_info[0]['sign_name'];
        $this->receive_data['competition_id'] = $sign_info[0]['sign_competition_id'];
        $this->enroll->edit_from_vote($this->receive_data);
//        $res_vote = $this->enroll->edit_from_vote($this->receive_data);
//        if(!$res_vote){
//            $resultArr = build_resultArr('EF003', FALSE, 0,'修改值出错', null );
//            http_data(200, $resultArr, $this);
//        }
        $resultArr = build_resultArr('EF000', TRUE, 0,'修改值成功', null );
        http_data(200, $resultArr, $this);
    }
    public function del_sign_info(){
        $res_price = $this->enroll->get_order_price($this->receive_data);
        if(!$res_price){
            $resultArr = build_resultArr('DSI001', FALSE, 0,'获取订单信息失败', null );
            http_data(200, $resultArr, $this);
        }
        $price = $res_price[0]['order_price'];
        if($price === "0"){
            $res_enroll = $this->enroll->del_sign_info_free($this->receive_data);
        }else{
            $res_enroll = $this->enroll->del_sign_info($this->receive_data);
        }
        if(!$res_enroll){
            $resultArr = build_resultArr('DSI002', FALSE, 0,'删除报名失败，请检查后重试', null );
            http_data(200, $resultArr, $this);
        }
        $resultArr = build_resultArr('DSI000', TRUE, 0,'更新文件成功', null );
        http_data(200, $resultArr, $this);
    }
    public function get_all_key(){
        $res = $this->enroll->get_key_text();
        if(!$res){
            $resultArr = build_resultArr('SUB001', FALSE, 0,'获取值出错', null );
            http_data(200, $resultArr, $this);
        }
        $resultArr = build_resultArr('SUB000', TRUE, 0,'获取值成功', $res );
        http_data(200, $resultArr, $this);
    }
    public function get_sign_model_list(){
        $res = $this->enroll->get_sign_model_list($this->receive_data);
        if(!$res){
            $resultArr = build_resultArr('GSM001', TRUE, 0,'获取模板信息错误', [] );
            http_data(200, $resultArr, $this);
        }
        $resultArr = build_resultArr('GSM000', TRUE, 0,'获取模板信息成功', $res);
        http_data(200, $resultArr, $this);
    }
    public function add_sign_model(){
        $res_add_model = $this->enroll->add_sign_model($this->receive_data);
        if(!$res_add_model){
            $resultArr = build_resultArr('ASM001', FALSE, 0,'新增报名模板信息错误', [] );
            http_data(200, $resultArr, $this);
        }
        $index = $this->receive_data['index_obj'];
        for($i = 0; $i < count($index); $i++){
            $index[$i]['sign_relevancy_id'] = $res_add_model;
            $index[$i]['sign_index_created_by'] = 'HFTX_Sys';
            $index[$i]['sign_index_created_time'] = date('Y-m-d H:i:s');
            $res = $this->enroll->add_index_option($index[$i]);
            if(!$res){
                $resultArr = build_resultArr('ASM002', FALSE, 0,'新增索引信息错误', [] );
                http_data(200, $resultArr, $this);
            }
        }
        $resultArr = build_resultArr('ASM000', TRUE, 0,'新增索引信息成功', $res_add_model);
        http_data(200, $resultArr, $this);
    }
    public function get_sign_index(){
        $res = $this->enroll->get_sign_index($this->receive_data);
        if(!$res){
            $resultArr = build_resultArr('GSM001', FALSE, 0,'获取索引信息错误', [] );
            http_data(200, $resultArr, $this);
        }
        $resultArr = build_resultArr('GSM000', TRUE, 0,'获取索引信息成功', $res);
        http_data(200, $resultArr, $this);
    }
    public function save_index_edition(){
        $res = $this->enroll->save_index_edition($this->receive_data);
        if(!$res){
            $resultArr = build_resultArr('GSM001', TRUE, 0,'无修改', FALSE );
            http_data(200, $resultArr, $this);
        }
        $resultArr = build_resultArr('GSM000', TRUE, 0,'修改索引信息成功', TRUE);
        http_data(200, $resultArr, $this);
    }
    public function add_index_option(){
        $index = $this->receive_data['index_obj'];
        for($i = 0; $i < count($index); $i++){
            $index[$i]['sign_index_created_by'] = 'HFTX_Sys';
            $index[$i]['sign_index_created_time'] = date('Y-m-d H:i:s');
            $res = $this->enroll->add_index_option($index[$i]);
            if(!$res){
                $resultArr = build_resultArr('AIO001', FALSE, 0,'新增索引信息错误', [] );
                http_data(200, $resultArr, $this);
            }
        }
        $resultArr = build_resultArr('AIO000', TRUE, 0,'新增索引信息成功', []);
        http_data(200, $resultArr, $this);
    }
    public function del_index_option(){
        $res = $this->enroll->del_index_option($this->receive_data);
        if(!$res){
            $resultArr = build_resultArr('DIO001', FALSE, 0,'删除索引信息错误', [] );
            http_data(200, $resultArr, $this);
        }
        $resultArr = build_resultArr('DIO000', TRUE, 0,'删除索引信息成功', []);
        http_data(200, $resultArr, $this);
    }
    public function get_enroll_info(){
        $res = $this->enroll->get_enroll_info($this->receive_data);
        if(!$res){
            $resultArr = build_resultArr('GEI001', FALSE, 0,'获取报名信息错误', [] );
            http_data(200, $resultArr, $this);
        }
        // 获取证件照
        $res_picture = array();
        if($res[0]['sign_picture'] !== ''){
            $dir_name_picture = './public/enroll/'.$res[0]['sign_picture'];
            if(file_exists($dir_name_picture)){
                $handler = opendir($dir_name_picture);
                if ($handler) {
                    while (($filename = readdir($handler)) !== false) {
                        if ($filename != "." && $filename != "..") {
                            $res_url = "https://hftx.fzz.cn/public/enroll/" . $res[0]['sign_picture'] . '/' . $filename;
                            array_push($res_picture,$res_url);
                        }
                    }
                }
                closedir($handler);
            }
        }
        // 获取宣传照
        $res_image = array();
        if($res[0]['sign_image'] !== ''){
            $dir_name_image = './public/enroll/'.$res[0]['sign_image'];
            if(file_exists($dir_name_image)){
                $handler = opendir($dir_name_image);
                if ($handler) {
                    while (($filename = readdir($handler)) !== false) {
                        if ($filename != "." && $filename != "..") {
                            $res_url = "https://hftx.fzz.cn/public/enroll/" . $res[0]['sign_image'] . '/' . $filename;
                            array_push($res_image,$res_url);
                        }
                    }
                }
                closedir($handler);
            }
        }
        $resultArr = build_resultArr('GEI000', TRUE, 0,'获取报名信息成功', [$res[0],$res_picture,$res_image]);
        http_data(200, $resultArr, $this);
    }
    public function get_activity_info(){
        $res = $this->enroll->get_activity_info($this->receive_data);
        if(!$res){
            $resultArr = build_resultArr('GAI001', FALSE, 0,'获取活动信息错误', [] );
            http_data(200, $resultArr, $this);
        }
        $this->receive_data['model_id'] = $res[0]['activity_sign_model'];
        $res_index = $this->enroll->get_sign_index($this->receive_data);
        if(!$res_index){
            $resultArr = build_resultArr('GAI002', FALSE, 0,'获取活动报名表信息错误', [] );
            http_data(200, $resultArr, $this);
        }
        $res[0]['activity_cover']="https://hftx.fzz.cn/public/activitycover/".$res[0]['activity_cover'];
        $res[0]['activity_graphic']="https://hftx.fzz.cn/public/activitygraphic/".$res[0]['activity_graphic'];
        $resultArr = build_resultArr('GAI000', TRUE, 0,'获取活动信息成功', [$res[0],$res_index]);
        http_data(200, $resultArr, $this);
    }
    public function get_activity_form(){
        $this->receive_data = $this->set_receive_data($this->receive_data);
//        $res_exits = $this->enroll->check_exits_state($this->receive_data);
//        if($res_exits){
//            $resultArr = build_resultArr('GAF001', FALSE, 0,'重复报名', [] );
//            http_data(200, $resultArr, $this);
//        }
        $type = $this->receive_data['order_info']['order_type'];
        $aim_id = $this->receive_data['order_info']['order_capid'];
        if($type === '活动'){
            $this->receive_data['sql_code'] = "UPDATE activity SET activity_number_limit = activity_number_limit-1 WHERE activity_id = ".$aim_id;
        }else{
            $this->receive_data['sql_code'] = "UPDATE course SET course_number_limit = course_number_limit-1 WHERE course_id = ".$aim_id;
        }
        $this->receive_data['form']['sign_type'] = '活动';
        $res = $this->enroll->set_order_enroll_data($this->receive_data);
        if(!$res){
            $resultArr = build_resultArr('GAF001', FALSE, 0,'活动报名失败', [] );
            http_data(200, $resultArr, $this);
        }
        $resultArr = build_resultArr('GAF000', TRUE, 0,'活动报名息成功', []);
        http_data(200, $resultArr, $this);
    }
    public function get_course_info(){
        $res = $this->enroll->get_course_info($this->receive_data);
        if(!$res){
            $resultArr = build_resultArr('GCI001', FALSE, 0,'获取课程信息错误', [] );
            http_data(200, $resultArr, $this);
        }
        $this->receive_data['model_id'] = $res[0]['course_sign_model'];
        $res_index = $this->enroll->get_sign_index($this->receive_data);
        if(!$res_index){
            $resultArr = build_resultArr('GCI002', FALSE, 0,'获取课程报名表信息错误', [] );
            http_data(200, $resultArr, $this);
        }
        $res[0]['course_cover']="https://hftx.fzz.cn/public/coursecover/".$res[0]['course_cover'];
        $res[0]['course_graphic']="https://hftx.fzz.cn/public/coursegraphic/".$res[0]['course_graphic'];
        $resultArr = build_resultArr('GAI000', TRUE, 0,'获取课程信息成功', [$res[0],$res_index]);
        http_data(200, $resultArr, $this);
    }
    public function set_course_form(){
        $this->receive_data = $this->set_receive_data($this->receive_data);
        $this->receive_data['form']['sign_type'] = '培训';
        $this->receive_data['sql_code'] = "UPDATE course SET course_number_limit = course_number_limit-1 WHERE course_id = ".$this->receive_data['order_info']['order_capid'];
        $res = $this->enroll->set_order_enroll_data($this->receive_data);
        if(!$res){
            $resultArr = build_resultArr('GCF001', FALSE, 0,'培训报名失败', [] );
            http_data(200, $resultArr, $this);
        }
        $this->receive_data['user_point'] = (int)$this->receive_data['user_point']+(int)$this->receive_data['course_signIntegral'];
        $res_p = $this->enroll->update_user_point($this->receive_data);
        if(!$res_p){
            $resultArr = build_resultArr('GCF002', FALSE, 0,'更新用户积分失败', [] );
            http_data(200, $resultArr, $this);
        }
        $resultArr = build_resultArr('GCF000', TRUE, 0,'培训报名息成功', $res);
        http_data(200, $resultArr, $this);
    }
    public function get_aim_sign(){
        $pages = $this->receive_data['pages'];
        $rows = $this->receive_data['rows'];
        $this->receive_data['offset'] = ($pages-1) * $rows;// 计算偏移量
        $res = $this->enroll->get_sign_data($this->receive_data);
        if(!$res){
            $resultArr = build_resultArr('GAS001', FALSE, 0,'获取目标信息错误', null );
            http_data(200, $resultArr, $this);
        }
        $res_num = $this->enroll->get_sign_data_num($this->receive_data);
        $data = array(
            'data'=>json_encode($res),
            'total'=>(int)$res_num[0]['count(*)']
        );
        $resultArr = build_resultArr('GAS000', TRUE, 0,'获取目标信息成功', $data);
        http_data(200, $resultArr, $this);
    }
    public function check_vote_data(){
        $res = $this->enroll->check_vote_data($this->receive_data);
        if(!$res){
            $resultArr = build_resultArr('CVD001', FALSE, 0,'获取目标信息错误', null );
            http_data(200, $resultArr, $this);
        }
        $resultArr = build_resultArr('CVD000', TRUE, 0,'获取目标信息成功', $res[0]['count(*)']);
        http_data(200, $resultArr, $this);
    }
    public function update_share_user_point(){
        $res_user = $this->enroll->get_user_info($this->receive_data);
        if(!$res_user){
            $resultArr = build_resultArr('GAI001', FALSE, 0,'获取目标活动信息错误', null );
            http_data(200, $resultArr, $this);
        }
        $user_info = $res_user[0];
        $this->receive_data['referrer']['referrer_name'] = $user_info['members_name'];
        $this->receive_data['referrer']['referrer_phone'] = $user_info['members_phone'];
        $this->receive_data['referrer']['referrer_datetime'] = date('Y-m-d H:i:s');
        $this->receive_data['referrer']['created_by'] = 'HFTX_Sys';
        $this->receive_data['referrer']['created_time'] = date('Y-m-d H:i:s');
        $this->receive_data['referrer']['referrer_datetime'] = date('Y-m-d H:i:s');
        $res = $this->enroll->update_share_user_point($this->receive_data);
        $resultArr = build_resultArr('USP000', TRUE, 0,'目标用户奖励积分获取成功', null);
        http_data(200, $resultArr, $this);
    }
    public function update_user_info(){
        $res = $this->enroll->update_user_info($this->receive_data);
        if(!$res){
            $resultArr = build_resultArr('GCF002', FALSE, 0,'更新用户信息失败', [] );
            http_data(200, $resultArr, $this);
        }
        $resultArr = build_resultArr('GCF000', TRUE, 0,'更新用户信息成功', []);
        http_data(200, $resultArr, $this);
    }
    public function check_is_exist_enroll(){
        $res = $this->enroll->check_exits_state($this->receive_data);
        if(!$res){
            $resultArr = build_resultArr('CES000', TRUE, 0,'未重复报名', $this->db->last_query() );
            http_data(200, $resultArr, $this);
        }
        $resultArr = build_resultArr('CES001', FALSE, 0,'重复报名', $this->db->last_query());
        http_data(200, $resultArr, $this);
    }
    public function set_receive_data($data): array
    {
        $data['order_info']['order_id'] = get_random_tool(4).time();
        $data['order_info']['created_by'] = 'HFTX_Sys';
        $data['order_info']['created_time'] = date('Y-m-d H:i:s');
        $data['form']['sign_competition_id'] = $data['order_info']['order_capid'];
        $data['form']['competition_name'] = $data['order_info']['order_product'];
        $data['sign_created_by'] = 'HFTX_Sys';
        $data['form']['sign_created_time'] = date('Y-m-d H:i:s');
        $data['form']['sign_statue'] = '未付款';
        if($data['order_info']['order_statue'] === '进行中'){
            $data['form']['sign_statue'] = '成功报名';
        }
        if(isset($data['form']['sign_name']) && isset($data['form']['sign_phone'])){
            $data['order_info']['order_customer_name'] = $data['form']['sign_name'];
            $data['order_info']['order_customer_phone'] = $data['form']['sign_phone'];
        }else{
            $data['order_info']['order_customer_name'] = $data['user_info']['members_name'];
            $data['order_info']['order_customer_phone'] = $data['user_info']['members_phone'];
        }
        return $data;
    }
    public function prize(){
        $url_token = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wx61088edf470bc1f4&secret=542a4b5583a35cce1361e60751d22e67';
        $res_token = httpRequest($url_token);
        if(!$res_token){
            $resultArr = build_resultArr('MSG001', FALSE, 0,'FALSE_token', []);
            http_data(200, $resultArr, $this);
        }
        $ACCESS_TOKEN = json_decode($res_token,true)['access_token'];
        $url_msg = 'https://api.weixin.qq.com/cgi-bin/message/subscribe/send?access_token='.$ACCESS_TOKEN;
        $curl_data = array(
            "touser"=>$this->receive_data['openid'],
            "template_id"=>'3eq1xPXmV3O9NRfFBTwh8tV26ybdoEpjegsNUpE7XiA',
            "data"=>array(
                "thing1"=> array(
                    "value"=> $this->receive_data['msg_title']
                ),
                "thing2"=> array(
                    "value"=> $this->receive_data['prize_name']
                ),
                "phone_number3"=> array(
                    "value"=> $this->receive_data['server_phone']
                ),
                "name4"=> array(
                    "value"=> $this->receive_data['server_name']
                ),
                "thing5"=> array(
                    "value"=> $this->receive_data['tip']
                ),
            )
        );
        $res_msg = httpRequest($url_msg,json_encode($curl_data),'POST');
        $resultArr = build_resultArr('MSG000', TRUE, 0,'OK', [$res_msg,http_build_query($curl_data)]);
        http_data(200, $resultArr, $this);
    }
}