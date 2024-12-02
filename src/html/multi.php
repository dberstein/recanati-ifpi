<html>

<body>
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
                "<b><a href='%s' target=_blank >%s - %s (%s)</a></b>\n",
                $url,
                htmlentities($data[0] ?? ''),
                htmlentities($data[1] ?? ''),
                htmlentities($data[2] ?? ''),
            );

            printf(
                "<iframe src='%s' width='100%%'></iframe>\n",
                $url
            );
        }
    }

    ?>
</body>

</html>