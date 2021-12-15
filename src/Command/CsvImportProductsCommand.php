<?php
namespace App\Command;

use App\Dto\Product\ImportProductHeaderDto;
use App\Service\Product\ImportProductsHelper;
use League\Csv\Reader;
use League\Csv\Statement;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;


class CsvImportProductsCommand extends Command
{

    private ImportProductsHelper $importProductsHelper;

    private int $batchingItemsCount = 1000;
    private ContainerBagInterface $params;

    /**
     * @param ImportProductsHelper $importProductsHelper
     */
    public function __construct(ImportProductsHelper $importProductsHelper,
                                ContainerBagInterface $params)
    {
        parent::__construct();
        $this->importProductsHelper = $importProductsHelper;
        $this->params = $params;
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

    public function determineCurrency(InputInterface $input, OutputInterface $output): string
    {
        $defaultCurrency = $this->params->get('app.currency.default');
        $currencies = $this->params->get('app.currencies');

        $helper = $this->getHelper('question');
        $question = new ChoiceQuestion(
            printf('Please select currency in the file. Default "%s" ', $defaultCurrency),
            array_keys($currencies),
            $defaultCurrency
        );

        $currency = $helper->ask($input, $output, $question);
        $output->writeln('You have just selected: ' . $currency);

        return $currency;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     *
     * @throws \League\Csv\Exception
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
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
        $currency = $this->determineCurrency($input, $output);

        try {
            $this->importProductsHelper->setImportCurrency($currency);
        } catch (\Exception $e) {
            $io->newLine(2);
            $io->error($e->getMessage());
            return Command::INVALID;
        }

        $productsCount = iterator_count($csvData);

        $io->title('Start to Import products!!!');
        $io->progressStart($productsCount);

        $offset = 0;
        $limit = $this->batchingItemsCount;
        try {
            $this->importProductsHelper->startTransaction();
            while ($productsCount > $offset) {
                $stmt = Statement::create()
                    ->offset($offset)
                    ->limit($limit);
                $products = $stmt->process($csvData);
                $this->importProductsHelper->importProducts($products, $headers);

                $io->progressAdvance(iterator_count($products));
                $offset += $limit;
            }
        } catch (\Exception $e) {
            $io->newLine(2);
            $io->error($e->getMessage());
            return Command::INVALID;
        }

        $this->importProductsHelper->commitTransaction();
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