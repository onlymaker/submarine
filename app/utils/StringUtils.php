<?php

namespace utils;

class StringUtils
{
    static function camelToSnake($camel)
    {
        $basename = lcfirst(preg_replace('/^.+[\\\\\\/]/', '', $camel));
        preg_match_all('/[A-Z]/', $basename, $matches);
        if ($matches) {
            foreach ($matches[0] as $match) {
                $basename = str_replace($match, '_' . chr(ord($match) + ord('a') - ord('A')), $basename);
            }
        }
        return $basename;
    }

    static function snakeToCamel($snake)
    {
        $parts = explode('_', $snake);
        foreach ($parts as &$part) {
            ucfirst($part);
        }
        return lcfirst(implode('', $parts));
    }
}