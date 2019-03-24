<?php
namespace Sublime;

class Ret{
	const RETURN_TYPE_STATUS_MSG        = 'status_message';
	const RETURN_TYPE_ALERT             = 'error_dialog';
	const RETURN_TYPE_MSG               = 'msg_dialog';
	const RETURN_TYPE_OK                = 'ok_cancel_dialog';
	const RETURN_TYPE_YES               = 'yes_no_cancel_dialog';
	const RETURN_TYPE_CMD_SET_CLIPBOARD = 'set_clipboard';
	const RETURN_TYPE_CMD_RUN_COMMAND   = 'run_command';
	const RETURN_TYPE_SHOW_QUICK_PANEL  = 'show_quick_panel';
	const RETURN_TYPE_SHOW_INPUT_PANEL  = 'show_input_panel';
	const RETURN_TYPE_SHOW_POPUP_MENU   = 'show_popup_menu';
	const RETURN_TYPE_SHOW_POPUP        = 'show_popup';
	const RETURN_TYPE_OPEN_TAB          = 'open_tab';

	const SUBLIMT_CONSTS = [
		'MONOSPACE_FONT'               => 1,
		'KEEP_OPEN_ON_FOCUS_LOST'      => 2,
		'COOPERATE_WITH_AUTO_COMPLETE' => 2,
		'HIDE_ON_MOUSE_MOVE'           => 4,
		'HIDE_ON_MOUSE_MOVE_AWAY'      => 8,
	];

	public static function alert($msg, $cmd=''){
		$arr = [
			'code' => 404,
			'type' => self::RETURN_TYPE_ALERT,
			'msg'  => $msg,
		];
		return self::encode($arr);
	}

	public static function msg($msg){
		$arr = [
			'code'  => 200,
			'type'  => self::RETURN_TYPE_MSG,
			'msg'   => $msg,
		];
		return self::encode($arr);
	}

	public static function ok($msg, $ok_title='', $ok_cmd=[]){
		$arr = [
			'code'     => 200,
			'type'     => self::RETURN_TYPE_OK,
			'msg'      => $msg,
			'ok_title' => $ok_title,
			'ok_cmd'   => $ok_cmd,
		];
		return self::encode($arr);
	}

	public static function yes($msg, $yes_title, $no_title, $yes_cmd=[], $no_cmd=[], $cancel_cmd =[]){
		$arr = [
			'code'       => 200,
			'type'       => self::RETURN_TYPE_YES,
			'msg'        => $msg,
			'yes_title'  => $yes_title,
			'yes_cmd'    => $yes_cmd,
			'no_title'   => $no_title,
			'no_cmd'     => $no_cmd,
			'cancel_cmd' => $cancel_cmd,
		];
		return self::encode($arr);
	}

	public static function status_message($str){
		$arr = [
			'code'  => 200,
			'type'  => self::RETURN_TYPE_STATUS_MSG,
			'msg'   => $str,
		];
		return self::encode($arr);
	}

	public static function set_clipboard($str){
		$arr = [
			'code'  => 200,
			'type'  => self::RETURN_TYPE_CMD_SET_CLIPBOARD,
			'str'   => $str,
		];
		return self::encode($arr);
	}

	/**
	 * [show_quick_panel]
	 * @param  array   $items     字符串数组或者键值数组
	 * @param  array   $on_done_cmd  选择结束命令 cmd 和args
	 * @param  int     $flag  sublime.MONOSPACE_FONT 或 KEEP_OPEN_ON_FOCUS_LOST
	 * @param  integer $selected_index     选中的list 的index
	 * @param  array   $on_highlighted_cmd 高亮命令
	 * @return string
	 */
	public static function show_quick_panel($items, $on_done_cmd = [], $flag, $selected_index = -1, $on_highlighted_cmd = []){
		$arr = [
			'code'               => 200,
			'type'               => self::RETURN_TYPE_SHOW_QUICK_PANEL,
			'items'              => $items,
			'on_done_cmd'        => $on_done_cmd,
			'flag'               => $flag,
			'on_highlighted_cmd' => $on_highlighted_cmd,
		];
		trace($arr);
		return self::encode($arr);
	}

