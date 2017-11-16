<?php
/**

 */
namespace Miniplanet\Helper;

use Miniplanet\Helper\MConfig;

class MipKafka
{

	/**
     * 发送消息
     *
     * @param string $topic
     * @param array $data
     * @return
     */
    public static function push( $key = '', $data = [] )
    {
    	if (!$key || empty($data)) return;
        
        global $rdKafka;
        // 连接Kafka
        if (!$rdKafka)
        {
            // return;
            $rdKafka = new \RdKafka\Producer();
            $rdKafka->addBrokers("127.0.0.1:9092");
        }
        // 添加到队列
		$topic = $rdKafka->newTopic($key);
		$topic->produce(0, 0, json_encode($data)); // RD_KAFKA_PARTITION_UA
    }

}