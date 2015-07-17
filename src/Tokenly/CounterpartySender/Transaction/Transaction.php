<?php

namespace Tokenly\CounterpartySender\Transaction;

use Exception;

/*
* Transaction
*/
class Transaction
{

    protected $signed_transaction_hex = null;
    protected $txid = null;

    ////////////////////////////////////////////////////////////////////////

    public function __construct($signed_transaction_hex) {
        $this->setTransactionHex($signed_transaction_hex);
    }


    public function setTransactionHex($signed_transaction_hex) {
        $this->signed_transaction_hex = $signed_transaction_hex;
        unset($this->txid);
    }

    public function getTransactionHex() {
        return $this->signed_transaction_hex;
    }


    public function getTransactionID() {
        if (!isset($this->txid)) {
            $this->txid = $this->txidFromRawTransaction($this->signed_transaction_hex);
        }

        return $this->txid;
    }


    protected function txidFromRawTransaction($raw_transaction_hex) {
        // get the raw tx_hash of the hex transaction
        $txHash = hash('sha256', hash('sha256', pack("H*", trim($raw_transaction_hex)), true));

        // flip the byte order (big/little endian) to get the txid
        $tx_id = implode('', array_reverse(str_split($txHash, 2)));

        return $tx_id;
    }

}

