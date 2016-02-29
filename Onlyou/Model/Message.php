<?php

class Model_Message extends PhalApi_Model_NotORM {

    public function receiveMessage($toUser) {

// return 11;
        DI()->notorm->message->where('toUser = ?', $toUser)->delete();
            	DI()->logger->info('Model_Message',json_encode($re));
            	// return $re;

    }

    public function saveMessage($userId) {
		
        return $rs;
    }

	protected function getTableName($id) {
		return 'message';
	}
}
