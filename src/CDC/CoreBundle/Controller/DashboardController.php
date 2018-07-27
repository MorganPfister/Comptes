<?php

namespace CDC\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DashboardController extends Controller {
    public function overviewAction() {
        return $this->render('CDCCoreBundle:Dashboard:overview.html.twig');
    }
}
