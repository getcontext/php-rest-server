<?php
namespace Framework;
/**
 *
 * @author Andrzej Salamon <andrzej.salamon@gmail.com>
 * @package framework
 */
interface Restable {
  public function processRequest();
  public function sendResponse($status = 200, $body = '', $contentType = 'text/html');  
}