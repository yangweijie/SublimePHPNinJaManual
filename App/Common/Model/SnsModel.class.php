<?php
namespace Common\Model;
Use Think\Model;

class SnsModel extends Model{

	public $default_oauths = array(
		'sina'=>array(
			'title'=>'微博',
			'status'=>0,
			'type'=>'sina',
		),
		'qq'=>array(
			'title'=>'QQ',
			'status'=>0,
			'type'=>'qq',
		)
	);

	public function extendOauth($uid){
		$member_oauths = $this->where("member_id={$uid}")->getField('type,id,type,access_token,expires_in,name,openid,openkey,create_time,update_time,status,extend');
		$result = array();
		foreach ($this->default_oauths as $key => $oauth) {
			$result[] = isset($member_oauths[$key])? array_merge($oauth, $member_oauths[$key]): $oauth;
		}
		return $result;
	}
}