<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2012 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi.cn@gmail.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------
// TypeEvent.class.php 2013-02-27
namespace Common\Controller;
class TypeEvent{
	//登录成功，获取腾讯QQ用户信息
	public function qq($token){
		$qq=new \Common\OauthSDK\sdk\QqSDK($token);
		$data = $qq->call('user/get_user_info');
		if($data['ret'] == 0){
			$userInfo['type'] = 'QQ';
			$userInfo['name'] = $data['nickname'];
			$userInfo['nick'] = $data['nickname'];
			$userInfo['head'] = $data['figureurl_2'];
			return $userInfo;
		} else {
			throw_exception("获取腾讯QQ用户信息失败：{$data['msg']}");
		}
	}

	//登录成功，获取新浪微博用户信息
	public function sina($token){
		$oauth=new \Common\OauthSDK\sdk\SinaSDK($token);
		$data = $oauth->call('users/show', "uid={$token['openid']}");

		if($data['error_code'] == 0){
			$userInfo['type'] = 'SINA';
			$userInfo['name'] = $data['name'];
			$userInfo['nick'] = $data['screen_name'];
			$userInfo['head'] = $data['avatar_large'];
			return $userInfo;
		} else {
			throw_exception("获取新浪微博用户信息失败：{$data['error']}");
		}
	}

	//登录成功，获取人人网用户信息
	public function renren($token){
		$renren=new \Common\OauthSDK\sdk\RenrenSDK($token);
		$data   = $renren->call('/v2/user/get');
		dump($data);die;
		if(!isset($data['error_code'])){
			$userInfo['type'] = 'RENREN';
			$userInfo['name'] = $data[0]['name'];
			$userInfo['nick'] = $data[0]['name'];
			$userInfo['head'] = $data[0]['headurl'];
			return $userInfo;
		} else {
			throw_exception("获取人人网用户信息失败：{$data['error_msg']}");
		}
	}

	//登录成功，获取360用户信息
	public function x360($token){
		$x360=new \Common\OauthSDK\sdk\X360SDK($token);
		$data = $x360->call('user/me');

		if($data['error_code'] == 0){
			$userInfo['type'] = 'X360';
			$userInfo['name'] = $data['name'];
			$userInfo['nick'] = $data['name'];
			$userInfo['head'] = $data['avatar'];
			return $userInfo;
		} else {
			throw_exception("获取360用户信息失败：{$data['error']}");
		}
	}

	//登录成功，获取豆瓣用户信息
	public function douban($token){
		$douban=new \Common\OauthSDK\sdk\DoubanSDK($token);
		$data   = $douban->call('user/~me');

		if(empty($data['code'])){
			$userInfo['type'] = 'DOUBAN';
			$userInfo['name'] = $data['name'];
			$userInfo['nick'] = $data['name'];
			$userInfo['head'] = $data['avatar'];
			return $userInfo;
		} else {
			throw_exception("获取豆瓣用户信息失败：{$data['msg']}");
		}
	}

	//登录成功，获取Github用户信息
	public function github($token){
		$github=new \Common\OauthSDK\sdk\GithubSDK($token);
		$data   = $github->call('user');

		if(!empty($data['id'])){
			$userInfo['type'] = 'GITHUB';
			$userInfo['name'] = $data['login'];
			// $userInfo['nick'] = $data['name'];
			$userInfo['head'] = $data['avatar_url'];
			return $userInfo;
		} else {
			throw_exception("获取Github用户信息失败：{$data}");
		}
	}


	//登录成功，获取淘宝网用户信息
	public function taobao($token){
		$taobao=new \Common\OauthSDK\sdk\TaobaoSDK($token);
		$fields = 'user_id,nick,sex,buyer_credit,avatar,has_shop,vip_info';
		$data   = $taobao->call('taobao.user.buyer.get', "fields={$fields}");

		if(!empty($data['user_buyer_get_response']['user'])){
			$user = $data['user_buyer_get_response']['user'];
			$userInfo['type'] = 'TAOBAO';
			$userInfo['name'] = $user['user_id'];
			$userInfo['nick'] = $user['nick'];
			$userInfo['head'] = $user['avatar'];
			return $userInfo;
		} else {
			throw_exception("获取淘宝网用户信息失败：{$data['error_response']['msg']}");
		}
	}

	//登录成功，获取百度用户信息
	public function baidu($token){
		$baidu=new \Common\OauthSDK\sdk\BaiduSDK($token);
		$data  = $baidu->call('passport/users/getLoggedInUser');

		if(!empty($data['uid'])){
			$userInfo['type'] = 'BAIDU';
			$userInfo['name'] = $data['uid'];
			$userInfo['nick'] = $data['uname'];
			$userInfo['head'] = "http://tb.himg.baidu.com/sys/portrait/item/{$data['portrait']}";
			return $userInfo;
		} else {
			throw_exception("获取百度用户信息失败：{$data['error_msg']}");
		}
	}

}