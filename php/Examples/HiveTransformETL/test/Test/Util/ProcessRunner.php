<?php
namespace Examples\HiveTransformETL\Test\Util;

class ProcessRunner {
    const STDIN     = 0;
    const STDOUT    = 1;
    const STDERR    = 2;
    
    protected $executable;
    protected $spec;
    protected $process;
    
    protected $pipes = array();
    protected $open = false;
    
    public function __construct($executable, $spec) {
        $this->setExecutable($executable)
            ->setSpec($spec);
    }
    
    public function open() {
        $this->open = is_resource($process = proc_open($this->getExecutable(), $this->getSpec(), $this->pipes, null, null));
        
        if($this->open) {
            $this->setProcess($process);
            stream_set_blocking($this->pipes[static::STDIN], 0);
        } else {
            throw new Exception("Unable to execute " . $this->getExecutable(), 0);
        }
    }
    
    public function write($stream, $input) {
        if(!$this->open) {
            throw new Exception("Connection to executable has not yet been opened", 0);
        }
        
        fwrite($this->pipes[$stream], $input, strlen($input));
    }
    
    public function read($stream) {
        if(!$this->open) {
            throw new Exception("Connection to executable has not yet been opened", 0);
        }
        
        return stream_get_line($this->pipes[$stream], 10240);
    }
    
    public function close() {
        if($this->open) {
            foreach($this->pipes as $stream => $mode) {
                $this->closeStream($stream);
            }
            
            proc_close($this->getProcess());
        }
    }
    
    public function closeStream($stream) {
        if($this->isValidStream($stream) && is_resource($this->pipes[$stream])) {
            fclose($this->pipes[$stream]);
        }
    }
    
    public function isRunning() {
        if($status = proc_get_status($this->getProcess())) {
            $status = $status['running'];
        }
        
        return $status;
    }
    
    
    
    protected function isValidStream($stream) {
        return in_array($stream, array(static::STDIN, static::STDOUT, static::STDERR));
    }
    
    /**
     * @return resource
     */
    protected function getProcess() {
        return $this->process;
    }
    
    /**
     * 
     * @param resource $process
     * @return \Examples\HiveTransformETL\Test\ProcessRunner
     */
    protected function setProcess($process) {
        $this->process = $process;
        return $this;
    }
    
    protected function getExecutable() {
        return $this->executable;
    }
    
    protected function setExecutable($executable) {
        $this->executable = $executable;
        return $this;
    }
    
    protected function getSpec() {
        return $this->spec;
    }
    
    protected function setSpec(array $spec) {
        $this->spec = $spec;
        return $this;
    }
}