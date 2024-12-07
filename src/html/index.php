<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Daniel\Ifpi\Ifpi;
use Daniel\Ifpi\Item;
use Daniel\Ifpi\Downloader;

// file submission signals multi request
if (count($_FILES)) {
    require_once 'multi.php';
    die();
}

$artist = (string) @$_REQUEST['artist'];
$song = (string) @$_REQUEST['song'];
$album = (string) @$_REQUEST['album'];
$fetch = (!@$_REQUEST['fetch']) ? 1 : (int) $_REQUEST['fetch'];

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=0.7">
    <meta name="description" content="Music search engine.">
    <title>IFPI 99fm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const fileInput = document.getElementById('fileToUpload');
            const submitButton = document.getElementById('upload-btn');
            submitButton.disabled = true;
            fileInput.addEventListener('change', () => {
                submitButton.disabled = fileInput.files.length === 0;
            });

            const form = document.getElementById('multi-form');
            const spinner = document.getElementById('spinner');
            form.addEventListener('submit', (e) => {
                form.classList.toggle('fade-out');
                spinner.classList.toggle('show');
            });

            // scroll to #results if table exists
            const results = document.getElementById('results');
            if (results) {
                location.hash = "#results";
            }
        });
    </script>
    <style>
        table#results {
            border-collapse: collapse;
            border: 1px solid black;
            width: 100%;
            margin-top: 5em;
            margin-left: 0%;
            margin-right: 0%;
        }

        table#results thead th {
            border: 1px solid black;
        }

        table#results tbody td {
            border: 1px solid black;
        }

        #forms {
            width: 100%;
            margin: 2em;
        }

        #forms table {
            margin-left: auto;
            margin-right: auto;
        }

        #upload-btn {
            margin-top: 1em;
        }

        form {
            width: 100%;
            border: 1px solid black;
            padding: 1.5em;
            border-radius: 2em;
        }

        legend {
            font-size: 1.5em;
        }

        #multi select {
            margin-top: 1em;
        }

        .source {
            text-align: center;
        }

        .center {
            display: block;
            margin-left: auto;
            margin-right: auto;
        }

        .hide {
            display: none;
        }

        .show {
            display: block;
        }

        .btn {
            margin: 1em;
        }
    </style>
</head>

<body>
    <div id="forms">
        <table>
            <tr>
                <td>
                    <form id="multi" method="POST" enctype="multipart/form-data">
                        <fieldset>
                            <legend>Upload CSV file (artist,song,album)</legend>
                            <div>
                                <label for="fileToUpload" class="form-label">CSV file:</label>
                                <input type="hidden" name="MAX_FILE_SIZE" value="524288" />
                                <input type="file" name="fileToUpload" class="form-control" id="fileToUpload"
                                    accept=".csv" />
                                <label for="fetchUpload" class="form-label">#<=:</label>
                                <select id="fetchUpload" name="fetch">
                                    <?php
                                    foreach ([1, 2, 3, 4] as $p) {
                                        $s = ($p == $fetch) ? 'selected' : '';
                                        printf("<option value=%d %s>%d</option>\n", $p, $s, $p * 25);
                                    }
                                    ?>
                                </select>
                            </div>
                            <input type="submit" id="upload-btn" class="btn btn-primary" />
                        </fieldset>
                    </form>
                </td>
                <td>
                    <p style="font-weight: bolder;width: 2em;text-align: center;">or</p>
                </td>
                <td>
                    <form id="multi-form">
                        <fieldset>
                            <legend>Search by artist/song/album</legend>
                            <table>
                                <tr>
                                    <td>
                                        <label for="artist" class="form-label">Artist:</label>
                                    </td>
                                    <td>
                                        <input name="artist" id="artist" class="form-control" style="width: 100%;"
                                            value="<?= @htmlentities($artist) ?>" /><br />
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label for="song" class="form-label">Song:</label>
                                    </td>
                                    <td>
                                        <input size=50 id="song" name="song" class="form-control" style="width: 100%;"
                                            value="<?= @htmlentities($song) ?>" /><br />
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label for="album" class="form-label">Album:</label>
                                    </td>
                                    <td>
                                        <input name="album" id="album" class="form-control" style="width: 100%ף"
                                            value="<?= @htmlentities($album) ?>" /><br />
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label for="fetch" class="form-label">#<=:</label>
                                    </td>
                                    <td>
                                        <select id="fetch" name="fetch">
                                            <?php
                                            foreach ([1, 2, 3, 4] as $p) {
                                                $s = ($p == $fetch) ? 'selected' : '';
                                                printf("<option value=%d %s>%d</option>\n", $p, $s, $p * 25);
                                            }
                                            ?>
                                        </select>
                                    </td>
                                </tr>
                            </table>
                            <input type="submit" class="btn btn-primary" />
                        </fieldset>
                    </form>
                </td>
            </tr>
        </table>
    </div>
    <img src="/spinner.gif" id="spinner" class="hide center" />
    <?php
    if (!empty($artist . $song . $album)) {
        ?>
        <a name="results" />
        <table id="results" class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th style="width: 33%;">Artist</th>
                    <th style="width: 33%;">Song</th>
                    <th style="width: 33%;">Album</th>
                </tr>
            </thead>
            <tbody>
                <?php

                $ifpi = new Ifpi($artist, $song, $album);

                $urls = [];
                for ($i = 0; $i < $fetch; $i++) {
                    $urls[] = $ifpi->url($i + 1);
                }

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
                    $page = $downloader->getFilePage($file);

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
                            "<tr><td colspan=4 class='source'><a href='%s' target=_blank>↑ source ↑</a></td></tr>\n",
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
            </tbody>
        </table>
        <?php
    }
    ?>
</body>

</html>