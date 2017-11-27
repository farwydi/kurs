#!/usr/bin/php -q
<?php

include_once __DIR__ . "/../bootstrap.php";

include_once __DIR__ . "/../src/belorussia-Impl.php";
include_once __DIR__ . "/../src/runner.php";

$config = include __DIR__ . "/../config/current/belorussia.php";

//exec("tail -25 " . GIRAR_LOG_DIR . "/kurs-log/belorussia.log", $out);
//foreach ($out as $d) {
//    echo "\033[1;40;37m {$d} \033[0m\n";
//}


$logger = new Zend\Log\Logger;
//$writer = new Zend\Log\Writer\Stream(GIRAR_LOG_DIR . "/kurs-log/belorussia.log");
$writer = new Zend\Log\Writer\Stream('php://output');

$logger->addWriter($writer);

runner(new KursBelImpl($logger), $config, $logger);