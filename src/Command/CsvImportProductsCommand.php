<?php
namespace App\Command;

use App\Entity\ProductData;
use Doctrine\ORM\EntityManagerInterface;
use ImportProductsHelper;
use League\Csv\Exception;
use League\Csv\Reader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class CsvImportProductsCommand extends Command
{

    private EntityManagerInterface $entityManager;
    private ValidatorInterface $validator;
    private ImportProductsHelper $importProductsHelper;

    private int $batchingItemsCount = 1000;

    /**
     * @param EntityManagerInterface $entityManager
     * @param ValidatorInterface $validator
     */
    public function __construct(EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->importProductsHelper = new ImportProductsHelper($entityManager, $validator);
    }

    protected function configure(): void
    {
        $this->setName('csv:import-products')
            ->setDescription('Import Products from file.')
            ->addArgument('path', InputArgument::REQUIRED, 'Path to import file.')
        ;
    }

    public function determineHeader(array $header, InputInterface $input, OutputInterface $output): array
    {

        $formatter = $this->getHelper('formatter');

        $formattedLine = $formatter->formatSection(
            'Determine Header naming',
            'System Determined [' . implode( ', ', $header) . '] columns' . "\n\r"
            . 'PLeas map correct columns.' . "\n\r"
        );
        $output->writeln($formattedLine);

        $helper = $this->getHelper('question');
        $determineHeaders = [];

        $labels = [
            'product_code' => 'Product Code',
            'product_name' => 'Product Name',
            'product_description' => 'Product Description',
            'stock' => 'Stock',
            'cost' => 'Cost',
            'discontinued' => 'Discontinued',
        ];

        foreach ($labels as $key => $label) {
            $question = new Question('Please determine a "' . $label . '": ');
            $question->setAutocompleterValues($header);
            $determineHeaders[$key] = $helper->ask($input, $output, $question);
        }

        return $determineHeaders;
    }

    /**
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $path = $input->getArgument('path');

        if (!file_exists($path)) {
            $io->error('File not exist: ' . $path);
            return Command::INVALID;
        }

        $reader = Reader::createFromPath($path)
            ->skipEmptyRecords()
        ;

        $csvData = $reader->setHeaderOffset(0);
        $headers = $this->determineHeader($csvData->getHeader(), $input, $output);

        $io->title('Start to Import products!!!');
        $io->progressStart(iterator_count($csvData));

        foreach ($csvData as $product) {
            $this->importProductsHelper->importProduct($product, $headers);

            $io->progressAdvance($this->batchingItemsCount);
        }

        $io->progressFinish();

        $io->listing([
            printf('Skipped %s Stocks.', 0),
            printf('Imported %s Stocks.', 0),
        ]);

        return Command::SUCCESS;
    }

    public function isFailValid($pathToFile)
    {
        # todo validating a csv file
    }

}