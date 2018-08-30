<?php
/**
 * Description  CmsTop MediaCloud
 *
 * @Author      liujing <liujing@cmstop.com>
 * @DateTime    2018/8/30 10:58
 * @CopyRight   Beijing CmsTop Technology Co.,Ltd.
 */

namespace App\Library\Jd;


class Jd
{
    private $client;
    public function __construct()
    {
        $config = config('jd');
        $this->client = new JdClient();
        $this->client->appKey = $config['appKey'];
        $this->client->appSecret = $config['appSecret'];
        $this->client->accessToken = $config['accessToken'];
    }

    public function request($api, $params, $result = '')
    {
        $requestClassName = ucfirst($this->camelize(substr($api, 8))) . "Request";
        $class = '\App\Library\Jd\Apis\\' . $requestClassName;
        if (!class_exists($class)) {
            throw new \Exception("No such api: " . $api);
        }
        $req = new $class();
        if (!empty($params)) foreach ($params as $key => $value) {
            if ($key) {
                $req->putOtherTextParam($key, $value);
            }
        }
        $this->client->request = $req;
        $this->client->result = $result;
        try {
            $resp = $this->client->execute();
            return $resp;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    private function camelize($str, $separator = '.')
    {
        $str = str_replace($separator , " ", $str);
        return ltrim(str_replace(" ", "", ucwords($str)), $separator);
    }
}