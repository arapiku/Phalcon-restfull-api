<?php

/**
 * Created by PhpStorm.
 * User: zhoubo
 * Date: 16/4/28
 * Time: 下午5:55
 */

use Phalcon\Mvc\Model;

class message extends Model {
    public function getSource() {
        return 'message';
    }
    public static function moveToMessage($array) {
        $phql = "INSERT INTO message (from_uid, to_uid, contents,time) VALUES (:from_uid:, :to_uid:, :contents:, :time:)";
        $status = $app->modelsManager->executeQuery($phql, $array
//        (
//            'from_uid' => $message->from_uid,
//            'to_uid' => $message->to_uid,
//            'contents' => $message->contents,
//            'time' => $message->time,
//        )
    );
    }
}
