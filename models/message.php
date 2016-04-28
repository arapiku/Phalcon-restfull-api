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
}
