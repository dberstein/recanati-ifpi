<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Daniel\Ifpi\Ifpi;

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


if (PHP_SAPI == 'cli') {
    $artist = ($argc > 1) ? $argv[1] : '';
    $song = ($argc > 2) ? $argv[2] : '';
    $album = ($argc > 3) ? $argv[3] : '';

    $ifpi = new Ifpi($artist, $song, $album);

    $dom = $ifpi->fetch(1);
    $xpath = new DOMXPath($dom);

    $i = 0;
    foreach ($xpath->query("//*[@id]") as $elem) {
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
?>

<html>
    <head>

    </head>
    <body>
        <form>
            <label for="artist">Artist</label>
            <input name="artist" />
            <label for="song">Song</label>
            <input name="song" />
            <label for="album">Album</label>
            <input name="album" />
            
            <input type="submit" />
        </form>
    </body>
</html>