<?php

namespace Utipd\CounterpartySender;

use Exception;

/*
* CounterpartySender
*/
class CounterpartySender
{

    ////////////////////////////////////////////////////////////////////////

    public function __construct($xcpd_client, $bitcoin_client) {
        $this->xcpd_client = $xcpd_client;
        $this->bitcoin_client = $bitcoin_client;
    }

    public function send($public_key, $private_key, $source, $destination, $quantity, $asset, $other_counterparty_vars=[]) {
        // build the raw transaction
        $raw_transaction_hex = $this->createSendTransactionHex($public_key, $private_key, $source, $destination, $quantity, $asset, $other_counterparty_vars);

        // sign and send
        return $this->signAndSendRawTransaction($raw_transaction_hex, $private_key);
    }

    public function payDividend($public_key, $private_key, $source, $asset, $quantity_per_unit, $dividend_asset, $other_counterparty_vars) {
        // build the raw transaction
        $raw_transaction_hex = $this->createDividendTransactionHex($public_key, $private_key, $source, $asset, $quantity_per_unit, $dividend_asset, $other_counterparty_vars);

        // sign and send
        return $this->signAndSendRawTransaction($raw_transaction_hex, $private_key);
    }

    ////////////////////////////////////////////////////////////////////////

    protected function createSendTransactionHex($public_key, $private_key, $source, $destination, $quantity, $asset, $other_counterparty_vars=[]) {
        $vars = [
            'pubkey'      => $public_key,
            'privkey'     => $private_key,
            'source'      => $source,
            'destination' => $destination,
            'quantity'    => $quantity,
            'asset'       => $asset,
        ];
        $vars = array_merge($vars, $other_counterparty_vars);

        $raw_transaction_hex = $this->xcpd_client->create_send($vars);
        return $raw_transaction_hex;
    }

    protected function createDividendTransactionHex($public_key, $private_key, $source, $asset, $quantity_per_unit, $dividend_asset, $other_counterparty_vars) {
        $vars = [
            'pubkey'            => $public_key,
            'privkey'           => $private_key,
            'source'            => $source,
            'asset'             => $asset,
            'dividend_asset'    => $dividend_asset,
            'quantity_per_unit' => $quantity_per_unit,
        ];
        $vars = array_merge($vars, $other_counterparty_vars);

        $raw_transaction_hex = $this->xcpd_client->create_dividend($vars);
        return $raw_transaction_hex;
    }

    protected function signAndSendRawTransaction($raw_transaction_hex, $private_key) {
        // sign the transaction
        $result = $this->bitcoin_client->signrawtransaction($raw_transaction_hex, [], [$private_key]);
        $signed_transaction_hex = $result->hex;
        if (!$signed_transaction_hex) { throw new Exception("Failed to sign transaction", 1); }

        // broadcast to the network
        $transaction_id = $this->bitcoin_client->sendrawtransaction($signed_transaction_hex);
        return $transaction_id;
    }


}

