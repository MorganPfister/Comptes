<?php

namespace CDC\CoreBundle\Controller;

use CDC\CoreBundle\Entity\BudgetInstance;
use CDC\CoreBundle\Entity\BudgetModele;
use CDC\CoreBundle\Entity\Categorie;
use CDC\CoreBundle\Entity\Compte;
use CDC\CoreBundle\Entity\Transfert;
use CDC\CoreBundle\Repository\BudgetInstanceRepository;
use CDC\CoreBundle\Repository\BudgetModeleRepository;
use CDC\CoreBundle\Repository\CategorieRepository;
use CDC\CoreBundle\Repository\CompteRepository;
use CDC\CoreBundle\Repository\TransfertRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
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
        $return_array['month_number'] = $month;
        $return_array['year'] = $year;

        // Récupérer les dépenses par catégories (parente) ; par compte et cumulé
        /** @var CompteRepository $repository_compte */
        $repository_compte = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('CDCCoreBundle:Compte');
        /** @var Compte[] $compte_a */
        $compte_a = $repository_compte->findBy(['user' => $user]);
        $compte_id_a = '-1';
        $compte_nom_a = 'Cumulé';
        for ($i=0; $i<sizeof($compte_a); $i++){
            $compte_id_a .= ','.$compte_a[$i]->getId();
            $compte_nom_a .= ','.$compte_a[$i]->getNom().' ('.$compte_a[$i]->getTitulaire().')';
        }
        $return_array['compte_id_a'] = $compte_id_a;
        $return_array['compte_nom_a'] = $compte_nom_a;

        // Récupérer les budgets
        $return_array['budget_instance_a'] = $this->getBudget($month, $year);

        return $this->render('CDCCoreBundle:Dashboard:overview.html.twig',
            $return_array
        );
    }

    public function resumeAction(){
        $user = $this->getUser();
        $return_array = [];

        $current_date = new \DateTime();
        $month = $this->getMonthFromDatetime($current_date);
        $year = $this->getYearFromDatetime($current_date);
        $return_array['previous_month'] = $month;
        $return_array['previous_year'] = $year;

        /** @var CompteRepository $repository_compte */
        $repository_compte = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('CDCCoreBundle:Compte');
        /** @var Compte[] $compte_a */
        $compte_a = $repository_compte->findBy(['user' => $user]);
        $compte_id_a = '-1';
        $compte_nom_a = 'Cumulé';
        for ($i=0; $i<sizeof($compte_a); $i++){
            $compte_id_a .= ','.$compte_a[$i]->getId();
            $compte_nom_a .= ','.$compte_a[$i]->getNom().' ('.$compte_a[$i]->getTitulaire().')';
        }
        $return_array['compte_id_a'] = $compte_id_a;
        $return_array['compte_nom_a'] = $compte_nom_a;

        return $this->render('CDCCoreBundle:Dashboard:resume.html.twig', $return_array);
    }

    public function getBalanceChartAction(Request $request){
        $response = [
            'success' => false
        ];

        if ($request->isMethod('POST')) {
            $months_number = intval($request->get('months_number'));
            $compte_id = intval($request->get('compte_id'));
            /** @var CompteRepository $repository_compte */
            $repository_compte = $this
                ->getDoctrine()
                ->getManager()
                ->getRepository('CDCCoreBundle:Compte');
            $compte = $compte_id > 0 ? $repository_compte->find($compte_id) : null;

            $combo_chart_data = $this->getSoldeAndBalanceOverMonth($months_number, $compte);
            array_unshift($combo_chart_data, ['Mois', 'Revenus', 'Dépenses', 'Solde']);

            $response = [
                'success' => true,
                'combo_chart_data' => $combo_chart_data
            ];
        }

        $response = new JSONresponse($response);
        return $response;
    }

    private function getSoldeAndBalanceOverMonth($months_number, Compte $compte = null){
        $user = $this->getUser();
        $combo_chart_data = [];

        /** @var CompteRepository $repository_compte */
        $repository_compte = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('CDCCoreBundle:Compte');

        /** @var TransfertRepository $repository_transfert */
        $repository_transfert = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('CDCCoreBundle:Transfert');

        if (!$compte){
            $current_solde = 0;
            /** @var Compte[] $compte_a */
            $compte_a = $repository_compte->findBy([
                'user' => $user
            ]);
            for ($i=0; $i<sizeof($compte_a); $i++){
                $current_solde += floatval($compte_a[$i]->getSolde());
            }
        }
        else {
            $current_solde = floatval($compte->getSolde());
        }
        $solde = $current_solde;

        $month = self::getMonthFromDatetime(new \DateTime());
        $year = self::getYearFromDatetime(new \DateTime());
        for ($i=0; $i<$months_number; $i++){
            $income = floatval($repository_transfert->getIncomeUsingDateAndCompte($user, $month, $year, $compte));
            $depense = floatval($repository_transfert->getDepenseUsingDateAndCompte($user, $month, $year, $compte));
            $balance = floatval($income + $depense);
            array_unshift($combo_chart_data, [$month.'/'.$year, $income, abs($depense), $solde]);

            $solde -= $balance;
            $month = $month > 1 ? $month - 1 : 12;
            $year = $month > 1 ? $year : $year - 1;
        }

        return $combo_chart_data;
    }

    public static function getMonthFromDatetime(\DateTime $datetime){
        return date_parse($datetime->format('d-m-Y'))['month'];
    }

    public static function getYearFromDatetime(\DateTime $datetime){
        return date_parse($datetime->format('d-m-Y'))['year'];
    }

    public function getDepenseByCategorieChart($month = null, $year = null, Compte $compte=null){
        $user = $this->getUser();

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

        $pie_chart_data['columns'] = [
            'Catégorie' => 'string',
            'Somme' => 'number'
        ];

        $sum_by_categorie_a = $repository_transfert->getSumDepenseByCategorie($user, $month, $year, $compte);
        $sum_by_categorie_parent_a = [];

        for($i=0; $i<sizeof($sum_by_categorie_a); $i++){
            /** @var Categorie $categorie */
            $categorie = $repository_categorie->find($sum_by_categorie_a[$i]['id']);
            if (!$categorie->getParent()){
                if (array_key_exists($categorie->getId(), $sum_by_categorie_parent_a)){
                    $sum_by_categorie_parent_a[$categorie->getId()]['sum'] += abs(floatval($sum_by_categorie_a[$i][1]));
                }
                else {
                    $sum_by_categorie_parent_a[$categorie->getId()]['sum'] = abs(floatval($sum_by_categorie_a[$i][1]));
                }
            }
            else {
                if (array_key_exists($categorie->getParent()->getId(), $sum_by_categorie_parent_a)){
                    $sum_by_categorie_parent_a[$categorie->getParent()->getId()]['sum'] += abs(floatval($sum_by_categorie_a[$i][1]));
                }
                else {
                    $sum_by_categorie_parent_a[$categorie->getParent()->getId()]['sum'] = abs(floatval($sum_by_categorie_a[$i][1]));
                }

                if (array_key_exists('tooltip', $sum_by_categorie_parent_a[$categorie->getParent()->getId()])){
                    $sum_by_categorie_parent_a[$categorie->getParent()->getId()]['tooltip'] .= '<tr><td><i class="'.$categorie->getIcon().'" style="color:'.$categorie->getColor().'"></i></td><td>'.$categorie->getTitre().'</td><td style="text-align: right;"><b>'.abs($sum_by_categorie_a[$i][1]).'€</b></td></tr>';
                }
                else {
                    $sum_by_categorie_parent_a[$categorie->getParent()->getId()]['tooltip'] = '<tr><td><i class="'.$categorie->getIcon().'" style="color:'.$categorie->getColor().'"></i></td><td>'.$categorie->getTitre().'</td><td style="text-align: right;"><b>'.abs($sum_by_categorie_a[$i][1]).'€</b></td></tr>';
                }
            }
        }

        $pie_chart_data['slices_color'] = [];
        foreach ($sum_by_categorie_parent_a as $categorie_id => $info){
            $categorie = $repository_categorie->find($categorie_id);
            if(!array_key_exists('tooltip', $info)){
                $info['tooltip'] = '<tr><td><i class="'.$categorie->getIcon().'" style="color:'.$categorie->getColor().'"></i></td><td>'.$categorie->getTitre().'</td><td style="text-align: right;"><b>'.$info['sum'].'€</b></td></tr>';
            }
            else{
                $info['tooltip'] = '<tr style="border-bottom: 2px solid #c7d1dd;"><td><i class="'.$categorie->getIcon().'" style="color:'.$categorie->getColor().'"></i></td><td>'.$categorie->getTitre().'</td><td style="text-align: right;"><b>'.$info['sum'].'€</b></td></tr>'.$info['tooltip'];
            }
            $pie_chart_data['rows'][$categorie->getTitre()] = [
                'sum' => $info['sum'],
                'color' => $categorie->getColor(),
                'tooltip' => '<div style="padding: 5px;"><table>'.$info['tooltip'].'</table></div>'
            ];
            array_push($pie_chart_data['slices_color'], $categorie->getColor());
            next($sum_by_categorie_parent_a);
        }

        return $pie_chart_data;
    }

    public function getBudget($month, $year){
        $user = $this->getUser();

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

        return $budget_instance_a;
    }

    public function retrieveDepenseForCompteAction(Request $request){
        $response = [
            'success' => false
        ];

        if ($request->isMethod('POST')){
            $repository_compte = $this
                ->getDoctrine()
                ->getManager()
                ->getRepository('CDCCoreBundle:Compte');

            $compte_id = $request->get('id');
            $month = intval($request->get('month')) == -1 ? null : intval($request->get('month'));
            $year = intval($request->get('year')) == -1 ? null : intval($request->get('year'));
            $compte = $compte_id == -1 ? null : $repository_compte->find($compte_id);

            $pie_chart_data = $this->getDepenseByCategorieChart($month, $year, $compte);

            $response = [
                'success' => true,
                'pie_chart_data' => $pie_chart_data
            ];
        }

        $response = new JSONresponse($response);
        return $response;
    }
}
