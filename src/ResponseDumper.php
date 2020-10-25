<?php
namespace Nyrados\Http\Utils;

use Psr\Http\Message\ResponseInterface;

/**
 * Class for dumping PSR-7 response
 */

class ResponseDumper
{
    
    /** @var ResponseInterface */
    private $response;
    
    private $end;
    private $start = 0;
    private $sentHeaders = false;
    private $streamed = false;
    
    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;

        if ($this->response->hasHeader('Content-Range')) {
            preg_match('/(\d+)-(\d+)\/(\d+)/', $this->response->getHeaderLine('Content-Range'), $data);
            $this->start = $data[1];
            $this->end = $data[2];
        } else {
            $this->end = $this->response->getBody()->getSize() - 1;
        }
    }
    
    /**
     * Writes given headers & response code
     *
     * @return void
     */
    public function dumpHeaders(): void
    {
        if (!$this->sentHeaders) {
            foreach ($this->response->getHeaders() as $name => $values) {
                foreach ($values as $value) {
                    header($name . ': ' . $value, count($values) > 1);
                }
            }

            header('HTTP/' . $this->response->getProtocolVersion() . ' ' . $this->response->getStatusCode() . ' ' . $this->response->getReasonPhrase());

            $this->sentHeaders = true;
        }
    }
    
    /**
     * Dumps headers & writes body
     *
     * @return void
     */
    public function dump(): void
    {
        $this->dumpHeaders();
        $this->dumpBody();
    }
    
    /**
     * Dumps response body and outputs it directly
     *
     * @return void
     */
    public function dumpBody(): void
    {
        if ($this->streamed) {
            return;
        }

        $stream = $this->response->getBody();

        //Dump Stream
        $buffer = 1024 * 8;
        $stream->seek($this->start);
        set_time_limit(0);
        while (!$stream->eof() && ($p = $stream->tell()) <= $this->end) {
            if ($p + $buffer > $this->end) {
                $buffer = $this->end - $p + 1;
            }

            echo $stream->read($buffer);
            flush();
        }
    }
}
