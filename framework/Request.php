<?php
/**
 *
 * @author Andrzej Salamon <andrzej.salamon@gmail.com>
 * @package framework
 */
namespace Framework;


final class Request
{

    private $controller;
    private $action;
    private $params = array();

    public function __construct()
    {
        $uri = $_SERVER['PATH_INFO'];
        $parts = explode("/", $uri);
        array_shift($parts);
        $this->setController($parts[0]);
        $this->setAction($parts[1]);
        $limit = count($parts);
        for ($i = 2; $i < $limit - 1; $i++) {
            $this->setParam($parts[$i], $parts[$i + 1]);
        }
    }

    /**
     * @param mixed $action
     */
    public function setAction($action)
    {
        $this->action = $action;
    }

    /**
     * @return mixed
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param mixed $controller
     */
    public function setController($controller)
    {
        $this->controller = $controller;
    }

    /**
     * @return mixed
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * @param mixed $param
     */
    public function setParam($param, $val)
    {
        $this->params[$param] = $val;
    }

    /**
     * @return mixed
     */
    public function getParam($name)
    {
        return isset($this->params[$name]) ? $this->params[$name] : null;
    }
} 