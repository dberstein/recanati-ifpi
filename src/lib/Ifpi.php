<?php

namespace Daniel\Ifpi;

use DOMDocument;

class Ifpi
{
    const URL = "https://ifpi.co.il/Search.asp";
    protected array $params = [];

    public function __construct($artist, $song, $album)
    {
        $this->params = [
            'freeSearch' => '',
            'artist' => $artist,
            'song' => $song,
            'album' => $album,
        ];
    }

    public function url($page = null)
    {
        //next: /html/body/div[2]/div/div/div[4]/div[58]/div[2]/img
        return sprintf(
            '%s?%s',
            self::URL,
            http_build_query(array_merge($this->params, ['page' => $page]))
        );
    }

    public function fetch($page = null)
    {
        printf("[<a href='%s'>link</a>]\n", $this->url($page));

        $html = file_get_contents($this->url($page));
        $dom = new DOMDocument('1.0');
        @$dom->loadHTML($html);
        return $dom;
    }

    public function allowed($xpath, $idx)
    {
        return count($xpath->query(
            sprintf('//div[@id="d%d"]/div[1]/div[4]/img', $idx)
        )) && count($xpath->query(
            sprintf('//div[@id="d%d"]/div[1]/div[7]/img', $idx)
        ));
    }
}
