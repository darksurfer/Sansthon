<?php

namespace Sansthon\ProdBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sansthon\ProdBundle\Entity\Etat;
use Sansthon\ProdBundle\Entity\Perte;
use Sansthon\ProdBundle\Form\EtatType;
use Symfony\Component\HttpFoundation\Response;
/**
 * Etat controller.
 *
 * @Route("/etat")
 */
class EtatController extends Controller
{
  /**
  *
  * Make an etat To Perte
  *
  *@Route("/{id}/toperte",name="etat_toperte")
  *@Method("GET")
  */
  public function toperteAction(Request $request,$id){
    $etat = $this->getDoctrine()
      ->getRepository('SansthonProdBundle:Etat')
      ->find($id);
    $perte = $this->getDoctrine()
      ->getRepository('SansthonProdBundle:Perte')
      ->createPerteFromEtat($etat);
   if($perte) {
    $this->getDoctrine()->getManager()->flush();
             $message =  "Création de la perte n°".$perte->getId()." de ".$perte->getQuantite()." ".$perte->getType();
          if($perte->getPersonne()){
              $message .= "attribué a ".$perte->getPersonne();
          }
              
          $this->get('session')->getFlashBag()->add('notice',$message);
    }else
    {
         $message = " une Erreur c'est produite la perte n'a pas était ajouter";
         $this->get('session')->getFlashBag()->add('error',$message);
    }
    
    return $this->redirect($this->getRequest()->headers->get("referer"));
  }
    /**
  *
  * divise an etat in two with less quanity
  *
  *@Route("/{id}/divise",name="etat_divise")
  *@Method("POST")
  */
    public function diviseAction(Request $request,$id){
    $etat = $this->getDoctrine()
      ->getRepository('SansthonProdBundle:Etat')
      ->find($id);
    if(!$etat) {
      throw $this->createNotFoundException('Etat was not found');
    }
    $quantite = $request->request->get("quantite");      
    if(!$quantite) {
      throw $this->createNotFoundException('quantite was not found');
    }
    if($quantite >= $etat->getQuantite() or $quantite <=0){
        $message = "Action impossible :La quantité a diviser est supérieur à la quantité disponible ou inférieur à 0";
        $this->get('session')->getFlashBag()->add('error',$message);
        return $this->redirect($this->getRequest()->headers->get("referer"));
    }
    $newEtat = new Etat();
    $newEtat->setDebut($etat->getDebut())
            ->setEtape($etat->getEtape())
            ->setEtapeorigine($etat->getEtapeorigine())
            ->setPersonne($etat->getPersonne())
            ->setType($etat->getType())
            ->setPrevue($etat->getPrevue())
            ->setCommentaire('Division de '.$etat->getId())
            ->setStocked($etat->getStocked())
            ->setfin($etat->getFin())
            ->setQuantite($quantite);
    $etat->setQuantite( ($etat->getQuantite()-$quantite) );
    $em=$this->getDoctrine()->getManager();
    $em->persist($newEtat);
    $em->flush();
    $message= "Divison en deux en cours de ".$etat->getQuantite()." et ".$newEtat->getQuantite()." de ".$newEtat->getType();
    $this->get('session')->getFlashBag()->add('notice',$message);
    return $this->redirect($this->getRequest()->headers->get("referer"));
  }
  
  /**
   *Valid and finish and Etat
   * 
   *@Route("/valide", name="etat_valide")
   *@Method("POST")
   */
  public function valideAction(Request $request)
  { 
    // Get All Needed Params
    $id = $request->request->get("id");
    $quantite = $request->request->get("quantite");
    $personne =  $this->getDoctrine()
      ->getRepository("SansthonProdBundle:Personne")->find($request->request->get("personne"));
    $prevue = $request->request->get("prevue");
    // new instance of needed object
    $etat = $this->getDoctrine()
      ->getRepository('SansthonProdBundle:Etat')
      ->find($id);
    if(!$etat) {
      throw $this->createNotFoundException('Etat was not found');
    }elseif ( $etat->getStocked() or $etat->getFin()){
            $this->get('session')->getFlashBag()->add('error', "etat ".$etat->getId()."est déja validé.");
            return $this->redirect($this->getRequest()->headers->get("referer"));
    }
    // processing
    $etat->setPersonne($personne);
    $etat->setPrevue(new \Datetime($prevue));
    $dif=$quantite - $etat->getQuantite();
    $etat->setQuantite($quantite);
    /*traitement de la différence de quantité*/
    if($dif < 0) {
      if(!$etat->getEtape()->getInitiale()){
          $perte = $this->getDoctrine()
            ->getRepository("SansthonProdBundle:Perte")
            ->createPerteByArray(array(
                  "personne" => $personne,
                  "quantite" => abs($dif),
                  "type" => $etat->getType(),
                  "etape" => $etat->getEtape()
                  ));
      }$this->get('session')->getFlashBag()->add('notice', "Santons ".$etat->getType()->getReference().", nouvelle perte de ".$perte->getQuantite(). " Attribuer à ".$perte->getPersonne());
    }elseif ($dif > 0) {
      if($etat->getEtapeorigine()){
        $stock= $this->getDoctrine()->getRepository('SansthonProdBundle:Stock')->getByEtapeAndType($etat->getEtapeorigine(),$etat->getType());
        $stock->subValue($dif);
        $this->get('session')->getFlashBag()->add('error', "Santons ".$etat->getType()->getReference().", stock de ".$etat->getEtapeorigine()." terminés décrémenter de ".$dif.' .');
      }
    }
    $etat->setFin(new \Datetime());
    $etat->setStocked(true);
    $perte = $this->getDoctrine()
      ->getRepository("SansthonProdBundle:Stock")
      ->getByEtapeAndType($etat->getEtape(),$etat->getType())
      ->addValue($etat->getQuantite());
    $this->getDoctrine()->getManager()->flush();
    $this->get('session')->getFlashBag()->add('success', "etat ".$etat->getId()." Validé ". $etat->getType()->getReference()." Stock de ". $etat->getEtape()->getNom()." terminés incrémenté de ".$etat->getQuantite());
    return $this->redirect($this->getRequest()->headers->get("referer"));
  }

