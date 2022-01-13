<?php

namespace App\Tests\Command;

use App\Command\CsvImportProductsCommand;
use App\Service\Product\ImportProductsHelper;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\Console\Command\Command;

final class CsvImportProductsTest extends KernelTestCase
{
    private string $pathToFile;
    private Command $command;


//    /**
//     * @test
//     */
//    public function importTest()
//    {
//
//        $entityManager = $this->createMock(EntityManagerInterface::class);
//        $validator = $this->createMock(ValidatorInterface::class);
//        $validator->method('validate')->willReturn([]);
//
////        validate
//        $kernel = self::bootKernel();
//        $application = new Application($kernel);
//
//        $application->add(new CsvImportProductsCommand($entityManager, $validator));
//
//        $command = $application->find('csv:import-products');
//        $commandTester = new CommandTester($command);
//        $commandTester->execute(array(
//            'command' => $command->getName(),
//            'path' => './var/data/import.csv',
//        ));
//
//        // the output of the command in the console
//        $output = $commandTester->getDisplay();
//
//        // #todo write correct assert
//        $assertOutput = '';
//
//        $this->assertStringContainsString($assertOutput, $output);
//    }


    /** @test */
    public function it_has_to_throw_file_not_exist_error()
    {
        // Set Up
        $wrongPath = $this->getContainer()->getParameter('kernel.project_dir') . '/var/data/wrong_path.csv';

        // Do something
        $this->expectException(\Exception::class);
        $this->expectExceptionCode(2);
        $this->expectExceptionMessage('File encoding is not correct.');

        // Make assertions
        $this->command->isFailValid($wrongPath);
    }
    /** @test */
    public function it_has_to_throw_file_encoding_incorrect_error()
    {
        // Set Up
        $wrongPath = $this->getContainer()->getParameter('kernel.project_dir') . '/var/data/incorrect_encoding.csv';

        // Do something
        $this->expectException(\Exception::class);
        $this->expectExceptionCode(2);
        $this->expectExceptionMessage('File not exist: ' . $wrongPath);

        // Make assertions
        $this->command->isFailValid($wrongPath);
    }

//isFailValid

    protected function setUp(): void
    {
        $importProductsHelper = $this->createMock(ImportProductsHelper::class);
        $containerBag = $this->createMock(ContainerBagInterface::class);
        $this->command = new CsvImportProductsCommand($importProductsHelper, $containerBag);

        $this->pathToFile =  $this->getContainer()->getParameter('kernel.project_dir') . '/var/data/stock.csv';
    }


}