<?php
namespace Dingtalk\Utils;

class Cache
{
    public static function set($key, $value, $file)
    {
        $file = $file?:dirname(__FILE__).'/filecache.php';
        if($key && $value){
            $data = json_decode(self::get_file($file),true);
            $item = array();
            $item["$key"] = $value;
            $item['expire_time'] = time() + 7000;
            $item['create_time'] = time();
            $data["$key"] = $item;
            self::set_file($file,json_encode($data));
        }
        return false;
    }

    public static function get($key, $file)
    {
        $file = $file?:dirname(__FILE__).'/filecache.php';
        if($key){
            $data = json_decode(self::get_file($file),true);
            if($data && array_key_exists($key,$data)){
                $item = $data["$key"];
                if(!$item){
                    return false;
                }
                if($item['expire_time']>0 && $item['expire_time'] < time()){
                    return false;
                }

                return $item["$key"];
            }
            return false;
        }
        return false;
    }

    private static function get_file($filename)
    {
        if (!file_exists($filename)) {
            $fp = fopen($filename, "w");
            fwrite($fp, "<?php exit();?>" . '');
            fclose($fp);
            return false;
        }else{
            $content = trim(substr(file_get_contents($filename), 15));
        }
        return $content;
    }

    private static function set_file($filename, $content)
    {
        $fp = fopen($filename, "w");
        fwrite($fp, "<?php exit();?>" . $content);
        fclose($fp);
    }
}