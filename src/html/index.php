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
$fetch = max(1, min(4, (!@$_REQUEST['fetch']) ? 1 : (int) $_REQUEST['fetch']));

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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

            const form = document.getElementById('single-form');
            const spinner = document.getElementById('spinner');
            form.addEventListener('submit', (e) => {
                form.classList.toggle('fade-out');
                spinner.classList.toggle('show');
            });

            const results = document.getElementById('results');
            if (results) { // scroll to #results if table exists
                location.hash = "#results";
            }
        });
    </script>
    <style>
        body {
            margin: 0;
            padding: 0;
        }

        table#results {
            border-collapse: collapse;
            border: 1px solid black;
            width: 100%;
            margin-top: 5em;
            margin-left: 0;
            margin-right: 0;
        }

        table#results thead th {
            border: 1px solid black;
        }

        table#results tbody td {
            border: 1px solid black;
        }

        #forms {
            width: 100%;
            margin-top: 2em;
        }

        #forms table {
            margin-left: auto;
            margin-right: auto;
        }

        #fileToUpload {
            margin-bottom: 2em;
        }

        #upload-btn {
            margin-top: 1em;
        }

        form {
            width: 100%;
            border: 1px solid black;
            padding: 1.5em;
            border-radius: 2em;
            /* box-shadow: rgba(0, 0, 0, 0.3) 0px 19px 38px, rgba(0, 0, 0, 0.22) 0px 15px 12px; */
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
                    <form id="multi-form" method="POST" enctype="multipart/form-data">
                        <fieldset>
                            <legend>Upload CSV file (artist,song,album)</legend>
                            <div>
                                <label for="fileToUpload" class="form-label">CSV file:</label>
                                <input type="hidden" name="MAX_FILE_SIZE" value="524288" />
                                <input type="file" name="fileToUpload" class="form-control" id="fileToUpload"
                                    accept=".csv" />
                                <label for="fetchUpload" class="form-label"><b>#</b>&lt;=:</label>
                                <select id="fetchUpload" name="fetch">
                                    <?php
                                    foreach (range(1, 4) as $p) {
                                        $s = ($p == $fetch) ? ' selected' : '';
                                        printf("<option value=%d%s>%d</option>", $p, $s, $p * 25);
                                    }
                                    ?>
                                </select>
                            </div>
                            <input type="submit" id="upload-btn" class="btn btn-primary" />
                        </fieldset>
                    </form>
                </td>
                <td>&nbsp;<b>or</b>&nbsp;</td>
                <td>
                    <form id="single-form">
                        <fieldset>
                            <legend>Search by artist/song/album</legend>
                            <table>
                                <tr>
                                    <td>
                                        <div class="form-floating mb-3">
                                            <input name="artist" id="artist" class="form-control"
                                                value="<?= @htmlentities($artist) ?>" />
                                            <label for="artist" class="form-label">Artist</label>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="form-floating mb-3">
                                            <input id="song" name="song" class="form-control"
                                                value="<?= @htmlentities($song) ?>" />
                                            <label for="song" class="form-label">Song</label>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="form-floating mb-3">
                                            <input name="album" id="album" class="form-control"
                                                value="<?= @htmlentities($album) ?>" />
                                            <label for="album" class="form-label">Album</label>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                            <div class="mb-3">
                                <label for="fetch" class="form-label"><b>#</b>&lt;=:</label>
                                <select id="fetch" name="fetch">
                                    <?php
                                    foreach (range(1, 4) as $p) {
                                        $s = ($p == $fetch) ? ' selected' : '';
                                        printf("<option value=%d%s>%d</option>", $p, $s, $p * 25);
                                    }
                                    ?>
                                </select>
                            </div>
                            <input type="submit" class="btn btn-primary" />
                        </fieldset>
                    </form>
                </td>
            </tr>
        </table>
    </div>
    <img src="/spinner.gif" id="spinner" class="hide center" />
    <?php
    $reload = false;
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
                foreach ($downloader->contents() as $file => $html) {
                    if (!trim($html)) {
                        continue;
                    }

                    $page = $downloader->getFilePage($file);
                    $xpath = $ifpi->getXpath($html);
                    $items = [true => [], false => []];
                    foreach ($xpath->query("//*[@id]") as $elem) {
                        if (!preg_match('/^c[0-9]+/', $elem->id)) {
                            continue;
                        }

                        $i++;
                        $lines = $ifpi->extractLines($elem);
                        $allowed = $ifpi->allowed($xpath, $i);
                        $items[$allowed][] = new Item(
                            $allowed,
                            trim($lines[1]),
                            trim($lines[2]),
                            trim($lines[4]),
                        );
                    }

                    foreach ([true, false] as $value) {
                        foreach ($items[$value] as $item) {
                            echo $item;
                        }
                    }

                    printf(
                        "<tr><td colspan=4 class='source'><a href='%s' target=_blank>↑ source ↑</a></td></tr>\n",
                        $ifpi->url($page),
                    );
                }

                if (!empty($artist . $song . $album) && $i == 0) {
                    $reload = true;
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

    if ($reload && mt_rand(1, 100) < 90) { // reload 90% of times
        ?>
        <script>
            var hash = window.location.hash;
            if (hash === '#results') {
                const spinner = document.getElementById('spinner');
                spinner.classList.toggle('show');
                spinner.scrollIntoView();
                setTimeout(function () {
                    window.location.reload(true);
                }, 1000 + Math.floor(Math.random() * 3000));
            }
        </script>
        <?php
    }
    ?>
</body>

</html>