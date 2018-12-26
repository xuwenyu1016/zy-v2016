<?php
namespace DuxCms\Controller;
use Home\Controller\SiteController;
/**
 * 表单列表
 */

class FormController extends SiteController {

	/**
     * 列表
     */
    public function index(){
        $name = urldecode(I('get.name'));
        $table = len($name,0,20);
        if(empty($table)){
            $this->error404();
        }
        //获取表单信息
        $where = array();
        $where['table'] = $table;
        $formInfo = D('DuxCms/FieldsetForm')->getWhereInfo($where);
        if(empty($formInfo)){
            $this->error404();
        }
        if(!$formInfo['show_list']){
            $this->error404();
        }
        //分页参数
        $size = intval($info['list_page']); 
        if (empty($size)) {
            $listRows = 20;
        } else {
            $listRows = $size;
        }
        //设置模型
        $model = D('DuxCms/FieldData');
        $model->setTable($formInfo['table']);
        //查询数据
        $where = array();
        if(!empty($formInfo['list_where'])){
            $where['_string'] = $formInfo['list_where'];
        }
        $count = $model->countList($where);
        $limit = $this->getPageLimit($count,$listRows);
        //查询内容
        $list = $model->loadList($where,$limit,$formInfo['list_order']);
        //字段列表
        $where = array();
        $where['A.fieldset_id'] = $formInfo['fieldset_id'];
        $fieldList = D('FieldForm')->loadList($where);
        //格式化表单内容为基本数据
        $data = array();
        if(!empty($list)){
            foreach ($list as $key => $value) {
                $data[$key]=$value;
                foreach ($fieldList as $v) {
                    $data[$key][$v['field']] = D('DuxCms/FieldData')->revertField($value[$v['field']],$v['type'],$v['config']);
                }                
            }
        }
        //URL参数
        $pageMaps = array();
        $pageMaps['name'] = $name;
        //获取分页
        $page = $this->getPageShow($pageMaps);
        //位置导航
        $crumb = array(array('name'=>$formInfo['name'],'url'=>U('DuxCms/Form/index',$pageMaps)));
        //MEDIA信息
        $media = $this->getMedia($formInfo['name']);
        $this->assign('crumb',$crumb);
        $this->assign('media', $media);
        $this->assign('pageList',$data);
        $this->assign('count', $count);
        $this->assign('page', $page);
        $this->assign('formInfo', $formInfo);
        $this->siteDisplay($formInfo['tpl_list']);
    }

    /**
     * 表单内容
     */
    public function info(){
        $name = urldecode(I('get.name'));
        $table = len($name,0,20);
        $id = I('get.id');
        if(empty($table)||empty($id)){
            $this->error404();
        }
        //获取表单信息
        $where = array();
        $where['table'] = $table;
        $formInfo = D('DuxCms/FieldsetForm')->getWhereInfo($where);
        if(empty($formInfo)){
            $this->error404();
        }
        if(!$formInfo['show_info']){
            $this->error404();
        }
        //设置模型
        $model = D('DuxCms/FieldData');
        $model->setTable($formInfo['table']);
        $info = $model->getInfo($id);
        if(empty($info)){
             $this->error404();
        }
        //字段列表
        $where = array();
        $where['A.fieldset_id'] = $formInfo['fieldset_id'];
        $fieldList = D('FieldForm')->loadList($where);
        //格式化表单内容为基本数据
        foreach ($fieldList as $v) {
            $info[$v['field']] = D('DuxCms/FieldData')->revertField($info[$v['field']],$v['type'],$v['config']);
        }
        //位置导航
        $crumb = array(
            array('name'=>$formInfo['name'],'url'=>U('DuxCms/Form/index',array('name'=>$name))),
            array('name'=>'详情','url'=>U('DuxCms/Form/index',array('name'=>$name,'id'=>$id))),
            );
        //MEDIA信息
        $media = $this->getMedia($formInfo['name'] . '- 详情 ');
        $this->assign('crumb',$crumb);
        $this->assign('media', $media);
        $this->assign('formInfo', $formInfo);
        $this->assign('info', $info);
        $this->siteDisplay($formInfo['tpl_info']);
    }

