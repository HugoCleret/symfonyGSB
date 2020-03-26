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

class GSBController extends AbstractController
{
    /**
     * @Route("/g/s/b", name="g_s_b")
     */
    public function index()
    {
        return $this->render('gsb/index.html.twig', [
            'controller_name' => 'GSBController',
        ]);
    }
    

    // public function findallvisiteur()
    // {
        
    //     return $this->render('gsb/ajout_formation.html.twig', [
    //         'controller_name' => 'GSBController',
    // }


    /**
    * @Route("/connexion", name="app_connexion")
    */
    public function connexion(Request $request ,$emp=null)
    // Connexion au site, puis selon le statut de l'utilisateur => redirige vers le controlleur employe ou visiteur
    // Renvoie un message d'erreur si les identifiants sont mauvais
    {    
        if($emp==null){
            $emp = new Visiteur();
        }
        $form=$this->createForm(ConnexionType::class,$emp);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $visiteur = $this->getDoctrine()->getRepository(Visiteur::class)->findOneBySomeLoginMdp($emp->getLogin(), $emp->getMdp());
            if($visiteur) {
                $session = new Session();
                $session->set('idVisiteur', $visiteur->getId());
                $session->set('nomVisiteur', $visiteur->getNom());
                $session->set('prenomVisiteur', $visiteur->getPrenom());

                if($visiteur->getStatut()=="employe") {
                    return $this->redirectToRoute('app_AccueilEmp');
                }
                else {
                    // redirection dans le controller InscriptionController
                    return $this->redirectToRoute('app_AccueilVisit');
                }
            }  
            else {
                $this->addFlash('mauvaisMDP', 'Mauvais MDP');
                return $this->redirectToRoute('app_connexion'); 
            }      
        }
        return $this->render('gsb/connexion.html.twig',array('form'=>$form->createView()));

    }
}
