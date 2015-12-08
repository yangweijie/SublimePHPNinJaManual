<?php
namespace Home\Controller;
use Think\Controller;
class DocController extends Controller{

	protected $funListFile;
	protected $funFile;

	protected function _before_find(){
		$config = C();
		$config['DB_TYPE'] = 'sqlite';
		$config['DB_NAME'] = './php_docs/'.i('lang').'/doc.sqlite';
		$config['DB_PREFIX'] = '';
		C($config);
	}

	protected function _initialize(){
		header('content-type:text/html;charset:utf-8');
		$config = C();
		$config['DB_TYPE'] = 'sqlite';
		$config['DB_NAME'] = './php_docs/'.i('lang').'/doc.sqlite';
		$config['DB_PREFIX'] = '';
		C($config);
		if(!is_dir('./php_docs/'.i('lang'))){
			@mkdir('./php_docs/'.i('lang'));
			foreach (explode(';', $sql) as $key => $query) {
				M()->execute($query);
			}
		}
		if(!file_exists($config['DB_NAME'])){
			$sql1 = <<<sql
CREATE TABLE "fun" (
	 "id" INTEGER NOT NULL,
	 "name" text NOT NULL,
	 "data" TEXT,
	PRIMARY KEY("id"),
	CONSTRAINT "funName" UNIQUE (name COLLATE NOCASE ASC)
);
sql;
			$sql2 = <<<sql
CREATE TABLE "funlist" (
	 "id" INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
	 "name" TEXT,
	CONSTRAINT "function_name" UNIQUE (name COLLATE NOCASE ASC)
);
sql;
			M()->execute($sql1);
			M()->execute($sql2);
		}
		$this->funListFile = DATA_PATH.i('lang').'_php_net.list.json';
		$this->funFile = DATA_PATH.i('lang').'_php_net.json';
	}

	//返回手册数据
	public function find($function, $lang){
		$foo = M('funlist')->where("name='{$function}'")->find();
		if($foo){
			$data = M('fun')->where("name='{$function}'")->getField('data');
			$data = json_decode($data, true);
			if(!$data['long_desc'])
				$data['long_desc'] = $data['desc'];
			// slog($data);
			$this->assign('fun', $data);
			$url = "http://www.php.net/manual/{$lang}/function.{$function}.php";
			$url = str_replace('_', '-', $url);
			$this->assign('url', $url);
			$output = $this->fetch('find');
			$output = str_replace('\_', '_', $output);
			$output = str_replace('&', '&amp;', $output);
			exit(json_encode($output));
		}else{
			exit();
		}
	}

	//导出手册到数据库 的 funlist表
	public function importFunList(){
		$model = M('funlist');
		$rowStr = file_get_contents($this->funListFile);
		$rowStr = json_decode($rowStr, true);
		foreach ($rowStr as $key => $value) {
			$data[] = array('name'=>$value);
		}
		foreach ($data as $key => $value) {
			$adds[floor($key % 20)+1][] = $value;
		}
		foreach ($adds as $key => $add) {
			$model->addAll($add, array(), true);
		}
		$this->show('导入函数列表名完成，共有'.$model->count().'个函数被导入了');
	}

	//导出手册到数据库 fun表
	public function importFun(){
		$model = M('fun');
		$rowStr = file_get_contents($this->funFile);
		$rowStr = json_decode($rowStr, true);
		$data = array();
		foreach ($rowStr as $key => $value) {
			$data[] = array('name'=>$key, 'data'=>json_encode($value, JSON_UNESCAPED_UNICODE));
		}
		foreach ($data as $key => $value) {
			$adds[floor($key % 20)+1][] = $value;
		}
		foreach ($adds as $key => $add) {
			$model->addAll($add, array(), true);
		}
		$this->show('导入函数列表名完成，共有'.count($data).'个函数被导入了');
	}
}