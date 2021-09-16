<?php
$route['pay/login'] = 'WxPayControl/login';// 测试登陆
$route['pay/prepay'] = 'WxPayControl/get_prepay_id';// 获取prepay_id数据
$route['pay/update'] = 'WxPayControl/update_order_info';// 未付款订单付款后更新订单

$route['p/info_s'] = 'ProductDetailControl/get_spec_info';// 获取赛区信息
$route['p/info_m'] = 'ProductDetailControl/get_match_info';// 获取赛事信息
$route['p/info_aim'] = 'ProductDetailControl/get_aim_match_info';// 获取赛事信息
$route['p/img'] = 'ProductDetailControl/get_img_url_arr';// 获取上传照片
$route['p/check'] = 'ProductDetailControl/check_order_info';// 判断是否可以退款
$route['p/refund'] = 'ProductDetailControl/set_order_refund';// 判断是否可以退款
$route['p/c_list'] = 'ProductDetailControl/get_course_info';// 获取课程列表
$route['p/c_info'] = 'ProductDetailControl/get_aim_course_info';// 获取课程信息
$route['p/my_c'] = 'ProductDetailControl/get_user_course';// 获取用户参加课程信息
$route['p/list_ac'] = 'ProductDetailControl/get_ac_list';// 获取目标信息列表
$route['p/state'] = 'ProductDetailControl/update_aim_p_state';// 更新目标数据的状态
$route['p/commodity'] = 'ProductDetailControl/gte_commodity_info';// 获取商品信息
$route['p/activity'] = 'ProductDetailControl/gte_activity_info';// 获取活动信息
$route['p/get_m_list_w'] = 'ProductDetailControl/gte_match_list_web';// 获取赛事列表_web
$route['p/get_s_list_w'] = 'ProductDetailControl/gte_specification_list_web';// 获取赛区列表_web
$route['p/cs_info'] = 'ProductDetailControl/get_match_spec_info';// 获取赛区列表_web

$route['my/point_s'] = 'ProductDetailControl/update_user_point';// 更新用户积分
$route['my/point_g'] = 'ProductDetailControl/gte_user_point';// 获取用户积分

$route['e/upload_i'] = 'EnrollControl/upload_img';// 上传照片
$route['e/update_d'] = 'EnrollControl/update_img_dir';// 修改选手照片文件夹
$route['e/submit'] = 'EnrollControl/submit';// 保存报名表
$route['e/key'] = 'EnrollControl/get_all_key';// 获取所有报名表字段
$route['e/from'] = 'EnrollControl/edit_from';// 修改选手来源
$route['e/del_info'] = 'EnrollControl/del_sign_info';// 删除选手报名信息
$route['e/get_model'] = 'EnrollControl/get_sign_model_list';// 获取报名表模板
$route['e/a_model'] = 'EnrollControl/add_sign_model';// 新增报名表模板
$route['e/get_index'] = 'EnrollControl/get_sign_index';// 获取报名表索引
$route['e/s_edition'] = 'EnrollControl/save_index_edition';// 修改报名表选项
$route['e/a_option'] = 'EnrollControl/add_index_option';// 新增报名表选项
$route['e/d_option'] = 'EnrollControl/del_index_option';// 删除报名表选项
$route['e/act'] = 'EnrollControl/get_activity_info';// 获取活动信息
$route['e/act_f'] = 'EnrollControl/get_activity_form';// 获取活动信息
$route['e/c_f'] = 'EnrollControl/get_course_info';// 获取课程信息
$route['e/course_f'] = 'EnrollControl/set_course_form';// 获取课程信息
$route['e/prize'] = 'EnrollControl/prize';// 发送中奖消息
$route['e/sign'] = 'EnrollControl/get_aim_sign';// 获取报名信息
$route['e/c_vote'] = 'EnrollControl/check_vote_data';// 检查报名数据
$route['e/u_s_p'] = 'EnrollControl/update_share_user_point';// 更新推荐用户积分
$route['e/u_s_i'] = 'EnrollControl/update_user_info';// 更新用户信息
$route['e/is_exist'] = 'EnrollControl/check_is_exist_enroll';// 更新用户信息
$route['e/e_info'] = 'EnrollControl/get_enroll_info';// 获取用户报名信息

$route['yac/list'] = 'UserAddressControl/getUserAddress'; //获取用户地址列表
$route['yac/aim'] = 'UserAddressControl/getAimAddress'; //获取目标收货地址
$route['yac/save'] = 'UserAddressControl/saveAddress'; //保存用户收获地址
$route['yac/de'] = 'UserAddressControl/getDefaultAddress'; //获取用户默认收获地址
