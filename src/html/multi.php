<html>
    <body>
<?php

if (false !== ($fh = fopen($_FILES['fileToUpload']['tmp_name'], 'r'))) {
    while (($data = fgetcsv($fh, 1000, ",")) !== false) {
        $artist = $data[1];
        $song = $data[2];
        $album = $data[0];

        $url = '/?' . http_build_query([
            'artist' => $data[0],
            'song' => $data[1],
            'album' => '',
        ]) . '#results';

        printf("<b><a href='%s' target=_blank >%s</a></b>\n", $url, $data[0] . ' ' . $data[1]);
        printf("<iframe src='%s' width='100%%'></iframe>\n", $url);
    }
}

?>
    </body>
</html>
