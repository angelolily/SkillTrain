<?php
defined('BASEPATH') OR exit('No direct script access allowed');





function RedisSet($key,$value,$expire=0){
	if(!$key||!$value) return false;
	$host = "127.0.0.1";
	$port = "6379";
	$redis = new Redis();
	$redis->connect($host, $port);
	$value = is_array($value)?json_encode($value):$value;
	return $expire>0?$redis->setex(getenv('REDIS_PREFIX').$key, $expire,$value):$redis->set(getenv('REDIS_PREFIX').$key,$value);
}

/**
 * redis get封装,如果传入的是数组,返回的也是数组,同理字符串 written:yangxingyi
 */
function RedisGet($key){
	$redis = new Redis();
	$host = "127.0.0.1";
	$port = "6379";
	$redis->connect($host, $port);
	$result = $redis->get($key);
	return is_null(json_decode($result))?$result:json_decode($result,true);
}
function get_reids_key($key)//获取
{
	$redis = new Redis();
	$redis->connect('127.0.0.1', 6379);
	$result = $redis->get($key);
	return $result;
}

function set_reids_key($key,$value)//设置
{
	$redis = new Redis();
	$redis->connect('127.0.0.1', 6379);
	$result = $redis->set($key,$value);
	return $result;
}

function del_reids_key($arrkey)//删除
{
	$redis = new Redis();
	$redis->connect('127.0.0.1', 6379);
	$result = $redis->delete($arrkey);
	return $result;
}

