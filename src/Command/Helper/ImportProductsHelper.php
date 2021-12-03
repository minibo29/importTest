<?php

use App\Entity\ProductData;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ImportProductsHelper
{
    private EntityManagerInterface $entityManager;
    private ValidatorInterface $validator;

    /**
     * @param EntityManagerInterface $entityManager
     * @param ValidatorInterface $validator
     */
    public function __construct(EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {

        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }

    /**
     * @param array $csvData import data.
     * @param array $headers list of header
     *
     * @return array
     */
    public function importProduct(array $csvData, array $headers): array
    {
        $imported = 0;
        $skippedStocks = [];

        foreach ($csvData as $product) {

            $productData = (new ProductData())
                ->setProductCode($product[$headers['product_code']])
                ->setProductName($product[$headers['product_name']])
                ->setProductDesc($product[$headers['product_description']])
                ->setStock($product[$headers['stock']])
                ->setCost($product[$headers['cost']])
                ->setDiscontinued($product[$headers['discontinued']] ?
                    (new \DateTime("now")) : null)
            ;

            /* Check if Entity is valid */
            $errors = $this->validator->validate($productData);
            if (count($errors) > 0) {
                $skippedStocks[] = [
                    'errors' => $errors,
                    'product' => $product,
                ];
            } else {
                $this->entityManager->persist($productData);
                $imported++;
            }

        }

        $this->entityManager->flush();

        return [
            $imported,
            $skippedStocks
        ];
    }

    public function validateProduct($product)
    {
        // #Todo implement validate a product logic
    }

    public function convertCurrency()
    {
        // #Todo 
    }
}