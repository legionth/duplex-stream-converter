<?php

use React\Stream\ReadableStream;
use Evenement\EventEmitter;
use React\Stream\ReadableStreamInterface;
use React\Stream\WritableStreamInterface;
use Legionth\React\Converter\ReadableConverter;
use React\Stream\Util;

class ReadableConverterTest extends TestCase
{
    public function setUp()
    {
        $this->testStream = new TestStream();
        $this->convertedStream = new ReadableConverter($this->testStream);
        $this->input = new ReadableStream();

        $this->input->on('data', array($this->convertedStream, 'handleData'));
        $this->input->on('end', array($this->convertedStream, 'handleEnd'));
        $this->input->on('error', array($this->convertedStream, 'handleError'));
        $this->input->on('close', array($this->convertedStream, 'close'));
    }
    
    public function testMethodIsCalled()
    {
        $this->testStream->on('data', $this->expectCallableOnceWith('hello'));
        $this->input->emit('data', array('hello'));
    }
    
    public function testHandleClose()
    {
        $this->testStream->on('close', $this->expectCallableOnce());
    
        $this->testStream->close();
    
        $this->assertFalse($this->convertedStream->isReadable());
    }
    
    public function testHandleError()
    {
        $this->testStream->on('error', $this->expectCallableOnce());
        $this->testStream->on('close', $this->expectCallableOnce());
    
        $this->input->emit('error', array(new \RuntimeException()));
    
        $this->assertFalse($this->convertedStream->isReadable());
    }
    
    public function testPauseStream()
    {
        $stream = $this->getMock('React\Stream\ReadableStreamInterface');
        $stream->expects($this->once())->method('pause');
        
        $convertedStream = new ReadableConverter($stream);
        $convertedStream->pause();
    }
    
    public function testResumeStream()
    {
        $stream = $this->getMock('React\Stream\ReadableStreamInterface');
        $stream->expects($this->once())->method('pause');
        
        $convertedStream = new ReadableConverter($stream);
        $convertedStream->pause();
        $convertedStream->resume();
    }
    
    public function testPipeStream()
    {
        $dest = $this->getMock('React\Stream\WritableStreamInterface');
        $ret = $this->convertedStream->pipe($dest);
        
        $this->assertSame($dest, $ret);
    }
    
    public function testCloseStream()
    {
        $stream = new TestStream();
        $stream->on('close', $this->expectCallableOnce());
        
        $convertedStream = new ReadableConverter($stream);
        $convertedStream->close();
        
        $this->assertFalse($convertedStream->isReadable());
        
    }
    
    public function testEnd()
    {
        $stream = $this->getMock('React\Stream\ReadableStreamInterface');
        $this->input->on('end', $this->expectCallableOnce());
        
        $this->convertedStream->close();
        
        $this->input->emit('end');
    }

    private function expectCallableConsecutive($numberOfCalls, array $with)
    {
        $mock = $this->createCallableMock();
    
        for ($i = 0; $i < $numberOfCalls; $i++) {
            $mock
                ->expects($this->at($i))
                ->method('__invoke')
                ->with($this->equalTo($with[$i]));
        }
    
        return $mock;
    }
}

/**
 * This is just a test class to test if a duplex stream can be converted into a readablestream 
 */
class TestStream extends EventEmitter implements ReadableStreamInterface, WritableStreamInterface
{
    private $closed = false;
    
    public function write($chunk)
    {
        $this->emit('data', array($chunk));
    }
    
    
    public function end($data = null)
    {
        if (!$this->closed) {
            $this->emit('end');
            $this->close();
        }
    }
    
    public function isReadable()
    {
        return !$this->closed;
    }
    
    public function pause()
    {
        return;
    }
    
    public function resume()
    {
        return;
    }
    
    public function pipe(WritableStreamInterface $dest, array $options = array())
    {
        Util::pipe($this, $dest, $options);
    
        return $dest;
    }
    
    public function close()
    {
        if ($this->closed) {
            return;
        }
    
        $this->closed = true;
        $this->started = false;
    
        $this->emit('close');
        $this->removeAllListeners();
    }
    
    public function isWritable()
    {
        return !$this->closed;
    }
}
