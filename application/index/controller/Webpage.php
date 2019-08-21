<?php
/**
 * @author yupoxiong<i@yufuping.com>
 * 前台基础控制器
 */
namespace app\index\controller;
use think\Request;
use think\Db;
class Webpage extends Controller
{

	public function initialize(){
		$controller = $this->request->controller();
		$action = $this->request->action();
		$url = $controller.'/'.$action;
		$info = Db::table('tz_webpage')->where("pageurl like '%$url'")->find();
		$this->assign('title',$info['title']);
		$this->assign('info',$info);
	}


	// 首頁
	// 强大的数据分析
	public function index_sjfx(){
		$this->initialize();
		return $this->fetch('template');
	}

	// 最新式科技手段
	public function index_kjsd(){
		$this->initialize();
		return $this->fetch('template');
	}

	// 发现人才 培养人才
	public function index_xypt(){
		$this->initialize();
		return $this->fetch('template');
	}

	// 创新学研平台
	public function index_pyrc(){
		$this->initialize();
		return $this->fetch('template');
	}
	// 服务内容
	// 数据分析排名
	public function service_fxpm(){
		$this->initialize();
		return $this->fetch('template');
	}
	// 数据研究
	public function service_sjyj(){
		$this->initialize();
		return $this->fetch('template');
	}
	// 订阅服务
	public function service_dyfw(){
		$this->initialize();
		return $this->fetch('template');
	}

	// 展示服务
	public function service_zsfw(){
		$this->initialize();
		return $this->fetch('template');
	}

	// 盘手服务
	public function service_psfw(){
		$this->initialize();
		return $this->fetch('template');
	}
	// 对接服务
	public function service_djfw(){
		$this->initialize();
		return $this->fetch('template');
	}
	// 定制服务
	public function service_dzfw(){
		$this->initialize();
		return $this->fetch('template');
	}
	// 其他服务
	public function service_qtfw(){
		$this->initialize();
		return $this->fetch('template');
	}
	// 学研中心
	// 深度文章
	public function research_sdwz(){
		$this->initialize();
		return $this->fetch('template');
	}
	// 指标解释
	public function research_zbjs(){
		$this->initialize();
		return $this->fetch('template');
	}
	// 研究报告
	public function research_yjbg(){
		$this->initialize();
		return $this->fetch('template');
	}
	// 策略源码
	public function research_clym(){
		$this->initialize();
		return $this->fetch('template');
	}
	// 培训活动
	public function research_pxhd(){
		$this->initialize();
		return $this->fetch('template');
	}

	// 新手指南
	// 交易界用户使用手册
	public function guide_jysc(){
		$this->initialize();
		return $this->fetch('template');
	}
	// 操盘手使用手册
	public function guide_cpsc(){
		$this->initialize();
		return $this->fetch('template');
	}
	// 工作室功能简介
	public function guide_gnjj(){
		$this->initialize();
		return $this->fetch('template');
	}
	// 用户中心功能介绍
	public function guide_yhzx(){
		$this->initialize();
		return $this->fetch('template');
	}

	// 客服中心
	public function customer_xrzn(){
		$this->initialize();
		return $this->fetch('template');
	}


	// 使用手册
	public function customer_sysc(){
		$this->initialize();
		return $this->fetch('template');
	}

	// 常见问题
	public function customer_cjwt(){
		$this->initialize();
		return $this->fetch('template');
	}
	// 用户中心
	public function customer_yhzx(){
		$this->initialize();
		return $this->fetch('template');
	}

	// 关于我们
	public function about_gywm(){
		$this->initialize();
		return $this->fetch('template');
	}
	// 合作伙伴
	public function about_hzhb(){
		$this->initialize();
		return $this->fetch('template');
	}
	// 招贤纳才
	public function about_zxnc(){
		$this->initialize();
		return $this->fetch('template');
	}

	// 联系方式
	public function about_lxfs(){
		$this->initialize();
		return $this->fetch('template');
	}
	// 法律声明
	public function about_flsm(){
		$this->initialize();
		return $this->fetch('template');
	}

	// 免责条款
	public function about_mztk(){
		$this->initialize();
		return $this->fetch('template');
	}

	// 用户须知
	public function about_yhxz(){
		$this->initialize();
		return $this->fetch('template');
	}

	// 加盟合作
	public function about_jmhz(){
		$this->initialize();
		return $this->fetch('template');
	}


}