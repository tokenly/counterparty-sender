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
        $raw_transaction_hex = $this->createSend($public_key, $private_key, $source, $destination, $quantity, $asset, $other_counterparty_vars);

        throw new Exception("Unimplemented...", 1);
        
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

        $response = $this->xcpd_client->create_send($vars);
        echo "\$response:\n".json_encode($response, 192)."\n";

    }

}

