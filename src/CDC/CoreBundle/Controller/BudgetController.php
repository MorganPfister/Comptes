<?php

namespace CDC\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BudgetController extends Controller {
    public function overviewAction(){
        return $this->render('CDCCoreBundle:Budget:overview.html.twig');
    }
}