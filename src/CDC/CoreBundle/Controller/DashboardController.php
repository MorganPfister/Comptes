<?php

namespace CDC\CoreBundle\Controller;

use CDC\CoreBundle\Entity\BudgetInstance;
use CDC\CoreBundle\Entity\BudgetModele;
use CDC\CoreBundle\Entity\Categorie;
use CDC\CoreBundle\Entity\Transfert;
use CDC\CoreBundle\Repository\BudgetInstanceRepository;
use CDC\CoreBundle\Repository\BudgetModeleRepository;
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
        $current_month = $this->getMonthFromDatetime($current_date);
        $current_year = $this->getYearFromDatetime($current_date);

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
                ->setHeight(300)
                ->setWidth(500)
                ->setPieSliceText('label')
                ->getLegend()
                    ->setPosition('none');
        $pieChart
            ->getOptions()
                ->getChartArea()
                    ->setWidth('95%')
                    ->setHeight('95%');
        $pieChart->getData()->setArrayToDataTable($pie_chart_data);

        $return_array['pie_chart'] = $pieChart;

        // Récupérer les budgets
        /** @var BudgetModeleRepository $repository_budgetmodele */
        $repository_budgetmodele = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('CDCCoreBundle:BudgetModele');
        /** @var BudgetModele[] $budget_modele_a */
        $budget_modele_a = $repository_budgetmodele->findBudgetModeleUsingUser($user);

        /** @var BudgetInstanceRepository $repository_budgetinstance */
        $repository_budgetinstance = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('CDCCoreBundle:BudgetInstance');

        // Crée une instance de modele si inexistant pour le mois
        for ($i=0; $i < sizeof($budget_modele_a); $i++){
            $budget_instance = $repository_budgetinstance->findBudgetInstanceUsingBudgetModele($budget_modele_a[$i], $month, $year);
            if (sizeof($budget_instance) < 1){
                $new_budget_instance = new BudgetInstance();
                $new_budget_instance->setBudgetmodele($budget_modele_a[$i]);
                $new_budget_instance->setSeuil(floatval($budget_modele_a[$i]->getSeuil()));
                $new_budget_instance->setDatestart(\DateTime::createFromFormat('d/m/Y', '01/'.$month.'/'.$year));
                $number_of_day = $new_budget_instance->getDatestart()->format('t');
                $new_budget_instance->setDateend(\DateTime::createFromFormat('d/m/Y', $number_of_day.'/'.$month.'/'.$year));

                $em = $this->getDoctrine()->getManager();
                $em->persist($new_budget_instance);
                $em->flush();
            }
        }

        /** @var TransfertRepository $repository_transfert */
        $repository_transfert = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('CDCCoreBundle:Transfert');

        /** @var BudgetInstance[] $budget_instance_a */
        $budget_instance_a = $repository_budgetinstance->findBudgetInstanceUsingUserAndDate($user, $month, $year);
        for ($i=0; $i < sizeof($budget_instance_a); $i++){
            if ($budget_instance_a[$i]->getBudgetmodele()->getCategorie()) {
                $current_value = abs(floatval($repository_transfert->getSumUsingCategorieAndDate($budget_instance_a[$i]->getBudgetmodele()->getCategorie(), $month, $year)));
            }
            else {
                $current_value = abs(floatval($repository_transfert->getGlobalSumUsingUserAndDate($user, $month, $year)));
            }
            $budget_instance_a[$i]->setCurrentvalue($current_value);
        }

        $return_array['budget_instance_a'] = $budget_instance_a;

        return $this->render('CDCCoreBundle:Dashboard:overview.html.twig',
            $return_array
        );
    }

    public function resumeAction(){
        return $this->render('CDCCoreBundle:Dashboard:resume.html.twig');
    }

    public static function getMonthFromDatetime(\DateTime $datetime){
        return date_parse($datetime->format('d-m-Y'))['month'];
    }

    public static function getYearFromDatetime(\DateTime $datetime){
        return date_parse($datetime->format('d-m-Y'))['year'];
    }
}
