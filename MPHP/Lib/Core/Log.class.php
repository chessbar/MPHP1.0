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
class log{
	static function write($msg,$level='ERROR',$type=3,$dest=null){
		if(!C('SAVE_LOG')) return;
		if(is_null($dest)){
			$dest=LOG_PATH.'/'.date('Y_m_d').'.log';
		}
		if(is_dir(LOG_PATH)) error_log("[TIME]:".date('Y-m-d H:i:s')."{$level}:{$msg}\r\n",$type,$dest);
	}
}
?>