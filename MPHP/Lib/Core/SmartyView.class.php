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
*smarty
*/
class SmartyView{
	private static $smarty =NULL;
	public function __construct(){
		if(!is_null(self::$smarty)) return;
		$smarty=new Smarty();
		//模板目录配置
		$smarty->template_dir=APP_VIEW_PATH.'/'.CONTROLLER;
		//编译
		$smarty->compile_dir=APP_COMPILE_PATH;
		//缓存
		$smarty->cache_dir=APP_CACHE_PATH;
		$smarty->left_delimiter=C('LEFT_DELIMITER');
		$smarty->right_delimiter=C('RIGHT_DELIMITER');
		$smarty->caching=C('CACHE_ON');
		$smarty->cache_lifetime=(C('CACHE_TIME'));
		self::$smarty=$smarty;
	}
	protected function display($tpl){
		self::$smarty->display($tpl,$_SERVER['REQUEST_URI']);
	}
	protected function assign($var,$value){
		self::$smarty->assign($var,$value);
	}
	protected function isCached($tpl=null){
		if(!C('SMARTY_ON')) halt('请先开始Smarty');
		$tpl=$this->get_tpl($tpl);
		return self::$smarty->isCached($tpl,$_SERVER['REQUEST_URI']);
	}
}