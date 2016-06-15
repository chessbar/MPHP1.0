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
class Model{
	//保存链接信息
	public static $link =null;
	//保存表名
	protected $table =null;
	//初始化信息
	private $opt;
	//发送的sql记录
	public static $sqls=array();
	public function __construct($table=null){
		$this->table =is_null($table) ? C('DB_PREFIX').$this->table:C('DB_PREFIX').$table;
		//链接数据库
		$this->_connect();
		//初始化SQl信息
		$this->_opt();
		//发送的sql记录
	}
	public function query($sql){
		self::$sqls[]=$sql;
		$link=self::$link;
		$result=$link->query($sql);
		if($link->errno) halt('mysql错误:'.$link->error.'</br>SQL:'.$sql);
		$rows=array();
		while($row=$result->fetch_assoc()){
			$rows[]=$row;
		}
		$result->free();
		$this->_opt();
		return $rows;
	}
	public function find(){
		$data=$this->limit(1)->all();
		return current($data);
	}
	public function one(){
		return $this->find();
	}
	public function all(){
		$sql="SELECT ".$this->opt['field'].' FROM '.$this->table.$this->opt['where'].$this->opt['group'].$this->opt['having'].$this->opt['order'].$this->opt['limit'];
		return $this->query($sql);
	}
	public function findAll(){
		return $this->all();
	}
	public function field($field){
		$this->opt['field']=$field;
		return $this;
	}
	public function where($where){
		$this->opt['where']=" WHERE ".$where;
		return $this;
	}
	public function order($order){
		$this->opt['order']=" ORDER BY ".$order;
		return $this;
	}
	public function limit($limit){
		$this->opt['limit']=" LIMIT ".$limit;
		return $this;
	}
	public function group($group){
		$this->opt['group']=" GROUP BY ".$group;
		return $this;
	}
	public function having($having){
		$this->opt['having']=" HAVING ".$having;
		return $this;
	}
	/**
	 * [exe 执行无结果集]
	 * @param  [type] $sql [description]
	 * @return [type]      [description]
	 */
	public function exe($sql){
		self::$sqls[]=$sql;
		$link=self::$link;
		$bool =$link->query($sql);
		$this->_opt();
		//执行有结果集就会返回一个对象
		if(is_object($bool)){
			halt('请用query方法发送查询sql');
		}
		if($bool){
			return $link->insert_id ? $link->insert_id : $link->affected_rows;
		}else{
			halt('mysql错误:'.$link->error.'</br>SQL:'.$sql);
		}
	}
	public function delete(){
		//判断有没有where条件
		if(empty($this->opt['where'])) halt('删除语句必须要有where条件');
		$sql='DELETE FROM '.$this->table.$this->opt['where'];
		return $this->exe($sql);
	}
	public function add($data=null){
		if(is_null($data)) $data=$_POST;
		$fields='';
		$values='';
		foreach ($data as $k => $v) {
			$fields.="`".$this->_safe_str($k)."`,";
			$values.="'".$this->_safe_str($v)."',";
		}
		$fields=trim($fields,',');
		$values=trim($values,',');
		$sql ="INSERT INTO ".$this->table.'('.$fields.') VALUES ('.$values.')';
		return $this->exe($sql);
	}
	public function update($data=null){
		if(empty($this->opt['where'])) halt('更新语句必须要有where条件');
		if(is_null($data)) $data=$_POST;
		$values='';
		foreach ($data as $f => $v) {
			$values.='`'.$this->_safe_str($f)."`='".$this->_safe_str($v)."',";
		}
		$values=trim($values,',');
		$sql="UPDATE ".$this->table." SET ".$values.$this->opt['where'];
		return $this->exe($sql);
	}
	/**
	 * [_save 安全处理]
	 * @return [type] [description]
	 */
	private function _safe_str($str){
		if(get_magic_quotes_gpc()){//判断系统是否开启自动转译
			$str=stripslashes($str);
		}
		return self::$link->real_escape_string($str);
	}
	private function _opt(){
		$this->opt=array(
				'field'=>'*',
				'where'=>'',
				'group'=>'',
				'having'=>'',
				'order'=>'',
				'limit'=>''
			);
	}
	/**
	 * [_connect 链接数据库]
	 * @return [type] [description]
	 */
	private function _connect(){
		if(is_null(self::$link)){
			$db=C('DB_DATABASE');
			if(empty($db)) halt('请先链接数据库');
			$link = new Mysqli(C('DB_HOST'),C('DB_USER'),C('DB_PASSWORD'),C('DB_DATABASE'),C('DB_PORT'));
			if($link->connect_error) halt('数据库链接失败,请检测配置项');
			$link->set_charset(C('DB_CHARSET'));
			self::$link =$link;
		}
	}
}