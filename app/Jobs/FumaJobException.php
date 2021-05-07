<?php

namespace fuma\Jobs;

class FumaJobException extends Exception {
    public $status = "";
    /**
     * Create an exception based on a FumaErrorInfo object and optional message
     * If msg is not supplied the default string will be used as the Exception message
     */
    public function __construct(FumaErrorInfo $errInfo, $msg=null, Throwable $previous = null) {
        $this->status = $errInfo->getMsg();
        if (is_null($msg)) {
            $message = $this->status;
        } else {
            $message = $msg;
        }
        $code = $errInfo->getCode();
        
        // make sure everything is assigned properly
        parent::__construct($message, $code, $previous);
    }
 }