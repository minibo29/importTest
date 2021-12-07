<?php
namespace App\Command;

use App\Dto\Product\ImportProductHeaderDto;
use App\Service\Product\ImportProductsHelper;
use League\Csv\Exception;
use League\Csv\Reader;
use League\Csv\Statement;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;


class CsvImportProductsCommand extends Command
{

    private ImportProductsHelper $importProductsHelper;

    private int $batchingItemsCount = 1000;

    /**
     * @param ImportProductsHelper $importProductsHelper
     */
    public function __construct(ImportProductsHelper $importProductsHelper)
    {
        parent::__construct();
        $this->importProductsHelper = $importProductsHelper;
    }

    protected function configure(): void
    {
        $this->setName('csv:import-products')
            ->setDescription('Import Products from file.')
            ->addArgument('path', InputArgument::REQUIRED, 'Path to import file.')
        ;
    }

    public function determineHeader(array $header, InputInterface $input, OutputInterface $output): ImportProductHeaderDto
    {
        $importProductHeaderDto = new ImportProductHeaderDto();
        $formatter = $this->getHelper('formatter');

        $formattedLine = $formatter->formatSection(
            'Determine Header naming',
            'System Determined [' . implode( ', ', $header) . '] columns' . "\n\r"
            . 'PLeas map correct columns.' . "\n\r"
        );
        $output->writeln($formattedLine);

        $helper = $this->getHelper('question');

        $labels = [
            'productCode' => 'Product Code',
            'productName' => 'Product Name',
            'productDesc' => 'Product Description',
            'stock' => 'Stock',
            'cost' => 'Cost',
            'discontinued' => 'Discontinued',
        ];

        foreach ($labels as $key => $label) {
            $question = new Question('Please determine a "' . $label . '": ');
            $question->setAutocompleterValues($header);
            $importProductHeaderDto->{$key} = $helper->ask($input, $output, $question);
        }

        return $importProductHeaderDto;
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
        $productsCount = iterator_count($csvData);

        $io->title('Start to Import products!!!');
        $io->progressStart($productsCount);

        $offset = 0;
        $limit = $this->batchingItemsCount;

        while ($productsCount > $offset) {
            $stmt = Statement::create()
                ->offset($offset)
                ->limit($limit)
            ;
            $products = $stmt->process($csvData);
            $this->importProductsHelper->importProducts($products, $headers);

            $io->progressAdvance(iterator_count($products));
            $offset += $limit;
        }

        $io->progressFinish();

        $io->listing([
            printf('Skipped %s Stocks.', $this->importProductsHelper->getSkippedProductsCount()),
            printf('Imported %s Stocks.', $this->importProductsHelper->getImportedProductsCount()),
        ]);

        return Command::SUCCESS;
    }

    public function isFailValid($pathToFile)
    {
        # todo validating a csv file
    }

}