<?php
namespace App\Command;

use App\Entity\ProductData;
use Doctrine\ORM\EntityManagerInterface;
use League\Csv\Exception;
use League\Csv\Reader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class CsvImportProductsCommand extends Command
{

    private EntityManagerInterface $entityManager;
    private ValidatorInterface $validator;


    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }

    protected function configure(): void
    {
        $this->setName('csv:import-products')
            ->setDescription('Import Products from file.')
            ->addArgument('path', InputArgument::REQUIRED, 'Path to import file.')
        ;
    }

    /**
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $path = $input->getArgument('path');
        $io = new SymfonyStyle($input, $output);
        $io->title('Start to Import products!!!');
        $skippedStocks = [];
        $imported = 0;

        if (!file_exists($path)) {
            $io->error('File not exist: ' . $path);
            return Command::INVALID;
        }

        $reader = Reader::createFromPath($path)
            ->skipEmptyRecords()
        ;

//        $io->definitionList($reader->getPathname());
//        $io->definitionList(...get_class_methods($reader));

        $csvData = $reader->setHeaderOffset(0);

        $io->progressStart(iterator_count($csvData));

        foreach ($csvData as $product) {
            list($productCode, $productName, $productDesc, $stock, $cost, $discontinued) = array_values($product);
            $productData = (new ProductData())
                ->setProductCode($productCode)
                ->setProductName($productName)
                ->setProductDesc($productDesc)
                ->setStock($stock)
                ->setCost($cost)
                ->setDiscontinued($discontinued ?
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

            $io->progressAdvance();
        }

        $this->entityManager->flush();

        $io->progressFinish();

        $io->listing([
            'Skipped ' . count($skippedStocks) . ' Stocks.',
            'Imported ' . $imported . ' Stocks.',
        ]);

        return Command::SUCCESS;
    }

    public function isFailValid($pathToFile)
    {
        
    }

}