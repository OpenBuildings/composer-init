<?php

namespace CL\ComposerInit;

class Inflector
{
    /**
     * Makes an underscored or dashed phrase human-readable.
     *
     *     $str = Inflector::humanize('kittens-are-cats'); // "kittens are cats"
     *     $str = Inflector::humanize('dogs_as_well');     // "dogs as well"
     *
     * @param  string $str phrase to make human-readable
     * @return string
     */
    public static function humanize($str)
    {
        return preg_replace('/[_-]+/', ' ', trim($str));
    }

    /**
     * @param  string $str
     * @return string
     */
    public static function title($str)
    {
        return ucwords(self::humanize($str));
    }

    /**
     * @param  string $str
     * @return string
     */
    public static function titlecase($str)
    {
        return str_replace(' ', '', self::title($str));
    }

    /**
     * @param  string $str
     * @return string
     */
    public static function initials($str)
    {
        $title = self::title($str);

        $initials = str_replace(' ', '', preg_replace('/[a-z]/', '', $title));

        if (strlen($initials) == 1) {
            $initials .= strtoupper($title[1]);
        }

        return $initials;
    }
}