    /**
     * 发布
     */
//    public function push(){
//        if(!IS_POST){
//            $this->error404();
//        }
//        $table = I('post.table');
//        $token = I('post.token');
//        $token = trim($token);
//        if(empty($table)||empty($token)){
//            $this->errorBlock();
//        }
//        //验证token
//        if(!D('FieldsetForm')->validToken($table,$token)){
//            $this->error('安全验证失败，请刷新页面后重新提交！');
//        }
//        //获取表单信息
//        $where = array();
//        $where['table'] = $table;
//        $formInfo = D('DuxCms/FieldsetForm')->getWhereInfo($where);
//        if(empty($formInfo)){
//            $this->errorBlock();
//        }
//        if(!$formInfo['post_status']){
//            $this->errorBlock();
//        }
//        $data = array();
//        foreach (I('post.') as $key => $value) {
//            $data['Fieldset_'.$key] = $value;
//        }
//        $_POST = $data;
//        //设置模型
//        $model = D('DuxCms/FieldData');
//        $model->setTable($formInfo['table']);
//        //增加信息
//        if ($model->saveData('add',$formInfo)){
//            $this->success($formInfo['post_msg'],$formInfo['post_return_url']);
//        }else{
//            $msg = $model->getError();
//            if (empty($msg))
//            {
//                $this->error($formInfo['name'].'发布失败，请刷新后重新尝试！');
//            }else{
//                $this->error($msg);
//            }
//        }
//    }
    public function push(){
        if(IS_AJAX)
            $_POST = $_GET;
        if($_POST['rumd']){
            if($_POST['rumd'] == $_SESSION['Admin']['rumd']){
                $this->error('请不要重复提交');
            }
            $_SESSION['Admin']['rumd'] = $_POST['rumd'];}
        if(!IS_POST and !IS_AJAX){
            $this->error404();
        }
        $table = I('post.table');
        $token = I('post.token');
        $token = trim($token);

        //      短信验证
        $yzm = $_POST['verifiy'];
        if($yzm != 111 || empty($yzm)){
//            echo "<script>alert('验证错误，请返回重做！');</script>";
            $this->error('验证码验证失败，请刷新页面后重新提交！');
        }


        if(empty($table)||empty($token)){
            $this->errorBlock();
        }
        //验证token
        if(!D('FieldsetForm')->validToken($table,$token)){
            $this->error('安全验证失败，请刷新页面后重新提交！');
        }
        //获取表单信息
        $where = array();
        $where['table'] = $table;
        $formInfo = D('DuxCms/FieldsetForm')->getWhereInfo($where);
        if(I('post.post_return_url'))$formInfo['post_return_url']=I('post.post_return_url');
        if(empty($formInfo)){
            $this->errorBlock();
        }
        if(!$formInfo['post_status']){
            $this->errorBlock();
        }
        $data = array();
        $post = I('post.');
        $post['ip']=get_client_ip();

        if($post['ip'] == "188.143.232.14" || $post['ip'] == "124.76.118.148" || $post['ip'] == "146.185.223.45" ||
            $post['ip'] == "60.255.33.26" || $post['ip'] == "60.255.32.90" ||
            $post['ip'] >= "5.188.211.0" && $post['ip'] <= "5.188.211.9"||
            $post['ip'] >= "91.1.1.1" && $post['ip'] <= "91.9.9.9"||
            $post['ip'] >= "46.118.1.1" && $post['ip'] <= "46.118.9.9"||
            $post['ip'] >= "217.70.28.1" && $post['ip'] <= "217.70.28.9" ||
            $post['ip'] >= "178.1.1.1" && $post['ip'] <= "178.999.99.999" ||
            $post['ip'] >= "109.1.1.1" && $post['ip'] <= "109.999.99.999" ||
            $post['ip'] >= "146.1.1.1" && $post['ip'] <= "146.999.99.999" ||
            $post['ip'] >= "185.1.1.1" && $post['ip'] <= "185.999.99.999" ||
            $post['ip'] >= "46.161.9.18" && $post['ip'] <= "46.161.9.3"){
            $this->error('提交失败,滚粗地球吧!');
        }

        foreach ($post as $key => $value) {
            $data['Fieldset_'.$key] = $value;
        }


        $dizhi  = $_POST['dizhi'];
        $flag = 0;
        $params= '';//要post的数据
        $verify = rand(123456, 999999);//获取随机验证码

        //以下信息自己填以下
//        $mobile= '15216755927';//手机号

//        发短信功能暂时去除
        $argv = array(
            'name'=>'zhenya',     //必填参数。用户账号
            'pwd'=>'C048F4670F2E34A37EF092A9DBA8',     //必填参数。（web平台：基本资料中的接口密码）
            'content'=>$dizhi,   //必填参数。发送内容（1-500 个汉字）UTF-8编码
            'mobile'=>'13681277204',   //必填参数。手机号码。多个以英文逗号隔开
            'stime'=>'',   //可选参数。发送时间，填写时已填写的时间发送，不填时为当前时间发送
            'sign'=>'上海震亚律所',    //必填参数。用户签名。
            'type'=>'pt',  //必填参数。固定值 pt
            'extno'=>''    //可选参数，扩展码，用户定义扩展码，只能为数字
        );
//        发短信功能暂时去除

        //print_r($argv);exit;
        //构造要post的字符串
        //echo $argv['content'];
        //dump($argv);
        foreach ($argv as $key=>$value) {
            if ($flag!=0) {
                $params .= "&";
                $flag = 1;
            }
            $params.= $key."="; $params.= urlencode($value);// urlencode($value);
            $flag = 1;
        }
        $url = "http://web.1xinxi.cn/asmx/smsservice.aspx?".$params; //提交的url地址
        //dump($url);
        $con= substr( file_get_contents($url), 0, 1 );  //获取信息发送后的状态

        if($con == '0'){
            $_POST = $data;
            //设置模型
            $model = D('DuxCms/FieldData');
            $model->setTable($formInfo['table']);
            //增加信息
            //dump($formInfo['post_msg']);
            if ($model->saveData('add',$formInfo)){
                if(!IS_AJAX)
                    $this->success($formInfo['post_msg'],$formInfo['post_return_url']);
                else
                    echo $formInfo['post_msg'];
            }else{
                $msg = $model->getError();
                if (empty($msg))
                {
                    $this->error($formInfo['name'].'发布失败，请刷新后重新尝试！');
                }else{
                    $this->error($msg);
                }
            }

        }else{

            $this->ajaxReturn('系统出错请重新提交');
        }



    }


