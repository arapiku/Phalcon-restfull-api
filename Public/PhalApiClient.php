<?php

class PhalApiClient
{
    protected $host;
    protected $secrect = '';

    protected $params = array();

    protected $moreParams = array();

    protected $url;
    protected $ret;
    protected $msg;
    protected $data = array();

    const RET_OK = 'OK';
    const RET_WRONG = 'WRONG';
    const RET_ERROR = 'ERROR';

    public function __construct($config)
       {
        $this->host = rtrim($config['host'], '/') . '/';
        $this->secrect = $config['secrect'];
    }

    public function request($service, $params = array(), $timeoutMs = 3000)
    {
        $this->params=$params;
        if (!empty($service)) {
            $this->params['service'] = $service;
        }
        $this->url = $this->host . '?' . http_build_query($this->params);
        $this->params['sign'] = $this->encryptAppKey($this->params, $this->secrect);
        $rs = $this->doRequest($this->url, $this->params, $timeoutMs);
        // return $rs;
// var_dump($rs);
        if ($rs === false) {
            $this->ret = self::RET_ERROR;
            $this->msg = '后台接口请求超时';
            return $this->getData();
        }

        $rs = json_decode($rs, true);
        if (isset($rs['data']['code']) && $rs['data']['code'] != 0) {
            $this->ret = self::RET_WRONG;
            $this->msg = '接口调用失败[code =' . $rs['data']['code'] . ']' . ', 错误>信息：' . isset($rs['data']['msg']) ? $rs['data']['msg'] : '无';
            $this->data = $rs['data'];
            return $this->getData();
        }

        $this->ret = intval($rs['ret']) == 200 ? self::RET_OK : self::RET_WRONG;
        $this->data = $rs['data'];
        $this->msg = $rs['msg'];

        return $this->getData();
    }

    public function getRet()
    {
        return $this->ret;
    }

    public function getData()
    {
        return $this->data;
    }
     public function getMsg()
    {
        return $this->msg;
    }

    public function getUrl()
    {
        return $this->url . '&' . http_build_query($this->moreParams);
    }

    protected function encryptAppKey($params, $secrect)
    {
        ksort($params);

        $paramsStrExceptSign = '';
        foreach ($params as $val) {
            $paramsStrExceptSign .= $val;
        }
        $paramsStrExceptSign .= $secrect;
        return md5($paramsStrExceptSign);
    }
  protected function doRequest($url, $data, $timeoutMs = 3000)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, $timeoutMs);

        if (!empty($data)) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }

        $rs = curl_exec($ch);

        curl_close($ch);

        return $rs;
    }
}