	public static function show_input_panel($caption, $initial_text = '', $on_done_cmd = [], $on_change_cmd = [], $on_cancel_cmd = []){
		$arr = [
			'code'          => 200,
			'type'          => self::RETURN_TYPE_SHOW_INPUT_PANEL,
			'caption'       => $caption,
			'initial_text'  => $initial_text,
			'on_done_cmd'   => $on_done_cmd,
			'on_change_cmd' => $on_change_cmd,
			'on_cancel_cmd' => $on_cancel_cmd,
		];
		return self::encode($arr);
	}

	public static function show_popup_menu($items, $on_done_cmd = []){
		$arr = [
			'code'          => 200,
			'type'          => self::RETURN_TYPE_SHOW_POPUP_MENU,
			'items'         => $items,
			'on_done_cmd'   => $on_done_cmd,
		];
		return self::encode($arr);
	}

	/**
	 * show_pop
	 * @param  string  $content         html内容
	 * @param  int  $flag            sublime.COOPERATE_WITH_AUTO_COMPLETE sublime.HIDE_ON_MOUSE_MOVE sublime.HIDE_ON_MOUSE_MOVE_AWAY
	 * @param  integer $location        -1 或 点坐标
	 * @param  integer $max_width       高度
	 * @param  integer $max_height      宽度
	 * @param  array   $on_navigate_cmd 当html里的a链接点击时的处理
	 * @param  array   $on_hide_cmd     隐藏时的处理
	 * @return string
	 */
	public static function show_popup($content, $flags , $location = -1, $max_width = 200, $max_height = 200, $on_navigate_cmd=[], $on_hide_cmd = []){
		$arr = [
			'code'            => 200,
			'type'            => self::RETURN_TYPE_SHOW_POPUP,
			'content'         => $content,
			'flags'           => $flags,
			'location'        => $location,
			'max_width'       => $max_width,
			'max_height'      => $max_height,
			'on_navigate_cmd' => $on_navigate_cmd,
			'on_hide_cmd'     => $on_hide_cmd,
		];
		return self::encode($arr);
	}

	public static function open_tab($url){
		$arr = [
			'code' => 200,
			'type' => self::RETURN_TYPE_OPEN_TAB,
			'url'  => $url,
		];
		return self::encode($arr);
	}

	/**
	 * 返回执行命令
	 * @param  string $cmd  命令
	 * @param  string $from view sublime applicant
	 * @param  array  $args 参数
	 * @return string
	 */
	public static function run_command($cmd, $from, $args=[]){
		$arr = [
			'code' => 200,
			'type' => self::RETURN_TYPE_CMD_RUN_COMMAND,
			'cmd'  => $cmd,
			'args' => $args,
			'from' => $from,
		];
		return self::encode($arr);
	}

	public static function run_box_command($cmd, $args = []){
		$args = [
			'cmd'=>'php_box',
			'args'=>[
				'call'     => $cmd,
				'cmd_args' => $args
			]
		];
		$arr = [
			'code' => 200,
			'type' => self::RETURN_TYPE_CMD_RUN_COMMAND,
			'cmd'  => $cmd,
			'args' => $args,
			'from' => 'window',
		];
		return self::encode($arr);
	}

	public static function encode($arr){
		foreach ($arr as &$ar) {
			$ar = mb_convert_encoding($ar, 'UTF-8', 'UTF-8');
		}
		$data = json_encode($arr, JSON_UNESCAPED_UNICODE|JSON_NUMERIC_CHECK);
		// trace($data === false);
		$data = base64_encode($data);
		return $data;
	}

	public static function decode($str){
		return json_decode(base64_decode($str), true);
	}
}