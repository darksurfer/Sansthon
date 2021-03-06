<?php

namespace Sansthon\ProdBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Sansthon\ProdBundle\Entity\Stock;
/**
 * StockRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class StockRepository extends EntityRepository
{

  public function getByEtapeAndType($etape,$type){
    $stock= $this->findOneBy(
      array('etape' => $etape, 'type' => $type)
    );   
    if(!$stock){
      $stock = new Stock();
      $stock->setEtape($etape);
      $stock->setType($type);
      $stock->setValue(0);
      $this->_em->persist($stock);
      $this->_em->flush();
    }
    return $stock;
  }
  
  public function getByEtat($etat){
    return $this->getByEtapeAndType($etat->getEtape(),$etat->getType());
  }
  public function getByType($type){
    $query = $this->_em
        ->createQuery('
            SELECT s, e FROM SansthonProdBundle:Stock s
            JOIN s.etape e
            WHERE s.type = :id
            ORDER BY e.displayorder'
        )->setParameter('id', $type->getId());
    $stocks =$query->getResult();
    $etapes = $this->_em->getRepository('SansthonProdBundle:Etape')->findAll();

    if(count($stocks) != count($etapes)){
      $stocks = array();
      foreach($etapes as $etape){
        array_push($stocks,$this->getbyEtapeAndType($etape,$type));
      }
    }
    return $stocks;
  }
  public function getAllEvenArray(){
    $tabStock=array();
    $query = $this->_em
        ->createQuery('
            SELECT s.value,e.id as etape ,t.id as type FROM SansthonProdBundle:Stock s
            JOIN s.etape e
            JOIN s.type t
            WHERE MOD(e.displayorder,2) = 0
            ORDER BY t.reference,e.displayorder'
        );
    $stocks =$query->getArrayResult();
    foreach($stocks as $stock){
        if(!array_key_exists($stock['type'], $tabStock))
        {
            $tabStock[$stock['type']]=array();
        }
        $tabStock[$stock['type']][$stock["etape"]] = $stock['value'];
        
    }
      unset($stocks);
      return $tabStock;
  }
}
