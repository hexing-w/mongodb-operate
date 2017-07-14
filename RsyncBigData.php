<?php

ignore_user_abort(true);
set_time_limit(0);
error_reporting(0);

// 引入配置
include 'config/kafka.php';

// 生成数据文件
$path = dirname(__FILE__);
$day = date('Ymd');
$time = date('YmdHis');
$country = 'US'; // 国家编码
$rsynclogfile = "{$path}/rsync/rsync_{$day}.log";
$zipfile = "{$path}/rsync/miniplanet@{$country}@{$time}@log.gz";
$filepath = "{$path}/rsync/{$time}/";
$file = $filepath."log.json";
mkdir($filepath);
chmod($filepath, 0777);

// 连接kafka
$conf = new RdKafka\Conf();
$conf->set('group.id', 'BDLogConsumerGroup');
$rk = new RdKafka\Consumer($conf);
$rk->addBrokers($config['kafka']);
$topicConf = new RdKafka\TopicConf();
$topicConf->set('auto.offset.reset', 'smallest');
$topicConf->set('auto.commit.interval.ms', 100);
$topic = $rk->newTopic('BDLog', $topicConf);
$topic->consumeStart(0, RD_KAFKA_OFFSET_STORED);
for ($i=0; $i < 10000; $i++)
{
	$msg = $topic->consume(0, 1000);
    if ($msg->err)
    {
    	break;
    }
    else
    {
    	if ($msg->payload)
    	{
    		file_put_contents($file, $msg->payload."\r\n", FILE_APPEND);
    	}
    	else
    	{
    		break;
    	}
    }
}

// 打包
if (file_exists($file))
{
    // 打包
	shell_exec("gzip -c {$file} > {$zipfile}");

	// rsync同步传输
	if (file_exists($zipfile))
	{
        // 传输
		$rsynclog = shell_exec("rsync -avzcp --port=33874 --bwlimit=5000 --password-file={$path}/config/rsync.pwd  {$zipfile}  Host:src");
        file_put_contents($rsynclogfile, $rsynclog."\r\n-----------------------------------\r\n", FILE_APPEND);
        // 删除ZIP
        // shell_exec("rm -rf {$zipfile}");
	}
}

// 删除文件夹
shell_exec("rm -rf {$filepath}");