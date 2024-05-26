<?php

namespace App\Tests\Command;

use App\Command\ProcessDepositWithdrawCommand;
use App\Handler\DepositHandler;
use App\Handler\WithdrawHandler;
use App\Service\ExchangeRatesApi\Handler\GetLatestExchangeRatesHandler;
use App\Service\ExchangeRatesApi\Response\GetExchangeRateResponse;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class ProcessDepositWithdrawCommandTest extends KernelTestCase
{
    private readonly string $projectDir;
    private readonly WithdrawHandler $withdrawHandler;
    private readonly DepositHandler $depositHandler;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withdrawHandler = self::getContainer()->get(WithdrawHandler::class);
        $this->depositHandler = self::getContainer()->get(DepositHandler::class);
        $this->projectDir = self::getContainer()->getParameter('kernel.project_dir');
    }

    protected function tearDown(): void
    {
        // Reset the exception handler
        restore_exception_handler();

        parent::tearDown();
    }

    public function testExec()
    {
        $fileName = 'input.csv';

        $exchangeRatesHandler = $this->createMock(GetLatestExchangeRatesHandler::class);
        $exchangeRatesHandler->method('getRates')
            ->willReturn(new GetExchangeRateResponse(['USD' => 1.1497, 'JPY' => 129.53]));

        $command = new ProcessDepositWithdrawCommand(
            $this->projectDir,
            $this->withdrawHandler,
            $this->depositHandler,
            $exchangeRatesHandler,
        );

        $application = new Application(self::bootKernel());
        $application->add($command);

        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'file' => $fileName,
        ]);

        $output = $commandTester->getDisplay();
        $expectedOutput = "0.60\r\n3.00\r\n0.00\r\n0.06\r\n1.50\r\n0\r\n0.70\r\n0.30\r\n0.30\r\n3.00\r\n0.00\r\n0.00\r\n8612\r\n";

        $this->assertSame($expectedOutput, $output);
    }
}
