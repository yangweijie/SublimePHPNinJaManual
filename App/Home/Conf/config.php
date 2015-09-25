<?php
return array(
    /* 模板相关配置 */
    'TMPL_PARSE_STRING' => array(
        '__STATIC__' => __ROOT__ . '/Public/static',
        '__BOWER__'=>__ROOT__. '/Public/bower_components',
        '__IMG__' => __ROOT__ . '/Public/images',
        '__CSS__' => __ROOT__ . '/Public/css',
        '__JS__' => __ROOT__ . '/Public/js',
    ),
    'SHOW_PAGE_TRACE'=>false,
);