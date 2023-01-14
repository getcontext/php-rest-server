<?php
namespace Framework;

require_once('Restable.php');
require_once('rest/Request.php');
require_once('helper/Rest.php');

use Framework\Rest\Request;
use Framework\Helper\Rest as RestHelper;

/**
 * Description of RestServer
 *
 * @author Andrzej Salamon <andrzej.salamon@gmail.com>
 */
class Rest implements Restable
{
    /**
     * prepares RestRequest object containing request data
     */
    public function processRequest()
    {
        $requestMethod = strtolower($_SERVER['REQUEST_METHOD']);
        $returnObj = new Request();
        $data = array();

        switch ($requestMethod) {
            case 'get':
                $data = $_GET;
                break;
            case 'post':
                $data = $_POST;
                break;
            case 'put':
                parse_str(file_get_contents('php://input'), $putVars);
                $data = $putVars;
                break;
        }

        $returnObj->setMethod($requestMethod);
        $returnObj->setRequestVars($data);

        if (isset($data['data'])) {
            $returnObj->setData(json_decode($data['data']));
        }
        return $returnObj;
    }

    /**
     * sends RESTfull response to client
     *
     * @param int $status
     * @param string $body
     * @param string $contentType
     */
    public function sendResponse($status = 200, $body = '', $contentType = 'text/html')
    {
        $statusHeader = 'HTTP/1.1 ' . $status . ' ' . RestHelper::getStatusCodeMessage($status);
        header($statusHeader);
        header('Content-type: ' . $contentType);

        if ($body != '') {
            echo $body;
            exit;
        } else {
            $message = '';
            // servers don't always have a signature turned on (this is an apache directive "ServerSignature On")
            $signature = ($_SERVER['SERVER_SIGNATURE'] == '') ? $_SERVER['SERVER_SOFTWARE'] . ' Server at ' . $_SERVER['SERVER_NAME'] . ' Port ' . $_SERVER['SERVER_PORT'] : $_SERVER['SERVER_SIGNATURE'];

            // this should be templatized
            $body = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
              <html>
                  <head>
                      <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
                      <title>' . $status . ' ' . RestHelper::getStatusCodeMessage($status) . '</title>
                  </head>
                  <body>
                      <h1>' . RestHelper::getStatusCodeMessage($status) . '</h1>
                      <p>' . $message . '</p>
                      <hr />
                      <address>' . $signature . '</address>
                  </body>
              </html>';

            echo $body;
            exit;
        }
    }
}