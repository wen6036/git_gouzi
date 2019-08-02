<?php
/**
 * 前台登录退出权限等控制器
 * @author yupoxiong<i@yufuping.com>
 */
namespace app\index\controller;
use think\Request;

use anerg\OAuth2\OAuth;
use think\Config;


use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode as QrCodeExt;
class Auth extends Controller
{
    public function qq(){
        $config   = Config::get('qq_login');
        $OAuth  = OAuth::getInstance($config, 'qq');
        /*if($this->request->isMobile()){
            $OAuth->setDisplay('mobile');
        }*/
        $OAuth->setDisplay('mobile');
        return redirect($OAuth->getAuthorizeURL());
    }


    public function qq_callback() {
        $config   = Config::get('qq_login');
        $OAuth    = OAuth::getInstance($config, 'qq');
        $OAuth->getAccessToken();

        $sns_info = $OAuth->userinfo();

        return
            '<h1>'.$sns_info['nick'].'</h1><br>'.
            '<img src="'.$sns_info['avatar'].'">';
    }

    /**
     * 生成二维码
     * @param  string $text    [字符]
     * @param  [type] $is_save [是否保存]
     * @param  [type] $pid     [唯一标识符]
     * @return [type]          [description]
     */
    public function make_qrcode($text = 'hello 我是稳哥', $is_save = 0, $pid = 0)
    {
        $qrCode = new QrCodeExt($text);
        $a = new ErrorCorrectionLevel(ErrorCorrectionLevel::HIGH);
        $qrCode->setSize(300);
        $qrCode->setWriterByName('png');
        $qrCode->setMargin(10);
        $qrCode->setEncoding('UTF-8');
        $qrCode->setErrorCorrectionLevel($a);


        $qrCode->setForegroundColor(['r' => 0, 'g' => 0, 'b' => 0, 'a' => 0]);
        $qrCode->setBackgroundColor(['r' => 255, 'g' => 255, 'b' => 255, 'a' => 0]);
        $qrCode->setLogoSize(150, 200);
        $qrCode->setRoundBlockSize(true);
        $qrCode->setValidateResult(false);
        $qrCode->setWriterOptions(['exclude_xml_declaration' => true]);
        // Directly output the QR code
        header('Content-Type: ' . $qrCode->getContentType());
        $pid = date('ymdhis').rand(10000,99999);
        if ($is_save) {
            // Save it to a file
            $qrCode->writeFile(config('qrcode_path')  . $pid . '.png');
        }
        // dump($qrCode->writeString());
        die($qrCode->writeString());
        // die("<img src='".$qrCode->writeString()."'>");

    }



    /**
     * 生成二维码
     * @param  string $text    [字符]
     * @param  [type] $is_save [是否保存]
     * @param  [type] $pid     [唯一标识符]
     * @return [type]          [description]
     */
    public function qrcode($text = 'hello 我是稳哥', $is_save = 0, $pid = 0)
    {
        $param = $this->request->param();
        $ordernum = $param['ordernum'];
        // dump($param);
        // exit;
        $qrCode = new QrCodeExt($text.$ordernum);
        $a = new ErrorCorrectionLevel(ErrorCorrectionLevel::HIGH);
        $qrCode->setSize(300);
        $qrCode->setWriterByName('png');
        $qrCode->setMargin(10);
        $qrCode->setEncoding('UTF-8');
        $qrCode->setErrorCorrectionLevel($a);
        $qrCode->setForegroundColor(['r' => 0, 'g' => 0, 'b' => 0, 'a' => 0]);
        $qrCode->setBackgroundColor(['r' => 255, 'g' => 255, 'b' => 255, 'a' => 0]);
        $qrCode->setLogoSize(150, 200);
        $qrCode->setRoundBlockSize(true);
        $qrCode->setValidateResult(false);
        $qrCode->setWriterOptions(['exclude_xml_declaration' => true]);
        // Directly output the QR code
        header('Content-Type: ' . $qrCode->getContentType());
        $pid = date('ymdhis').rand(10000,99999);
        if ($is_save) {
            // Save it to a file
            $qrCode->writeFile(config('qrcode_path')  . $pid . '.png');
        }
        die($qrCode->writeString());

    }
}