<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Daniel\Ifpi\Ifpi;

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
            return sprintf(
                '<tr style="%s"><td>%s</td><td>%s</td><td>%s</td></tr>',
                'background-color: ' . ($this->allowed ? '#9de59d' : '#d77d7d'),
                htmlentities($this->artist),
                htmlentities($this->song),
                htmlentities($this->album),
            );
        }
    }
}

$artist = @$_GET['artist'];
$song = @$_GET['song'];
$album = @$_GET['album'];

$ifpi = new Ifpi($artist, $song, $album);

$dom = $ifpi->fetch(1);
$xpath = new DOMXPath($dom);

?>
<html>

    <head>

    </head>

    <body>
        <form>
            <label for="artist">Artist</label>
            <input name="artist" value="<?= htmlentities($artist) ?>" />
            <label for="song">Song</label>
            <input name="song" value="<?= htmlentities($song) ?>" />
            <label for="album">Album</label>
            <input name="album" value="<?= htmlentities($album) ?>" />

            <input type="submit" />
        </form>
        <table>
            <tr>
                <th>Artist</th>
                <th>Song</th>
                <th>Album</th>
            </tr>
<?php

$i = 0;
foreach ($xpath->query("//*[@id]") as $elem) {
    if (!preg_match('/^c[0-9]+/', $elem->id)) {
        continue;
    }

    $i++;
    $lines = array_filter(array_map(function ($s) {
        return trim($s);
    }, explode("\n", $elem->textContent)));

    echo new Item(
        $ifpi->allowed($xpath, $i),
        trim($lines[1]),
        trim($lines[2]),
        trim($lines[4]),
    );
}
?>

        </table>
    </body>

</html>
