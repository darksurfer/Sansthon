<?php

namespace Sansthon\ProdBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Etat
 *
 * @ORM\Table(name="etat")
 * @ORM\Entity(repositoryClass="Sansthon\ProdBundle\Entity\EtatRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Etat
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="debut", type="datetime", nullable=true)
     */
    private $debut;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="prevue", type="datetime", nullable=true)
     */
     
     
    private $prevue;
   
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fin", type="datetime", nullable=true)
     */
     
     
    private $fin;

    /**
     * @var boolean
     *
     * @ORM\Column(name="stocked", type="boolean", nullable=true)
     */
    private $stocked;

    /**
     * @var integer
     *
     * @ORM\Column(name="quantite", type="integer", nullable=true)
     */
    private $quantite;

    /**
     * @var string
     *
     * @ORM\Column(name="commentaire", type="text", nullable=true)
     */
    private $commentaire;

    /**
     * @var \Etape
     *
     * @ORM\ManyToOne(targetEntity="Etape")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="etape_id", referencedColumnName="id")
     * })
     */
    private $etape;

    /**
     * @var \Etape
     *
     * @ORM\ManyToOne(targetEntity="Etape")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="origine_etape_id", referencedColumnName="id")
     * })
     */
    private $etapeorigine;

    
    /**
     * @var \Personne
     *
     * @ORM\ManyToOne(targetEntity="Personne")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="personne_id", referencedColumnName="id")
     * })
     */
    private $personne;

    /**
     * @var \Type
     *
     * @ORM\ManyToOne(targetEntity="Type")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="type_id", referencedColumnName="id")
     * })
     */
    private $type;



    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set debut
     *
     * @param \DateTime $debut
     * @return Etat
     */
    public function setDebut($debut)
    {
        $this->debut = $debut;
    
        return $this;
    }

    /**
     * Get debut
     *
     * @return \DateTime 
     */
    public function getDebut()
    {
        return $this->debut;
    }

    /**
     * Set fin
     *
     * @param \DateTime $fin
     * @return Etat
     */
    public function setFin($fin)
    {
        $this->fin = $fin;
    
        return $this;
    }

    /**
     * Get fin
     *
     * @return \DateTime 
     */
    public function getFin()
    {
        return $this->fin;
    }

    /**
     * Set stocked
     *
     * @param boolean $stocked
     * @return Etat
     */
    public function setStocked($stocked)
    {
        $this->stocked = $stocked;
    
        return $this;
    }

    /**
     * Get stocked
     *
     * @return boolean 
     */
    public function getStocked()
    {
        return $this->stocked;
    }

    /**
     * Set quantite
     *
     * @param integer $quantite
     * @return Etat
     */
    public function setQuantite($quantite)
    {
        $this->quantite = $quantite;
    
        return $this;
    }

    /**
     * Get quantite
     *
     * @return integer 
     */
    public function getQuantite()
    {
        return $this->quantite;
    }

    /**
     * Set commentaire
     *
     * @param string $commentaire
     * @return Etat
     */
    public function setCommentaire($commentaire)
    {
        $this->commentaire = $commentaire;
    
        return $this;
    }

    /**
     * Get commentaire
     *
     * @return string 
     */
    public function getCommentaire()
    {
        return $this->commentaire;
    }

    /**
     * Set etape
     *
     * @param \Sansthon\ProdBundle\Entity\Etape $etape
     * @return Etat
     */
    public function setEtape(\Sansthon\ProdBundle\Entity\Etape $etape = null)
    {
        $this->etape = $etape;
    
        return $this;
    }

    /**
     * Get etape
     *
     * @return \Sansthon\ProdBundle\Entity\Etape 
     */
    public function getEtape()
    {
        return $this->etape;
    }

    /**
     * Set personne
     *
     * @param \Sansthon\ProdBundle\Entity\Personne $personne
     * @return Etat
     */
    public function setPersonne(\Sansthon\ProdBundle\Entity\Personne $personne = null)
    {
        $this->personne = $personne;
    
        return $this;
    }

    /**
     * Get personne
     *
     * @return \Sansthon\ProdBundle\Entity\Personne 
     */
    public function getPersonne()
    {
        return $this->personne;
    }

    /**
     * Set type
     *
     * @param \Sansthon\ProdBundle\Entity\Type $type
     * @return Etat
     */
    public function setType(\Sansthon\ProdBundle\Entity\Type $type = null)
    {
        $this->type = $type;
    
        return $this;
    }

    /**
     * Get type
     *
     * @return \Sansthon\ProdBundle\Entity\Type 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set prevue
     *
     * @param \DateTime $prevue
     * @return Etat
     */
    public function setPrevue($prevue)
    {
        $this->prevue = $prevue;
    
        return $this;
    }

    /**
     * Get prevue
     *
     * @return \DateTime 
     */
    public function getPrevue()
    {
        return $this->prevue;
    }

    /**
     * Set etapeorigine
     *
     * @param \Sansthon\ProdBundle\Entity\Etape $etapeorigine
     * @return Etat
     */
    public function setEtapeorigine(\Sansthon\ProdBundle\Entity\Etape $etapeorigine = null)
    {
        $this->etapeorigine = $etapeorigine;
    
        return $this;
    }

    /**
     * Get etapeorigine
     *
     * @return \Sansthon\ProdBundle\Entity\Etape 
     */
    public function getEtapeorigine()
    {
        return $this->etapeorigine;
    }
    /**
    * Preinsert methode
    * 
    * @ORM\PrePersist
    */
    public function prePersist(){
      $this->setDebut(new \Datetime("now"));
      $this->stocked=false;
    }
    /*
    *
    * Finish this etat
    *
    */
    public function finish($stocked=true){
      $this->setFin(new \Datetime());
      $this->setStocked($stocked);
      return $this;
    }
}
