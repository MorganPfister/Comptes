<?php

namespace CDC\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DashboardController extends Controller {
    public function overviewAction(Request $request) {
        $date = $request->get('date');
        if (!$date){
            $date = new \DateTime();
        }
        $date_a = date_parse($date->format('d-m-Y'));
        $month = $date_a['month'];
        $year = $date_a['year'];

        return $this->render('CDCCoreBundle:Dashboard:overview.html.twig', [
            
        ]);
    }
}
