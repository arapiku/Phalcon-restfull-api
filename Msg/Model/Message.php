<?php

class Model_Message extends PhalApi_Model_NotORM {

    public function getMessage($toUser) {
        $rs = DI()->notorm->message->where(['toUser' =>$toUser, 'status'=>0])->fetchall();
        try {
            $strs = DI()->notorm->message->where('toUser', $toUser)->update(['status'=>1]);
            if ($strs >= 1) {
                // 更新成功
                // return true;
            } else if ($strs === 0) {
                //相同数据，无更新
                return false;
            } else if ($strs === false) {
                //更新失败
                return false;
            }
        } catch (PDOException $e) {
            DI()->logger->debug('edit package error', $e->getMessage());
            return array('status' => false, 'msg' => '更新失败');
        }

        return $rs;
    }

    public function saveMessage($array) {
        DI()->logger->info('saveMessage',json_encode($array));
		try {
            $data = DI()->notorm->message;
            $data->insert($array);
            $id = $data->insert_id();
            return ($id > 0) ? true : false;
        } catch (PDOException $e) {
            DI()->logger->debug('saveMessage error', $e->getMessage());
            return false;
        }
    }

	protected function getTableName($id) {
		return 'message';
	}
}
