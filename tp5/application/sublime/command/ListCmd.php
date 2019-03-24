<?php
namespace app\sublime\command;

use think\facade\Env;
use Sublime\Ret;

class ListCmd{

	public static function run($index = -100){
		$cwd      = getcwd();
		// trace(file_get_contents('Data/Packages/PhpBox/PhpBox.sublime-settings'));
		$app_path = Env::get('app_path');
		// trace($app_path);
		chdir($app_path.'sublime'.DIRECTORY_SEPARATOR.'command');
		$classes = glob('*.php');
		$items = [];
		foreach ($classes as $class) {
			$arr = \explode(DIRECTORY_SEPARATOR, $class);
			$last = array_pop($arr);
			$items[] = [
				'app\sublime\command\\'.\str_ireplace('.php', '', $last),
				$class
			];
		}
		// trace($items);
		if($index == -100){
			return Ret::show_quick_panel($items, $on_done_cmd = [
				'cmd'=>'php_box',
				'args'=>[
					'call'=>'app\sublime\command\ListCmd',
					'cmd_args'=>[
						'index' => -100,
					]
				]
			], $flag = -1, -1, $on_cancel_cmd = [
				'cmd'  => 'status_message',
				'args' => [
					'msg'=>'canceled'
				],
				'from'=>'window'
			]);
		}else{
			if(isset($items[$index])){
				$class = $items[$index];
				try {
					$params = [];
					if(class_exists($class[0])){
						return call_user_func_array([$class[0], 'run'], $params);
					}else if(function_exists($class[0])){
						return call_user_func_array($class[0], $params);
					}else{
						return Ret::alert("没有{$class[0]}对应的方法或函数");
					}
				} catch (\Exception $e) {
					return Ret::alert($e->getMessage());
				}
			}else{
				// trace($index);
				// trace($items);
				return Ret::status_message('canceled');
			}
		}
	}
}