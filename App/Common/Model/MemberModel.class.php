<?php
namespace Common\Model;
use Think\Model;

class MemberModel extends Model{
	protected $_validate = array(
		array('nickname','','帐号名称已经存在！',0,'unique',self::MODEL_INSERT), // 在新增的时候验证name字段是否唯一
	);

	protected $_auto = array (
		array('status','1'),  // 新增的时候把status字段设置为1
		array('settings', '{}'),
		array('pwd','password',1,'function') , // 对password字段在新增的时候使password函数处理
		array('create_at','datetime',self::MODEL_INSERT,'function'), // 对create_at字段在更新的时候写入当前时间戳
		array('update_at','datetime',self::MODEL_BOTH,'function'), // 对create_time字段在更新的时候写入当前时间戳
	);

	public function checkLogin($nickname, $pwd){
		$member = $this->where("nickname = '{$nickname}'")->find();
		if($member){
			if(password($pwd) == $member['pwd']){
				return $member['id'];
			}else{
				return -2;
			}
		}else{
			return -1;
		}
	}

	public function login($uid, $nickname){
		$array = array(
			'uid' =>$uid,
			'nickname'=>$nickname
		);
		try {
			session('user', $array);
			return true;
		} catch (Exception $e) {
			$this->error = '未知错误';
			return false;
		}

	}
}