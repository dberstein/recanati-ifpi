<?php

declare(strict_types=1);

namespace Daniel\Ifpi;

class Item
{
    protected $allowed;
    protected $artist;
    protected $song;
    protected $album;

    public function __construct($allowed, $artist, $song, $album)
    {
        $this->allowed = $allowed;
        $this->artist = $artist;
        $this->song = $song;
        $this->album = $album;
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
