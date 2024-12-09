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

        ul {
            margin: 0;
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
        <a href="/" class="btn btn-warning" style="text-decoration: none; margin: 1em;">← back ←</a>
    </h1>
    <ol>
        <?php

        $file = $_FILES['fileToUpload']['tmp_name'];
        if (!empty($file) && false !== ($fh = fopen($file, 'r'))) {
            $i = 0;
            while (($data = fgetcsv($fh, 1000, ",")) !== false) {
                $i++;
                $url = sprintf(
                    '/?%s#results',
                    http_build_query([
                        'artist' => $data[0] ?? '',
                        'song' => $data[1] ?? '',
                        'album' => $data[2] ?? '',
                        'fetch' => (!$_REQUEST['fetch']) ? 1 : (int) $_REQUEST['fetch'],
                    ]),
                );

                $title = implode(" - ", [
                    empty($data[0]) ? '*' : $data[0],
                    empty($data[1]) ? '*' : $data[1],
                    empty($data[2]) ? '*' : $data[2],
                ]);

                $reload = sprintf("<a onclick='reload(\"f%d\")' class='btn btn-secondary reload-open'>&#8634; (reload frame)</a>", $i);
                $open = sprintf("<a href='%s' target=_blank class='btn btn-secondary reload-open'>⧉ (open new tab)<a>", $url);
                printf(
                    "<li>&nbsp;%s&nbsp;%s&nbsp;<span class='font-monospace'>%s</span></li>",
                    $reload,
                    $open,
                    htmlentities($title)
                );

                printf(
                    "<div class='container'><iframe id='f%d' src='%s'></iframe></div>",
                    $i,
                    $url
                );
            }
        }

        ?>
    </ol>
</body>

</html>