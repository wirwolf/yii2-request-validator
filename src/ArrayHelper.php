<?php
namespace wirwolf\yii2RequestValidator;

/**
 * Created by Chr0non.
 * Author: Artem Grebenik
 * E-mail: chronon314[dog]gmail[dot]com
 */


/**
 * Class ArrayHelper
 * @package app\helpers
 */
class ArrayHelper extends \yii\helpers\ArrayHelper {
    /**
     * @param $a1
     * @param $a2
     * @return array
     */
    public static function arrayStructureKeyDiffKeys($a1, $a2)
    {
        $r = array();
        foreach ($a1 as $k => $v) {
            if (is_array($v)) {
                if (!isset($a2[$k]) || !is_array($a2[$k])) {
                    $r[] = $k;
                }else {
                    if ($diff = static::arrayStructureKeyDiffKeys($a1[$k], $a2[$k])) {
                        foreach($diff as $k0=>$v0){
                            $r[] = $v0;
                        }
                    }
                }
            }else {
                if (!isset($a2[$k]) || is_array($a2[$k])) {
                    $r[] = $k;
                }
            }
        }
        return $r;
    }

    /**
     * @param $a1
     * @param $a2
     * @return array
     */
    public static function arrayStructureKeyDiff($a1, $a2)
    {
        $r = array();
        foreach ($a1 as $k => $v) {
            if (is_array($v)) {
                if (!isset($a2[$k]) || !is_array($a2[$k])) {
                    $r[$k] = $a1[$k];
                }else {
                    if ($diff = static::arrayStructureKeyDiff($a1[$k], $a2[$k])) {
                        $r[$k] = $diff;
                    }
                }
            }else {
                if (!isset($a2[$k]) || is_array($a2[$k])) {
                    $r[$k] = $v;
                }
            }
        }
        return $r;
    }

    /**
     * @param array $array
     * @return array
     */
    public static function arrayKeysRecursive(array $array)
    {
        $keys = array();

        foreach ($array as $key => $value) {
            $keys[] = $key;

            if (is_array($value)) {
                $keys = array_merge($keys, static::arrayKeysRecursive($value));
            }
        }

        return $keys;
    }

    /**
     * @param $n
     * @param $arr
     * @return bool|int|string
     */
    public static function arrayKeyExistsRecursive($n, $arr) {
        foreach ($arr as $key=>$val) {
            if ($n===$key) {
                return $key;
            }
            if (is_array($val)) {
                if(static::arrayKeyExistsRecursive($n, $val)) {
                    return true;
                }
            }
        }
        return false;
    }
}