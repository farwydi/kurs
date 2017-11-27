<?php
/**
 * Created by PhpStorm.
 * User: zharikov
 * Date: 17.11.2017
 * Time: 11:31
 */

use PHPUnit\Framework\TestCase;
use Zend\Cache\StorageFactory;
use DEX\Hook;

include_once __DIR__ . "/../src/belorussia-Impl.php";

class KursBelImplTest extends TestCase
{
    private function resetCache()
    {
        $cache = StorageFactory::factory(array(
            'adapter' => array(
                'name' => 'filesystem',
                'options' => array(
                    'ttl' => 1,
                    'cache_dir' => GIRAR_DATA_DIR . '/kurs-cache'
                )
            )
        ));

        $cache->setItem('cb_bel', "");

        sleep(1);
    }

    private function fakeCache()
    {
//        $logger = new Zend\Log\Logger;
//        $writer = new Zend\Log\Writer\Stream('php://output');
//
//        $logger->addWriter($writer);
//
//
//        $logger->log(Zend\Log\Logger::INFO, 'Informational message');
//        $logger->info('Informational message');
//
//        $logger->log(Zend\Log\Logger::EMERG, 'Emergency message');
//        $logger->emerg('Emergency message');


        $cache = StorageFactory::factory(array(
            'adapter' => array(
                'name' => 'filesystem',
                'options' => array(
                    'ttl' => 36,
                    'cache_dir' => GIRAR_DATA_DIR . '/kurs-cache'
                )
            )
        ));

        $cache->setItem('cb_bel',
            "{\"currencies\":{\"aud\":1.4146,\"bgn\":1.1089,\"uah\":7.5675,\"dkk\":3.178,\"usd\":2.005,\"eur\":2.3648,\"pln\":5.574,\"isk\":1.9401,\"jpy\":1.7813,\"cad\":1.5718,\"kwd\":6.6369,\"mdl\":1.1399,\"nok\":2.4473,\"rub\":3.3615,\"xdr\":2.8255,\"sgd\":1.4782,\"kgs\":2.8746,\"kzt\":6.0392,\"try\":5.1538,\"gbp\":2.6564,\"czk\":9.249,\"sek\":2.3868,\"chf\":2.0222},\"quantities\":{\"aud\":1,\"bgn\":1,\"uah\":100,\"dkk\":10,\"usd\":1,\"eur\":1,\"pln\":10,\"isk\":100,\"jpy\":100,\"cad\":1,\"kwd\":1,\"mdl\":10,\"nok\":10,\"rub\":100,\"xdr\":1,\"sgd\":1,\"kgs\":100,\"kzt\":1000,\"try\":10,\"gbp\":1,\"czk\":100,\"sek\":10,\"chf\":1}}");
    }

    public function testOne()
    {
        $this->resetCache();

        $config = include __DIR__ . "/../config/current/belorussia.php";
        $kurs = new KursBelImpl();

        $hook = new Hook($kurs, $config);

        try {
            $this->assertTrue($hook->do());
        }
        catch (Exception $e) {
            $this->fail();
        }
    }

    /**
     * @depends testOne
     */
    public function testTwo()
    {
        $this->fakeCache();

        $config = include __DIR__ . "/../config/current/belorussia.php";
        $kurs = new KursBelImpl();

        $hook = new Hook($kurs, $config);

        try {
            $this->assertTrue($hook->do());
        }
        catch (Exception $e) {
            $this->fail();
        }
    }
}
