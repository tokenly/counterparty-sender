The CounterpartySender component for Tokenly.

Example of usage:


```php

$xcpd_connection_string = 'http://addressof.counterpartyserver.com:4000';
$xcpd_rpc_user = 'rpcusername';
$xcpd_rpc_password  = 'securerpcpassword';

$bitcoind_connection_string = 'httpd://btcrpcusername:btcrpcpassword@addressof.bitcoinserver.com:8332';

$xcpd = new Tokenly\XCPDClient\Client($xcpd_connection_string, $xcpd_rpc_user, $xcpd_rpc_password);
$bitcoind = new Nbobtc\Bitcoind\Bitcoind(new Nbobtc\Bitcoind\Client($bitcoind_connection_string));

$sender = new Tokenly\CounterpartySender\CounterpartySender($xcpd, $bitcoind);


$public_key  = 'PUBLICBITCOINADDRESSKEY';
$private_key = 'PRIVATEBITCOINADDRESSKEY';
$source      = 'SOURCE_BITCOINADDRESS';
$destination = 'DESTINATION_BITCOINADDRESS';
$quantity    = 1000;
$asset       = 'LTBCOIN';
$transaction_id = $sender->send($public_key, $private_key, $source, $destination, $quantity, $asset);


echo "transaction id: $transaction_id\n";

```
