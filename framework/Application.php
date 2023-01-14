<?php
namespace Framework;

/**
 * abstract application
 *
 * @author Andrzej Salamon <andrzej.salamon@gmail.com>
 * @package framework
 *
 */
abstract class Application
{

    /**
     * @var
     * @static Application[]
     */
    protected static $instance;

    /**
     *
     * @var string
     * @access protected
     */
    protected $message;


    /**
     * implement custom setup code
     */
    protected function __construct()
    {
        //do some stuff here if u wish
    }


    /**
     * gets app message
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * sets app message
     */
    protected function setMessage($msg)
    {
        $this->message = $msg;
    }

    /**
     * gets app instance
     * @return Application
     */
    public static function get()
    {
        if (!isset(self::$instance)) {
            $className = get_called_class();
            return self::$instance = new $className();
        }
        return self::$instance;
    }

}