  /**
   * add a Etat new attributed and etape linked entity.
   *
   * @Route("/add", name="etat_add")
   * @Method("POST")
   */
  public function addAction(Request $request)
  {
    /* Verification de l'existance des params indispensable Type Etape Quantite*/


    $type = $this->getDoctrine()
      ->getRepository('SansthonProdBundle:Type')
      ->find($request->request->get('type'));
    if(!$type) {
      throw $this->createNotFoundException('The Type was not found');
    }
    $personne= $this->getDoctrine()
      ->getRepository('SansthonProdBundle:Personne')
      ->find($request->request->get('personne'));

    $etape = $this->getDoctrine()
      ->getRepository('SansthonProdBundle:Etape')
      ->find($request->request->get('etape'));
    if(!$etape) {
      throw $this->createNotFoundException('The etape was not found');
    }

    $quantite=$request->request->get('quantite');
    if(!$quantite){
      throw new \Exception('No quantity given');
    }
    if($request->request->get('prevue')){
      $prevue= new \Datetime($request->request->get('prevue'));
    }else {
      $prevue = null;
    }
    /* On préléve la quantite à l'etape d'origine si il y en a une*/
    $origin= $this->getDoctrine()
      ->getRepository('SansthonProdBundle:Etape')
      ->find($request->request->get('origin'));
    if($origin){
      $stockOrigin= $this->getDoctrine()
        ->getRepository('SansthonProdBundle:Stock')
        ->getByEtapeAndType($origin,$type);
      $stockOrigin->subValue($quantite);
    }
    $newEtat = new Etat();
    $newEtat->setType($type)
      ->setEtapeorigine($origin)
      ->setPrevue($prevue)
      ->setEtape($etape)
      ->setFin(null)
      ->setPersonne($personne)
      ->setQuantite($quantite);
    $em = $this->getDoctrine()->getManager();
    $em->persist($newEtat);
    $em->flush();
    $this->get('session')->getFlashBag()->add('success', 'Etat N°'.$newEtat->getId().'.  '.$newEtat->getQuantite().' "'.$newEtat->getType().' '.$newEtat->getType()->getNom().'" en '.$newEtat->getEtape()->getNom().' lancé'); 
    return $this->redirect($this->getRequest()->headers->get("referer"));
  }

 /**
   * add a Etat new attributed and etape linked entity.
   *
   * @Route("/finalperte", name="etat_final_perte")
   * @Method("POST")
   */
  public function finalPerteAction(Request $request)
  {
    /* Verification de l'existance des params indispensable Type Etape Quantite*/
    $type = $this->getDoctrine()
      ->getRepository('SansthonProdBundle:Type')
      ->find($request->request->get('type'));
    if(!$type) {
      throw $this->createNotFoundException('The Type was not found');
    }
    $personne= $this->getDoctrine()
      ->getRepository('SansthonProdBundle:Personne')
      ->find($request->request->get('personne'));

    $etape = $this->getDoctrine()
      ->getRepository('SansthonProdBundle:Etape')
      ->find($request->request->get('etape'));
    if(!$etape) {
      throw $this->createNotFoundException('The etape was not found');
    }

    $quantite=$request->request->get('quantite');
    if(!$quantite){
      throw new \Exception('No quantity given');
    }
    $commentaire= $request->request->get('commentaire');
    
    /* On préléve la quantite à l'etape d'origine si il y en a une*/
 
      $stockEtape= $this->getDoctrine()
        ->getRepository('SansthonProdBundle:Stock')
        ->getByEtapeAndType($etape,$type);
      $stockEtape->subValue($quantite);
    $newPerte = new Perte();
    $newPerte->setType($type)
      ->setEtape($etape)
      ->setCommentaire($commentaire)
      ->setPersonne($personne)
      ->setQuantite($quantite)
      ->setDate(new \Datetime());
    $em = $this->getDoctrine()->getManager();
    $em->persist($newPerte);
    $em->flush();
    $this->get('session')->getFlashBag()->add('notice', 'Perte N°'.$newPerte->getId().'. '.$newPerte->getQuantite().' "'.$newPerte->getType().' '.$newPerte->getType()->getNom().'" au '.$newPerte->getEtape()->getNom().'Attribuer à'.$newPerte->getPersonne()); 
    return $this->redirect($this->getRequest()->headers->get("referer"));
  }
  
