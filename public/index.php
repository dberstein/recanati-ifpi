<?php

class Item {
    protected $artist;
    protected $song;
    protected $album;

    public function __construct($artist, $song, $album) {
        $this->artist = $artist;
        $this->song = $song;
        $this->album = $album;
    }

    // public function __tostring() {
    //     return sprintf("%d)\t[%s] %s\t%s\t%s\n", $i, $allowed ? 'T' : 'F', $artist, $song, $album);
    // }
}

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
        return sprintf(
            '%s?%s',
            self::URL,
            http_build_query(array_merge($this->params, ['page' => $page]))
        );
    }

    public function fetch($page = null)
    {
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

if (PHP_SAPI == 'cli') {
    $artist = ($argc > 1) ? $argv[1] : '';
    $song = ($argc > 2) ? $argv[2] : '';
    $album = ($argc > 3) ? $argv[3] : '';

    $ifpi = new Ifpi($artist, $song, $album);

    $dom = $ifpi->fetch(1);
    $xpath = new DOMXPath($dom);
    $elements = $xpath->query("//*[@id]");
    $i = 0;
    foreach ($elements as $elem) {
        if (!preg_match('/^c[0-9]+/', $elem->id)) {
            continue;
        }

        $i++;
        $lines = array_filter(array_map(function ($s) {
            return trim($s);
        }, explode("\n", $elem->textContent)));

        $_artist = trim($lines[1]);
        $_song = trim($lines[2]);
        $_album = trim($lines[4]);

        $allowed = $ifpi->allowed($xpath, $i);
        printf("%d)\t[%s] %s\t%s\t%s\n", $i, $allowed ? 'T' : 'F', $_artist, $_song, $_album);
    }
}
