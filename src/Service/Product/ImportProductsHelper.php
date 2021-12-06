<?php
namespace App\Service\Product;

use App\Dto\Product\ImportProductDto;
use App\Dto\Product\Transformer\ImportProductDtoTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ImportProductsHelper
{
    private EntityManagerInterface $entityManager;
    private ValidatorInterface $validator;
    private ImportProductDtoTransformer $importProductDtoTransformer;

    /**
     * @param EntityManagerInterface $entityManager
     * @param ValidatorInterface $validator
     */
    public function __construct(EntityManagerInterface $entityManager, ValidatorInterface $validator, ImportProductDtoTransformer $importProductDtoTransformer)
    {

        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->importProductDtoTransformer = $importProductDtoTransformer;
    }

    /**
     * @param array $csvData import data.
     * @param array $headers list of header
     *
     * @return array
     */
    public function importProducts(iterable $csvData, array $headers): array
    {
        $imported = 0;
        $skippedStocks = [];

        foreach ($csvData as $product) {
            $productDto = new ImportProductDto();
            $productDto->productCode = $product[$headers['product_code']];
            $productDto->productName = $product[$headers['product_code']];
            $productDto->productDesc = $product[$headers['product_description']];
            $productDto->cost = $product[$headers['cost']];
            $productDto->stock = $product[$headers['stock']];
            $productDto->discontinued = $product[$headers['discontinued']] ? new \DateTime("now") : null;

            $errors = $this->validator->validate($productDto);
            if (count($errors) > 0) {
                $skippedStocks[] = [
                    'errors' => $errors,
                    'product' => $product,
                ];
            } else {
                $this->entityManager->persist($this->importProductDtoTransformer->transformToEntity($productDto));
                $imported++;
            }

        }

        $this->entityManager->flush();

        return [
            $imported,
            $skippedStocks
        ];
    }

    public function convertCurrency()
    {
        // #Todo
    }
}