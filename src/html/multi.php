<!DOCTYPE html>
<html>

<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <title>IFPI 99fm: multi</title>
    <style>
        iframe {
            width: 100%;
            border: 1px solid black;
            overflow: scroll;
            background: transparent;
            border-radius: 10px;
            margin-bottom: 3em;
        }

        ul {
            margin: 0;
        }

        .container {
            background-image: url('/spinner.gif');
            background-position: center;
            background-repeat: no-repeat;
            padding: 0;
            margin: 0;
        }

        .reload-open {
            font-family: Lucida Sans Unicode
        }

        .title {
            border: 1px solid black;
            border-radius: 5px;
            padding: 0.5em;
            background-color: yellow;
        }

        .back {
            text-decoration: none;
            margin: 1em;
        }
    </style>
    <script>
        function reload(id) {
            document.getElementById(id).contentWindow.location.reload();
        }
    </script>
</head>

<body>
    <h1>
        <a href="/" class="btn btn-warning back">← back ←</a>
    </h1>
    <ol>
        <?php

        $file = @$_FILES['fileToUpload']['tmp_name'];
        if (!empty($file) && false !== ($fh = fopen($file, 'r'))) {
            $i = 0;
            while (($data = fgetcsv($fh, 1000, ",")) !== false) {
                $i++;

                $_artist = trim($data[0] ?? '');
                $_song = trim($data[1] ?? '');
                $_album = trim($data[2] ?? '');

                $url = sprintf(
                    '/?%s#results',
                    http_build_query([
                        'artist' => $_artist,
                        'song' => $_song,
                        'album' => $_album,
                        'fetch' => (!$_REQUEST['fetch']) ? 1 : (int) $_REQUEST['fetch'],
                    ]),
                );

                $title = implode(" - ", [
                    $_artist ?: '*',
                    $_song ?: '*',
                    $_album ?: '*',
                ]);

                $frame = sprintf('f%d', $i);
                ?>
                <li>
                    &nbsp;
                    <a onclick='reload("<?= $frame ?>")' class="btn btn-secondary reload-open">&#8634; (reload frame)</a>
                    &nbsp;
                    <a href="<?= $url ?>" target="_blank" class="btn btn-secondary reload-open">⧉ (open new tab)</a>
                    &nbsp;
                    <span class="font-monospace title"><?= htmlentities($title) ?></span>
                    <div class='container'>
                        <iframe id="<?= $frame ?>" src="<?= $url ?>"></iframe>
                    </div>
                </li>
                <?php
            }
            fclose($fh);
        }
        ?>
    </ol>
</body>

</html>