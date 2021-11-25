<?php

namespace App\Entity;

use App\Repository\ProductDataRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ProductDataRepository::class)
 * @Table(name="tblProductData",
 *     uniqueConstraints={@UniqueConstraint(name="product_code",
 *     columns={"strProductCode"})})
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
     * @Assert\NotBlank
     */
    private $productName;

    /**
     * @ORM\Column(name="strProductDesc", type="string", length=255)
     * @Assert\NotBlank
     */
    private $productDesc;

    /**
     * @ORM\Column(name="strProductCode", type="string", length=10)
     * @Assert\NotBlank
     */
    private $productCode;

    /**
     * @ORM\Column(name="dtmAdded", type="datetime", nullable=true)
     */
    private $added;

    /**
     * @ORM\Column(name="dtmDiscontinued", type="datetime", nullable=true)
     */
    private $discontinued;

    /**
     * @ORM\Column(name="stmTimestamp", type="datetime", columnDefinition="DATETIME DEFAULT CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP")
     */
    private $timestamp;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProductName()
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

    public function setDiscontinued($discontinued): ProductData
    {
        $this->discontinued = $discontinued;
        return $this;
    }

    public function getTimestamp()
    {
        return $this->timestamp;
    }
}
