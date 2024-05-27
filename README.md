# PTP_exchange

## Description
Simple implementation of currency deposit and withdrawal commission calculation

## Usage
1. Clone the repository `git clone https://github.com/PaCCiFFisT/PTP_exchange`
2. run `composer install`
3. copy `filename.csv` file with input data to `./data` folder
4. update `.env` with given EXCHANGE_RATES_API_KEY or use your own
5. run `php bin/console app:process-deposit-withdrawal filename.csv`

## Tests
run `composer phpunit`
