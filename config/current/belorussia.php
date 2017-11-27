<?php
/**
 * Created by PhpStorm.
 * User: zharikov
 * Date: 17.11.2017
 * Time: 11:42
 */

return array(
    'url' => "http://www.nbrb.by/API/ExRates/Rates?Periodicity=0",
    'table' => 'kurs_bel',
    'currency' => [
        'usd',
        'eur',
        'rub',
        'aud',
        'gbp',
        'bgn',
        'dkk',
        'isk',
        'kzt',
        'cad',
        'kwd',
        'kgs',
        'mdl',
        'nok',
        'pln',
        'xdr',
        'sgd',
        'uah',
        'czk',
        'sek',
        'chf',
        'jpy',
        'try'
    ],
    'database' => [
        'driver' => 'Pdo',
        'dsn' => 'pgsql:host=pg9devel.immo;dbname=kurs',
        'username' => 'postgres',
        'password' => 'y0dsqgfh0km'
    ]
);