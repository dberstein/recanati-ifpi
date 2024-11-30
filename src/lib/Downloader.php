<?php

namespace Daniel\Ifpi;

class Downloader
{
    protected array $urls;
    protected $handle;
    protected array $handles;
    protected array $filePointers;

    public function __construct(string ...$url)
    {
        $this->urls = $url;
        $this->handles = [];
        $this->filePointers = [];
    }

    public function add(string $url)
    {
        $this->urls[] = $url;
    }

    public function filename(string $url)
    {
        return sprintf("/tmp/%s-ifpi.html", md5($url));
    }

    public function prepare(bool $overwrite = false)
    {
        $this->handle = curl_multi_init();
        $this->handles = [];
        $this->filePointers = [];

        foreach ($this->urls as $key => $url) {
            $file = $this->filename($url);
            $this->filePointers[$key] = fopen($file, "w+");

            $this->handles[$key] = curl_init($url);
            curl_setopt($this->handles[$key], CURLOPT_FILE, $this->filePointers[$key]);
            curl_setopt($this->handles[$key], CURLOPT_HEADER, 0);
            curl_setopt($this->handles[$key], CURLOPT_CONNECTTIMEOUT, 45);
            curl_multi_add_handle($this->handle, $this->handles[$key]);
        }
    }

    public function do($overwrite = false)
    {
        $this->prepare($overwrite);

        // Download the files...
        do {
            curl_multi_exec($this->handle, $running);
        } while ($running > 0);

        $this->cleanup();
    }

    public function cleanup()
    {
        foreach ($this->urls as $key => $url) {
            curl_multi_remove_handle($this->handle, $this->handles[$key]);
            curl_close($this->handles[$key]);
            fclose($this->filePointers[$key]);
        }
        curl_multi_close($this->handle);

        foreach ($this->filePointers as $fh) {
            // fclose($fh);
        }
    }

    public function contents()
    {
        $contents = [];
        foreach ($this->urls as $key => $url) {
            $file = $this->filename($url);
            $contents[$file] = file_get_contents($file);
            unlink($file);
        }

        return $contents;
    }
}
