<?php

namespace fuma\Jobs;

class FumaJobException extends \Exception {
    public $status = "";
    public $statusCode = null;
    /**
     * Create an exception based on a FumaErrorInfo object and optional message
     * If msg is not supplied the default string will be used as the Exception message
     */
    public function __construct(FumaErrorInfo $errInfo, $msg=null, Throwable $previous = null) {
        $this->status = $errInfo->getStatus();
        if (is_null($msg)) {
            $message = $this->status;
        } else {
            $message = $msg;
        }
        $this->statusCode = $errInfo->getCode();
        
        // make sure everything is assigned properly
        parent::__construct($message, $this->statusCode, $previous);
    }
    
    public function getStatusCode() {
        return $this->statusCode;
    }
 }