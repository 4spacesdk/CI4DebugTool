<?php namespace DebugTool;
use Config\Database;

/**
 * Created by PhpStorm.
 * User: martin
 * Date: 12/11/2018
 * Time: 13.34
 */
class Data {

    private static $store = [
        'status' => null
    ];

    public static function set($key, $value) {
        Data::$store[$key] = $value;
    }

    public static function getStore() {
        return Data::$store;
    }

    public static function lastQuery() {
        Data::sql(Database::connect()->showLastQuery());
    }
    public static function sql($sql) {
        Data::debug(str_replace("\n", " ", str_replace("\t", " ", $sql)));
    }
    public static function debug($info = 'test') {
        $func_args = func_get_args();
        if(count($func_args) > 1) $info = implode(' ', $func_args);

        if(!isset(Data::$store['debug'])) Data::$store['debug'] = array();
        $time = explode(" ",microtime());
        $time = date("H:i:s", $time[1]).substr((string)$time[0],1,4);
        if(is_object($info) && method_exists($info, 'count') && method_exists($info, 'allToArray') && $info->count())
            Data::$store['debug'][] = [
                $time => $info->allToArray()
            ];
        else if(is_object($info) && method_exists($info, 'toArray'))
            Data::$store['debug'][] = [
                $time => $info->toArray()
            ];
        else if(is_object($info))
            Data::$store['debug'][] = [
                $time => $info
            ];
        else if(is_array($info))
            Data::$store['debug'][] = [
                $time => $info
            ];
        else
            Data::$store['debug'][] = "$time: $info";
    }

    public static function memory() {
        $memory_limit = ini_get('memory_limit');
        if(preg_match('/^(\d+)(.)$/', $memory_limit, $matches)) {
            if($matches[2] == 'M') {
                $memory_limit = $matches[1] * 1024 * 1024; // nnnM -> nnn MB
            } else if($matches[2] == 'K') {
                $memory_limit = $matches[1] * 1024; // nnnK -> nnn KB
            }
        }
        $memory_limit = $memory_limit / 1024 / 1024;
        $memory_limit = round($memory_limit, 0);

        $mem = memory_get_usage(true);
        $mem = $mem / 1024 / 1024;
        $mem = round($mem, 4);

        $mb = $mem . 'mb of ' . $memory_limit . 'mb';

        Data::debug($mb . ' (' . round(100 / $memory_limit * $mem, 0) . '%)');
    }

}