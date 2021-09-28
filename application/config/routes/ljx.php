<?php
$route['sg/get'] = 'SignControl/getRow';//获取报名用户的信息
$route['sg/modify'] = 'SignControl/modifyRow';//修改用户信息
$route['sg/into'] = 'SignControl/intoRow';//导入excel
$route['ord/getall'] = 'OrderControl/getallRow';//获取商品订单
$route['ord/getac'] = 'OrderControl/getactivityRow';//获取活动订单
$route['ord/getcou'] = 'OrderControl/getcourseRow';//获取课程订单
$route['ord/getdet'] = 'OrderControl/getdetialOrder';//获取商品订单详情
$route['ord/yesno'] = 'OrderControl/yesornoRow';//同意或拒绝退款
$route['ord/modifyprice'] = 'OrderControl/modifypriceRow';//修改订单价格
$route['ord/modifystatu'] = 'OrderControl/modifystatuRow';//修改订单状态
$route['ord/pay'] = 'OrderControl/paymentRow';//支付
$route['ord/ship'] = 'OrderControl/shippedRow';//卖家已发货
$route['ord/rec'] = 'OrderControl/receivedgoodRow';//确认收货
$route['ord/asr'] = 'OrderControl/aftersalesRow';//申请售后
$route['ord/many'] = 'OrderControl/manyimageupload';//申请售后时，多图片上传
$route['ord/read'] = 'OrderControl/readmaneypic';//读取目录下多图片
$route['ord/sel'] = 'OrderControl/selleragree';//卖家是否同意
$route['ord/ass'] = 'OrderControl/aftersalessuccess';//售后结束，只有状态4可以点
$route['csc/new'] = 'CourseControl/newRow';//新增课程
$route['csc/upd'] = 'CourseControl/Uploaddetail';//存入课程图文
$route['csc/fdd'] = 'CourseControl/finddetail';//获取课程图文
$route['csc/upc'] = 'CourseControl/Uploadcover';//存入课程封面
$route['csc/fdc'] = 'CourseControl/findcover';//获取课程封面
$route['csc/show'] = 'CourseControl/showRow';//课程下拉
$route['csc/modify'] = 'CourseControl/modifyRow';//修改课程动
$route['csc/del'] = 'CourseControl/delRow';//删除课程
$route['csc/get'] = 'CourseControl/getRow';//获取课程
//$route['csc/publish'] = 'CourseControl/publishRow';//发布课程
$route['csc/finally'] = 'CourseControl/finallyRow';//结束课程
//$route['csc/lowactivity'] = 'CourseControl/lowactivity';//下架课程
$route['mem/get'] = 'MemberControl/getRow';//获取会员报表分页
$route['clc/new'] = 'ClassmanControl/newRow';//新增班级排课
$route['clc/show'] = 'ClassmanControl/showRow';//会员下拉
$route['clc/modify'] = 'ClassmanControl/modifyRow';//修改班级排课
$route['clc/del'] = 'ClassmanControl/delRow';//删除班级排课
$route['clc/get'] = 'ClassmanControl/getclassRow';//获取班级
$route['clc/getschedule'] = 'ClassmanControl/getscheduleRow';//班级获取排课
$route['scd/get'] = 'ScheduleControl/getRow';//获取搜索排课表
$route['scd/modify'] = 'ScheduleControl/modifyRow';//修改单条单人排课





