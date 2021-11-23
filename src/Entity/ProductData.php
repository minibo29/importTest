<?php

namespace App\Entity;

use App\Repository\ProductDataRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Table;

/**
 * @ORM\Entity(repositoryClass=ProductDataRepository::class)
 * @Table(name="tblProductData")
 */
class ProductData
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue
     * @ORM\Column(name="intProductDataId", type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="strProductName", type="string", length=50)
     */
    private $productName;

    /**
     * @ORM\Column(name="strProductDesc", type="string", length=255)
     */
    private $productDesc;

    /**
     * @ORM\Column(name="strProductCode", type="string", length=10)
     */
    private $productCode;

    /**
     * @ORM\Column(name="dtmAdded", type="datetime")
     */
    private $added;

    /**
     * @ORM\Column(name="dtmDiscontinued", type="datetime")
     */
    private $discontinued;

    /**
     * @ORM\Column(name="stmTimestamp", type="time", columnDefinition="DATETIME DEFAULT CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP")
     */
    private $timestamp;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStrProductName()
    {
        return $this->productName;
    }

    public function setProductName($productName): ProductData
    {
        $this->productName = $productName;
        return $this;
    }

    public function getProductDesc()
    {
        return $this->productDesc;
    }

    public function setProductDesc($productDesc): ProductData
    {
        $this->productDesc = $productDesc;
        return $this;
    }

    public function getProductCode()
    {
        return $this->productCode;
    }

    public function setProductCode($productCode): ProductData
    {
        $this->productCode = $productCode;
        return $this;
    }

    public function getAdded()
    {
        return $this->added;
    }

    public function setAdded($added): ProductData
    {
        $this->added = $added;
        return $this;
    }

    public function getDiscontinued()
    {
        return $this->discontinued;
    }

    public function setDiscontinued($discontinued)
    {
        $this->discontinued = $discontinued;
        return $this;
    }

    public function getTimestamp()
    {
        return $this->timestamp;
    }
}
