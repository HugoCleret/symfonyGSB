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
use Symfony\Component\Form\Extension\Core\Type\DateIntervalType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class EmployeController extends AbstractController
{
    /**
     * @Route("/employe", name="employe")
     */
    public function index()
    {
        return $this->render('employe/index.html.twig', [
            'controller_name' => 'EmployeController',
        ]);
    }

    
    
    /**
    * @Route("/AccueilEmp", name="app_AccueilEmp")
    */
    public function afficheToutesLesFormations()
    {
        $formation =$this->getDoctrine()->getRepository(Formation::class)->findall();      

        if(!$formation){
            $message="pas de formation";
        }else{
            $message=null;
        }
        return $this->render('employe/all_formationemploye.html.twig',array('ensFormations'=>$formation,'message'=>$message));
        

        // ON ETAIT ICI 30 OCTOBRE, 
        // Fait : s'inscrire mais peut avoir des doublons
        // Entrain de faire : , creation de connexion mais il faut encore verifier la fonction find..., afficher les formations
        // A faire : afficher les formations => attention format date + heure
        // pourvoir en ajouter, confirmer ou refuser les inscriptions

    }
    
    
    /** 
     * @Route("/suppFormation/{id}", name="app_SupprFormation")
     */
    public function suppFormation($id, ObjectManager $manager) 
    {   
        $Formation = $this->getDoctrine()->getRepository(Formation::class)->find($id);
        $testSuppr = $this->getDoctrine()->getRepository(Inscription::class)->findBy(['Formation' => $Formation]);

        if($testSuppr==null){
            $manager->remove($Formation);
            $manager->flush();
            return $this->redirectToRoute('app_AccueilEmp');   
        }
        else {
            $this->addFlash('suppr', 'Article Created! Knowledge is power!');
            return $this->redirectToRoute('app_AccueilEmp');   

        }
    }

    /** 
     * @Route("/ajoutFormation/", name="app_AjoutFormation")
     */
    public function ajoutFormation(ObjectManager $manager, Request $request, $formation=null) 
    {   
        if($formation == null){
            $formation = new Formation();
        }
        $form=$this->createForm(AjoutFormationType::class,$formation);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            //$visiteur = $this->getDoctrine()->getRepository(Visiteur::class)->find($login);
            $em->persist($formation);
            $em->flush();
            return $this->redirectToRoute('app_AccueilEmp');
            
        }
        return $this->render('employe/ajout_formation.html.twig',array('form'=>$form->createView()));
    }
    

    /** 
     * @Route("/gererInscription/", name="app_GererInscription")
     */
    public function gererInscriptionDeVisiteur(ObjectManager $manager, Request $request, $formation=null) 
    {   
        $inscription =$this->getDoctrine()->getRepository(Inscription::class)->findby(['statut' => 'En cours']);

        if(!$inscription){
            $message="pas d'inscription";
        }else{
            $message=null;
        }
        return $this->render('employe/all_Inscriptions.html.twig',array('ensInscriptions'=>$inscription,'message'=>$message));
    }
    
    /** 
     * @Route("/accepterInscription/{id}", name="app_AccepterInscription")
     */
    public function accepterInscriptionVisiteur(ObjectManager $manager, Request $request, $id) 
    {   
        $inscription = $this->getDoctrine()->getRepository(Inscription::class)->find($id);
        if ($inscription == null) {
            $inscription = new Inscription();
        }
        $inscription ->setStatut("Acceptée");
        $manager = $this->getDoctrine()->getManager();
        $manager->persist($inscription);
        $manager->flush();
        $this->addFlash('success', 'Article Created! Knowledge is power!');

        return $this->redirectToRoute('app_GererInscription');
    }
     
    /** 
     * @Route("/refuserInscription/{id}", name="app_RefuserInscription")
     */
    public function refuserInscriptionVisiteur(ObjectManager $manager, Request $request, $id) 
    {   
        $inscription = $this->getDoctrine()->getRepository(Inscription::class)->find($id);
        if ($inscription == null) {
            $inscription = new Inscription();
        }
        $inscription ->setStatut("Refusée");
        $manager = $this->getDoctrine()->getManager();
        $manager->persist($inscription);
        $manager->flush();
        $this->addFlash('echec', 'Article Created! Knowledge is power!');

        return $this->redirectToRoute('app_GererInscription');
    }

    /**
    * @Route("/FormationDuMoisProchain", name="app_formation_moisProchain")
    */
    public function afficheLesFormationsDuMoisProchain()
    {
        $dateAjrdhui = new \DateTime('2019-11-28 10:20:00');
        // $dateAjrdhui->add(New DateInterval('P1M'));
        // $dateAjrdhui = $dateAjrdhui->format('y/m');
        // var_dump($dateAjrdhui);
        // $dateAjrdhui = new \DateTime($dateAjrdhui + '/d');

        // var_dump($dateAjrdhui);
        // $date = new \DateTime($dateAjrdhui);
        // var_dump($date);
        // $formation =$this->getDoctrine()->getRepository(Formation::class)->findBy(['dateDebut'->format('y/m/d')=>$dateAjrdhui->format('y/m/d')]);      
        $formation =$this->getDoctrine()->getRepository(Formation::class)->findBy(['dateDebut'=>$dateAjrdhui]);      
     
        if(!$formation){
            $message="Il n'y a pas de formation le mois prochain";
        }else{
            $message=null;
        }
        return $this->render('employe/all_formationDuMoisProchain.html.twig',array('ensFormations'=>$formation,'message'=>$message));
        

    }

    /**
    * @Route("/formationsParProduitHisto/{id}", name="app_formationsParProduitHisto")
    */
    public function GetLesFormationsByProduit($id)
    {
        //test de l'histogramm sans les données formations
        // $chart = $this->get('app.chart');

        // return $this->render('home/index.html.twig', ['amountByYear' => $chart->amountByYear()]);
        //

        $produit = $this->getDoctrine()->getRepository(Produit::class)->findOneBy(['id'=>$id]);
        $formations = $this->getDoctrine()->getRepository(Formation::class)->findBy(['Produit'=>$produit]);
        if(!$formations){
            $message="Il n'y a pas de formation pour ce produit";
        }else{
            $message=null;
        }
        return $this->render('employe/histogramme_formationByProduit.html.twig',array('ensFormationsByProduit'=>$formations,'message'=>$message));
        

    }
}
