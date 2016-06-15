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
final class Application{
	public static function run(){
		self::_init();
		set_error_handler(array(__CLASS__,'error'));
		//致命错误
		register_shutdown_function(array(__CLASS__,'fatal_error'));
		self::_user_import();
		self::_set_url();
		//注册一个自动载入的函数
		spl_autoload_register(array(__CLASS__,'_autoload'));
		self::_create_demo();
		self::_app_run();
	}
	/**
	 * [error 错误提示]
	 * @param  [type] $errno [description]
	 * @param  [type] $error [description]
	 * @param  [type] $file  [description]
	 * @param  [type] $line  [description]
	 * @return [type]        [description]
	 */
	public static function error($errno,$error,$file,$line){
		switch($errno){
			case E_ERROR:
			case E_PARSE:
			case E_CORE_ERROR;
			case E_COMPILE_ERROR;
			case E_USER_ERROR;
			$msg=$error.$file."第{$line}行";
			halt($msg);
			case E_STRICT:
			case E_USER_WARNING:
			case E_USER_NOTICE:
			default:
				if(DEBUG){
					include DATA_PATH.'/Tpl/notice.html';
				}
			break;
		}

	}
	/**
	 * [fatal_error php代码走到头也会调用这个函数]
	 * @return [type] [description]
	 */
	public static function fatal_error(){
		//如果有错
		if($e = error_get_last()){
			 self::error($e['type'],$e['message'],$e['file'],$e['line']);
		}
	}
	/**
	 * [_autoload 自动载入功能]
	 * @return [type] [description]
	 */
	public static function _autoload($className){
		switch (true) {
			//判断是否为控制器
			case strlen($className) > 10 && substr($className,-10) == 'Controller':
			$path=APP_CONTROLLER_PATH.'/'.$className.'.class.php';
				if(!is_file($path)){
					$emptyPath=APP_CONTROLLER_PATH.'/EmptyController.class.php';
					if(is_file($emptyPath)){
						include $emptyPath;
						return;
					}else{
						halt($path.'控制器未找到');
					}
			    }
				break;
			case strlen($className) >5 && substr($className,-5)=='Model':
				$path =COMMON_MODEL_PATH.'/'.$className.'.class.php';
				break;
			default:
				$path =TOOL_PATH.'/'.$className.'.class.php';
				if(!is_file($path)) halt($path.'类未找到');
				break;
		}
		include $path;
	}
	/**
	 * [_user_import description]
	 * @return [type] [description]
	 */
	private static function _user_import(){
		$fileArr=C('AUTO_LOAD_FILE');
		if(is_array($fileArr) && !empty($fileArr)){
			foreach ($fileArr as $v) {
				require_once COMMON_LIB_PATH.'/'.$v;
			}
		}
	}
	/**
	 * [_app_run 实例化应用控制器]
	 * @return [type] [description]
	 */
	private static function _app_run(){
		$c=isset($_GET[C('VAR_CONTROLLER')])?$_GET[C('VAR_CONTROLLER')]:'Index';
		define('CONTROLLER',$c);
		$c.='Controller';
		$a=isset($_GET[C('VAR_ACTION')])?$_GET[C('VAR_ACTION')]:'index';
		define('ACTION',$a);
		if(class_exists($c)){
			$obj =new $c();
			if(!method_exists($obj,$a)){
				if(method_exists($obj,'__empty')){
					$obj->__empty();
				}else{
					halt($c.'控制器中'.$a.'方法不存在');
				}
			}else{
				$obj->$a();
			}
		}else{
			$obj = new EmptyController();
			$obj->index();
		}
		
	}
	/**
	 * [_create_demo 创建默认控制器]
	 * @return [type] [description]
	 */
	private static function _create_demo(){
		$path =APP_CONTROLLER_PATH.'/IndexController.class.php';
		$str=<<<str
<?php
class IndexController extends Controller{
	public function index(){
		header('Content-type:text/html;charset=utf-8');
		echo '<h2>欢迎使用MPHP框架 (: </h2>';
	}
}
?>
str;
		is_file($path) || file_put_contents($path,$str);
	}
	/**
	 * [_init 初始化框架]
	 * @return [type] [description]
	 */
	private static function _init(){
		//加载配置项
		C(include CONFIG_PATH.'/config.php');
		$Config=<<<str
<?php
return array(
//配置项=>配置值
);
?>
str;
		//记载公共配置项
		$commonPath=COMMON_CONFIG_PATH.'/config.php';
		is_file($commonPath) || file_put_contents($commonPath,$Config);
		C(include $commonPath);
		$userPath= APP_CONFIG_PATH.'/config.php';
		is_file($userPath) || file_put_contents($userPath,$Config);
		//加载用户配置
		C(include $userPath);
		//设置默认时区
		date_default_timezone_set(C('DEFAULT_TIME_ZONE'));
		//是否开始session
		C('SESSION_AUTO_START') && session_start();
	}
	/**
	 * [_set_url 设置外部路径]
	 */
	private static function _set_url(){
		$path='http://'.$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];
		$path =str_replace('\\','/',$path);
		define('__APP__',$path);
		define('__ROOT__',dirname(__APP__));
		define('__VIEW__',__ROOT__.'/'.APP_NAME.'/View');
		define('__PUBLIC__',__VIEW__.'/Public');
	}
}
?>