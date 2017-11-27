<?php
/**
 * Created by PhpStorm.
 * User: zharikov
 * Date: 15.11.2017
 * Time: 17:12
 */

include_once __DIR__ . "/../bootstrap.php";
//include_once __DIR__ . "/../vendor/autoload.php";
include_once __DIR__ . "/../src/kurs_impl.php";
include_once __DIR__ . "/../src/announcement.php";
include_once __DIR__ . "/../src/autofixer.php";

use Zend\Cache\StorageFactory;
use Zend\Db\Adapter\Adapter;

class KursBelImpl extends KursImpl implements IAutoFixer, IAnnouncement
{
    public function notice()
    {
        // TODO: Implement notice() method.
    }

    public function fix()
    {
        // TODO: Implement fix() method.

        return false;
    }

    public function validation($body)
    {
        return json_decode($body) !== null;
    }

    /**
     * @param $body string
     * @return boolean
     */
    public function run($body)
    {
        $this->last_data = new stdClass();
        $currencies = array();
        $quantities = array();

        $dataFetch = json_decode($body);

        foreach ($dataFetch as $curs) {
            $name = strtolower($curs->Cur_Abbreviation);

            if (array_search($name, $this->template) !== false) {
                $quantities[$name] = $curs->Cur_Scale;
                $currencies[$name] = $curs->Cur_OfficialRate;
            }
        }

        $this->last_data->currencies = $currencies;
        $this->last_data->quantities = $quantities;

        return true;
    }


    private function save()
    {
        $this->cache->setItem('cb_bel', json_encode($this->last_data));
    }
}