<?php
require_once('CsvCache.php');
use RNW\CsvCache;

$cache = CsvCache::getCache();

// Ошибка типа
// var_dump($cache->get(1));

// Корректное значение
// var_dump($cache->get('test'));
// var_dump($cache->delete('test'));
// var_dump($cache->getMultiple(['test','test2','test6']));

// Вывод кэша
// var_dump($cache->test());

// $cache->clear();

// Сохранение кэша 
// $cache->updateCache();

// $cache->set('test',[1,2,3,4]);
// $cache->set('test2',[3,2,1,0]);
// $cache->set('test3',[6,7,8,9]);

$cache->setMultiple([
    'test7' => [6,7,8,9]
]);

