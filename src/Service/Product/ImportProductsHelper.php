<?php
namespace App\Service\Product;

use App\Dto\Product\ImportProductDto;
use App\Dto\Product\ImportProductHeaderDto;
use App\Dto\Product\Transformer\ImportProductDtoTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
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
    private array $importCurrency;
    private bool $transactionOpened;

    /**
     * @var ContainerBagInterface
     */
    private $params;

    /**
     * @param EntityManagerInterface $entityManager
     * @param ValidatorInterface $validator
     * @param ImportProductDtoTransformer $importProductDtoTransformer
     * @param LoggerInterface $logger
     * @param ContainerBagInterface $params
     */
    public function __construct(EntityManagerInterface $entityManager,
                                ValidatorInterface $validator,
                                ImportProductDtoTransformer $importProductDtoTransformer,
                                LoggerInterface $logger,
                                ContainerBagInterface $params
    )
    {
        $this->logger = $logger;
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->importProductDtoTransformer = $importProductDtoTransformer;
        $this->params = $params;
        $this->setImportCurrency($this->params->get('app.currency.default'));
    }

    /**
     * @param iterable $csvData import data.
     * @param ImportProductHeaderDto $headers list of header
     *
     * @throws \Exception
     */
    public function importProducts(iterable $csvData, ImportProductHeaderDto $headers): void
    {
        foreach ($csvData as $product) {
            $productDto = new ImportProductDto();
            $productDto->productCode = $product[$headers->productCode];
            $productDto->productName = $product[$headers->productName];
            $productDto->productDesc = $product[$headers->productDesc];
            $productDto->cost = $this->convertCurrency($product[$headers->cost]);
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

        try {
            $this->entityManager->flush();
        } catch (\Exception $e) {
            $this->rollBackTransaction();
            $this->logger->info($e->getMessage());
            $errorMessage = sprintf('Products cannot be imported. Please check a log file for more information(%s).',
                $this->logger->popHandler()->getUrl());

            throw new \Exception($errorMessage);
        }
    }

    public function startTransaction()
    {
        $this->entityManager->beginTransaction();
        $this->transactionOpened = true;
    }

    public function commitTransaction()
    {
        if ($this->transactionOpened) {
            $this->entityManager->commit();
            $this->transactionOpened = false;
        }
    }

    public function rollBackTransaction()
    {
        if ($this->transactionOpened) {
            $this->entityManager->rollBack();
            $this->transactionOpened = false;
        }
    }

    /**
     * @param $value
     * @return float
     */
    public function convertCurrency($value): float
    {
        preg_match('/\A(\$|€|£)?(\d+[.]?\d{0,2})\z/', $value, $matchedCurrency);
        if (empty($matchedCurrency)) {
            return 0;
        }
        $value = (float) $matchedCurrency[2];

        $currency = $this->importCurrency;

        if (!empty($matchedCurrency[1])) {
            $currency = $this->getCurrencyBySymbol($matchedCurrency[1]) ?: $currency;
        }
        return (double) ((float) $value * (float) $currency['rate'] );
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
            $this->logger->info($error->getPropertyPath() . ': ' .  $error->getMessage(), $product);
        }
    }

    public function getImportCurrency(): array
    {
        return $this->importCurrency;
    }

    /**
     * @param string $currency
     * @return void
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function setImportCurrency(string $currency)
    {
        $currencies = $this->params->get('app.currencies');
        if (empty($currencies[$currency])) {
            throw new \RuntimeException('Currency not exist in system!!!');
        }

        $this->importCurrency = $currencies[$currency];
    }

    public function getCurrencyBySymbol($symbol)
    {
        $hash = md5($symbol);
        static $staticCurrency;
        if (isset($staticCurrency[$hash])) {
            return $staticCurrency[$hash];
        }

        $currencies = $this->params->get('app.currencies');
        foreach ($currencies as $currency) {
            if ($currency['symbol'] === $symbol) {
                return $staticCurrency[$hash] = $currency;
            }
        }

        return null;
    }
}