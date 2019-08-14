<?php


namespace Evenvi\Tianyi;


use Exception;
use GuzzleHttp\Client;

class HttpHandler
{
    private $accessToken;
    private $appConfig;
    public function __construct($appConfig)
    {
        $this->appConfig = $appConfig;
        $ak = new AccessToken($appConfig);
        $this->accessToken = $ak->getToken();
    }

    function request($methd, $requestData){
        $client = new Client([
            'timeout' => 5,
            'verify' => false,
            'cert' => [
                $this->appConfig['telecom_nbiot_cert'],
                $this->appConfig['telecom_nbiot_cert_pwd']
            ]
        ]);

        $headers = [
            'Content-Type' => 'application/json',
            "Authorization" => "Bearer $this->accessToken",
            'app_key' => $this->appConfig['telecom_nbiot_appid']
        ];
        $appId = $this->appConfig['telecom_nbiot_appid'];
        $serverHost = $this->appConfig['telecom_nbiot_server'];
        $serverPort = $this->appConfig['telecom_nbiot_port'];
        $url = 'https://' . $serverHost . ':' . $serverPort . '/iocm/app/reg/v1.1.0/deviceCredentials?appId=' . $appId;

        

        try {
            $response = $client->post($url, [
                'headers' => $headers,
                'body' => json_encode($requestData),
            ]);
            $body = $response->getBody();
            $ret = $body->getContents();
            $retArr = json_decode($ret, true);
            if (isset($retArr['error_code'])) {
                return [
                    'code' => '0',
                    'reason' => $retArr['error_code']
                ];
            }
        } catch (Exception $e) {
            return [
                'code' => '0',
                'reason' => $e->getMessage()
            ];
        }
    }
}
