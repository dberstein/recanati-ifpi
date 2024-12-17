<?php

declare (strict_types = 1);

namespace Daniel\Ifpi;

class Item
{
    /**
     * Constructor
     *
     * @param bool $allowed
     * @param string $artist
     * @param string $song
     * @param string $album
     */
    public function __construct(
        protected bool $allowed,
        protected string $artist,
        protected string $song,
        protected string $album,
    ) {}

    /**
     * Returns single string representation of this object.
     *
     * @return string
     */
    public function __tostring()
    {
        static $idx = 0;

        return sprintf(
            "<tr class='%s'><td style='text-align: center'><b>%d</b></td><td>%s</td><td>%s</td><td>%s</td></tr>\n",
            $this->allowed ? 'table-success' : 'table-danger',
            ++$idx,
            htmlentities($this->artist),
            htmlentities($this->song),
            htmlentities($this->album),
        );
    }
}
