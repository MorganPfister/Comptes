<?php

namespace CDC\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use CDC\CoreBundle\Entity\Compte;
use Symfony\Component\HttpFoundation\Request;

class CompteController extends Controller {
    public function overviewAction(){
        $user = $this->getUser();
        $repositoryCompte = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('CDCCoreBundle:Compte');
        $compte_a = $repositoryCompte->findBy([
            'user' => $user
        ]);

        return $this->render('CDCCoreBundle:Compte:overview.html.twig', array(
            'compte_a' => $compte_a
        ));
    }

    public function addCompteAction(Request $request){
        $response = [
            'success' => false
        ];

        if ($request->isMethod('POST')){
            $repository = $this
                ->getDoctrine()
                ->getManager()
                ->getRepository('CDCCoreBundle:Compte')
            ;

            $titulaire = $request->get('_titulaire');
            $nom = $request->get('_nom');
            $banque = $request->get('_banque');
            $solde = $request->get('_solde');
            $user = $this->getUser();

            $compte_a = $repository->findBy([
                'user' => $user,
                'nom' => $nom
            ]);

            if (sizeof($compte_a) > 0){
                $response = [
                    'success' => false,
                    'error' => "Un compte existe déjà avec ce nom"
                ];
            }
            else {
                $compte = new Compte();
                $compte->setTitulaire($titulaire);
                $compte->setNom($nom);
                $compte->setBanque($banque);
                $compte->setSolde($solde);
                $compte->setUser($user);

                $em = $this->getDoctrine()->getManager();
                $em->persist($compte);
                $em->flush();

                $response = [
                    'success' => true,
                    'Compte' => [
                        'id' => $compte->getId(),
                        'titulaire' => $titulaire,
                        'nom' => $nom,
                        'banque' => $banque,
                        'solde' => $solde
                    ]
                ];
            }
        }

        $response = new JSONresponse($response);
        return $response;
    }

    public function deleteCompteAction(Request $request){
        $response = [
            'success' => false
        ];

        if ($request->isMethod('POST')){
            $repository = $this
                ->getDoctrine()
                ->getManager()
                ->getRepository('CDCCoreBundle:Compte')
            ;
            $user = $this->getUser();
            $id = $request->get('id');
            $compte = $repository->findOneBy([
                'id' => $id,
                'user' => $user
            ]);

            if (sizeof($compte) == 1){
                $em = $this->getDoctrine()->getManager();
                $em->remove($compte);
                $em->flush();

                $response = [
                    'success' => true
                ];
            }
        }

        $response = new JSONresponse($response);
        return $response;
    }

    public function editCompteAction(Request $request){
        $response = [
            'success' => false
        ];
        $error = false;

        if ($request->isMethod('POST')){
            $repository = $this
                ->getDoctrine()
                ->getManager()
                ->getRepository('CDCCoreBundle:Compte')
            ;
            $user = $this->getUser();
            $id = $request->get('id');
            $compte = $repository->findOneBy([
                'id' => $id,
                'user' => $user
            ]);

            if (sizeof($compte) == 1){
                $titulaire = $request->get('_titulaire');
                $nom = $request->get('_nom');
                $banque = $request->get('_banque');
                $solde = $request->get('_solde');

                if ($nom != $compte->getNom()){
                    $check_compte_a = $repository->findBy([
                        'nom' => $nom
                    ]);
                    if (sizeof($check_compte_a) > 0){
                        $response = [
                            'success' => false,
                            'error' => "Un compte existe déjà avec ce nom"
                        ];
                        $error = true;
                    }
                }
                if (!$error){
                    $compte->setTitulaire($titulaire);
                    $compte->setNom($nom);
                    $compte->setBanque($banque);
                    $compte->setSolde($solde);
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($compte);
                    $em->flush();

                    $response = [
                        'success' => true,
                        'Compte' => [
                            'id' => $compte->getId(),
                            'titulaire' => $titulaire,
                            'nom' => $nom,
                            'banque' => $banque,
                            'solde' => $solde
                        ]
                    ];
                }
            }
        }

        $response = new JSONresponse($response);
        return $response;
    }
}