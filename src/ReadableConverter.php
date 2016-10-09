<?php

namespace Legionth\React\Converter;

use Evenement\EventEmitter;
use React\Stream\ReadableStreamInterface;
use React\Stream\WritableStreamInterface;

/**
 * This class is a converted just-readable stream of your stream
 * Use this wrap-up if you need a readable stream
 */
class ReadableConverter extends EventEmitter implements ReadableStreamInterface
{

    private $stream;
    
    /**
     * @param ReadableStreamInterface $stream - Stream which should only be readable
     */
    public function __construct(ReadableStreamInterface $stream)
    {
        $this->stream = $stream;
    }
    
    /** @internal */
    public function handleData($data)
    {
        $this->stream->emit('data', array($data));
    }
    
    /** @internal */
    public function handleEnd()
    {
        if (!$this->stream->isReadable()) {
            $this->stream->emit('end');
            $this->stream->close();
        }
    }
    
    /** @internal */
    public function handleError(\Exception $e)
    {
        $this->stream->emit('error', array($e));
        $this->stream->close();
    }

    
    public function isReadable()
    {
        return $this->stream->isReadable();
    }
    
    public function pause()
    {
        $this->stream->pause();
    }
    
    public function resume()
    {
        $this->stream->resume();
    }
    
    public function pipe(WritableStreamInterface $dest, array $options = array())
    {
        return $this->stream->pipe($dest, $options);
    }
    
    public function close()
    {
        $this->stream->close();
    }
}
