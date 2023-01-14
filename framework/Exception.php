<?php
namespace Framework;
/**
 * basic implementation of exception interface
 *
 * @author Andrzej Salamon <andrzej.salamon@gmail.com>
 * @package project_name
 *
 */
class AppException extends Exception
{
    /**
     * constructor
     */
    public function __construct()
    {
//        if (!$this->getMessage()) {
//            throw new Exception('Empty exception message');
//        }
    }


}