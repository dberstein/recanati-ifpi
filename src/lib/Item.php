<?php

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
        if (PHP_SAPI == 'cli') {
            return sprintf(
                "[%s] %s\t%s\t%s\n",
                $this->allowed ? 'T' : 'F',
                $this->artist,
                $this->song,
                $this->album
            );
        } else {
            $green = '#9de59d';
            $red = '#d77d7d';
            return sprintf(
                "<tr class='%s'><td>%s</td><td>%s</td><td>%s</td></tr>\n",
                ($this->allowed ? 'g' : 'r'),
                htmlentities($this->artist),
                htmlentities($this->song),
                htmlentities($this->album),
            );
        }
    }
}
