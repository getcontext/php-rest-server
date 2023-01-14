<?php
require_once('application/ImageReverseSearchApplication.php');

use Framework\Request;
use Application\ImageReverseSearchApplication;

$request = new Request();
//print_r($request);
if ($request->getController() == 'imagereversesearch') {

    $app = ImageReverseSearchApplication::get();
    $app->execute($request);

}


