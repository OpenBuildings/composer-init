<?php

namespace CL\ComposerInit;

class Inflector
{
    /**
     * Makes an underscored or dashed phrase human-readable.
     *
     *     $string = $inflector->humanize('kittens-are-cats'); // "kittens are cats"
     *     $string = $inflector->humanize('dogs_as_well');     // "dogs as well"
     *
     * @param  string $string phrase to make human-readable
     * @return string
     */
    public function humanize($string)
    {
        return preg_replace('/[_-]+/', ' ', trim($string));
    }

    /**
     * @param  string $string
     * @return string
     */
    public function title($string)
    {
        return ucwords($this->humanize($string));
    }

    /**
     * @param  string $string
     * @return string
     */
    public function titlecase($string)
    {
        return str_replace(' ', '', $this->title($string));
    }

    /**
     * @param  string $string
     * @return string
     */
    public function initials($string)
    {
        $title = $this->title($string);

        $initials = str_replace(' ', '', preg_replace('/[a-z]/', '', $title));

        if (mb_strlen($initials) == 1) {
            $initials .= mb_strtoupper($title[1]);
        }

        return $initials;
    }
}
