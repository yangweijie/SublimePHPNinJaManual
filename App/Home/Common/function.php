<?php
	/**
	 * 判断是否登录，如果登录了返回uid
	 */
	function is_login(){
		return session('?user')? session('user.uid'): 0;
	}