<?php
namespace App\Command;

use App\Dto\Product\ImportProductHeaderDto;
use App\Service\Product\ImportProductsHelper;
use Doctrine\DBAL\Exception;
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
use Traversable;


class CsvImportProductsCommand extends Command
{

    const FILE_NOT_EXIST = 1;
    const FILE_ENCODING_INCORRECT = 1;

    private ImportProductsHelper $importProductsHelper;

    private int $batchingItemsCount = 1000;
    private ContainerBagInterface $params;
    private SymfonyStyle $io;

    /**
     * @param ImportProductsHelper $importProductsHelper
     * @param ContainerBagInterface $params
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

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);
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
        $path = $input->getArgument('path');

        try {
            $this->isFailValid($path);

            $reader = Reader::createFromPath($path)
                ->skipEmptyRecords()
            ;

            $csvData = $reader->setHeaderOffset(0);
            $headers = $this->determineHeader($csvData->getHeader(), $input, $output);
            $currency = $this->determineCurrency($input, $output);

            $this->importProductsHelper->setImportCurrency($currency);

            $this->importFile($csvData, $headers);
        } catch (\Exception $e) {
            $this->io->newLine(2);
            $this->io->error($e->getMessage());
            return Command::INVALID;
        }

        return Command::SUCCESS;
    }

    /**
     * @param Traversable $csvData
     * @param ImportProductHeaderDto $headers
     *
     * @return void
     *
     * @throws \League\Csv\Exception
     * @throws \Exception
     */
    public function importFile(Traversable $csvData, ImportProductHeaderDto $headers)
    {
        $productsCount = iterator_count($csvData);

        $this->io->title('Start to Import products!!!');
        $this->io->progressStart($productsCount);

        $offset = 0;
        $limit = $this->batchingItemsCount;
        $this->importProductsHelper->startTransaction();
        while ($productsCount > $offset) {
            $stmt = Statement::create()
                ->offset($offset)
                ->limit($limit);
            $products = $stmt->process($csvData);
            $this->importProductsHelper->importProducts($products, $headers);

            $this->io->progressAdvance(iterator_count($products));
            $offset += $limit;
        }
        $this->importProductsHelper->commitTransaction();

        $this->io->progressFinish();
        $this->io->listing([
            printf('Skipped %s Stocks.', $this->importProductsHelper->getSkippedProductsCount()),
            printf('Imported %s Stocks.', $this->importProductsHelper->getImportedProductsCount()),
        ]);
    }

    /**
     * @param $pathToFile
     *
     * @throws Exception
     */
    public function isFailValid($pathToFile): bool
    {
        if (!file_exists($pathToFile)) {
            throw new Exception('File not exist: ' . $pathToFile, 1);
        }

        if (!mb_detect_encoding(file_get_contents($pathToFile), ['UTF-8'], true)) {
            throw new Exception('File encoding is not correct.', 2);
        }

        return true;
    }
}