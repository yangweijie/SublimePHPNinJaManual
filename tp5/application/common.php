<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件


/**
 * 无阻塞读取标准输入
 * @param 目标输入流 $fd
 * @param 数据写入指针 $data
 * @return boolean
 * @throws Exception
 */
function non_block_read($fd, &$data) {
    $read = array($fd);
    $write = array();
    $except = array();
    $result = stream_select($read, $write, $except, 0);
    if ($result === false) {
        throw new Exception('stream_select failed');
    }
    if ($result === 0) {
        return false;
    }
    $data = stream_get_line($fd, 1);
    return true;
}
