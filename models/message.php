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
        $robots = new Msg();
        $array = [
            'from_uid' => $array->from_uid,
            'to_uid' => $array->to_uid,
            'contents' => $array->contents,
            'time' => $array->time,
        ];
        return $robots->modelsManager->executeQuery($phql, $array);
    }
}
