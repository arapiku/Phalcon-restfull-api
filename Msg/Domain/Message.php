<?php

class Domain_Message {

    public function getMessage($toUser) {
        $rs = array();
        $model = new Model_Message();
        $rs = $model->getMessage($toUser);

        return $rs;
    }
    public function saveMessage($array)
    {
    	$rs = false;
    	$array['status']=0;
    	$model = new Model_Message();
    	$rs = $model->saveMessage($array);

    	return $rs;
    }
}
