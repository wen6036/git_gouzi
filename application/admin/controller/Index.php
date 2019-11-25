<?php
/**
 * 后台首页
 * @author yupoxiong <i@yufuping.com>
 */

namespace app\admin\controller;

use Parsedown;
use tools\Sysinfo;
use think\Db;
class Index extends Base
{
    public function index()
    {
        $sysinfo  = new Sysinfo();
        $sys_info = [
            'lang'    => $sysinfo->getLang(),
            'browser' => $sysinfo->getBrowser(),
            'ip'      => $sysinfo->getIp(),
            'city'    => $sysinfo->getCity(),
            'os'      => $sysinfo->getOS(),
            'date'    => date('Y-m-d')
        ];

        $time = strtotime("-0 year -6 month -0 day");
        Db::table('bear_admin_logs')->where("create_time < $time")->delete();

        $Parsedown = new Parsedown();

        $this->assign([
            'readme'=> $Parsedown->text(file_get_contents(ROOT_PATH.'README.md')),
            'sys'      => $sys_info,
        ]);
        return $this->fetch();
    }
}