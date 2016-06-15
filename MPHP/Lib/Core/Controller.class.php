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
*父类控制
*/
class Controller extends SmartyView{
	private $var = array();
	public function __construct(){
		if(C('SMARTY_ON')){
			parent::__construct();
		}
		if(method_exists($this,'__init')){
			$this->__init();
		}
		if(method_exists($this,'__auto')){
			$this->__auto();
		}
	}
	protected function get_tpl($tpl){
		if(is_null($tpl)){
			$path=APP_VIEW_PATH.'/'.CONTROLLER.'/'.ACTION.C('TPL_SUFFIX');
		}else{
			$suffix=strrchr($tpl,'.');
			$tpl=empty($suffix)?$tpl.C('TPL_SUFFIX'):$tpl;
			$path=APP_VIEW_PATH.'/'.CONTROLLER.'/'.$tpl;
		}
		return $path;
	}
	/**
	 * [display 载入模板]
	 * @param  [type] $tpl [description]
	 * @return [type]      [description]
	 */
	protected function display($tpl=NULL){
		$path=$this->get_tpl($tpl);
		if(!is_file($path)) halt($path.'模板文件不存在');
		if(C('SMARTY_ON')){
			parent::display($path);
		}else{
			extract($this->var);
		    include $path;
		}	
	}
	/**
	 * [assign description]
	 * @param  [type] $var   [description]
	 * @param  [type] $value [description]
	 * @return [type]        [description]
	 */
	protected function assign($var,$value){
		if(C('SMARTY_ON')){
			parent::assign($var,$value);
		}else{
			$this->var[$var]=$value;
		}	
	}
	/**
	 * [success 成功提示]
	 * @param  string  $message [description]
	 * @param  [type]  $url     [description]
	 * @param  integer $time    [description]
	 * @return [type]           [description]
	 */
	protected function success($message="成功",$url=null,$time=3){
		$url=$url ? "window.location.href='".$url."'":'window.history.back(-1)';
		include APP_PUBLIC_PATH.'/success.html';
		die;
	}
	/**
	 * [error 错误提示]
	 * @param  string  $message [description]
	 * @param  [type]  $url     [description]
	 * @param  integer $time    [description]
	 * @return [type]           [description]
	 */
	protected function error($message="失败",$url=null,$time=3){
		$url=$url ? "window.location.href='".$url."'":'window.history.back(-1)';
		include APP_PUBLIC_PATH.'/error.html';
		die;
	}
}
?>