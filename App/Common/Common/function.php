<?php

	function datetime($str = 'now'){
		return @date("Y-m-d H:i:s" ,strtotime( $str ));
	}

	//密码加密
	function password($password){
		$md5str=md5($password);
		$salt=get_password_salt($md5str);
		return hash('sha256',$md5str.$salt);
	}

	function get_password_salt($md5str,$d=1001){
		$crc32_value = floatval(sprintf("%u", crc32($md5str)));
		$crc32_value = ($crc32_value > PHP_INT_MAX) ?
			(($crc32_value - PHP_INT_MAX ) % $d + PHP_INT_MAX % $d) % $d : $crc32_value % $d;
		return $crc32_value;
	}

	/**
	 * 快速文件数据读取和保存 针对简单类型数据 字符串、数组
	 * @param string $name 缓存名称
	 * @param mixed $value 缓存值
	 * @param string $path 缓存路径
	 * @return mixed
	 */
	function d_f($name, $value, $path = DATA_PATH) {
		static $_cache = array();
		$filename = $path . $name . '.php';
		if ('' !== $value) {
			if (is_null($value)) {
				// 删除缓存
				// return false !== strpos($name,'*')?array_map("unlink", glob($filename)):unlink($filename);
			} else {
				// 缓存数据
				$dir = dirname($filename);
				// 目录不存在则创建
				if (!is_dir($dir))
					mkdir($dir, 0755, true);
				$_cache[$name] = $value;
				$content = strip_whitespace("<?php\treturn " . var_export($value, true) . ";?>") . PHP_EOL;
				return file_put_contents($filename, $content, FILE_APPEND);
			}
		}
		if (isset($_cache[$name]))
			return $_cache[$name];
		// 获取缓存数据
		if (is_file($filename)) {
			$value = include $filename;
			$_cache[$name] = $value;
		} else {
			$value = false;
		}
		return $value;
	}

	function friendly_datetime($datetime){
		return date('Y/m/d H:i:s A', strtotime($datetime));
	}

	/**
	 * 获取文档封面图片
	 * @param int $cover_id
	 * @param string $field
	 * @return 完整的数据  或者  指定的$field字段值
	 * @author huajie <banhuajie@163.com>
	 */
	function get_cover($cover_id, $field = 'path'){
		if(empty($cover_id)){
			return false;
		}
		$picture = M('Picture')->where(array('status'=>1))->getById($cover_id);
		if($field == 'path'){
			if(!empty($picture['url'])){
				$picture['path'] = $picture['url'];
			}else{
				$picture['path'] = __ROOT__.$picture['path'];
			}
		}
		return empty($field) ? $picture : $picture[$field];
	}

	function get_nickname_by_uid ($uid = 0){
		return 0 == intval($uid) ? '系统发布': M('Member')->getFieldById($uid, 'nickname');
	}

	/**
	 * 获取文档封面图片
	 * @param int $cover_id
	 * @param string $field
	 * @return 完整的数据  或者  指定的$field字段值
	 * @author huajie <banhuajie@163.com>
	 */
	function get_file($id, $field = 'path'){
		if(empty($id)){
			return false;
		}
		$file = M('File')->where(array('status'=>1))->getById($id);
		if($field == 'path'){
			if(!empty($file['url'])){
				$file['path'] = $file['url'];
			}else{
				$file['path'] = __ROOT__.$file['path'];
			}
		}
		return empty($field) ? $file : $file[$field];
	}

	/**
	 * 获取标签的显示
	 */
	function get_tag($tags, $link = true){
		if($link && $tags){
			$tags = explode(',', $tags);
			$link = array();
			foreach ($tags as $value) {
				$link[] = '<a href="'. U('/') . '?tag='.$value.'"><span class="label label-info">' . $value . '</span></a>';
			}
			return join($link,' ');
		}else{
			return $tags? $tags : '';
		}
	}

	/**
	 * 网站上文件模拟上传，避免重复，且可以统一管理
	 * @param $local_file string 本地文件绝对路径，
	 * @param $model string  Picture 或 File 走哪个模型 对应图片 和普通文件
	 * @param $save_path string 保存的目标路径，尽量和项目中2种文件一致，当然支持自定义
	 * @return array 成功返回array('status'=>1, 'id'=>1, 'path'=>'./Uploads/picture/2015_04_05_09_45_00.png') 类似地址， 失败status=0, error=失败原因
	 */
	function local_upload($local_file, $model = 'Pictrue', $saveroot = './Uploads/picture/'){
		if (!$local_file)
			return array('status'=>0, 'error'=>'文件路径为空');
		$md5 = md5_file($local_file);
		if($record = M($model)->where("md5 = '{$md5}'")->find()){
			return array('status'=>1, 'id'=>$record['id'], 'path'=>$record['path']);
		}
		$upload_config = C(strtoupper($model).'_UPLOAD');
		$ext_arr = explode(',', $upload_config['exts']);
		$ext = strtolower(end(explode('.', $local_file)));
		if (!in_array($ext, $ext_arr))
			return array('status'=>0, 'error'=>"非法后缀{$ext}");

		$filename = uniqid();
		if($upload_config['autoSub']){
			$rule = $upload_config['subName'];
			$name = '';
			if(is_array($rule)){ //数组规则
				$func     = $rule[0];
				$param    = (array)$rule[1];
				foreach ($param as &$value) {
				   $value = str_replace('__FILE__', $filename, $value);
				}
				$name = call_user_func_array($func, $param);
			} elseif (is_string($rule)){ //字符串规则
				if(function_exists($rule)){
					$name = call_user_func($rule);
				} else {
					$name = $rule;
				}
			}
			$savepath .= $name.'/';
		}
		$savepath = $saveroot. $savepath;
		if (!is_dir($savepath)){
			if(!mkdir($savepath, 0777))
				return array('status'=>0, 'error'=>"创建保存目录{$savepath}失败");
		}
		if (!is_readable($savepath)){
			chmod($savepath, 0777);
		}

		$filename = $savepath . $filename . '.' . $ext;

		ob_start();
		readfile($local_file);
		$file = ob_get_contents();
		ob_end_clean();
		$size = strlen($file);
		slog($filename);
		$fp2 = @fopen($filename, "a");
		if(false === fwrite($fp2, $file)){
			return array('status'=>0, 'error'=>'写入目标文件失败');
		}
		fclose($fp2);
		$data = array(
			'name'		  => end(explode('/', $local_file)),
			'path'        => str_replace('./', '/', $filename),
			'md5'         => $md5,
			'sha1'        => sha1($filename),
			'status'      => 1,
			'create_time' => NOW_TIME,
		);
		$id = M($model)->add($data);
		slog($local_file);
		@unlink($local_file);
		if(false === $id){
			@unlink($filename);
			return array('status'=>0, 'error'=>'保存上传文件记录失败');
		}else{
			return array('status'=>1, 'id'=>$id, 'path'=>$data['path']);
		}
	}

	//安全过滤函数
