<?php
namespace App\Service\Product;

use App\Dto\Product\ImportProductDto;
use App\Dto\Product\ImportProductHeaderDto;
use App\Dto\Product\Transformer\ImportProductDtoTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ImportProductsHelper
{
    private EntityManagerInterface $entityManager;
    private ValidatorInterface $validator;
    private ImportProductDtoTransformer $importProductDtoTransformer;
    private LoggerInterface $logger;

    private int $importedProductsCount = 0;
    private int $skippedProductsCount = 0;

    /**
     * @param EntityManagerInterface $entityManager
     * @param ValidatorInterface $validator
     * @param ImportProductDtoTransformer $importProductDtoTransformer
     * @param LoggerInterface $logger
     */
    public function __construct(EntityManagerInterface $entityManager,
                                ValidatorInterface $validator,
                                ImportProductDtoTransformer $importProductDtoTransformer,
                                LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->importProductDtoTransformer = $importProductDtoTransformer;
    }

    /**
     * @param iterable $csvData import data.
     * @param ImportProductHeaderDto $headers list of header
     *
     */
    public function importProducts(iterable $csvData, ImportProductHeaderDto $headers)
    {
        foreach ($csvData as $product) {
            $productDto = new ImportProductDto();
            $productDto->productCode = $product[$headers->productCode];
            $productDto->productName = $product[$headers->productName];
            $productDto->productDesc = $product[$headers->productDesc];
            $productDto->cost = $product[$headers->cost];
            $productDto->stock = $product[$headers->stock];
            $productDto->discontinued = $product[$headers->discontinued] ? new \DateTime("now") : null;

            $errors = $this->validator->validate($productDto);
            if (count($errors) > 0) {
                $this->logErrors($product, $errors);
                $this->skippedProductsCount++;
            } else {
                $this->entityManager->persist($this->importProductDtoTransformer->transformToEntity($productDto));
                $this->importedProductsCount++;
            }
        }

        $this->entityManager->flush();
    }

    public function convertCurrency()
    {
        // #Todo
    }

    /**
     * @return int
     */
    public function getImportedProductsCount(): int
    {
        return $this->importedProductsCount;
    }

    /**
     * @return int
     */
    public function getSkippedProductsCount(): int
    {
        return $this->skippedProductsCount;
    }

    /**
     * @param $product
     * @param ConstraintViolationList $errors
     * @return void
     */
    private function logErrors($product, ConstraintViolationList $errors): void
    {
        foreach ($errors as $error) {
            $this->logger->error($error->getPropertyPath() . ': ' .  $error->getMessage(), $product);
        }
    }
}