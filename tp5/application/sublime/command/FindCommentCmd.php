<?php
namespace app\sublime\command;

use think\facade\Env;
use think\Db;
use think\facade\View;
use Sublime\Ret;
use Medoo\Medoo;
class FindCommentCmd{

	public static function run($function = '', $lang = 'zh'){
		// try {
			define('DS', DIRECTORY_SEPARATOR);
			$package_path = Env::get('root_path').'..';
			trace(1);
			trace($package_path);
			$package_path = realpath($package_path);
			trace(2);
			trace($package_path);
			$db_path = $package_path.DS.'php_docs'.DS.$lang.DS.'doc.sqlite';
			// trace($db_path);

			$db = new Medoo([
			    'database_type' => 'sqlite',
				'database_file' => $db_path
			]);
			if($function){
				$fun = $db->has('funlist', ['name'=>$function]);
				if($fun){
					trace('11');
					$data = $db->get('fun', 'data', ['name'=>$function]);
					trace($data);
					$data = json_decode($data, true);
					if(!$data['long_desc'])
						$data['long_desc'] = $data['desc'];

					$url = "http://www.php.net/manual/{$lang}/function.{$function}.php";
					$url = str_replace('_', '-', $url);
					trace($data);
					$output = View::fetch('doc/find', [
						'url'  => $url,
						'fun' => $data,
					]);
					$output = str_replace('\_', '_', $output);
					$output = str_replace('&', '&amp;', $output);
					trace($output);
					// return json_encode($output, JSON_UNESCAPED_UNICODE);
					return Ret::show_popup($output, Ret::SUBLIMT_CONSTS['HIDE_ON_MOUSE_MOVE_AWAY'], -1, 700, 1400, [
						'cmd'  => 'open_tab',
						'args' => ['url'=>''],
					]);
				}else{
					trace('none');
					return Ret::alert("未找到函数 {$function}");
				}
			}else{
				trace('none2');
				return Ret::alert("function: {$function} 为空");
			}

		// } catch (\Exception $e) {
		// 	return Ret::alert('errors:'. $e->getMessage());
		// }
	}
}