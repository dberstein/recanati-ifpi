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
        }

        .container {
            background-image: url('/spinner.gif');
            background-position: center;
            background-repeat: no-repeat;
            padding: 0;
            margin: 0;
        }

        ul {
            margin: 0;
        }
    </style>
</head>

<body>
    <h1>
        <a href="/" class="btn btn-warning" style="text-decoration: none; margin: 1em;">← back ←</a>
    </h1>
    <ol>
        <?php

        $file = $_FILES['fileToUpload']['tmp_name'];
        if (!empty($file) && false !== ($fh = fopen($file, 'r'))) {
            while (($data = fgetcsv($fh, 1000, ",")) !== false) {
                $url = sprintf(
                    '/?%s#results',
                    http_build_query([
                        'artist' => $data[0] ?? '',
                        'song' => $data[1] ?? '',
                        'album' => $data[2] ?? '',
                        'fetch' => (!$_REQUEST['fetch']) ? 1 : (int) $_REQUEST['fetch'],
                    ]),
                );

                printf(
                    "<li>&nbsp;<b><a class='btn btn-secondary' href='%s' target=_blank style='text-decoration: underline;'>%s - %s (%s)</a></b></li>\n",
                    $url,
                    htmlentities($data[0] ?? ''),
                    htmlentities($data[1] ?? ''),
                    htmlentities($data[2] ?? ''),
                );

                printf(
                    "<div class='container'><iframe src='%s'></iframe></div><br />\n",
                    $url
                );
            }
        }

        ?>
    </ol>
</body>

</html>