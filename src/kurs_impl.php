<?php
/**
 * Created by PhpStorm.
 * User: zharikov
 * Date: 24.11.2017
 * Time: 17:30
 */

include_once __DIR__ . "/../vendor/autoload.php";

use DEX\IParser;
use Zend\Db\Adapter\Adapter;
use Zend\Cache\StorageFactory;
use Zend\Cache\Storage\StorageInterface;
use Zend\Config\Config;
use Zend\Log\Logger;

abstract class KursImpl implements IParser
{
    const STRATEGY_NO = 0;
    const STRATEGY_NEW_ROW = 1;
    const STRATEGY_UPDATE_ROW = 2;
    const STRATEGY_SKIP = 3;

    /**
     * @var int Стратегия.
     */
    protected $strategy = self::STRATEGY_NO;

    /**
     * @var Adapter
     */
    protected $adapter_db;

    /**
     * @var StorageInterface
     */
    protected $cache;

    /**
     * @var stdClass
     */
    protected $last_data;

    /**
     * @var stdClass
     */
    protected $new_data;

    /**
     * @var stdClass
     */
    protected $data_cache;

    /**
     * @var array
     */
    protected $template;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * KursImpl constructor.
     * @param $logger Logger
     */
    public function __construct($logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param Config $config
     * @return bool
     */
    public function before($config)
    {
        $this->logger->info("Connect to DB");
        $this->adapter_db = new Adapter($config->database->toArray());

        // Get data.
        $this->logger->info("Get last data");
        $this->template = $config->currency->toArray();
        $select = "date, " . join(", ", $this->template);
        $table = $config->table;
        $res = $this->adapter_db->query("SELECT {$select} FROM {$table} ORDER BY id DESC LIMIT 1",
            Adapter::QUERY_MODE_EXECUTE);
        $res = $res->current();

        // Data from db for a last day.
        $this->last_data = new stdClass();
        foreach ($this->template as $tm) {
            $this->last_data->{$tm} = $res[$tm];
        }

        // Exist row in db for a given day.
        $date = strtotime($res['date']);
        if (date("d") == date("d", $date)) {

            $this->logger->info("Data exist for a given day");

            // Get cache.
            $this->cache = StorageFactory::factory(array(
                'adapter' => array(
                    'name' => 'filesystem',
                    'options' => array(
                        'ttl' => 86400 + (3600 * 3), // 1 day + 3 hour
                        'cache_dir' => GIRAR_DATA_DIR . '/kurs-cache'
                    )
                )
            ));

            $data_cache = $this->cache->getItem($table);
            if ($data_cache === null) {

                $this->strategy = self::STRATEGY_UPDATE_ROW;
                return true;
            }
            else {

                // Valid cache.
                $this->data_cache = json_decode($data_cache);
                if ($this->data_cache !== null) {

                    if (@is_array($this->data_cache->currencies) && is_array($this->last_data->currencies)) {

                        if (count($this->data_cache->currencies) == count($template)
                            && count($this->last_data->currencies) == count($template)) {

                            // Comparing data cache vs database.
                            if (array_intersect_assoc($this->data_cache->currencies,
                                    $this->last_data->currencies) !== null) {

                                // Are equal do nothing.
                                return false;
                            }
                        }
                    }

                    $this->strategy = self::STRATEGY_UPDATE_ROW;
                    return true;
                }
                else {
                    $this->strategy = self::STRATEGY_UPDATE_ROW;
                    return true;
                }
            }
        }
        else {
            $this->logger->info("Data not found");

            $this->strategy = self::STRATEGY_NEW_ROW;
            return true;
        }
    }

    /**
     * @return boolean
     */
    public function after()
    {
        switch ($this->strategy) {
            case self::STRATEGY_NEW_ROW:
                return $this->strategy_new_row();

            case self::STRATEGY_UPDATE_ROW:
                return $this->strategy_update();

            default:
                return true;
        }
    }

    /**
     * Стратегия обновление уже имеющейся записи.
     * @return boolean
     */
    private function strategy_update()
    {
        return true;
    }

    /**
     * Стратегия добовление новой записи.
     * @return boolean
     */
    private function strategy_new_row()
    {
        $al = (array)$this->data_cache->currencies; // alias
        $dt = date("Y-m-d") . " 00:00:00";

        $insert = "date, " . join(", ", $this->template);
        $data = join(", ", (array)$this->new_data);

        $this->adapter_db->query("INSERT INTO kurs_bel ({$insert}) VALUES ('{$dt}', {$data})",
            Adapter::QUERY_MODE_EXECUTE);

        return true;
    }
}