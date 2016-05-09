<?php
use Phalcon\Mvc\Model;

class msg extends Model {
	public function getSource() {
		return 'msg';
	}
    public static function getMsg($to_uid){
        $phql = "SELECT * FROM msg WHERE to_uid = :to_uid: ORDER BY time";
        $robots = new Msg();
        $messages = $robots->modelsManager->executeQuery(
            $phql,
            array(
                'to_uid' => $to_uid,
            )
        );
        return $messages;
    }
    public static function delMsgById($id) {
        $phql = "DELETE FROM msg WHERE id = :id:";
        $robots = new Msg();
        $robots->modelsManager->executeQuery ($phql, array (
            'id' => $id,
        ));
    }
    public static function saveMsg($array) {
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