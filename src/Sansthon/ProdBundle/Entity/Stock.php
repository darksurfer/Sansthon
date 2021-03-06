<?php

namespace Sansthon\ProdBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Stock
 *
 * @ORM\Table(name="stock")
 * @ORM\Entity(repositoryClass="Sansthon\ProdBundle\Entity\StockRepository")
 */
class Stock
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
     * @var integer
     *
     * @ORM\Column(name="value", type="bigint", nullable=true)
     */
    protected $value;

    /**
     * @var \Etape
     *
     * @ORM\ManyToOne(targetEntity="Etape")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="etape_id", referencedColumnName="id")
     * })
     */
    protected $etape;

    /**
     * @var \Type
     *
     * @ORM\ManyToOne(targetEntity="Type")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="type_id", referencedColumnName="id")
     * })
     */
    protected $type;


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
     * Set value
     *
     * @param integer $value
     * @return Stock
     */
    public function setValue($value)
    {
        $this->value = $value;
    
        return $this;
    }

    /**
     * Get value
     *
     * @return integer 
     */
    public function getValue()
    {
        return $this->value;
    }
     /**
     * Add to value
     *
     * @param integer $value
     * @return Stock
     */
    public function addValue($avalue){
        $this->value +=$avalue;
        return $this;
    }
    /**
     * substract to value
     *
     * @param integer $value
     * @return Stock
     */
    public function subValue($svalue){
        $this->value -=$svalue;
        return $this;
    }

    /**
     * Set etape
     *
     * @param \Sansthon\ProdBundle\Entity\Etape $etape
     * @return Stock
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
     * Set type
     *
     * @param \Sansthon\ProdBundle\Entity\Type $type
     * @return Stock
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
}
