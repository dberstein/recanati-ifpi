<?php

declare (strict_types = 1);

namespace Daniel\Ifpi;

use DOMXPath;
use Generator;

class Ifpi
{
    const SERVICE_URL = "https://ifpi.co.il/Search.asp";
    protected array $params = [];

    /**
     * Constructor
     *
     * @param string $artist
     * @param string $song
     * @param string $album
     */
    public function __construct(
        string $artist,
        string $song,
        string $album
    ) {
        $this->params = [
            'freeSearch' => '',
            'artist' => $artist,
            'song' => $song,
            'album' => $album,
        ];
    }

    /**
     * Returns URL for results page $page.
     *
     * @param mixed $page
     * @return string
     */
    public function url(int $page)
    {
        return sprintf(
            '%s?%s',
            self::SERVICE_URL,
            http_build_query(array_merge($this->params, ['page' => $page]))
        );
    }

    /**
     * Returns wether item is allowed to play by IFPI.
     *
     * @param \DOMXPath $xpath
     * @param int $idx
     * @return bool
     */
    public function allowed(DOMXPath $xpath, int $idx)
    {
        return count($xpath->query(
            sprintf('//div[@id="d%d"]/div[1]/div[4]/img', $idx)
        )) && count($xpath->query(
            sprintf('//div[@id="d%d"]/div[1]/div[7]/img', $idx)
        ));
    }

    /**
     * Generator that yields on items whose id is "c" followed by a number.
     *
     * @param \DOMXPath $xpath
     * @return \Generator
     */
    public function divWalker(DOMXPath $xpath): Generator
    {
        foreach ($xpath->query("//*[@id]") as $elem) {
            if (preg_match('/^c[0-9]+/', $elem->id)) {
                yield $elem;
            }
        }
    }
}
