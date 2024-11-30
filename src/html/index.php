<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Daniel\Ifpi\Ifpi;
use Daniel\Ifpi\Item;
use Daniel\Ifpi\Downloader;


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
            width: 90%;
            margin-left: 5%;
        }
        td {
            border: 1px solid black;
        }
        form {
            width: 90%;
            margin-left: 5%;
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
                <th style="width 33%">Artist</th>
                <th style="width 33%">Song</th>
                <th style="width 33%">Album</th>
            </tr>
<?php
            $ifpi = new Ifpi($artist, $song, $album);

            $downloader = new Downloader($ifpi->url(1), $ifpi->url(2), $ifpi->url(3), $ifpi->url(4));
            $downloader->do();
            foreach ($downloader->contents() as $file => $html) {
                $dom = new DOMDocument('1.0');
                @$dom->loadHTML($html);
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
            }
?>

        </table>
<?php
    }
?>
    </body>

</html>