function filter($str){
	if(is_string($str) && ($arr=json_decode($str,true)) && is_array($arr))
	{
		//如果是json，则转换为数组进行过滤
		$arr=array_map('filter',$arr);
		return json_encode($arr,JSON_PRETTY_PRINT);
	}
	if(is_array($str))
	{
		return array_map('filter',$str);
	}
	//没有过滤script字符，要严格对网址格式进行判断
	return str_replace('\\','&#x005C;',htmlentities(strip_tags(trim($str)),ENT_QUOTES,'UTF-8'));
}

/**
 * 数组转字符串
 */
function arr2str($arr, $sep = ',') {
	return implode($sep, $arr);
}

/**
 * 字符串转数组
 */
function str2arr($str, $sep = ',') {
	return explode($sep, $str);
}

//文件名转成文件系统对应的编码
function file_iconv($name) {
	return iconv('UTF-8', C('FILE_SYSTEM_ENCODE'), $name);
}

function url($link = '', $param = '', $default = '') {
	return $default ? $default : U($link, $param);
}

//删除目录及下面文件（递归）
function rmdirr($dirname){
	if (!file_exists($dirname)) {
		return false;
	}
	if (is_file($dirname) || is_link($dirname)) {
		return unlink($dirname);
	}
	$dir = dir($dirname);
	if ($dir) {
		while (false !== $entry = $dir->read()) {
			if ($entry == '.' || $entry == '..') {
				continue;
			}
			rmdirr($dirname . DIRECTORY_SEPARATOR . $entry);
		}
	}
	$dir->close();
	return rmdir($dirname);
}

