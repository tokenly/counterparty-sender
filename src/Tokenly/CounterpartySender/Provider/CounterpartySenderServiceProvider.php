<?php

namespace Tokenly\CounterpartySender\Provider;

use Exception;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use Nbobtc\Bitcoind\Bitcoind;
use Nbobtc\Bitcoind\Client;
use Tokenly\CounterpartySender\CounterpartySender;

/*
* CounterpartySenderServiceProvider
*/
class CounterpartySenderServiceProvider extends ServiceProvider
{

    public function boot()
    {
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->bindConfig();

        $this->app->bind('Nbobtc\Bitcoind\Bitcoind', function($app) {
            $url_pieces = parse_url(Config::get('counterparty-sender.connection_string'));
            $rpc_user = Config::get('counterparty-sender.rpc_user');
            $rpc_password = Config::get('counterparty-sender.rpc_password');

            $connection_string = "{$url_pieces['scheme']}://{$rpc_user}:{$rpc_password}@{$url_pieces['host']}:{$url_pieces['port']}";
            $bitcoin_client = new Client($connection_string);
            $bitcoind = new Bitcoind($bitcoin_client);
            return $bitcoind;
        });

        $this->app->bind('Tokenly\CounterpartySender\CounterpartySender', function($app) {
            $xcpd_client = $app->make('Tokenly\XCPDClient\Client');
            $bitcoind = $app->make('Nbobtc\Bitcoind\Bitcoind');


            $sender = new CounterpartySender($xcpd_client, $bitcoind);
            return $sender;
        });
    }

    protected function bindConfig()
    {

        // simple config
        $config = [
            'counterparty-sender.connection_string' => env('NATIVE_CONNECTION_STRING', 'http://localhost:8332'),
            'counterparty-sender.rpc_user'          => env('NATIVE_RPC_USER', null),
            'counterparty-sender.rpc_password'      => env('NATIVE_RPC_PASSWORD', null),
        ];

        // set the laravel config
        Config::set($config);
    }


}

