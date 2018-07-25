<?php

namespace CDC\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DashboardController extends Controller {
    public function indexAction() {
        return $this->render('CDCCoreBundle:Dashboard:overview.html.twig');
    }
}
