<?php

declare(strict_types=1);

namespace Daniel\Ifpi;

class Item
{
    public function __construct(public $allowed, public $artist, public $song, public $album)
    {
    }

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
