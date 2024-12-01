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

    public function add(string $url)
    {
        $this->urls[] = $url;
    }

    public function filename(string $url)
    {
        return sprintf("/tmp/%s-%d-ifpi.html", md5($url), $this->rand);
    }

    public function prepare()
    {
        $this->multi_handle = curl_multi_init();
        $this->handles = [];
        $this->filePointers = [];

        foreach ($this->urls as $key => $url) {
            $file = $this->filename($url);
            $this->filePointers[$key] = fopen($file, "w+");

            $this->handles[$key] = curl_init($url);
            curl_setopt($this->handles[$key], CURLOPT_FILE, $this->filePointers[$key]);
            curl_setopt($this->handles[$key], CURLOPT_HEADER, 0);
            curl_setopt($this->handles[$key], CURLOPT_CONNECTTIMEOUT, 45);
            curl_setopt($this->handles[$key], CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
            curl_multi_add_handle($this->multi_handle, $this->handles[$key]);
        }
    }

    public function do()
    {
        $this->prepare();

        // Download the files...
        do {
            curl_multi_exec($this->multi_handle, $running);
        } while ($running > 0);

        $this->cleanup();
    }

    public function cleanup()
    {
        foreach ($this->urls as $key => $url) {
            curl_multi_remove_handle($this->multi_handle, $this->handles[$key]);
            curl_close($this->handles[$key]);
            fclose($this->filePointers[$key]);
        }
        curl_multi_close($this->multi_handle);
    }

    public function contents()
    {
        $contents = [];
        foreach ($this->urls as $key => $url) {
            $file = $this->filename($url);
            $contents[$file] = file_exists($file) ? file_get_contents($file) : '<html></html>';
            @unlink($file);
        }

        return $contents;
    }
}