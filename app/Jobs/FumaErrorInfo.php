<?php

namespace fuma\Jobs;

interface FumaErrorInfo
{
	/**
	 * Return the error code
	 * 
	 * @return int
	 * 
	 */
    public function getStatus();
    
	/**
	 * Return the error string associated with the code
	 * 
	 * @return string
	 *
	 */
	public function getCode();

}