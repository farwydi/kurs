<?php
/**
 * Created by PhpStorm.
 * User: zharikov
 * Date: 14.11.2017
 * Time: 11:04
 */

exec("php -f ../cron/belorussia.php", $out);

print_r($out);