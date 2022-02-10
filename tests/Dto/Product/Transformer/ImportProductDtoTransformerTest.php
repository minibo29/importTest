<?php

namespace Dto\Product\Transformer;

use App\Dto\Product\ImportProductDto;
use App\Dto\Product\Transformer\ImportProductDtoTransformer;
use App\Entity\ProductData;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ImportProductDtoTransformerTest extends KernelTestCase
{
    private ImportProductDtoTransformer $importProductDtoTransformer;

    /** @test */
    public function it_has_to_return_dto_object()
    {
        // Set Up
        $productData = (new ProductData())
            ->setProductName('TV Simple 55"')
            ->setProductDesc('Smart TV')
            ->setProductCode('C33342')
            ->setStock(34)
            ->setCost(120)
        ;

        // Do something
        $dto = $this->importProductDtoTransformer->transformToDto($productData);

        // Make assertions
        $this->assertObjectHasAttribute('productName', $dto, 'TV Simple 55"');
        $this->assertObjectHasAttribute('productDesc', $dto, 'Smart TV');
        $this->assertObjectHasAttribute('productCode', $dto, 'C33342');
        $this->assertObjectHasAttribute('stock', $dto, 34);
        $this->assertObjectHasAttribute('cost', $dto, 120);
    }


    /** @test */
    public function it_has_to_return_entity_object()
    {
        // Set Up
        $dto = new ImportProductDto();
        $dto->productName = 'TV Simple 55"';
        $dto->productDesc = 'Smart TV';
        $dto->productCode = 'C33342';
        $dto->stock = 34;
        $dto->cost = 120;

        // Do something
        $entity = $this->importProductDtoTransformer->transformToEntity($dto);

        // Make assertions
        $this->assertEquals('TV Simple 55"', $entity->getProductName());
        $this->assertEquals('Smart TV', $entity->getProductDesc());
        $this->assertEquals('C33342', $entity->getProductCode());
        $this->assertEquals(34, $entity->getStock());
        $this->assertEquals(120, $entity->getCost());
        $this->assertEquals(null, $entity->getDiscontinued());
    }


    protected function setUp(): void
    {
        $this->importProductDtoTransformer = new ImportProductDtoTransformer();
    }

}