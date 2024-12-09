<?php

namespace Daniel\Ifpi;

class Downloader
{
    protected array $urls;
    protected $multi_handle;
    protected array $handles;
    protected array $filePointers;
    protected int $rand;

    public function __construct(string ...$url)
    {
        $this->urls = $url;
        $this->handles = [];
        $this->filePointers = [];
        $this->rand = mt_rand(1000, 9999);
    }

    public function filename(string $url): string
    {
        $page=null;
        if (preg_match('/&page=(\d+)/', $url, $matches)) {
            $page = $matches[1];
        }
        // See Downloader::getFilePage() for reverse operation.
        return sprintf("/tmp/%s-%d-ifpi.%d.html", md5($url), $this->rand, $page);
    }

    public function getFilePage(string $file): int
    {
        // Downloader saves URL contents in filename of this regex format.
        // See Downloader::filename() for implementation.
        preg_match('/.*-ifpi\.(\d+)\.html/', $file, $matches);
        return (int) $matches[1];
    }

    public function do(): void
    {
        $this->prepare();

        // Download the files...
        do {
            curl_multi_exec($this->multi_handle, $running);
        } while ($running > 0);

        $this->cleanup();
    }

    public function contents(): array
    {
        $contents = [];
        foreach ($this->urls as $key => $url) {
            $file = $this->filename($url);
            $contents[$file] = file_exists($file) ? file_get_contents($file) : '<html></html>';
            @unlink($file);
        }

        return $contents;
    }

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
