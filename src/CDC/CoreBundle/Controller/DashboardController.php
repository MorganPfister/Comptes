<?php

namespace CDC\CoreBundle\Controller;

use CDC\CoreBundle\Entity\Compte;
use CDC\CoreBundle\Repository\CompteRepository;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\ColumnChart;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DashboardController extends Controller {
    public function overviewAction() {
        $user = $this->getUser();

        /** @var CompteRepository $repository_compte */
        $repository_compte = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('CDCCoreBundle:Compte');
        $compte_a = $repository_compte->findBy([
            'user' => $user
        ]);

        $col_array = [
            [ 'Compte', 'Solde', ['role' => 'style'], ['role' => 'annotation'] ]
        ];
        for ($i=0; $i<sizeof($compte_a);$i++){
            /** @var Compte $compte */
            $compte = $compte_a[$i];
            if ($compte->getSolde() >= 0){
                $color = 'stroke-color: #2D54B0; stroke-width: 2; fill-color: #A3CCFA';
            }
            else {
                $color = 'stroke-color: #E4070D; stroke-width: 2; fill-color: #FC9496';
            }
            array_push($col_array, [
                $compte->getNom()." \n(".$compte->getTitulaire().")",
                intval($compte->getSolde()),
                $color,
                $compte->getSolde()
            ]);
        }

        $col = new ColumnChart();
        $col->getData()->setArrayToDataTable($col_array);

        $col->getOptions()
            ->getAnnotations()
                ->setAlwaysOutside(true)
                ->getTextStyle()
                    ->setFontSize(14)
                    ->setColor('#000')
                    ->setAuraColor('none');
        $col->getOptions()
            ->getVAxis()
                ->setTitle('Solde (€)');
        $col->getOptions()
            ->setHeight(300);
        $col->getOptions()
            ->getLegend()
                ->setPosition('none');

        return $this->render('CDCCoreBundle:Dashboard:overview.html.twig', [
            'colchart' => $col
        ]);
    }
}
