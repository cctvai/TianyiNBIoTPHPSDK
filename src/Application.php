<?php


namespace Evenvi\Tianyi;


class Application
{
    /**
     * 设备管理
     * 应用服务器向物联网平台添加设备,获取设备的 ID 和验证码,待设备完成接入物联网平台流程后,设备与应用服务器建立从属关系。
     */
    private function device(){

    }

    /**
     * 批量处理
     */
    private function deviceBatch(){

    }

    /**
     * 设备组管理
     */
    private function deviceGroup(){

    }

    /**
     * 设备升级
     */
    private function deviceUpgrade(){

    }

    /**
     * 数据采集
     */
    private function deviceInformation(){

    }

    /**
     * 消息推送
     */
    private function notice(){

    }

    /**
     * 命令下发
     */
    private function command(){

    }


    /**
     * 订阅管理
     */
    private function subscribe(){

    }

    /**
     * 规则管理
     */
    private function rules(){

    }

    /**
     * @param $method
     * @param $args
     * @return mixed
     */
    public function __call($method, $args)
    {
        return $this->$method(...$args);
    }
}
