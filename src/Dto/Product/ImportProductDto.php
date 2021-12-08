<?php
namespace App\Dto\Product;

use JMS\Serializer\Annotation as Serializing;
use Symfony\Component\Validator\Constraints as Assert;


class ImportProductDto
{
    /**
     * @Assert\NotBlank
     * @Serializing\Type("sring")
     * @var $productCode string
     */
    public $productCode;

    /**
     * @Assert\NotBlank
     * @Serializing\Type("sring")
     * @var $productName string
     */
    public $productName;

    /**
     * @Assert\NotBlank
     * @Serializing\Type("sring")
     * @var $productDesc string
     */
    public $productDesc;

    /**
     * @Assert\NotBlank
     * @Assert\GreaterThan(10)
     * @Serializing\Type("integer")
     * @var $stock integer
     */
    public $stock;

    /**
     * @Assert\GreaterThan(5)
     * @Assert\LessThan(1000)
     * @Serializing\Type("float")
     * @var $cost float
     */
    public $cost;

    /**
     * @Serializing\Type("DateTime<Y-m-d\TH:I:S>")
     * @var $discontinued ?|\DateTime
     */
    public $discontinued;
}