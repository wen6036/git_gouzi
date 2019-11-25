<?php
/**
 * 用户中心
 * @author yupoxiong<i@yufuping.com>
 */
namespace app\index\controller;
use think\Db;
class User  extends Controller
{
    public function index(){
        $userinfo = session('userinfo');
        if(!$userinfo){
            $this->redirect('index/login/login');
        }
        
        $con['id'] = $userinfo['id'];
        $info = Db::table('tz_userinfo')->where($con)->find();
        $address_id = explode('-', $info['address_id']);


        $this->assign('headimg',$info['headimg']);
        $this->assign('nickname',$info['nickname']);
        $this->assign('title','用户管理');
        $this->assign('userinfo', $userinfo);
        return $this->fetch();
    }


    //基本资料
    public function user(){
        return $this->fetch();
    }
    //基本资料
    public function userinfo(){
        $userinfo = session('userinfo');
        $con['id'] = $userinfo['id'];
        $info = Db::table('tz_userinfo')->where($con)->find();

        $address_id = explode('-', $info['address_id']);


        $whereprovince['parent_id'] = 0;
        $listprovince = Db::table('region')->where($whereprovince)->select();

         $this->assign([
            'listprovince'=>$listprovince,
            'info'=>$info,
            'address_id'=>$address_id
        ]);
    	return $this->fetch();
    }

    public function ajax_userinfo(){
        $param = $this->request->param();
        $image_url = $param['image_url'];
        $con['id'] = $param['userid'];

        if(strlen($image_url)<200){
            $data['headimg'] = $image_url;
        }else{
            $data['headimg'] = basepic($image_url);
        }
        $data['nickname'] = $param['nickname'];
        $data['email'] = $param['email'];
        $data['sex'] = $param['sex'];
        $data['wx_num'] = $param['wx_num'];
        $data['address_id'] = $param['province_id'].'-'.$param['city_id'].'-'.$param['district_id'];
        $data['address_detail'] = $param['address_detail'];
        
        $status = Db::table('tz_userinfo')->where($con)->update($data);
        if($status){
            return json(['code'=>1,'msg'=>'修改成功']);
        }else{
            return json(['code'=>0,'msg'=>'修改失败']);
        }
    }

    public function ajax_agreement(){
        $info = Db::table('tz_agreement')->field('content')->where("id=3")->find();
        return json($info);
    }

    // 用户安全
    public function usersafe(){
        $userinfo = session('userinfo');
        $con['id'] = $userinfo['id'];
        $info = Db::table('tz_userinfo')->where($con)->find();

        $bankinfo = json_decode($info['bankinfo']);

        $length = count($bankinfo);

         $this->assign([
            'info'=>$info,
            'bankinfo'=>$bankinfo,
            'length'=>$length
        ]);

    	return $this->fetch();
    }

    // 修改密码
    public function edit_pwd(){
        $param = $this->request->param();
        $userinfo = session('userinfo');
        $con['id'] = $userinfo['id'];
        $con['password'] = md5(md5($param['oldpwd']));

        $status = Db::table('tz_userinfo')->where($con)->find();
        if(!$status) return json(['code'=>0,'msg'=>'原密码不正确']);

        $data['password'] = md5(md5($param['newpwd']));
        $res = Db::table('tz_userinfo')->where($con)->update($data);
        if(!$res){
            return json(['code'=>0,'msg'=>'修改失败']);
        }else{
            return json(['code'=>1,'msg'=>'修改成功']);
        }
    }

    // 修改绑定手机
    public function edit_tel(){
        $param = $this->request->param();
        $userinfo = session('userinfo');
        $id = $userinfo['id'];

        $info = Db::table('tz_userinfo')->where("id=$id")->find();
        if($info['usertel'] == $param['usertel']) return json(['code'=>0,'msg'=>'不能和原手机号一致']);

        $con['usertel'] = $param['usertel'];
        $status = Db::table('tz_userinfo')->where($con)->find();

        if($status) return json(['code'=>0,'msg'=>'该手机号已绑定过了']);

        $res = Db::table('tz_userinfo')->where("id=$id")->update($con);

        if(!$res){
            return json(['code'=>0,'msg'=>'修改失败']);
        }else{
            return json(['code'=>1,'msg'=>'修改成功']);
        }
    }

