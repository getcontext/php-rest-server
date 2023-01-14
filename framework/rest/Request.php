<?php
namespace Framework\Rest;

use Framework;
/**
 * Description of RestRequest
 *
 * @author Andrzej Salamon <andrzej.salamon@gmail.com>
 * @package framework
 */
class Request
{
    private $requestVars;
    private $data;
    private $httpAccept;
    private $method;

    /**
     * constructor
     */
    public function __construct()
    {
        $this->setRequestVars(array());
        $this->setData('');
        $this->setHttpAccept((strpos($_SERVER['HTTP_ACCEPT'], 'json')) ? 'json' : 'xml');
        $this->setMethod('get');
    }

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @param string $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * @param string $requestVars
     */
    public function setRequestVars($requestVars)
    {
        $this->requestVars = $requestVars;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @return string
     */
    public function getHttpAccept()
    {
        return $this->httpAccept;
    }

    /**
     * @param string $accept
     */
    public function setHttpAccept($accept)
    {
        $this->httpAccept = $accept;
    }

    /**
     * @return mixed
     */
    public function getRequestVars()
    {
        return $this->requestVars;
    }
}