    //    短信验证
    public function miji_yz(){
        $phone  = $_POST['tell'];
        $flag = 0;
        $params= '';//要post的数据
//        $verify = rand(123456, 999999);
        $verify = 111;
        //获取随机验证码

        $_SESSION['yzm'] = $verify;
//        print_r($_SESSION);

        //以下信息自己填以下
        $mobile= $phone;//手机号
        $argv = array(
            'name'=>'zhenya',     //必填参数。用户账号
            'pwd'=>'C048F4670F2E34A37EF092A9DBA8',     //必填参数。（web平台：基本资料中的接口密码）
            'content'=>'短信验证码为：'.$verify.'，请勿将验证码提供给他人。',
//            'content'=>'短信验证码为：'.$verify.'，请勿将验证码提供给他人。',
            //必填参数。发送内容（1-500 个汉字）UTF-8编码
            'mobile'=>$mobile,   //必填参数。手机号码。多个以英文逗号隔开
            'stime'=>'',   //可选参数。发送时间，填写时已填写的时间发送，不填时为当前时间发送
            'sign'=>'上海震亚律所',    //必填参数。用户签名。
            'type'=>'pt',  //必填参数。固定值 pt
            'extno'=>''    //可选参数，扩展码，用户定义扩展码，只能为数字
        );
        //print_r($argv);exit;
        //构造要post的字符串
        //echo $argv['content'];
        //dump($argv);
        foreach ($argv as $key=>$value) {
            if ($flag!=0) {
                $params .= "&";
                $flag = 1;
            }
            $params.= $key."="; $params.= urlencode($value);// urlencode($value);
            $flag = 1;
        }
        $url = "http://web.1xinxi.cn/asmx/smsservice.aspx?".$params; //提交的url地址
        //dump($url);
        $con= substr( file_get_contents($url), 0, 1 );  //获取信息发送后的状态

        if($con == '0'){
            $Form = M("diyyzm");
            $Form->phone = $mobile;
            $Form->yzm = $verify;
            $Form->add();
            $this->ajaxReturn('发送成功');
        }else{

            $this->ajaxReturn('系统出错请重新提交');
        }

    }

}

