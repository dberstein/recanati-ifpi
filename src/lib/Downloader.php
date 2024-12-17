<?php

declare (strict_types = 1);

namespace Daniel\Ifpi;

use DOMDocument;
use DOMXPath;

class Downloader
{
    /**
     * List of URLs to download.
     * @var array
     */
    protected array $urls;

    /**
     * Our cURL multihandle
     * @var resource
     */
    protected $multi_handle;

    /**
     * Handlers handled by our multi_handle
     * @var array
     */
    protected array $handles;

    /**
     * File pointers (handles) per cURL handle for downloaded page.
     * @var array
     */
    protected array $filePointers;

    /**
     * Random discriminator to avoid collisions.
     * @var int
     */
    protected int $rand;

    /**
     * Constructor
     *
     * @param string[] $url
     */
    public function __construct(string ...$url)
    {
        $this->urls = $url;
        $this->handles = [];
        $this->filePointers = [];
        $this->rand = mt_rand(1000, 9999);
    }

    /**
     * Converts $url into a temporary filename where downloaded data will be written.
     *
     * @param string $url
     * @return string
     */
    protected function filename(string $url): string
    {
        $page = null;
        if (preg_match('/&page=(\d+)/', $url, $matches)) {
            $page = $matches[1];
        }
        // See Downloader::getFilePage() for reverse operation.
        return sprintf("/tmp/%s-%d-ifpi.%d.html", md5($url), $this->rand, $page);
    }

    /**
     * Extracts page number from $filename.
     *
     * @param string $filename
     * @return int
     */
    protected function getFilePage(string $filename): int
    {
        // Downloader saves URL contents in filename of this regex format.
        // See Downloader::filename() for implementation.
        preg_match('/.*-ifpi\.(\d+)\.html/', $filename, $matches);
        return (int) $matches[1];
    }

    /**
     * Perform concurrent downloads.
     *
     * @return void
     */
    public function download(): void
    {
        $this->prepare();

        // Download the files...
        do {
            curl_multi_exec($this->multi_handle, $running);
        } while ($running > 0);

        $this->cleanup();
    }

    /**
     * Return associative array of results. Keys are the page number and values the downloaded data.
     * Temporary file is deleted after being read.
     *
     * @return array
     */
    public function contents(): array
    {
        $contents = [];
        foreach ($this->urls as $url) {
            $file = $this->filename($url);
            $page = $this->getFilePage($file);

            if (file_exists($file)) {
                $html = trim(file_get_contents($file));
                @unlink($file);
            } else {
                $html = '<html></html>';
            }

            if ($html) {
                $dom = new DOMDocument('1.0');
                @$dom->loadHTML($html);
                $contents[$page] = new DOMXPath($dom);
            }
        }

        return $contents;
    }

    /**
     * Prepare our multi_handle for concurrent downloads.
     *
     * @return void
     */
    protected function prepare(): void
    {
        $this->multi_handle = curl_multi_init();
        $this->handles = [];
        $this->filePointers = [];

        foreach ($this->urls as $key => $url) {
            $this->filePointers[$key] = fopen($this->filename($url), "w+");
            $this->handles[$key] = curl_init($url);
            curl_setopt($this->handles[$key], CURLOPT_FILE, $this->filePointers[$key]);
            curl_setopt($this->handles[$key], CURLOPT_HEADER, 0);
            curl_setopt($this->handles[$key], CURLOPT_CONNECTTIMEOUT, value: 5);
            curl_setopt($this->handles[$key], CURLOPT_TIMEOUT, 45);
            curl_setopt($this->handles[$key], CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
            curl_multi_add_handle($this->multi_handle, $this->handles[$key]);
        }
    }

    /**
     * Cleanup of resources used by multi_handler.
     * Note that Downloader::contents() deletes the temporary files.
     *
     * @return void
     */
    protected function cleanup(): void
    {
        foreach ($this->urls as $key => $url) {
            curl_multi_remove_handle($this->multi_handle, $this->handles[$key]);
            curl_close($this->handles[$key]);
            fclose($this->filePointers[$key]);
        }
        curl_multi_close($this->multi_handle);
    }
}
