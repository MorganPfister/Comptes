<?php

namespace CDC\CoreBundle\Controller;

use CDC\CoreBundle\Entity\Categorie;
use CDC\CoreBundle\Entity\Transfert;
use CDC\CoreBundle\Repository\CategorieRepository;
use CDC\CoreBundle\Repository\TransfertRepository;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\PieChart;
use CMEN\GoogleChartsBundle\GoogleCharts\Options\PieChart\PieSlice;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DashboardController extends Controller {
    private function getMonthNames(){
        return ['Janvier', 'Février', 'Mars', 'Avril', 'Mai' , 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
    }

    public function overviewAction(Request $request) {
        $return_array = [];
        $user = $this->getUser();
        $month = $request->get('month');
        $year = $request->get('year');

        $current_date = new \DateTime();
        $current_month = date_parse($current_date->format('d-m-Y'))['month'];
        $current_year = date_parse($current_date->format('d-m-Y'))['year'];

        if (!$month || !$year){
            $month = $current_month;
            $year = $current_year;
        }
        else {
            $next_month = $month < 12 ? $month + 1 : 1;
            $next_year = $month < 12 ? $year : $year + 1;
            if ($next_month > $current_month || $next_year > $current_year){
            }
            else {
                $return_array['next_month'] = $next_month;
                $return_array['next_year'] = $next_year;
            }
        }

        $previous_month = $month > 1 ? $month - 1 : 12;
        $previous_year = $month > 1 ? $year : $year - 1;
        $return_array['previous_month'] = $previous_month;
        $return_array['previous_year'] = $previous_year;

        $return_array['month'] = $this->getMonthNames()[$month - 1];
        $return_array['year'] = $year;

        // Récupérer les dépenses par catégories (parente)
        /** @var TransfertRepository $repository_transfert */
        $repository_transfert = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('CDCCoreBundle:Transfert');

        /** @var CategorieRepository $repository_categorie */
        $repository_categorie = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('CDCCoreBundle:Categorie');
        /** @var Categorie[] $parent_categorie_a */
        $parent_categorie_a = $repository_categorie->getParentCategorie_a($user);

        $pieChart = new PieChart();
        $pieSlice_a = [];
        $pie_chart_data = [
            ['Categorie', 'Somme']
        ];
        for ($i=0; $i<sizeof($parent_categorie_a); $i++){
            $sum = 0;
            $children_categorie_a = $parent_categorie_a[$i]->getChildren();
            for($j = 0; $j < sizeof($children_categorie_a);$j++){
                $categorie = $children_categorie_a[$j];
                $sum_for_categorie = $repository_transfert->getSumUsingCategorieAndDate($categorie, $month, $year);
                if ($sum_for_categorie){
                    $sum += floatval($sum_for_categorie);
                }
            }
            $sum_for_categorie = $repository_transfert->getSumUsingCategorieAndDate($parent_categorie_a[$i], $month, $year);
            if ($sum_for_categorie){
                $sum += floatval($sum_for_categorie);
            }

            array_push($pie_chart_data, [$parent_categorie_a[$i]->getTitre(), abs(intval($sum))]);
            $pieSlice = new PieSlice();
            $pieSlice->setColor($parent_categorie_a[$i]->getColor());
            array_push($pieSlice_a, $pieSlice);
        }
        $pieChart
            ->getOptions()
                ->setBackgroundColor('#E6EAF3')
                ->setSlices($pieSlice_a)
                ->setPieHole(0.3)
                ->setHeight(400)
                ->setWidth(700)
                ->setPieSliceText('label')
                ->getLegend()
                    ->setPosition('none');
        $pieChart
            ->getOptions()
                ->getChartArea()
                    ->setWidth('80%')
                    ->setHeight('80%');

        $pieChart->getData()->setArrayToDataTable($pie_chart_data);

        $return_array['pie_chart'] = $pieChart;

        // Récupérer les budgets

        return $this->render('CDCCoreBundle:Dashboard:overview.html.twig',
            $return_array
        );
    }

    public function resumeAction(){
        return $this->render('CDCCoreBundle:Dashboard:resume.html.twig');
    }
}
