<?php
/**
 * 信息接口服务类
 *
 * @author: zhoubo <congminghaoxue@gmail.com>
 */

class Api_Message extends PhalApi_Api {

    public function getRules() {
        return array(
            'index' => array(
                'username'  => array('name' => 'username', 'default' => 'PHPer', ),
            ),
        );
    }
    
    /**
     * 接收信息接口服务
     * @return string title 标题
     * @return string content 内容
     * @return string version 版本，格式：X.X.X
     * @return int time 当前时间戳
     */
    public function receive() {
        $rs = array('code' => 0, 'msg' => 'success', 'info' => array());
        $data = DI()->request->getAll();
        unset($data['sign']);
        unset($data['service']);

        $domain = new Domain_Message();
        $info = $domain->getMessage($data['toUser']);

        if (empty($info)) {
            $rs['code'] = 1;
            $rs['msg'] = 'no messages to you';
            return $rs;
        }

        $rs['info'] = $info;
        return $rs;
    }
    public function send()
    {
        $rs = array('code' => 0, 'msg' => 'success', 'info' => array());
        $data = DI()->request->getAll();
        unset($data['sign']);
        unset($data['service']);
        $domain = new Domain_Message();
        $info = $domain->saveMessage($data);
        if ($info==false) {
            $rs['code'] = 1;
            $rs['msg'] = 'send messages fail';
            return $rs;
        }
        $rs['info'] = $info;
        return $rs;

    }
}