  /**
   * Cancel an Etat and Give back quantite to origin
   *
   *@Route("/{id}/cancel",name="etat_cancel")
   *
   *
   */
  public function cancel(Request $request,$id){
    $repo=$this->getDoctrine()
      ->getRepository('SansthonProdBundle:Etat');
    $etat =$repo->find($id);
    $repo->cancel($etat);
    $this->get('session')->getFlashBag()->add('success'," Annulation N° ".$id);
    $this->get('session')->getFlashBag()->add('notice', $etat->getEtape()." de ".$etat->getType().' '.$etat->getType()->getNom().' incrémenté de '.$etat->getQuantite()."."  );

    return $this->redirect($this->getRequest()->headers->get("referer"));
  }

  
     /**
   * Show Entity filter by a type.
   *
   * @Route("/byetape", name="etat_by_etape")
   * @Template("SansthonProdBundle:Etat:byEtape.html.twig")
   */
  public function byEtapeAction(Request $request){
    $id=$request->query->get('id');
    $etapeList= $this->getDoctrine()
      ->getRepository('SansthonProdBundle:Etape')
      ->findAll();

    if(empty($id)){
      return array(
          'etapes' => $etapeList,
          'etape' => null,
          'etats' => null,
          );
    }
    $currentEtape= $this->getDoctrine()
      ->getRepository('SansthonProdBundle:Etape')
      ->find($id);
    $etatList =$this->getDoctrine()
      ->getRepository('SansthonProdBundle:Etat')
      ->findBy(array("etape" => $currentEtape,"fin" => null, "stocked" => false ));
    return array(
        'etats' => $etatList,
        'etapes' => $etapeList,
        'etape' => $currentEtape,
        );

  }
  
  
   /**
   * Show Entity filter by a type.
   *
   * @Route("/bypersonne", name="etat_by_personne")
   * @Template("SansthonProdBundle:Etat:bypersonne.html.twig")
   */
  public function byPersoneAction(Request $request){
    $id=$request->query->get('id');
    $personneList= $this->getDoctrine()
      ->getRepository('SansthonProdBundle:Personne')
      ->findAll();
    $types= $this->getDoctrine()
      ->getRepository('SansthonProdBundle:Type')
      ->findAll();

    if(empty($id)){
      return array(
          'personnes' => $personneList,
          'personne' => null,
          'etats' => null,
          'types' => $types,
          );
    }
    $currentPersonne= $this->getDoctrine()
      ->getRepository('SansthonProdBundle:Personne')
      ->find($id);
    $etatList =$this->getDoctrine()
      ->getRepository('SansthonProdBundle:Etat')
      ->findBy(array("personne" => $currentPersonne,"fin" => null, "stocked" => false ));
    return array(
        'etats' => $etatList,
        'personnes' => $personneList,
        'personne' => $currentPersonne,
        'types' => $types,
        );

  }
  
  

  /**
   * Show Entity filter by a type.
   *
   * @Route("/bytype", name="etat_by_type")
   * @Template("SansthonProdBundle:Etat:bytype.html.twig")
   */
  public function byTypeAction(Request $request){
    $id=$request->query->get('id');
    $typeList= $this->getDoctrine()
      ->getRepository('SansthonProdBundle:Type')
      ->findAll();

    if(empty($id)){
      return array(
          'types' => $typeList,
          'type' => null,
          'etatList' => null,
          'stocks' =>array(),
          );
    }
    $currentType= $this->getDoctrine()
      ->getRepository('SansthonProdBundle:Type')
      ->find($id);
    $stockList = $this->getDoctrine()
      ->getRepository('SansthonProdBundle:Stock')
      ->getByType($currentType);
    $etatList =$this->getDoctrine()
      ->getRepository('SansthonProdBundle:Etat')
      ->findBy(array("type" => $currentType,"fin" => null, "stocked" => false ));
    return array(
        'etatList' => $etatList,
        'types' => $typeList,
        'type' => $currentType,
        'stocks' => $stockList,
        );

  }


