<?php
/**
 * Created by PhpStorm.
 * User: zhoubo
 * Date: 16/4/28
 * Time: 下午10:05
 */
use Phalcon\Mvc\Model;

class user extends Model {
    public function getSource() {
        return 'user';
    }
}
