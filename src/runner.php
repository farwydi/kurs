<?php
/**
 * Created by PhpStorm.
 * User: zharikov
 * Date: 17.11.2017
 * Time: 11:45
 */

use Zend\Db\Adapter\Adapter;
use Zend\Config\Config;

/**
 * @param $impl DEX\IParser
 * @param $config array
 * @param $logger Zend\Log\Logger
 */
function runner($impl, $config, $logger)
{
    $logger->notice("Start runner");

    try {
        $hook = new DEX\Hook($impl, $config, $logger);

        $hook->do();

        /**
         * @var $impl IAnnouncement
         */

        $impl->notice();
    }
    catch (Zend\Db\Adapter\Exception\InvalidArgumentException $e) {
        $logger->err("Problem connecting to the database");
        $logger->err("InvalidArgumentException: " . $e->getMessage());
        $logger->err("File: " . $e->getFile());
        $logger->err("Line: " . $e->getLine());

        $logger->info("Params: " . var_export($config['database'], true));
    }
    catch (\DEX\HookException $e) {
        $logger->info("HookException: " . $e->getMessage());

        $logger->info("Sleep 5 sec");
        sleep(5);

        /**
         * @var $impl IAutoFixer
         */

        $logger->info("Try fix");

        if ($impl->fix()) {

            /**
             * @var $impl IAnnouncement
             */

            $logger->info("Fix is successful");

            $impl->notice();
        }
        else {
            $logger->err("Fix fail");
        }
    }
    catch (PDOException $e) {
        $logger->err("PDO Exception");
        $logger->err($e->getMessage());
        $logger->err("File: " . $e->getFile());
        $logger->err("Line: " . $e->getLine());
    }
    catch (Exception $e) {
        $logger->err("Unrecognized error");
        $logger->err("Message: " . $e->getMessage());
        $logger->err("File: " . $e->getFile());
        $logger->err("Line: " . $e->getLine());
    }
    finally {
        $logger->notice("End runner");
    }
}