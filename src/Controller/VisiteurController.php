<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Formation;
use App\Entity\Visiteur;
use App\Entity\Produit;
use App\Entity\Inscription;
use Doctrine\Common\Persistence\ObjectManager;
use App\Form\FormloginType;
use Symfony\Component\HttpFoundation\Request;
use App\Form\InscriptionType;
use App\Form\ConnexionType;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Form\AjoutFormationType;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Session\Session;

class VisiteurController extends AbstractController
{
    /**
     * @Route("/visiteur", name="visiteur")
     */
    public function index()
    {
        return $this->render('visiteur/index.html.twig', [
            'controller_name' => 'VisiteurController',
        ]);
    }

    /**
    * @Route("/AccueilVisiteur", name="app_AccueilVisit")
    */
    public function afficheLesFormationsPourSinscrire()
    // Affiche toutes les formations avec un bouton s'inscrire
    {
        $formation =$this->getDoctrine()->getRepository(Formation::class)->findall();      
        if(!$formation){
            $message="Il n'y a pas de formation à venir pour le moment. Veuillez réessayer ultérieurement.";
        }else{
            $message=null;
        }
        return $this->render('visiteur/all_formationvisiteur.html.twig',array('ensFormations'=>$formation,'message'=>$message));
    }


    /**
    * @Route("/InscriptionAUneFormation/{id}", name="app_InscriptionAUneFormation")
    */
    public function sInscrireAUneFormation($id, ObjectManager $manager)
    // Permet au visiteur de s'inscrire à une formation + cette fonction permet de ne pas s'inscrire deux fois à une formation
    {
        $idVisiteur = $this->get('session')->get('idVisiteur');

        $formation = $this->getDoctrine()->getRepository(Formation::class)->find($id);
        $visiteur = $this->getDoctrine()->getRepository(Visiteur::class)->find($idVisiteur);

        $testInscription = $this->getDoctrine()->getRepository(Inscription::class)->findBy(['Visiteur' => $visiteur,'Formation' => $formation]);
        if($testInscription == null) {
            $inscription = new Inscription();
            $inscription->setVisiteur($visiteur);
            $inscription->setFormation($formation);
            $inscription->setStatut("En cours");

            $manager->persist($inscription);
            $manager->flush();
                        
            $this->addFlash('success', 'Article Created! Knowledge is power!');

            return $this->redirectToRoute('app_AccueilVisit');
        }
        
        else {
            $this->addFlash('dejaInscrit', 'Article Not Created! Knowledge is power!');

            return $this->redirectToRoute('app_AccueilVisit');
        }
    }

    /**
    * @Route("/InscriptionsDuVisiteur", name="app_inscriptions_visiteur")
    */
    public function afficheLesInscriptionsDuVisiteur()
    // Affiche toutes les formations avec un bouton s'inscrire
    {
        $idVisiteur = $this->get('session')->get('idVisiteur');
        $visiteur = $this->getDoctrine()->getRepository(Visiteur::class)->find($idVisiteur);
        $inscription =$this->getDoctrine()->getRepository(Inscription::class)->findBy(['Visiteur' => $visiteur]);     
        if(!$inscription){
            $message="pas de formation";
        }else{
            $message=null;
        }
        return $this->render('visiteur/all_inscriptionformation.html.twig',array('ensInscriptions'=>$inscription,'message'=>$message));
    }
}