  /**
   * Lists all Etat entities.
   *
   * @Route("/", name="etat")
   * @Method("GET")
   * @Template()
   */
  public function indexAction()
  {
    $em = $this->getDoctrine()->getManager();

    $entities = $em->getRepository('SansthonProdBundle:Etat')->findAll();

    return array(
        'entities' => $entities,
        );
  }

  /**
   * Creates a new Etat entity.
   *
   * @Route("/", name="etat_create")
   * @Method("POST")
   * @Template("SansthonProdBundle:Etat:new.html.twig")
   */
  public function createAction(Request $request)
  {
    $entity  = new Etat();
    $form = $this->createForm(new EtatType(), $entity);
    $form->bind($request);

    if ($form->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->persist($entity);
      $em->flush();

      return $this->redirect($this->generateUrl('etat_show', array('id' => $entity->getId())));
    }

    return array(
        'entity' => $entity,
        'form'   => $form->createView(),
        );
  }

  /**
   * Displays a form to create a new Etat entity.
   *
   * @Route("/new", name="etat_new")
   * @Method("GET")
   * @Template()
   */
  public function newAction()
  {
    $entity = new Etat();
    $form   = $this->createForm(new EtatType(), $entity);

    return array(
        'entity' => $entity,
        'form'   => $form->createView(),
        );
  }

  /**
   * Finds and displays a Etat entity.
   *
   * @Route("/{id}", name="etat_show")
   * @Method("GET")
   * @Template()
   */
  public function showAction($id)
  {
    $em = $this->getDoctrine()->getManager();

    $entity = $em->getRepository('SansthonProdBundle:Etat')->find($id);

    if (!$entity) {
      throw $this->createNotFoundException('Unable to find Etat entity.');
    }

    $deleteForm = $this->createDeleteForm($id);

    return array(
        'entity'      => $entity,
        'delete_form' => $deleteForm->createView(),
        );
  }

  /**
   * Displays a form to edit an existing Etat entity.
   *
   * @Route("/{id}/edit", name="etat_edit")
   * @Method("GET")
   * @Template()
   */
  public function editAction($id)
  {
    $em = $this->getDoctrine()->getManager();

    $entity = $em->getRepository('SansthonProdBundle:Etat')->find($id);

    if (!$entity) {
      throw $this->createNotFoundException('Unable to find Etat entity.');
    }

    $editForm = $this->createForm(new EtatType(), $entity);
    $deleteForm = $this->createDeleteForm($id);

    return array(
        'entity'      => $entity,
        'edit_form'   => $editForm->createView(),
        'delete_form' => $deleteForm->createView(),
        );
  }

  /**
   * Edits an existing Etat entity.
   *
   * @Route("/{id}", name="etat_update")
   * @Method("PUT")
   * @Template("SansthonProdBundle:Etat:edit.html.twig")
   */
  public function updateAction(Request $request, $id)
  {
    $em = $this->getDoctrine()->getManager();

    $entity = $em->getRepository('SansthonProdBundle:Etat')->find($id);

    if (!$entity) {
      throw $this->createNotFoundException('Unable to find Etat entity.');
    }

    $deleteForm = $this->createDeleteForm($id);
    $editForm = $this->createForm(new EtatType(), $entity);
    $editForm->bind($request);

    if ($editForm->isValid()) {
      $em->persist($entity);
      $em->flush();

      return $this->redirect($this->generateUrl('etat_edit', array('id' => $id)));
    }

    return array(
        'entity'      => $entity,
        'edit_form'   => $editForm->createView(),
        'delete_form' => $deleteForm->createView(),
        );
  }

  /**
   * Deletes a Etat entity.
   *
   * @Route("/{id}", name="etat_delete")
   * @Method("DELETE")
   */
  public function deleteAction(Request $request, $id)
  {
    $form = $this->createDeleteForm($id);
    $form->bind($request);

    if ($form->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $entity = $em->getRepository('SansthonProdBundle:Etat')->find($id);

      if (!$entity) {
        throw $this->createNotFoundException('Unable to find Etat entity.');
      }

      $em->remove($entity);
      $em->flush();
    }

    return $this->redirect($this->generateUrl('etat'));
  }

  /**
   * Creates a form to delete a Etat entity by id.
   *
   * @param mixed $id The entity id
   *
   * @return Symfony\Component\Form\Form The form
   */
  private function createDeleteForm($id)
  {
    return $this->createFormBuilder(array('id' => $id))
      ->add('id', 'hidden')
      ->getForm()
      ;
  }
}
