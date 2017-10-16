<?php
namespace Dingtalk\Utils;

class Log
{
    public static function i($msg)
    {
        self::write('I', $msg);
    }
    
    public static function e($msg)
    {
        self::write('E', $msg);
    }

    private static function write($level, $msg)
    {
        $filename = dirname(__FILE__) . "/corp.log";
        $logFile = fopen($filename, "aw");
        fwrite($logFile, $level . "/" . date(" Y-m-d h:i:s") . "  " . $msg . "\n");
        fclose($logFile);
    }
}