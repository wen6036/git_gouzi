<?php

namespace Wechat;

use Wechat\Lib\Cache;

/**
 * 注册SDK自动加载机制
 * @author Anyon <zoujingli@qq.com>
 * @date 2016/10/26 10:21
 */
spl_autoload_register(function ($class) {
    if (0 === stripos($class, 'Wechat\\')) {
        $filename = dirname(__DIR__) . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
        file_exists($filename) && require($filename);
    }
});

/**
 * 微信SDK加载器
 * @author Anyon <zoujingli@qq.com>
 * @date 2016-08-21 11:06
 */
class Loader
{

    /**
     * 事件注册函数
     * @var array
     */
    static public $callback = array();

    /**
     * 配置参数
     * @var array
     */
    static protected $config = array();

    /**
     * 对象缓存
     * @var array
     */
    static protected $cache = array();


    static private function default_conf()
    {
        $arr = array(
                    // 'appid' =>'wx3545ffff8e607c21' , //weijin
                    'appid' =>'wx6b842dc122641a3b' , //测试号
                    // 'appid' =>'wxd0fe63fadb24f083' , //个人

                    // 'appsecret' =>'13c216595b15b2f95ee4b83dd1652246' , 
                    'appsecret' =>'891be8c518260f94cb71224dd3aa7ca5' , 
                    // 'appsecret' =>'75704905269e0c43ef464d054eec5ae9', 

                    // 'token' => '1q2w3e4r5t', 
                    'token' => 'token', 
                    'encodingaeskey' => '8533ec8f7ece95260025a07895706dd6', 
                    'mch_id' => '1298676901', 
                    'partnerkey' => 'e4da3b7fbbce2345d7772b0674a318d5', 
                    'ssl_key' => 'Upload/Cert/wx3545ffff8e607c21_key.pem', 
                    'ssl_cer' => 'Upload/Cert/wx3545ffff8e607c21_cert.pem'
                );
        self::$config=$arr;  //默认微信配置

       // self::$config=M('manage_wechat_conf')->field('appid,appsecret,token,encodingaeskey,mch_id,partnerkey,ssl_key,ssl_cer')->where(array('env' => $con))->find();  //默认微信配置
    }


    /**
     * 动态注册SDK事件处理函数
     * @param string $event 事件名称（getAccessToken|getJsTicket）
     * @param string $method 处理方法（可以是普通方法或者类中的方法）
     * @param string|null $class 处理对象（可以直接使用的类实例）
     */
    static public function register($event, $method, $class = null)
    {
        if (!empty($class) && class_exists($class, false) && method_exists($class, $method)) {
            self::$callback[$event] = array($class, $method);
        } else {
            self::$callback[$event] = $method;
        }
    }

    /**
     * 获取微信SDK接口对象(别名函数)
     * @param string $type 接口类型(Card|Custom|Device|Extends|Media|Menu|Oauth|Pay|Receive|Script|User|Poi)
     * @param array $config SDK配置(token,appid,appsecret,encodingaeskey,mch_id,partnerkey,ssl_cer,ssl_key,qrc_img)
     * @return WechatCard|WechatCustom|WechatDevice|WechatExtends|WechatMedia|WechatMenu|WechatOauth|WechatPay|WechatPoi|WechatReceive|WechatScript|WechatService|WechatUser
     */
    static public function & get_instance($type, $config = array())
    {
        return self::get($type, $config);
    }

    /**
     * 获取微信SDK接口对象
     * @param string $type 接口类型(Card|Custom|Device|Extends|Media|Menu|Oauth|Pay|Receive|Script|User|Poi)
     * @param array $config SDK配置(token,appid,appsecret,encodingaeskey,mch_id,partnerkey,ssl_cer,ssl_key,qrc_img)
     * @return WechatCard|WechatCustom|WechatDevice|WechatExtends|WechatMedia|WechatMenu|WechatOauth|WechatPay|WechatPoi|WechatReceive|WechatScript|WechatService|WechatUser
     */
    static public function & get($type, $config = array())
    {
        if(empty($config))self::default_conf();
        $index = md5(strtolower($type) . md5(json_encode(self::$config)));
        if (!isset(self::$cache[$index])) {
            $basicName = 'Wechat' . ucfirst(strtolower($type));
            $className = "\\Wechat\\{$basicName}";
            // 注册类的无命名空间别名，兼容未带命名空间的老版本SDK
            !class_exists($basicName, false) && class_alias($className, $basicName);
            self::$cache[$index] = new $className(self::config($config));
        }
        return self::$cache[$index];
    }

    /**
     * 设置配置参数
     * @param array $config
     * @return array
     */
    static public function config($config = array())
    {
        if(empty(self::$config))self::default_conf();  //默认配置

        !empty($config) && self::$config = array_merge(self::$config, $config);

        if (!empty(self::$config['cachepath'])) {
            Cache::$cachepath = self::$config['cachepath'];
        }
        if (empty(self::$config['component_verify_ticket'])) {
            self::$config['component_verify_ticket'] = Cache::get('component_verify_ticket');
        }
        if (empty(self::$config['token']) && !empty(self::$config['component_token'])) {
            self::$config['token'] = self::$config['component_token'];
        }
        if (empty(self::$config['appsecret']) && !empty(self::$config['component_appsecret'])) {
            self::$config['appsecret'] = self::$config['component_appsecret'];
        }
        if (empty(self::$config['encodingaeskey']) && !empty(self::$config['component_encodingaeskey'])) {
            self::$config['encodingaeskey'] = self::$config['component_encodingaeskey'];
        }
        return self::$config;
    }


}
