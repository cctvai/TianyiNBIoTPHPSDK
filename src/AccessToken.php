<?php

namespace Evenvi\Tianyi;


use GuzzleHttp\Client;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class AccessToken
{
    private $accessTokenName;
    private $appConfig;

    public function __construct($appConfig)
    {
        $this->accessTokenName = 'TNPS.access_token';
        $this->appConfig = $appConfig;
    }

    function getToken()
    {
        $cache = new FilesystemAdapter();
        $accessToken = $cache->getItem('tnps.access_token');
        if (!$accessToken->isHit()) {
            $newAccessToken = $this->requestNewAccessToken();
            if(!$newAccessToken){
                return false;
            }
            $accessToken->set($newAccessToken);
            $accessToken->expiresAfter(7000);
            $cache->save($accessToken);

            return $newAccessToken;
        }

        return $accessToken;

    }

    private function requestNewAccessToken()
    {
        $client = new Client([
//        'base_uri' => config('common.telecom_nbiot_server'),
            'timeout' => 5,
            'verify' => false,
            'cert' => [
                $this->appConfig['telecom_nbiot_cert'],
                $this->appConfig['telecom_nbiot_cert_pwd']
            ]
        ]);
        $headers = [
            'Content-Type' => 'application/x-www-form-urlencoded',
        ];

        $serverHost = $this->appConfig['telecom_nbiot_server'];
        $serverPort = $this->appConfig['telecom_nbiot_port'];

        /*------------- TODO 貌似测试平台刷新Token接口报错，这里临时只获取不刷新 -----------------*/
        $url = 'https://' . $serverHost . ':' . $serverPort . '/iocm/app/sec/v1.1.0/login';
        $requestApiData = [
            'appId' => $this->appConfig['telecom_nbiot_appid'],
            'secret' => $this->appConfig['telecom_nbiot_secret']
        ];
        $response = $client->post($url, [
            'headers' => $headers,
            'form_params' => $requestApiData,
        ]);
        $headerCode = $response->getStatusCode();
        if ($headerCode != '200') {
            return false;
        }

        $body = $response->getBody();
        $ret = $body->getContents();
        $retArr = json_decode($ret, true);
        return $retArr['accessToken'];
    }

    function showInfo()
    {
        var_dump("SHOW HOW TO USE");
    }
}
