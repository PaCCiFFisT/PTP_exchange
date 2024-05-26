<?php

declare(strict_types=1);

namespace App\Command;

use App\Enum\OperationTypeEnum;
use App\Handler\DepositHandler;
use App\Handler\WithdrawHandler;
use Brick\Math\RoundingMode;
use Brick\Money\Currency;
use Brick\Money\Money;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    'app:process-deposit-withdrawal',
    'this command calculates deposit and withdrawal fees from given CSV file',
)]
class ProcessDepositWithdrawCommand extends Command
{
    private const string BASE_CURRENCY = 'EUR';

    private const string  DATA_PATH = '/data/';

    public function __construct(
        private readonly string $projectDir,
        private readonly WithdrawHandler $withdrawHandler,
        private readonly DepositHandler $depositHandler,
        ?string $name = null,
    ) {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        parent::configure();
        $this->addArgument(
            'file',
            InputArgument::REQUIRED,
            'Name of the CSV file to process transactions from.'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $fileName = $input->getArgument('file');

        if (!file_exists($this->projectDir.self::DATA_PATH.$fileName)) {
            $output->writeln('File not found: '.$this->projectDir.self::DATA_PATH.$fileName);

            return Command::FAILURE;
        }

        $data = array_map('str_getcsv', file($this->projectDir.self::DATA_PATH.$fileName));

        foreach ($data as $item) {
            $item[4] = self::BASE_CURRENCY === $item[5] ? $item[4] : $this->convertToBaseCurrency($item[4], $item[5]);

            $item[3] === OperationTypeEnum::Deposit->value ?
                $commission = $this->depositHandler->calculateDepositCommission($item[4]) :
                $commission = $this->withdrawHandler->calculateWithdrawCommission($item);

            $commission = self::BASE_CURRENCY === $item[5] ?
                $commission :
                $this->convertToOperationCurrency($commission, $item[5]);

            $output->writeln($item[3].' '.$commission);
        }

        return Command::SUCCESS;
    }

    private function convertToBaseCurrency(string $amount, string $currency)
    {
        if ('USD' === $currency) {
            return bcdiv($amount, '1.1497', 5);
        }

        if ('JPY' === $currency) {
            return bcdiv($amount, '129.53', 5);
        }
    }

    private function convertToOperationCurrency(string $commission, string $currency)
    {
        if ('USD' === $currency) {
            $amount = bcmul($commission, '1.1497', 5);
        }

        if ('JPY' === $currency) {
            $amount = bcmul($commission, '129.53', 5);
        }

        $currency = Currency::of($currency);
        $scale = $currency->getDefaultFractionDigits();
        $scaledAmount = bcmul($amount, bcpow('10', (string) $scale), 5);

        $commission = Money::ofMinor($scaledAmount, $currency, roundingMode: RoundingMode::CEILING);

        return $commission->getAmount()->toScale($scale);
    }
}