/**
 * 把返回的数据集转换成Tree
 * @access public
 * @param array $list 要转换的数据集
 * @param string $pid parent标记字段
 * @param string $level level标记字段
 * @return array
 */
function list_to_tree($list, $pk = 'id', $pid = 'pid', $child = '_child', $root = 0) {
	// 创建Tree
	$tree = array();
	if (is_array($list)) {
		// 创建基于主键的数组引用
		$refer = array();
		foreach ($list as $key => $data) {
			$refer[$data[$pk]] = & $list[$key];
		}
		foreach ($list as $key => $data) {
			// 判断是否存在parent
			$parentId = $data[$pid];
			if ($root == $parentId) {
				$tree[] = & $list[$key];
			} else {
				if (isset($refer[$parentId])) {
					$parent = & $refer[$parentId];
					$parent[$child][] = & $list[$key];
				}
			}
		}
	}
	return $tree;
}

/**
 * 将list_to_tree的树还原成列表
 * @param  array $tree  原来的树
 * @param  string $child 孩子节点的键
 * @param  string $order 排序显示的键，一般是主键 升序排列
 * @param  array  $list  过渡用的中间数组，
 * @return array        返回排过序的列表数组
 * @author yangweijie <yangweijiester@gmail.com>
 */
function tree_to_list($tree, $child = '_child', $order='id', &$list = array()){
	if(is_array($tree)) {
		$refer = array();
		foreach ($tree as $key => $value) {
			$reffer = $value;
			if(isset($reffer[$child])){
				unset($reffer[$child]);
				tree_to_list($value[$child], $child, $order, $list);
			}
			$list[] = $reffer;
		}
		$list = list_sort_by($list, $order, $sortby='asc');
	}
	return $list;
}

/**
 * 对查询结果集进行排序
 * @access public
 * @param array $list 查询结果
 * @param string $field 排序的字段名
 * @param array $sortby 排序类型
 * asc正向排序 desc逆向排序 nat自然排序
 * @return array
 */
function list_sort_by($list, $field, $sortby = 'asc') {
	if (is_array($list)) {
		$refer = $resultSet = array();
		foreach ($list as $i => $data)
			$refer[$i] = &$data[$field];
		switch ($sortby) {
			case 'asc': // 正向排序
				asort($refer);
				break;
			case 'desc':// 逆向排序
				arsort($refer);
				break;
			case 'nat': // 自然排序
				natcasesort($refer);
				break;
		}
		foreach ($refer as $key => $val)
			$resultSet[] = &$list[$key];
		return $resultSet;
	}
	return false;
}

/**
 * 在数据列表中搜索
 * @access public
 * @param array $list 数据列表
 * @param mixed $condition 查询条件
 * 支持 array('name'=>$value) 或者 name=$value
 * @return array
 */
function list_search($list,$condition) {
	if(is_string($condition))
		parse_str($condition,$condition);
	// 返回的结果集合
	$resultSet = array();
	foreach ($list as $key=>$data){
		$find = false;
		foreach ($condition as $field=>$value){
			if(isset($data[$field])) {
				if(0 === strpos($value,'/')) {
					$find = preg_match($value,$data[$field]);
				}elseif($data[$field] == $value){
					$find = true;
				}
			}
		}
		if($find)
			$resultSet[] = &$list[$key];
	}
	return $resultSet;
}

//下载文件
function download_file($file, $o_name = '') {
	if (is_file($file)) {
		$length = filesize($file);
		$type = mime_content_type($file);
		$showname = ltrim(strrchr($file, '/'), '/');
		if ($o_name)
			$showname = $o_name;
		header("Content-Description: File Transfer");
		header('Content-type: ' . $type);
		header('Content-Length:' . $length);
		if (preg_match('/MSIE/', $_SERVER['HTTP_USER_AGENT'])) { //for IE
			header('Content-Disposition: attachment; filename="' . rawurlencode($showname) . '"');
		} else {
			header('Content-Disposition: attachment; filename="' . $showname . '"');
		}
		readfile($file);
		exit;
	} else {
		exit('文件不存在');
	}
}

