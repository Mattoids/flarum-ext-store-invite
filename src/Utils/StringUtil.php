<?php

namespace Mattoid\StoreInvite\Utils;

class StringUtil
{
    /**
     * 驼峰转下划线
     * @param $str
     * @return string
     */
    public static function toUnderScore($str)
    {
        $dstr = preg_replace_callback('/([A-Z]+)/',function($matchs)
        {
            return '_'.strtolower($matchs[0]);
        },$str);
        return trim(preg_replace('/_{2,}/','_',$dstr),'_');
    }

    /**
     * 下划线转驼峰
     * @param $str
     * @return mixed|string
     */
    public static function toCamelCase($str)
    {
        $array = explode('_', $str);
        $result = $array[0];
        $len=count($array);
        if($len>1)
        {
            for($i=1;$i<$len;$i++)
            {
                $result.= ucfirst($array[$i]);
            }
        }
        return $result;
    }

    /**
     * 生成邀请码
     */
    public static function getInviteCode($userId)
    {
        $rand_str = sprintf(
            '%s%s%04d%s',
            bin2hex(random_bytes(1)), // 前缀随机数
            base_convert(time() - 1645539742, 10, 16), // 当前时间 - 2022-02-22 22:22:22
            base_convert($userId, 10, 16),    // 用户 ID 5 位数应该够用了
            bin2hex(random_bytes(2)), // 后随机数
        );
        return strtoupper(base_convert($rand_str, 16, 36));
    }
}
