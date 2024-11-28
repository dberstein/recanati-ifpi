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
            $green = '#9de59d';
            $red = '#d77d7d';
            return sprintf(
                "<tr style='%s'><td>%s</td><td>%s</td><td>%s</td></tr>\n",
                'background-color: ' . ($this->allowed ? $green : $red),
                htmlentities($this->artist),
                htmlentities($this->song),
                htmlentities($this->album),
            );
        }
    }
}

$artist = (string) @$_GET['artist'];
$song = (string) @$_GET['song'];
$album = (string) @$_GET['album'];

?>
<html>

<head>
    <style>
        table.results {
            border-collapse: collapse;
            border: 1px solid black;
            width: 80%;
            margin-left: 10%;
        }
        td {
            border: 1px solid black;
        }
    </style>
</head>

<body>
    <form>
        <fieldset>
            <legend>Search artist/song/album</legend>
            <table>
                <tr>
                    <td>
                        <label for="artist">Artist</label>
                    </td>
                    <td>
                        <input name="artist" style="width: 500px;" value="<?= @htmlentities($artist) ?>" /><br/>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="song">Song</label>
                    </td>
                    <td>
                        <input name="song" style="width: 500px;" value="<?= @htmlentities($song) ?>" /><br/>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="album">Album</label>
                    </td>
                    <td>
                        <input name="album" style="width: 500px;" value="<?= @htmlentities($album) ?>" /><br/>
                    </td>
                </tr>
            </table>
            <input type="submit" />
        </fieldset>
    </form>

<?php
    if (!empty($artist . $song . $album)) {
?>
        <table class="results">
            <tr>
                <th>Artist</th>
                <th>Song</th>
                <th>Album</th>
            </tr>
<?php
            $ifpi = new Ifpi($artist, $song, $album);
            $page = 0;
            while (true) {
                $page++;

                $dom = $ifpi->fetch($page);
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

                    echo new Item(
                        $ifpi->allowed($xpath, $i),
                        trim($lines[1]),
                        trim($lines[2]),
                        trim($lines[4]),
                    );
                }

                if ($page > 4) {
                // if (0 == count($xpath->query('/html/body/div[2]/div/div/div[4]/div[58]/div[2]/img'))) {
                    break;
                }
            }

?>

        </table>
<?php
    }
?>
    </body>

</html>