/**
 * 格式化字节大小
 * @param  number $size      字节数
 * @param  string $delimiter 数字和单位分隔符
 * @return string            格式化后的带单位的大小
 * @author yangweijie <917647288@qq.com>
 */
function format_bytes($size, $delimiter = '') {
	$units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
	for ($i = 0; $size >= 1024 && $i < 5; $i++) $size /= 1024;
	return round($size, 2) . $delimiter . $units[$i];
}

/**
 * Description 截取指定长度的字符串 微博使用 汉字或全角字符占1个长度, 英文字符占0.5个长度
 * @param string $str
 * @param int $len = 140 截取长度
 * @param string $ext = '' 添加的后缀
 * @return string $output
 */
function wb_substr($str, $len = 140, $dots = 1, $ext = '') {
	$str = htmlspecialchars_decode(strip_tags(htmlspecialchars($str)));
	$strlenth = 0;
	$output = '';
	preg_match_all("/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/", $str, $match);
	foreach ($match[0] as $v) {
		preg_match("/[\xe0-\xef][\x80-\xbf]{2}/", $v, $matchs);
		if (!empty($matchs[0])) {
			$strlenth += 1;
		} elseif (is_numeric($v)) {
			$strlenth += 0.545;
		} else {
			$strlenth += 0.475;
		}
		if ($strlenth > $len) {
			$output .= $ext;
			break;
		}
		$output .= $v;
	}
	if ($strlenth > $len && $dots)
		$output.='...';
	return $output;
}

/**
 * 字符串截取，支持中文和其他编码
 * @static
 * @access public
 * @param string $str 需要转换的字符串
 * @param string $start 开始位置
 * @param string $length 截取长度
 * @param string $charset 编码格式
 * @param string $suffix 截断显示字符
 * @return string
 */
function msubstr($str, $start=0, $length, $charset="utf-8", $suffix=true) {
	if(function_exists("mb_substr"))
		$slice = mb_substr($str, $start, $length, $charset);
	elseif(function_exists('iconv_substr')) {
		$slice = iconv_substr($str,$start,$length,$charset);
		if(false === $slice) {
			$slice = '';
		}
	}else{
		$re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
		$re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
		$re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
		$re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
		preg_match_all($re[$charset], $str, $match);
		$slice = join("",array_slice($match[0], $start, $length));
	}
	return $suffix ? $slice.'...' : $slice;
}

function highlight_map($str, $keyword) {
	return str_replace($keyword, "<em class='keywords'>{$keyword}</em>", $str);
}

//删除文件前处理文件名
function del_file($file) {
	$file = file_iconv($file);
	@unlink($file);
}

function curl($url, $params, $is_post=0){
	// $url.=(strpos($url,'?')?'&':'?').'run_test=1';//标记是在跑测试

	if($slog_force_client_id=getenv('slog_force_client_id'))
	{
		$url.=(strpos($url,'?')?'&':'?').'slog_force_client_id='.$slog_force_client_id;
	}

	if(!$is_post){
		$url .= (strpos($url,'?')?'&':'?').http_build_query($params);
	}
	slog($url);
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	if($is_post)
	{
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params );
	}

	$result = curl_exec($ch);
	if(curl_errno($ch))
	{
		slog('<h3>接口请求失败</h3>,url:'.$url.',错误信息'.curl_error($ch));
		return false;
	}
	return $result;
}

function curl_file_get_contents($durl){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $durl);
	curl_setopt($ch, CURLOPT_TIMEOUT, 5);
	curl_setopt($ch, CURLOPT_USERAGENT, '');
	curl_setopt($ch, CURLOPT_REFERER,'b');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$file_contents = curl_exec($ch);
	curl_close($ch);
	return $file_contents;
}

// http://www.php.net/manual/en/function.json-decode.php#95782
function json_decode_nice($json, $assoc = FALSE){
	$json = str_replace(array("\n","\r"),"",$json);
	$json = preg_replace('/([{,]+)(\s*)([^"]+?)\s*:/','$1"$3":',$json);
	$json = preg_replace('/(,)\s*}$/','}',$json);
	return json_decode($json,$assoc);
}