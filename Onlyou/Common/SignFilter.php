<?php

class Common_SignFilter implements PhalApi_Filter
{
    public function check()
    {
        $signature = DI()->request->get('signature');
        $timestamp = DI()->request->get('timestamp');
        $nonce = DI()->request->get('nonce');  

        $token = 'aaa';
        $tmpArr = array($token, $timestamp, $nonce);
        DI()->logger->info(json_encode($tmpArr));
        sort($tmpArr, SORT_STRING);
        DI()->logger->info(json_encode($tmpArr));

        $tmpStr = implode( $tmpArr );
        DI()->logger->info(json_encode($tmpArr));
        $tmpStr = sha1( $tmpStr );
        DI()->logger->error($tmpStr);
        if ($tmpStr != $signature) {
            throw new PhalApi_Exception_BadRequest('wrong sign', 1);
        }
    }
}