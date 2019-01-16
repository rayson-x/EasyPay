<?php

if (!function_exists('array_forget')) {
    /**
     * 删除数组中的指定元素
     *
     * @param $array
     * @param $keys
     */
    function array_forget(&$array, $keys)
    {
        $original = &$array;

        $keys = (array) $keys;

        if (count($keys) === 0) {
            return;
        }

        foreach ($keys as $key) {
            if (array_key_exists($key, $array)) {
                unset($array[$key]);
                continue;
            }

            $parts = explode('.', $key);

            // 将数组重置为原输入数组
            $array = &$original;

            while (count($parts) > 1) {
                $part = array_shift($parts);

                if (isset($array[$part]) && is_array($array[$part])) {
                    $array = &$array[$part];
                } else {
                    continue 2;
                }
            }

            unset($array[array_shift($parts)]);
        }
    }
}

if (!function_exists('array_removal_empty')) {
    /**
     * 清除数组中的空值
     *
     * @param array $array
     * @return array
     */
    function array_removal_empty(array $array)
    {
        foreach ($array as $key => $value) {
            // bool不为空值
            if ($value == '' && !is_bool($value)) {
                array_forget($array, $key);
            }
        }

        return $array;
    }
}