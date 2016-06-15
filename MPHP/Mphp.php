<?php
final class MPHP{
	public static function run(){
		//设置常量
		self::_set_const();
		defined('DEBUG') || define('DEBUG',false);
		if(DEBUG){
			//创建文件
			self::_create_dir();
			self::_import_file();
		}else{
			error_reporting(0);
			include TEMP_PATH.'/~boot.php';
		}
		Application::run();
	}
	private static function _set_const(){
		$path=str_replace('\\','/',__FILE__);
		//根目录
		define('MPHP_PATH',dirname($path));
		define('CONFIG_PATH',MPHP_PATH.'/Config');
		define('DATA_PATH',MPHP_PATH.'/Data');
		define('LIB_PATH',MPHP_PATH.'/Lib');
		define('CORE_PATH',LIB_PATH.'/Core');
		define('FUNCTION_PATH',LIB_PATH.'/Function');
		define('EXTENDS_PATH',MPHP_PATH.'/Extends');
		define('ORG_PATH',EXTENDS_PATH.'/Org');
		define('TOOL_PATH',EXTENDS_PATH.'/Tool');
		define('ROOT_PATH',dirname(MPHP_PATH));
		//临时目录
		define('TEMP_PATH',ROOT_PATH.'/Temp');
		define('LOG_PATH',TEMP_PATH.'/Log');
		define('APP_PATH',ROOT_PATH.'/'.APP_NAME);
		define('APP_CONFIG_PATH',APP_PATH.'/Config');
		define('APP_CONTROLLER_PATH',APP_PATH.'/Controller');
		define('APP_VIEW_PATH',APP_PATH.'/View');
		define('APP_PUBLIC_PATH',APP_VIEW_PATH.'/Public');
		define('APP_COMPILE_PATH',TEMP_PATH.'/'.APP_NAME.'/Compile');
		define('APP_CACHE_PATH',TEMP_PATH.'/'.APP_NAME.'/Cache');
		//创建公共路径
		define('COMMON_PATH',ROOT_PATH.'/Common');
		define('COMMON_CONFIG_PATH',COMMON_PATH.'/Config');
		define('COMMON_MODEL_PATH',COMMON_PATH.'/Model');
		define('COMMON_CONTROLLER_PATH',COMMON_PATH.'/Controller');
		define('COMMON_LIB_PATH',COMMON_PATH.'/Lib');

		define('MPHP_VERSION','1.0');
		define('IS_POST',($_SERVER['REQUEST_METHOD'] == 'POST') ? true:false);
		define('IS_GET',($_SERVER['REQUEST_METHOD'] == 'GET') ? true:false);
		if(isset($_SERVER['HTTP_X_REQUEST_WITH']) && isset($_SERVER['HTTP_X_REQUEST_WITH'])=='XMLHttpRequest'){
			define('IS_AJAX',true);
		}else{
			define('IS_AJAX',false);
		}
	}
	private static function _create_dir(){
		$arr= array(
				APP_PATH,
				APP_CONFIG_PATH,
				APP_CONTROLLER_PATH,
				APP_VIEW_PATH,
				APP_PUBLIC_PATH,
				TEMP_PATH,
				LOG_PATH,
				COMMON_CONFIG_PATH,
				COMMON_CONTROLLER_PATH,
				COMMON_LIB_PATH,
				COMMON_MODEL_PATH,
				APP_COMPILE_PATH,
				APP_CACHE_PATH,
			);
		foreach ($arr as $v) {
			is_dir($v) || mkdir($v,0777,true);
		}
		is_file(APP_PUBLIC_PATH.'/success.html') || copy(DATA_PATH.'/Tpl/success.html',APP_PUBLIC_PATH.'/success.html');
		is_file(APP_PUBLIC_PATH.'/error.html') || copy(DATA_PATH.'/Tpl/error.html',APP_PUBLIC_PATH.'/error.html');
	}
	/**
	 * [_import_file 载入框架所需文件]
	 * @return [type] [description]
	 */
	private static function _import_file(){
		$fileArr=array(
			    CORE_PATH.'/Log.class.php',
			    FUNCTION_PATH.'/function.php',
			    ORG_PATH.'/Smarty/Smarty.class.php',
			    CORE_PATH.'/SmartyView.class.php',
			    CORE_PATH.'/Controller.class.php',
				CORE_PATH.'/Application.class.php'
			);
		$str ='';

		foreach($fileArr as $v){
			$str .=trim(substr(file_get_contents($v),5,-2));
			require_once $v;
		}
		$str="<?php\r\n".$str;
		file_put_contents(TEMP_PATH.'/~boot.php',$str) || die('ACCESS NOT ALLOW'); 
	}
}
MPHP::run();