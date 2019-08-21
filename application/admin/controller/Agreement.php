<?php

namespace app\admin\controller;

use think\Controller;
use app\admin\model\AdminAgreement;
use think\Request;
// 网站用户协议
class Agreement extends Base
{
    public function index(){
        $model = new AdminAgreement();
        $page_param = ['query' => []];
        $list = $model
            ->order('id desc')
            ->paginate($this->webData['list_rows'], false, $page_param);


        $this->assign([
            'list'  => $list,
            'page'  => $list->render(),
            'total' => $list->total(),
        ]);
        return $this->fetch();
    }

    public function add()
    {
        if ($this->request->isPost()) {
            $this->param['content'] = $this->param['editorValue'];
            // dump($this->param);exit;
            $result = AdminAgreement::create($this->param);
            if ($result) {
                return $this->success();
            }
            return $this->error();
        }

        return $this->fetch();
    }

    public function edit()
    {
        $info = AdminAgreement::get($this->id);
        if ($this->request->isPost()) {
            $this->param['create_time'] = time();
            $this->param['content'] = $_POST['editorValue'];
            if (false !== $info->save($this->param)) {
                return $this->success();
            }
            return $this->error();
        }

        $this->assign([
            'info'       => $info,
        ]);
        return $this->fetch('add');
    }




}