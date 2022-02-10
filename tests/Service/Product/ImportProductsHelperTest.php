<?php

namespace Service\Product;

use App\Dto\Product\ImportProductHeaderDto;
use App\Dto\Product\Transformer\ImportProductDtoTransformer;
use App\Service\Product\ImportProductsHelper;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class ImportProductsHelperTest extends KernelTestCase
{
    private EntityManagerInterface|MockObject $entityManager;
    private ValidatorInterface|MockObject $validator;
    private ImportProductDtoTransformer|MockObject $importProductDtoTransformer;
    private LoggerInterface|MockObject $logger;
    private ImportProductsHelper $importProductsHelper;

    /**
     * @dataProvider productsProvider
     * @test
     */
    public function it_has_to_import_product(array $products, $importProductHeaderDto)
    {
        // Set Up
        $this->validator->expects($this->any())
            ->method('validate')
            ->willReturn([]);
        $this->entityManager->expects($this->any())
            ->method('persist');
        $this->entityManager->expects($this->once())
            ->method('flush');
        $this->importProductDtoTransformer->expects($this->any())
            ->method('transformToEntity');

        // Do something
        $this->importProductsHelper->importProducts($products, $importProductHeaderDto);

        // Make assertions
    }

    public function productsProvider(): array
    {
        $importProductHeaderDto = new ImportProductHeaderDto();
        $importProductHeaderDto->productName = 'ProductName';
        $importProductHeaderDto->productDesc = 'ProductDesc';
        $importProductHeaderDto->productCode = 'ProductCode';
        $importProductHeaderDto->stock = 'Stock';
        $importProductHeaderDto->cost = 'Cost';
        $importProductHeaderDto->discontinued = 'Discontinued';

        return [
            [[[
                'ProductCode' => 'P0001',
                'ProductName' => 'TV',
                'ProductDesc' => '32” Tv',
                'Stock' => 102,
                'Cost' => 399.99,
                'Discontinued' => 'Yes',
            ],[
                'ProductCode' => 'P0002',
                'ProductName' => 'TV',
                'ProductDesc' => '44” Tv',
                'Stock' => 18,
                'Cost' => 399.99,
                'Discontinued' => 'No',
            ],[
                'ProductCode' => 'P0003',
                'ProductName' => 'TV',
                'ProductDesc' => '50” Tv',
                'Stock' => 22,
                'Cost' => 399.99,
                'Discontinued' => 'Yes',
            ],[
                'ProductCode' => 'P0004',
                'ProductName' => 'TV',
                'ProductDesc' => '55” Tv',
                'Stock' => 43,
                'Cost' => 399.99,
                'Discontinued' => '',
            ],[
                'ProductCode' => 'P0005',
                'ProductName' => 'TV',
                'ProductDesc' => '24” Tv',
                'Stock' => 25,
                'Cost' => 399.99,
                'Discontinued' => '',
            ],[
                'ProductCode' => 'P0006',
                'ProductName' => 'TV',
                'ProductDesc' => '27” Tv',
                'Stock' => 11,
                'Cost' => 399.99,
                'Discontinued' => null,
            ]], $importProductHeaderDto],
        ];
    }

    /** @test */
    public function it_has_to_throw_fatal_error()
    {
        // Set Up

        // Do something

        // Make assertions
    }

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->validator = $this->createMock(ValidatorInterface::class);
        $this->importProductDtoTransformer = $this->createMock(ImportProductDtoTransformer::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->importProductsHelper = new ImportProductsHelper(
            $this->entityManager,
            $this->validator,
            $this->importProductDtoTransformer,
            $this->logger
        );
    }

}