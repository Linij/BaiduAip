<?php

namespace Linij\BaiduAip\Lib;

class AipHttpUtil
{
    // 根据RFC 3986，除了：
    //   1.大小写英文字符
    //   2.阿拉伯数字
    //   3.点'.'、波浪线'~'、减号'-'以及下划线'_'
    // 以外都要编码
    public static $PERCENT_ENCODED_STRINGS;

    //填充编码数组
    public static function __init()
    {
        self::$PERCENT_ENCODED_STRINGS = array();
        for ($i = 0; $i < 256; ++$i) {
            self::$PERCENT_ENCODED_STRINGS[$i] = sprintf("%%%02X", $i);
        }

        //a-z不编码
        foreach (range('a', 'z') as $ch) {
            self::$PERCENT_ENCODED_STRINGS[ord($ch)] = $ch;
        }

        //A-Z不编码
        foreach (range('A', 'Z') as $ch) {
            self::$PERCENT_ENCODED_STRINGS[ord($ch)] = $ch;
        }

        //0-9不编码
        foreach (range('0', '9') as $ch) {
            self::$PERCENT_ENCODED_STRINGS[ord($ch)] = $ch;
        }

        //以下4个字符不编码
        self::$PERCENT_ENCODED_STRINGS[ord('-')] = '-';
        self::$PERCENT_ENCODED_STRINGS[ord('.')] = '.';
        self::$PERCENT_ENCODED_STRINGS[ord('_')] = '_';
        self::$PERCENT_ENCODED_STRINGS[ord('~')] = '~';
    }

    /**
     * 在uri编码中不能对'/'编码
     * @param  string $path
     * @return string
     */
    public static function urlEncodeExceptSlash($path)
    {
        return str_replace("%2F", "/", self::urlEncode($path));
    }

    /**
     * 使用编码数组编码
     * @param  string $path
     * @return string
     */
    public static function urlEncode($value)
    {
        $result = '';
        for ($i = 0; $i < strlen($value); ++$i) {
            $result .= self::$PERCENT_ENCODED_STRINGS[ord($value[$i])];
        }
        return $result;
    }

    /**
     * 生成标准化QueryString
     * @param  array $parameters
     * @return array
     */
    public static function getCanonicalQueryString(array $parameters)
    {
        //没有参数，直接返回空串
        if (count($parameters) == 0) {
            return '';
        }

        $parameterStrings = array();
        foreach ($parameters as $k => $v) {
            //跳过Authorization字段
            if (strcasecmp('Authorization', $k) == 0) {
                continue;
            }
            if (!isset($k)) {
                throw new \InvalidArgumentException(
                    "parameter key should not be null"
                );
            }
            if (isset($v)) {
                //对于有值的，编码后放在=号两边
                $parameterStrings[] = self::urlEncode($k)
                    . '=' . self::urlEncode((string) $v);
            } else {
                //对于没有值的，只将key编码后放在=号的左边，右边留空
                $parameterStrings[] = self::urlEncode($k) . '=';
            }
        }
        //按照字典序排序
        sort($parameterStrings);

        //使用'&'符号连接它们
        return implode('&', $parameterStrings);
    }

    /**
     * 生成标准化uri
     * @param  string $path
     * @return string
     */
    public static function getCanonicalURIPath($path)
    {
        //空路径设置为'/'
        if (empty($path)) {
            return '/';
        } else {
            //所有的uri必须以'/'开头
            if ($path[0] == '/') {
                return self::urlEncodeExceptSlash($path);
            } else {
                return '/' . self::urlEncodeExceptSlash($path);
            }
        }
    }

    /**
     * 生成标准化http请求头串
     * @param  array $headers
     * @return array
     */
    public static function getCanonicalHeaders($headers)
    {
        //如果没有headers，则返回空串
        if (count($headers) == 0) {
            return '';
        }

        $headerStrings = array();
        foreach ($headers as $k => $v) {
            //跳过key为null的
            if ($k === null) {
                continue;
            }
            //如果value为null，则赋值为空串
            if ($v === null) {
                $v = '';
            }
            //trim后再encode，之后使用':'号连接起来
            $headerStrings[] = self::urlEncode(strtolower(trim($k))) . ':' . self::urlEncode(trim($v));
        }
        //字典序排序
        sort($headerStrings);

        //用'\n'把它们连接起来
        return implode("\n", $headerStrings);
    }
}

AipHttpUtil::__init();