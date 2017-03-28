<?php
namespace ThreeBenji\GoldPalm;
/**
 * Created by PhpStorm.
 * User: yan
 * Date: 2017/3/1
 * Time: 下午6:13
 */
class Utils
{
    /**
     * 判断数字是否为奇数
     * @param $num
     * @return int
     */
    public static function is_odd($num)
    {
        return (is_numeric($num) & ($num & 1));

    }

    /**
     * 返回身份证号性别 18位 or 15位
     * @param $id_card
     * @return string
     */
    public static function gender($id_card)
    {
        if (strlen($id_card) == 18) {
            $x = mb_substr($id_card, 16, 1);
        }else{
            $x = mb_substr($id_card, 14, 1);
        }

        if (self::is_odd($x)) {
            return '男';
        } else {
            return '女';
        }
    }

    /**
     * 返回身份证号生日 18位 15位
     * @param $id_card
     * @return string 20170301
     */
    public static function birthday($id_card)
    {
        if (strlen($id_card) == 18) {
            return mb_substr($id_card, 6, 8);
        }else{
            return "19".mb_substr($id_card, 6, 6);
        }
    }

}