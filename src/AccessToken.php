<?php
namespace Evenvi\Tianyi;


use GuzzleHttp\Client;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class AccessToken
{
    function showInfo()
    {
        var_dump("SHOW HOW TO USE");
    }

    function get()
    {
        $cache = new FilesystemAdapter();
        $accessToken = $cache->getItem('tnps.access_token');
        return $accessToken->get();

        $configSystem = DB::table('config_system')->first();
        if ($configSystem && time() - strtotime($configSystem->timestamp_update) + 600 < $configSystem->expires_in) {
            return $configSystem->access_token;
        }

        $client = new Client([
//        'base_uri' => config('common.telecom_nbiot_server'),
            'timeout' => 5,
            'verify' => false,
            'cert' => [
                storage_path("app/ca/" . config('common.telecom_nbiot_cert')),
                config('common.telecom_nbiot_cert_pwd')
            ]
        ]);
        $headers = [
            'Content-Type' => 'application/x-www-form-urlencoded',
        ];

        $serverHost = config('common.telecom_nbiot_server');
        $serverPort = config('common.telecom_nbiot_port');

        /*------------- TODO 貌似测试平台刷新Token接口报错，这里临时只获取不刷新 -----------------*/
        $url = 'https://' . $serverHost . ':' . $serverPort . '/iocm/app/sec/v1.1.0/login';
        $requestApiData = [
            'appId' => config('common.telecom_nbiot_appid'),
            'secret' => config('common.telecom_nbiot_secret')
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
        if ($configSystem) {
            DB::table('config_system')
                ->update([
                    'access_token' => $retArr['accessToken'],
                    'token_type' => $retArr['tokenType'],
                    'scope' => $retArr['scope'],
                    'refresh_token' => $retArr['refreshToken'],
                    'expires_in' => $retArr['expiresIn'],
                    'timestamp_update' => date('Y-m-d H:i:s')
                ]);
        } else {
            DB::table('config_system')
                ->insert([
                    'access_token' => $retArr['accessToken'],
                    'token_type' => $retArr['tokenType'],
                    'scope' => $retArr['scope'],
                    'refresh_token' => $retArr['refreshToken'],
                    'expires_in' => $retArr['expiresIn'],
                    'timestamp_update' => date('Y-m-d H:i:s')
                ]);
        }
        return $retArr['accessToken'];
    }
}
