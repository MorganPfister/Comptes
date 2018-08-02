<?php

namespace CDC\CoreBundle\Controller;

use CDC\CoreBundle\Entity\Categorie;
use CDC\CoreBundle\Entity\Compte;
use CDC\CoreBundle\Entity\MoyenTransfert;
use CDC\CoreBundle\Entity\Transfert;
use CDC\CoreBundle\Entity\TypeTransfert;
use CDC\CoreBundle\Entity\User;
use CDC\CoreBundle\Repository\CategorieRepository;
use CDC\CoreBundle\Repository\CompteRepository;
use CDC\CoreBundle\Repository\MoyenTransfertRepository;
use CDC\CoreBundle\Repository\TransfertRepository;
use CDC\CoreBundle\Repository\TypeTransfertRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class TransfertController extends Controller {
    public function overviewAction() {
        /** @var User $user */
        $user = $this->getUser();

        /** @var TransfertRepository $repositoryTransfert */
        $repository_transfert = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('CDCCoreBundle:Transfert');
        /** @var Transfert[] $transfert_a */
        $transfert_a = $repository_transfert->findTransfertUsingUser($user);

        /** @var CompteRepository $repository_compte */
        $repository_compte = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('CDCCoreBundle:Compte');
        /** @var Compte[] $compte_a */
        $compte_a = $repository_compte->findBy([
            'user' => $user
        ]);

        /** @var CategorieRepository $repository_categorie */
        $repository_categorie = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('CDCCoreBundle:Categorie');
        /** @var Categorie[] $categorie_a */
        $parentcategorie_a = $repository_categorie->getParentCategorie_a($user);

        /** @var MoyenTransfertRepository $repository_moyentransfert */
        $repository_moyentransfert = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('CDCCoreBundle:MoyenTransfert');
        /** @var MoyenTransfert[] $moyentransfert_a */
        $moyentransfert_a = $repository_moyentransfert->findAll();

        return $this->render('CDCCoreBundle:Transfert:overview.html.twig', [
            'transfert_a' => $transfert_a,
            'compte_a' => $compte_a,
            'parentcategorie_a' => $parentcategorie_a,
            'moyentransfert_a' => $moyentransfert_a
        ]);
    }

    public function addTransfertAction(Request $request){
        $response = [
            'success' => false
        ];

        if ($request->isMethod('POST')) {
            /** @var CompteRepository $repository_compte */
            $repository_compte = $this
                ->getDoctrine()
                ->getManager()
                ->getRepository('CDCCoreBundle:Compte');

            /** @var CompteRepository $repository_categorie */
            $repository_categorie = $this
                ->getDoctrine()
                ->getManager()
                ->getRepository('CDCCoreBundle:Categorie');

            /** @var MoyenTransfertRepository $repository_moyentransfert */
            $repository_moyentransfert = $this
                ->getDoctrine()
                ->getManager()
                ->getRepository('CDCCoreBundle:MoyenTransfert');

            /** @var TypeTransfertRepository $repository_typetransfert */
            $repository_typetransfert = $this
                ->getDoctrine()
                ->getManager()
                ->getRepository('CDCCoreBundle:TypeTransfert');

            $titre = $request->get('_titre');
            $montant = $request->get('_montant');
            $compte_id = $request->get('_compte');
            $categorie_id = $request->get('_categorie');
            $date = $request->get('_date');
            $moyentransfert_id = $request->get('_moyentransfert');
            $description = $request->get('_description');

            /** @var Categorie $categorie */
            $categorie = $repository_categorie->find(intval($categorie_id));
            /** @var Compte $compte */
            $compte = $repository_compte->find(intval($compte_id));
            /** @var MoyenTransfert $moyentransfert */
            $moyentransfert = $repository_moyentransfert->find(intval($moyentransfert_id));

            $transfert = new Transfert();
            $transfert->setTitre($titre);
            $transfert->setMontant(floatval($montant));
            $transfert->setCompte($compte);
            $transfert->setCategorie($categorie);
            $transfert->setDate(\DateTime::createFromFormat('d/m/Y', $date));
            $transfert->setMoyen($moyentransfert);
            $transfert->setCommentaire($description);
            if ($montant >= 0){
                $type = $repository_typetransfert->find(TypeTransfert::RECETTE_ID);
            }
            else {
                $type = $repository_typetransfert->find(TypeTransfert::DEPENSE_ID);
            }
            /** @var TypeTransfert $type */
            $transfert->setType($type);

            $em = $this->getDoctrine()->getManager();
            $em->persist($transfert);
            $em->flush();

            $compte->setSolde(floatval($compte->getSolde()) + floatval($montant));
            $em->persist($compte);
            $em->flush();

            $response = [
                'success' => true,
                'Transfert' => [
                    'id' => $transfert->getId(),
                    'date' => $date,
                    'montant' => $montant,
                    'titre' => $titre,
                    'commentaire' => $description,
                ],
                'Compte' => [
                    'nom' => $compte->getNom(),
                    'titulaire' => $compte->getTitulaire(),
                    'banque' => $compte->getBanque()
                ],
                'Categorie' => [
                    'titre' => $categorie->getTitre(),
                    'icon' => $categorie->getIcon(),
                    'color' => $categorie->getColor()
                ],
                'MoyenTransfert' => [
                    'description' => $moyentransfert->getDescription()
                ]
            ];
        }

        $response = new JSONresponse($response);
        return $response;
    }

    public function retrieveTransfertAction(Request $request){
        $response = [
            'success' => false
        ];

        if ($request->isMethod('POST')) {
            $transfert_id = $request->get('id');

            /** @var TransfertRepository $repositoryTransfert */
            $repository_transfert = $this
                ->getDoctrine()
                ->getManager()
                ->getRepository('CDCCoreBundle:Transfert');
            /** @var Transfert $transfert */
            $transfert = $repository_transfert->find($transfert_id);
            if ($transfert) {
                $response = [
                    'success' => true,
                    'Transfert' => [
                        'titre' => $transfert->getTitre(),
                        'montant' => $transfert->getMontant(),
                        'compte' => $transfert->getCompte()->getId(),
                        'categorie' => $transfert->getCategorie()->getId(),
                        'date' => $transfert->getDate()->format('d/m/Y'),
                        'moyentransfert' => $transfert->getMoyen()->getId(),
                        'description' => $transfert->getCommentaire()
                    ]
                ];
            }
        }

        $response = new JSONresponse($response);
        return $response;
    }

    public function editTransfertAction(Request $request){
        $response = [
            'success' => false
        ];

        if ($request->isMethod('POST')) {
            /** @var CompteRepository $repository_compte */
            $repository_compte = $this
                ->getDoctrine()
                ->getManager()
                ->getRepository('CDCCoreBundle:Compte');

            /** @var CompteRepository $repository_categorie */
            $repository_categorie = $this
                ->getDoctrine()
                ->getManager()
                ->getRepository('CDCCoreBundle:Categorie');

            /** @var MoyenTransfertRepository $repository_moyentransfert */
            $repository_moyentransfert = $this
                ->getDoctrine()
                ->getManager()
                ->getRepository('CDCCoreBundle:MoyenTransfert');

            /** @var TypeTransfertRepository $repository_typetransfert */
            $repository_typetransfert = $this
                ->getDoctrine()
                ->getManager()
                ->getRepository('CDCCoreBundle:TypeTransfert');

            /** @var TransfertRepository $repository_transfert */
            $repository_transfert = $this
                ->getDoctrine()
                ->getManager()
                ->getRepository('CDCCoreBundle:Transfert');
            $transfert_id = $request->get('id');
            /** @var Transfert $transfert */
            $transfert = $repository_transfert->find($transfert_id);
            $old_montant = $transfert->getMontant();

            if ($transfert) {
                $titre = $request->get('_titre');
                $montant = $request->get('_montant');
                $compte_id = $request->get('_compte');
                $categorie_id = $request->get('_categorie');
                $date = $request->get('_date');
                $moyentransfert_id = $request->get('_moyentransfert');
                $description = $request->get('_description');

                /** @var Categorie $categorie */
                $categorie = $repository_categorie->find(intval($categorie_id));
                /** @var Compte $compte */
                $compte = $repository_compte->find(intval($compte_id));
                /** @var MoyenTransfert $moyentransfert */
                $moyentransfert = $repository_moyentransfert->find(intval($moyentransfert_id));

                $transfert->setTitre($titre);
                $transfert->setMontant(floatval($montant));
                $transfert->setCompte($compte);
                $transfert->setCategorie($categorie);
                $transfert->setDate(\DateTime::createFromFormat('d/m/Y', $date));
                $transfert->setMoyen($moyentransfert);
                $transfert->setCommentaire($description);
                if ($montant >= 0){
                    $type = $repository_typetransfert->find(TypeTransfert::RECETTE_ID);
                }
                else {
                    $type = $repository_typetransfert->find(TypeTransfert::DEPENSE_ID);
                }
                /** @var TypeTransfert $type */
                $transfert->setType($type);

                $em = $this->getDoctrine()->getManager();
                $em->persist($transfert);
                $em->flush();

                $compte->setSolde(floatval($compte->getSolde()) - floatval($old_montant));
                $compte->setSolde(floatval($compte->getSolde()) + floatval($montant));
                $em->persist($compte);
                $em->flush();

                $response = [
                    'success' => true,
                    'Transfert' => [
                        'id' => $transfert->getId(),
                        'date' => $date,
                        'montant' => $montant,
                        'titre' => $titre,
                        'commentaire' => $description,
                    ],
                    'Compte' => [
                        'nom' => $compte->getNom(),
                        'titulaire' => $compte->getTitulaire(),
                        'banque' => $compte->getBanque()
                    ],
                    'Categorie' => [
                        'titre' => $categorie->getTitre(),
                        'icon' => $categorie->getIcon(),
                        'color' => $categorie->getColor()
                    ],
                    'MoyenTransfert' => [
                        'description' => $moyentransfert->getDescription()
                    ]
                ];
            }
        }

        $response = new JSONresponse($response);
        return $response;
    }

    public function deleteTransfertAction(Request $request){
        $response = [
            'success' => false
        ];

        if ($request->isMethod('POST')) {
            /** @var TransfertRepository $repository_transfert */
            $repository_transfert = $this
                ->getDoctrine()
                ->getManager()
                ->getRepository('CDCCoreBundle:Transfert');
            $transfert_id = $request->get('id');
            /** @var Transfert $transfert */
            $transfert = $repository_transfert->find($transfert_id);

            if ($transfert){
                $compte = $transfert->getCompte();
                $compte->setSolde($transfert->getCompte()->getSolde() - $transfert->getMontant());

                $em = $this->getDoctrine()->getManager();

                $em->persist($compte);
                $em->flush();

                $em->remove($transfert);
                $em->flush();

                $response = [
                    'success' => true
                ];
            }
        }

        $response = new JSONresponse($response);
        return $response;
    }
}