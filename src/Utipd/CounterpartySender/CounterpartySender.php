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
        $raw_transaction_hex = $this->createSend($public_key, $private_key, $source, $destination, $quantity, $asset, $other_counterparty_vars);

        // sign the transaction
        $result = $this->bitcoin_client->signrawtransaction($raw_transaction_hex, [], [$private_key]);
        $signed_transaction_hex = $result->hex;
        if (!$signed_transaction_hex) { throw new Exception("Failed to sign transaction", 1); }

        // broadcast to the network
        $transaction_id = $this->bitcoin_client->sendrawtransaction($signed_transaction_hex);
        return $transaction_id;
    }

    ////////////////////////////////////////////////////////////////////////

    protected function createSend($public_key, $private_key, $source, $destination, $quantity, $asset, $other_counterparty_vars=[]) {
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

}

