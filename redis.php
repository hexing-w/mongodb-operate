<?php

include ("MConfig.php");
include ("FlexiHash.php");

class MipRedis
{

    public static $redisCluster = [];

    public static function cluster($module = 'global', $user_id = 0)
    {
        $codisConfig = MConfig::get('redis')[$module];
        // redis_id
        $redis_id = self::selectRedis($codisConfig, $user_id);
        $host = $codisConfig[$redis_id]['host'];
        $port = $codisConfig[$redis_id]['port'];
        $redisKey = md5($host.":".$port);

        // 连接Redis
        if (!isset(self::$redisCluster[$redisKey]))
        {
            $redis = new \Redis();
            $redis->connect($codisConfig[$redis_id]['host'], $codisConfig[$redis_id]['port']);
            self::$redisCluster[$redisKey] = $redis;
        }
        
        return self::$redisCluster[$redisKey];
    }

    public static function selectRedis( $codisConfig = [], $user_id = 0 )
    {
        $num = count($codisConfig);
        $hash_id = self::getHash($user_id, $num);
        $redis_id = $hash_id % $num;
        return $redis_id;
    }

    public static function getHash( $user_id = 0 , $num )
    {
        $hash = new FlexiHash();
        $targets = [];
        for($i=0; $i<$num; $i++)
        {
            $targets[] = $i;
        }
        $hash->addTargets( $targets);
        $hashValue = $hash->lookup( $user_id );
        return $hashValue;
    }

}