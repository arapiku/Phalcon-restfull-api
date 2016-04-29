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

}