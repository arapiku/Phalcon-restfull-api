<?php
/**
 * Created by PhpStorm.
 * User: zhoubo
 * Date: 16/4/28
 * Time: 下午10:35
 */
class Toolkit
{
    public static function validatePassword($password, $hash)

    {
        if (!is_string($password) || $password === '') {
            echo ('Password must be a string and cannot be empty.');
        }

        if (!preg_match('/^\$2[axy]\$(\d\d)\$[\.\/0-9A-Za-z]{22}/', $hash, $matches) || $matches[1] < 4 || $matches[1] > 30) {
            echo ('Hash is invalid.');
        }
        $test = crypt($password, $hash);
        $n = strlen($test);
        if ($n !== 60) {
            return false;
        }
        return self::compareString($test, $hash);

    }
    static function compareString($expected, $actual)
        {
            $expected .= "\0";
            $actual .= "\0";
            $expectedLength = mb_strlen($expected,'8bit');
            $actualLength = mb_strlen($actual,'8bit');
            $diff = $expectedLength - $actualLength;
            for ($i = 0; $i < $actualLength; $i++) {
                $diff |= (ord($actual[$i]) ^ ord($expected[$i % $expectedLength]));
            }
            return $diff === 0;
        }
}