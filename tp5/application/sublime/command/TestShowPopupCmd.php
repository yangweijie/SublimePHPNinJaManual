<?php
namespace app\sublime\command;

use think\facade\Env;
use Sublime\Ret;

class TestShowPopupCmd{

	public static function run(){
		return Ret::show_popup('<p>html</p>', 2);
	}
}