    // 修改绑定手机
    public function other_phone(){
        $param = $this->request->param();
        $userinfo = session('userinfo');
        $id = $userinfo['id'];

        $info = Db::table('tz_userinfo')->where("id=$id")->find();
        if($info['other_phone'] == $param['other_phone']) return json(['code'=>0,'msg'=>'不能和原手机号一致']);


        $data['other_phone'] = $param['other_phone'];
        $res = Db::table('tz_userinfo')->where("id=$id")->update($data);

        if(!$res){
            return json(['code'=>0,'msg'=>'修改失败']);
        }else{
            return json(['code'=>1,'msg'=>'修改成功']);
        }
    }

    // 修改绑定邮箱
    public function edit_email(){
        $param = $this->request->param();
        $userinfo = session('userinfo');
        $id = $userinfo['id'];

        $info = Db::table('tz_userinfo')->where("id=$id")->find();
        if($info['email'] == $param['email']) return json(['code'=>0,'msg'=>'不能和原邮箱一致']);


        $data['email'] = $param['email'];
        $res = Db::table('tz_userinfo')->where("id=$id")->update($data);

        if(!$res){
            return json(['code'=>0,'msg'=>'修改失败']);
        }else{
            return json(['code'=>1,'msg'=>'修改成功']);
        }
    }


    //绑定银行卡
    public function binkcard(){
        Db::startTrans();
        $userinfo = session('userinfo');
        $id = $userinfo['id'];
        if(!$id){return json(['code'=>0,'msg'=>'请确定用户是否在线']);}

        try {
            $param = $this->request->param();
            $data['bankinfo'] = json_encode($param['array']);
            $res = Db::table('tz_userinfo')->where("id=$id")->update($data);
            if($res){
                 Db::commit();   
                return json(['code'=>1,'msg'=>'保存成功']);
                 Db::rollback();
                return json(['code'=>0,'msg'=>'保存失败']);
            }
        } catch (Exception $e) {
                 Db::rollback();
                return json(['code'=>0,'msg'=>'保存失败']);
        }

    }



    //保存银行卡 和真实姓名
    public function saveinfo(){
        Db::startTrans();
        $userinfo = session('userinfo');
        $id = $userinfo['id'];
        if(!$id){return json(['code'=>0,'msg'=>'请确定用户是否在线']);}

        try {
            $param = $this->request->param();
            $data['truename'] = $param['truename'];
            $data['shenfenzheng'] = $param['shenfenzheng'];
            $data['bankinfo'] = json_encode($param['array']);
            $res = Db::table('tz_userinfo')->where("id=$id")->update($data);
            if($res){
                 Db::commit();   
                return json(['code'=>1,'msg'=>'保存成功']);
                 Db::rollback();
                return json(['code'=>0,'msg'=>'保存失败']);
            }
        } catch (Exception $e) {
                 Db::rollback();
                return json(['code'=>0,'msg'=>'保存失败']);
        }


    }






    public function get_country(){
         header("Access-Control-Allow-Origin: *");
        $whereprovince['type'] = 1;
        $listprovince = Db::table('region')->where($whereprovince)->select();
        return json($listprovince);
    }
    //获取地级市
    public function get_citys(){
         header("Access-Control-Allow-Origin: *");
        // $whereprovince['type'] = 2;
        $whereprovince['parent_id'] = input('param.province_id');
        $listprovince = Db::table('region')->where($whereprovince)->select();
        return json($listprovince);
    }
    //获取地级县
    public function get_district(){
         header("Access-Control-Allow-Origin: *");
        // $whereprovince['type'] = 3;
        $whereprovince['parent_id'] = input('param.city_id');
        $listprovince = Db::table('region')->where($whereprovince)->select();
        return json($listprovince);
    }

}