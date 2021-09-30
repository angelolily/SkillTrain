<?php
$route['msg/get'] = 'MessageControl/getMessageinfo';//获取消息信息
$route['msg/add'] = 'MessageControl/addMessageinfo';//新增消息信息
$route['msg/getAtt'] = 'MessageControl/getAttendanceRow';//获取主考勤消息信息
$route['msg/getAttDetail'] = 'MessageControl/getAttDetailRow';//获取明细考勤消息信息
$route['msg/getQRclass'] = 'MessageControl/getCrouseQR';//获取二维码