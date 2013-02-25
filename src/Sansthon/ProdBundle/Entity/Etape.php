<?php

namespace Sansthon\ProdBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Etape
 *
 * @ORM\Table(name="etape")
 * @ORM\Entity(repositoryClass="Sansthon\ProdBundle\Entity\EtapeRepository")
 */
class Etape
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
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=15, nullable=true)
     */
    private $nom;

    /**
     * @var boolean
     *
     * @ORM\Column(name="initiale", type="boolean", nullable=true)
     */
    private $initiale;

    /**
     * @var boolean
     *
     * @ORM\Column(name="final", type="boolean", nullable=true)
     */
    private $finale;

    /**
     * @var boolean
     *
     * @ORM\Column(name="display_order", type="integer", nullable=true)
     */
    private $order;


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
     * Set nom
     *
     * @param string $nom
     * @return Etape
     */
    public function setNom($nom)
    {
        $this->nom = $nom;
    
        return $this;
    }

    /**
     * Get nom
     *
     * @return string 
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set initiale
     *
     * @param boolean $initiale
     * @return Etape
     */
    public function setInitiale($initiale)
    {
        $this->initiale = $initiale;
    
        return $this;
    }

    /**
     * Get initiale
     *
     * @return boolean 
     */
    public function getInitiale()
    {
        return $this->initiale;
    }

    /**
     * Set final
     *
     * @param boolean $final
     * @return Etape
     */
    public function setFinal($final)
    {
        $this->final = $final;
    
        return $this;
    }

    /**
     * Get final
     *
     * @return boolean 
     */
    public function getFinal()
    {
        return $this->final;
    }

    /**
     * Set order
     *
     * @param integer $order
     * @return Etape
     */
    public function setOrder($order)
    {
        $this->order = $order;
    
        return $this;
    }

    /**
     * Get order
     *
     * @return integer 
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Set finale
     *
     * @param boolean $finale
     * @return Etape
     */
    public function setFinale($finale)
    {
        $this->finale = $finale;

        return $this;
    }

    /**
     * Get finale
     *
     * @return boolean 
     */
    public function getFinale()
    {
        return $this->finale;
    }
}
