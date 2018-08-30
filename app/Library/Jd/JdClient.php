<?php
/**
 * Description  CmsTop MediaCloud
 *
 * @Author      liujing <liujing@cmstop.com>
 * @DateTime    2018/8/30 9:39
 * @CopyRight   Beijing CmsTop Technology Co.,Ltd.
 */

namespace App\Library\Jd;


use GuzzleHttp\Client;

class JdClient
{
    public $serverUrl = "https://api.jd.com/routerjson";
    public $accessToken;
    public $connectTimeout = 0;
    public $readTimeout = 0;
    public $appKey;
    public $appSecret;
    public $version = "2.0";
    public $format = "json";
    private $json_param_key = "360buy_param_json";
    public $request;
    public $result;

    protected function generateSign($params)
    {
        ksort($params);
        $stringToBeSigned = $this->appSecret;
        foreach ($params as $k => $v)
        {
            if("@" != substr($v, 0, 1))
            {
                $stringToBeSigned .= "$k$v";
            }
        }
        unset($k, $v);
        $stringToBeSigned .= $this->appSecret;
        return strtoupper(md5($stringToBeSigned));
    }

    public function execute()
    {
        //组装系统参数
        $sysParams["app_key"] = $this->appKey;
        $sysParams["v"] = $this->version;
        $sysParams["method"] = $this->request->getApiMethodName();
        $sysParams["timestamp"] = date("Y-m-d H:i:s");
        if (null != $this->accessToken) {
            $sysParams["access_token"] = $this->accessToken;
        }

        //获取业务参数
        $apiParams = $this->request->getApiParas();
        $sysParams[$this->json_param_key] = $apiParams;

        //签名
        $sysParams["sign"] = $this->generateSign($sysParams);
        //系统参数放入GET请求串
        $requestUrl = $this->serverUrl . "?";
        foreach ($sysParams as $sysParamKey => $sysParamValue) {
            $requestUrl .= "$sysParamKey=" . urlencode($sysParamValue) . "&";
        }
        //发起HTTP请求
        $client = new Client();
        $data = $client->post($requestUrl, ['body' => $apiParams]);
        $rawData = $data->getBody()->getContents();
        if (!$rawData) {
            throw new \Exception("接口数据获取失败");
        }
        try {
            $rawData = json_decode($rawData, true);
            if (isset($rawData[$this->_get_response()])) {
                $data = $rawData[$this->_get_response()];
                if (!$this->result) {
                    $this->result = $this->_get_result();
                }
                return json_decode($data[$this->result], true);
            } elseif (isset($rawData['error_response'])) {
                throw new \Exception("接口数据解析失败, 错误码:"
                    . $rawData['error_response']['code']
                    . " 错误信息:" . $rawData['error_response']['zh_desc']);
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

    }

    private function _get_response()
    {
        return str_replace('.', '_', $this->request->getApiMethodName()) . '_responce';
    }

    private function _get_result()
    {
        $arr = explode('.', $this->request->getApiMethodName());
        $arr = end($arr);
        return $arr . '_result';
    }
}