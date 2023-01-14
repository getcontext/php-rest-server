<?php
namespace Application;

use Framework;
use Framework\Application;
use Framework\Rest;
use Framework\Actions;
use Framework\Request;

require_once(dirname(__FILE__) . '/../framework/Application.php');
require_once(dirname(__FILE__) . '/../framework/Rest.php');
require_once(dirname(__FILE__) . '/../framework/Actions.php');
require_once(dirname(__FILE__) . '/../framework/Request.php');
require_once(dirname(__FILE__) . '/../vendor/salamon/google/Request.php');


/**
 *
 * @author Andrzej Salamon <andrzej.salamon@gmail.com>
 * @package framework
 */
class ImageReverseSearchApplication extends Application implements Actions
{
    const DOWNLOAD_URL = 'https://www.salamon.com';
    const SHUTDOWN_FILE = 'SHUTDOWN';

    /**
     * gets app instance
     * @return  ImageReverseSearchApplication
     */
    public static function get()
    {
        return parent::get();
    }

    public function execute(Request $request)
    {
        $rest = new Rest();
        $restReq = $rest->processRequest();

        if ($restReq->getMethod() != 'get') {
            $rest->sendResponse(501);
        }

        $output = array();
        $output['result'] = 'error';

        switch ($request->getAction()) {
            case 'search':
                $output = $this->reverseSearchAction($request);
                break;
            default:
                $rest->sendResponse(501);
        }

        $rest->sendResponse(200, json_encode($output), 'application/json');
    }

    /**
     * @param Request $request
     * @return array
     */
    private function reverseSearchAction(Request $request)
    {
        $output = array();
        $output['result'] = 'error';
        $imageName = $request->getParam('image');

        if (empty($imageName)) {
            $output['message'] = 'empty image name';
            return $output;
        }

        $imageUrl = self::DOWNLOAD_URL . '/' . $imageName;

        file_put_contents($imageName, file_get_contents($imageUrl));

        $localImageUrl = $this->getLocalhostAddress() . '/' . $imageName;

        $req = new \Salamon\Google\ImageReverseSearch\Request($localImageUrl);

        if (!$req->process()) {
            if ($req->isCaptcha()) {
                unlink($request->getParam('image'));
                file_put_contents(self::SHUTDOWN_FILE, "1");
                return $output;
            }
        }

        unlink($imageName);

        $rs = $req->getResults();
//        $rs[]=array('a'=>'b','x'=>'y');

        $output['result'] = 'success';
        $output['data'] = json_encode($rs);

        return $output;
    }

    /**
     * @return mixed
     */
    private function getLocalhostAddress()
    {
        $localhost = $_SERVER['SERVER_ADDR'];
        return $localhost;
    }
}
