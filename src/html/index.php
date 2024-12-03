<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Daniel\Ifpi\Ifpi;
use Daniel\Ifpi\Item;
use Daniel\Ifpi\Downloader;

$artist = (string) @$_REQUEST['artist'];
$song = (string) @$_REQUEST['song'];
$album = (string) @$_REQUEST['album'];

?>
<html>

<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <style>
        table#results {
            border-collapse: collapse;
            border: 1px solid black;
            width: 90%;
            margin-left: 5%;
        }

        table#results th {
            border: 1px solid black;
        }

        table#results td {
            border: 1px solid black;
        }

        #forms {
            width: 90%;
            margin-left: 5%;
        }

        /* form {
            width: 100%;
        }

        input:not(button) {
            width: 100%;
        } */

        legend {
            font-size: 20px;
        }

        .g {
            background-color: #9de59d;
        }

        .r {
            background-color: #d77d7d;
        }

        .source {
            text-align: center;
        }
    </style>
</head>

<body>
    <div id="forms">
    <form action="multi.php" method="POST" enctype="multipart/form-data">
        <fieldset>
            <legend>Upload CSV (artist,song,album)</legend>
            <div>
                <input type="hidden" name="MAX_FILE_SIZE" value="524288" />
                <input type="file" name="fileToUpload" id="fileToUpload" accept=".csv" />
            </div>
            <input type="submit" value="Upload" />
        </fieldset>
    </form>
    <p>... or ...</p>
    <form>
        <fieldset>
            <legend>Search artist/song/album</legend>
            <table>
                <tr>
                    <td>
                        <label for="artist">Artist</label>
                    </td>
                    <td>
                        <input name="artist" style="width: 500px;" value="<?= @htmlentities($artist) ?>" /><br />
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="song">Song</label>
                    </td>
                    <td>
                        <input name="song" style="width: 500px;" value="<?= @htmlentities($song) ?>" /><br />
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="album">Album</label>
                    </td>
                    <td>
                        <input name="album" style="width: 500px;" value="<?= @htmlentities($album) ?>" /><br />
                    </td>
                </tr>
            </table>
            <input type="submit" />
        </fieldset>
    </form>
    </div>
<?php
if (!empty($artist . $song . $album)) {
?>
    <a name="results" />
    <table id="results">
        <tr>
            <th>#</th>
            <th style="width 33%">Artist</th>
            <th style="width 33%">Song</th>
            <th style="width 33%">Album</th>
        </tr>
        <?php

        $ifpi = new Ifpi($artist, $song, $album);

        $urls = [
            $ifpi->url(1),
            $ifpi->url(2),
            $ifpi->url(3),
            $ifpi->url(4),
        ];

        $downloader = new Downloader(...$urls);
        if (!empty($artist . $song . $album)) {
            $downloader->do();
        }

        $i = 0;
        $page = 0;
        foreach ($downloader->contents() as $file => $html) {
            if (!trim($html)) {
                continue;
            }
            $dom = new DOMDocument('1.0');
            @$dom->loadHTML($html);
            $xpath = new DOMXPath($dom);

            preg_match('/.*-ifpi\.(\d+)\.html/', $file, $matches);
            $page = $matches[1];

            $n = 0;

            foreach ($xpath->query("//*[@id]") as $elem) {
                if (!preg_match('/^c[0-9]+/', $elem->id)) {
                    continue;
                }

                $i++;
                $n++;

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

            if ($n > 0) {
                printf(
                    "<tr><td colspan=4 class='source'><a href='%s'>↑ source ↑</a></td></tr>\n",
                    $ifpi->url($page),
                );
            }
        }

        if (!empty($artist . $song . $album) && $i == 0) {
            printf(
                "<tr><td colspan=4 class='source'>%s</td></tr>\n",
                "No results found",
            );
        }
        ?>
    </table>
<?php
}
?>
</body>

</html>