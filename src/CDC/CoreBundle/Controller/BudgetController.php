<?php

namespace CDC\CoreBundle\Controller;

use CDC\CoreBundle\Entity\BudgetInstance;
use CDC\CoreBundle\Entity\BudgetModele;
use CDC\CoreBundle\Entity\Categorie;
use CDC\CoreBundle\Repository\BudgetInstanceRepository;
use CDC\CoreBundle\Repository\BudgetModeleRepository;
use CDC\CoreBundle\Repository\CategorieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class BudgetController extends Controller {
    public function overviewAction(){
        $user = $this->getUser();
        $return_array = [];

        /** @var BudgetModeleRepository $repository_budgetmodele */
        $repository_budgetmodele = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('CDCCoreBundle:BudgetModele');

        /** @var CategorieRepository $repository_categorie */
        $repository_categorie = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('CDCCoreBundle:Categorie');
        /** @var Categorie[] $categorie_a */
        $categorie_a = $repository_categorie->getParentCategorie_a($user);

        // Récupération des budgets par catégorie
        for ($i=0; $i < sizeof($categorie_a); $i++){
            $budgetmodele = $repository_budgetmodele->findBudgetModeleUsingUserAndCategorie($user, $categorie_a[$i]);
            if (sizeof($budgetmodele) == 1) {
                $categorie_a[$i]->setBudgetmodele($budgetmodele[0]);
            }
            $categorie_children_a = $categorie_a[$i]->getChildren();
            for ($j = 0; $j < sizeof($categorie_children_a); $j++){
                $budgetmodele = $repository_budgetmodele->findBudgetModeleUsingUserAndCategorie($user, $categorie_children_a[$j]);
                if (sizeof($budgetmodele) == 1) {
                    $categorie_children_a[$j]->setBudgetmodele($budgetmodele[0]);
                }
            }
        }
        $return_array['categorie_a'] = $categorie_a;

        // Récupération du budget global
        $budget_global = $repository_budgetmodele->findOneBy([
            'user' => $user,
            'actif' => true
        ]);
        if ($budget_global){
            $return_array['budget_global'] = $budget_global;
        }

        return $this->render('CDCCoreBundle:Budget:overview.html.twig', $return_array);
    }

    public function addBudgetModeleAction(Request $request){
        $user = $this->getUser();
        $response = [
            'success' => false
        ];

        if ($request->isMethod('POST')){
            $categorie_id = $request->get('id');

            /** @var CategorieRepository $repository_categorie */
            $repository_categorie = $this
                ->getDoctrine()
                ->getManager()
                ->getRepository('CDCCoreBundle:Categorie');
            /** @var Categorie $categorie */
            $categorie = $repository_categorie->find($categorie_id);

            /** @var BudgetModeleRepository $repository_budgetmodele */
            $repository_budgetmodele = $this
                ->getDoctrine()
                ->getManager()
                ->getRepository('CDCCoreBundle:BudgetModele');

            if($categorie){
                // Check si modele deja existant
                $budget_modele = $repository_budgetmodele->findBudgetModeleUsingUserAndCategorie($user, $categorie);
                if (sizeof($budget_modele) >= 1){
                    $response = [
                        'success' => false,
                        'error' => 'Un budget existe déjà pour cette catégorie'
                    ];
                }

                else {
                    $seuil = $request->get('_seuil');
                    $budgetmodele_valid = $this->checkBudgetModeleValidity($categorie, $seuil);

                    // le budget peut etre ajouté
                    if ($budgetmodele_valid){
                        $budget_modele = new BudgetModele();
                        $budget_modele->setCategorie($categorie);
                        $budget_modele->setSeuil($seuil);

                        $em = $this->getDoctrine()->getManager();
                        $em->persist($budget_modele);
                        $em->flush();

                        $response = [
                            'success' => true,
                            'BudgetModele' => [
                                'id' => $budget_modele->getId(),
                                'seuil' => $seuil
                            ]
                        ];
                    }
                    else {
                        $response = [
                            'success' => false,
                            'error' => 'La somme des budgets définis pour les enfants de cette catégorie ne doit pas dépasser le budget de la catégorie parente'
                        ];
                    }
                }
            }
            else if ($categorie_id == -1){
                // Check si modele global deja existant
                $budget_modele = $repository_budgetmodele->findBy([
                    'user' => $user,
                    'actif' => true
                ]);
                if (sizeof($budget_modele) >= 1){
                    $response = [
                        'success' => false,
                        'error' => 'Un budget gloabl existe déjà'
                    ];
                }

                else {
                    $seuil = $request->get('_seuil');
                    $budget_modele = new BudgetModele();
                    $budget_modele->setUser($user);
                    $budget_modele->setSeuil($seuil);

                    $em = $this->getDoctrine()->getManager();
                    $em->persist($budget_modele);
                    $em->flush();

                    $response = [
                        'success' => true,
                        'BudgetModele' => [
                            'id' => $budget_modele->getId(),
                            'seuil' => $seuil
                        ]
                    ];
                }
            }
        }

        $response = new JSONresponse($response);
        return $response;
    }

    public function editBudgetModeleAction(Request $request){
        $response = [
            'success' => false
        ];

        if ($request->isMethod('POST')) {
            $budgetmodele_id = $request->get('id');
            $seuil = $request->get('_seuil');

            /** @var BudgetModeleRepository $repository_budgetmodele */
            $repository_budgetmodele = $this
                ->getDoctrine()
                ->getManager()
                ->getRepository('CDCCoreBundle:BudgetModele');
            /** @var BudgetModele $budgetmodele */
            $budgetmodele = $repository_budgetmodele->find($budgetmodele_id);

            if($budgetmodele){
                $old_seuil = $budgetmodele->getSeuil();
                $budgetmodele->setSeuil(0);
                $budgetmodele_valid = $this->checkBudgetModeleValidity($budgetmodele->getCategorie(), $seuil);
                if ($budgetmodele_valid){
                    $budgetmodele->setSeuil($seuil);
                    // changer le seuil de l'instance en cours
                    /** @var BudgetInstanceRepository $repository_budgetinstance */
                    $repository_budgetinstance = $this
                        ->getDoctrine()
                        ->getManager()
                        ->getRepository('CDCCoreBundle:BudgetInstance');
                    $current_date = new \DateTime();
                    /** @var BudgetInstance[] $budget_instance */
                    $budget_instance = $repository_budgetinstance->findBudgetInstanceUsingBudgetModele($budgetmodele, DashboardController::getMonthFromDatetime($current_date), DashboardController::getYearFromDatetime($current_date));
                    if (sizeof($budget_instance) == 1){
                        $budget_instance[0]->setSeuil($seuil);
                    }

                    $em = $this->getDoctrine()->getManager();
                    $em->persist($budgetmodele);
                    $em->flush();

                    $response = [
                        'success' => true,
                        'BudgetModele' => [
                            'id' => $budgetmodele->getId(),
                            'seuil' => $seuil,
                        ]
                    ];
                    if ($budgetmodele->getCategorie()){
                        $response['BudgetModele']['categorie_id'] = $budgetmodele->getCategorie()->getId();
                    }
                    else {
                        $response['BudgetModele']['categorie_id'] = -1;
                    }
                }
                else {
                    $budgetmodele->setSeuil($old_seuil);
                    $response = [
                        'success' => false,
                        'error' => 'La somme des budgets définis pour les enfants de cette catégorie ne doit pas dépasser le budget de la catégorie parente'
                    ];
                }
            }
        }

        $response = new JSONresponse($response);
        return $response;
    }

    public function checkBudgetModeleValidity($categorie, $seuil){
        $valid = true;
        if ($categorie) {
            /** @var Categorie $categorie */
            $categorie_children_a = $categorie->getChildren();
            $categorie_parent = $categorie->getParent();
            $seuil_sum = 0;
            $valid = true;

            /** @var BudgetModeleRepository $repository_budgetmodele */
            $repository_budgetmodele = $this
                ->getDoctrine()
                ->getManager()
                ->getRepository('CDCCoreBundle:BudgetModele');

            // Si c'est une catégorie parente, on regarde si la somme des budgets des catégorie enfants <= budget parent
            if (sizeof($categorie_children_a) > 0) {
                for ($i = 0; $i < sizeof($categorie_children_a); $i++) {
                    /** @var BudgetModele $budget */
                    $budget = $repository_budgetmodele->findOneBy([
                        'categorie' => $categorie_children_a[$i],
                        'actif' => true
                    ]);
                    if ($budget) {
                        $seuil_sum += intval($budget->getSeuil());
                    }
                }
                if ($seuil_sum > $seuil) {
                    $valid = false;
                }
            } // Si c'est une catégorie enfant, même vérification
            else {
                /** @var BudgetModele $budget_parent */
                $budget_parent = $repository_budgetmodele->findOneBy([
                    'categorie' => $categorie_parent
                ]);
                if ($budget_parent) {
                    $seuil_parent = $budget_parent->getSeuil();
                    $categorie_children_a = $categorie_parent->getChildren();
                    if (sizeof($categorie_children_a) > 0) {
                        for ($i = 0; $i < sizeof($categorie_children_a); $i++) {
                            /** @var BudgetModele $budget */
                            $budget = $repository_budgetmodele->findOneBy([
                                'categorie' => $categorie_children_a[$i],
                                'actif' => true
                            ]);
                            if ($budget) {
                                $seuil_sum += intval($budget->getSeuil());
                            }
                        }
                        if ($seuil_sum + $seuil > $seuil_parent) {
                            $valid = false;
                        }
                    }
                }
            }
        }
        return $valid;
    }

    public function deleteBudgetmodeleAction(Request $request){
        $response = [
            'success' => false
        ];

        if ($request->isMethod('POST')) {
            $budgetmodele_id = $request->get('id');
            /** @var BudgetModeleRepository $repository_budgetmodele */
            $repository_budgetmodele = $this
                ->getDoctrine()
                ->getManager()
                ->getRepository('CDCCoreBundle:BudgetModele');
            /** @var BudgetModele $budgetmodele */
            $budgetmodele = $repository_budgetmodele->find($budgetmodele_id);

            if($budgetmodele){
                $budgetmodele->setActif(false);
                $em = $this->getDoctrine()->getManager();
                $em->persist($budgetmodele);
                $em->flush();

                $response = [
                    'success' => true,
                ];
                if($budgetmodele->getCategorie()){
                    $response['categorie_id'] = $budgetmodele->getCategorie()->getId();
                }
                else {
                    $response['categorie_id'] = -1;
                }
            }
        }
        $response = new JSONresponse($response);
        return $response;
    }
}