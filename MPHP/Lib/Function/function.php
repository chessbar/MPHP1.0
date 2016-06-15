<?php
// .-----------------------------------------------------------------------------------
// |  Software: [MPHP framework]
// |   Version: 2016.01
// |-----------------------------------------------------------------------------------
// |    Author: M <1006760526@qq.com>
// |-----------------------------------------------------------------------------------
// |   License: http://www.apache.org/licenses/LICENSE-2.0
// '-----------------------------------------------------------------------------------
/**
*@version
*
*/
/**
 * [p description]
 * @param  [type] $arr [description]
 * @return [type]      [description]
 */
function p($arr){
	if(is_bool($arr) || is_null($arr)){
		var_dump($arr);
	}else{
		echo '<pre style="padding:10px;border-radius:5px;bacground:#f5f5f5;border:1px solid #ccc;font-size:14px;">'.print_r($arr,true).'</pre>';
	}
}
//加载配置项
//C($sysConfig) C(userConfig)
//d读取配置项
//临时动态改动配置项
function C($var =NULL ,$value=NULL){
	static $config=array();
	//加载配置项
	if(is_array($var)){
		$config=array_merge($config,array_change_key_case($var,CASE_UPPER));
		return;
	}
	//读取或者更改配置项
	if(is_string($var)){
		$var =strtoupper($var);
		if(!is_null($value)){//有两个参数传递 
			$config[$var]=$value;
			return;
		}
		//读取配置项
		return isset($config[$var])?$config[$var]:NULL;
	}
	if(is_null($var) && is_null($value)){
		return $config;
	}
}
/**
 * [go description]
 * @param  [type]  $url  [description]
 * @param  integer $time [description]
 * @param  string  $msg  [description]
 * @return [type]        [description]
 */
function go($url,$time=0,$msg=''){
	if(!headers_sent()){
		$time == 0 ? header('Location:'.$url) : header("refresh:{$time};url={$url}");
		die($msg);
	}else{
		echo "<meta http-equiv='Refresh' content ='{$time};URL={$url}'>";
		if($time) die($msg);
	}
}
/**
 * [halt description]
 * @param  [type]  $error [description]
 * @param  string  $level [description]
 * @param  integer $type  [description]
 * @param  [type]  $dest  [description]
 * @return [type]         [description]
 */
function halt($error,$level='ERROR',$type=3,$dest=NULL){
	if(is_array($error)){
		Log::write($error['message'],$level,$type,$dest);
	}else{
		Log::write($error,$level,$type,$dest);
	}
	$e=array();
	if(DEBUG){
		if(!is_array($error)){
			$trace =debug_backtrace();
			$e['message']=$error;
			$e['file']=$trace[0]['file'];
			$e['line']=$trace[0]['line'];
			$e['class']=isset($trace[0]['class'])?$trace[0]['class']:'';
			$e['function']=isset($trace[0]['function'])?$trace[0]['function']:'';
			ob_start();//开启缓冲区
			debug_print_backtrace();
			$e['trace']=htmlspecialchars(ob_get_clean());
		}else{
			$e=$error;
		}
	}else{
		if($url=C('ERROR_URL')){
			go($url);
		}else{
			$e['message']=C('ERROR_MSG');
		}
	}
	include DATA_PATH.'/Tpl/halt.html';
	die;
}
/**
 * [print_const 打印用户定义的常量]
 * @return [type] [description]
 */
function print_const(){
	$const=get_defined_constants(true);
	p($const['user']);
}
function M($table){
	$obj= new Model($table);
	return $obj;
}
function K($model){
	$model.='Model';
	return new $model;
}
?>