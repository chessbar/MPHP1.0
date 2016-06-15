<?php
class IndexController extends Controller{
	public function __empty(){
		echo '方法不存在';
	}
	public function index(){
		if(!$this->isCached()){
			$this->assign('data',time());
		}
		$this->display();
	}
}
?>