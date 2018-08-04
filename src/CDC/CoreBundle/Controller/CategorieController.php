<?php

namespace CDC\CoreBundle\Controller;

use CDC\CoreBundle\Entity\BudgetInstance;
use CDC\CoreBundle\Entity\BudgetModele;
use CDC\CoreBundle\Entity\Transfert;
use CDC\CoreBundle\Repository\BudgetInstanceRepository;
use CDC\CoreBundle\Repository\BudgetModeleRepository;
use CDC\CoreBundle\Repository\TransfertRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use CDC\CoreBundle\Entity\Categorie;
use CDC\CoreBundle\Repository\CategorieRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class CategorieController extends Controller {
    public function overviewAction(){
        $user = $this->getUser();

        /** @var CategorieRepository $repositoryCategorie */
        $repositoryCategorie = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('CDCCoreBundle:Categorie');
        $categorie_a = $repositoryCategorie->getParentCategorie_a($user);

        return $this->render('CDCCoreBundle:Categorie:overview.html.twig', array(
            'categorie_a' => $categorie_a
        ));
    }

    public function addCategorieAction(Request $request){
        if ($request->isMethod('POST')){
            $parent = $request->get('_parent');
            $nom = $request->get('_nom');
            $icone = $request->get('_icone');
            $color = $request->get("_color");

            /** @var CategorieRepository $repository */
            $repository = $this
                ->getDoctrine()
                ->getManager()
                ->getRepository('CDCCoreBundle:Categorie');

            $categorie = new Categorie();
            if ($parent != -1) {
                /** @var Categorie $categorie_parent */
                $categorie_parent = $repository->find($parent);
                $categorie->setParent($categorie_parent);
                $categorie_parent->addChild($categorie);
            }
            $categorie->setTitre($nom);
            $categorie->setIcon($icone);
            $categorie->setUser($this->getUser());
            $categorie->setColor($color);

            $em = $this->getDoctrine()->getManager();
            $em->persist($categorie);
            $em->flush();
        }

        return $this->redirectToRoute('cdc_core_categoriepage');
    }

    public function deleteCategorieAction(Request $request){
        if ($request->isMethod('POST')){
            $id_categorie = $request->get('id');

            /** @var CategorieRepository $repository */
            $repository = $this
                ->getDoctrine()
                ->getManager()
                ->getRepository('CDCCoreBundle:Categorie');

            /** @var Categorie $categorie */
            $categorie = $repository->find($id_categorie);

            if (sizeof($categorie) == 1){
                // Changer les catégories des transferts vers Inconnu pour la catégorie à supprimer et tous ses enfants
                $this->updateTransfertCategorieToUnknown($categorie);

                /** @var Categorie[] $categorie_child_a */
                $categorie_child_a = $categorie->getChildren();
                $categorie_parent = $categorie->getParent();

                $em = $this->getDoctrine()->getManager();
                if (sizeof($categorie_child_a) > 0){
                    for($i=0; $i < sizeof($categorie_child_a); $i++){
                        $this->updateTransfertCategorieToUnknown($categorie_child_a[$i]);

                        $categorie_child_a[$i]->setActif(false);
                        $categorie->removeChild($categorie_child_a[$i]);
                        $em->persist($categorie_child_a[$i]);
                        $em->flush();
                    }
                }
                else if ($categorie_parent) {
                    $categorie_parent->removeChild($categorie);
                    $categorie->setParent();
                    $em->persist($categorie_parent);
                    $em->flush();
                }

                // Désactiver le budgetmodele lié à la catégorie
                /** @var BudgetModeleRepository $repository_budgetmodele */
                $repository_budgetmodele = $this
                    ->getDoctrine()
                    ->getManager()
                    ->getRepository('CDCCoreBundle:BudgetModele');
                /** @var BudgetModele $budgetmodele */
                $budgetmodele = $repository_budgetmodele->findOneBy([
                    'categorie' => $categorie
                ]);

                if ($budgetmodele){
                    $budgetmodele->setActif(false);

                    $em->persist($budgetmodele);
                    $em->flush();
                }

                $categorie->setActif(false);

                $em->persist($categorie);
                $em->flush();
            }
        }
        $response = [
            'success' => true
        ];

        $response = new JSONresponse($response);
        return $response;
    }

    public function updateTransfertCategorieToUnknown($categorie){
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();

        /** @var CategorieRepository $repository_categorie */
        $repository_categorie = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('CDCCoreBundle:Categorie');
        /** @var Categorie $unknown_categorie */
        $unknown_categorie = $repository_categorie->find(Categorie::UNKNOWN_CATEGORIE_ID);

        /** @var TransfertRepository $repository_transfert */
        $repository_transfert = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('CDCCoreBundle:Transfert');
        /** @var Transfert[] $transfert_a */
        $transfert_a = $repository_transfert->findTransfertUsingUserAndCategorie($user, $categorie);
        for($i=0; $i < sizeof($transfert_a); $i++){
            $transfert_a[$i]->setCategorie($unknown_categorie);
            $em->persist($transfert_a[$i]);
            $em->flush();
        }
    }
}