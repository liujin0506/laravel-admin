<?php
/**
 * 域名组配置
 *
 * @Author      liujing <liujing@cmstop.com>
 * @DateTime    2018/8/29 10:16
 * @CopyRight   Beijing CmsTop Technology Co.,Ltd.
 */
return [
    'http_method' => env('HTTP_METHOD', 'http'),
    'domain' => env('DOMAIN', ''),
    'backend' => env('DOMAIN_BACKEND', '') . '.' . env('DOMAIN', ''),
    'wechat' => env('DOMAIN_WECHAT', '') . '.' . env('DOMAIN', ''),
    'api' => env('DOMAIN_API', '') . '.' . env('DOMAIN', ''),
];