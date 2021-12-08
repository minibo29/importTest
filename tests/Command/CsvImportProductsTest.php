<?php

namespace App\Tests\Command;

use App\Command\CsvImportProductsCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CsvImportProductsTest extends KernelTestCase
{


    /**
     * @test
     */
    public function importTest()
    {

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $validator = $this->createMock(ValidatorInterface::class);
        $validator->method('validate')->willReturn([]);

//        validate
        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $application->add(new CsvImportProductsCommand($entityManager, $validator));

        $command = $application->find('csv:import-products');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command'  => $command->getName(),
            'path' => './var/data/import.csv',
        ));

        // the output of the command in the console
        $output = $commandTester->getDisplay();

        // #todo write correct assert
        $assertOutput = '';

        $this->assertStringContainsString($assertOutput, $output);
    }

    /**
     * @test
     */
    public function fileNotExistTest()
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $validator = $this->createMock(ValidatorInterface::class);
        $validator->method('validate')->willReturn([]);

        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $application->add(new CsvImportProductsCommand($entityManager, $validator));

        $command = $application->find('csv:import-products');

        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command'  => $command->getName(),
            'path' => './var/data/wrongPath.csv',
        ));

        $output = $commandTester->getDisplay();

        $assertOutput = <<<EOT
                        Start to Import products!!!
                        ===========================
                        
                        [ERROR] File not exist: ./var/data/wrongPath.csv
                        
                        EOT;

        $this->assertStringContainsString($assertOutput, $output);
    }

    /**
     * @test
     */
    public function failingFailTest()
    {
        $this->fail();
    }

}