<?php
namespace DuxCms\Controller;
use Home\Controller\SiteController;
/**
 * TAG列表
 */

class ValidateCodeController extends SiteController {

    public function index(){
        session_start();
        $_vc = new \framework\ext\ValidateCode();
        $_SESSION['VC'] = $_vc->createCode();
        $_vc->doimg();  
    }

}

