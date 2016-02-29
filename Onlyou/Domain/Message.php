<?php

class Domain_Message {

    public function receiveMessage($toUser) {
        $rs = array();
        $model = new Model_Message();
        $rs = $model->receiveMessage($toUser);

        return $rs;
    }
}
