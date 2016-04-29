<?php
/**
 * Created by PhpStorm.
 * User: zhoubo
 * Date: 16/4/28
 * Time: 下午10:05
 */
use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Resultset\Simple as Resultset;
use Phalcon\Db\Adapter\Pdo\Mysql as PdoMysql;
class user extends Model {

    public static function findByEmail($conditions)
    {
        $robot = new User();
        $phql = "SELECT password_hash FROM user WHERE email = :email:";

        $message = $robot->modelsManager->executeQuery($phql, array(
            'email' => $conditions,
        ))->getFirst();
            return $message;
    }
}
