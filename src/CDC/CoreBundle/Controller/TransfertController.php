<?php

namespace CDC\CoreBundle\Controller;

use CDC\CoreBundle\Entity\User;
use CDC\CoreBundle\Repository\TransfertRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class TransfertController extends Controller {
    public function overviewAction() {
        /** @var User $user */
        $user = $this->getUser();

        /** @var TransfertRepository $repositoryTransfert */
        $repositoryTransfert = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('CDCCoreBundle:Transfert');
        $transfert_a = $repositoryTransfert->findTransfertUsingUser($user);

        return $this->render('CDCCoreBundle:Transfert:overview.html.twig', [
            'transfert_a' => $transfert_a
        ]);
    }
}