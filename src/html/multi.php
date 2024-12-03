<html>

<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <style>
        iframe {
            border: 1px solid black;
            margin-left: 5%;
            width: 90%;
        }
    </style>
</head>

<body>
    <ul>
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
                ]),
            );

            printf(
                "<li><b><a href='%s' target=_blank >%s - %s (%s)</a></b></li>\n",
                $url,
                htmlentities($data[0] ?? ''),
                htmlentities($data[1] ?? ''),
                htmlentities($data[2] ?? ''),
            );

            printf(
                "<iframe src='%s' width='100%%' scrolling=yes></iframe><br />\n",
                $url
            );
        }
    }

    ?>
    </ul>
</body>

</html>