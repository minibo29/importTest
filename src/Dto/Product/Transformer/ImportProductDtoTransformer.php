<?php

namespace App\Dto\Product\Transformer;

use App\Dto\Product\ImportProductDto;
use App\Entity\ProductData;

class ImportProductDtoTransformer extends AbstractDtoTransformer
{

    /**
     * @param ImportProductDto $object
     * @return ProductData
     */
    public function transformToEntity(ImportProductDto $object): ProductData
    {
        return (new ProductData())
            ->setProductCode($object->productCode)
            ->setProductName($object->productName)
            ->setProductDesc($object->productDesc)
            ->setStock($object->stock)
            ->setCost($object->cost)
            ->setDiscontinued($object->discontinued)
        ;
    }

    /**
     * @param ProductData $entity
     * @return ImportProductDto
     */
    public function transformToDto($entity): ImportProductDto
    {
        $dto = new ImportProductDto();
        $dto->productCode = $entity->getProductCode();
        $dto->productName = $entity->getProductName();
        $dto->productDesc = $entity->getProductDesc();
        $dto->stock = $entity->getStock();
        $dto->cost = $entity->getCost();
        $dto->discontinued = $entity->getDiscontinued();

        return $dto;
    }

}