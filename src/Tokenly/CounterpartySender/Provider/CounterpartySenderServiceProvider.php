<?php

namespace Tokenly\CounterpartySender\Provider;

use Exception;
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
        $this->package('tokenly/counterparty-sender', 'counterparty-sender', __DIR__.'/../../');

        $this->app->bind('Nbobtc\Bitcoind\Bitcoind', function($app) {
            $config = $app['config']['bitcoin'];

            // \Illuminate\Support\Facades\Log::info('$config:'.json_encode($config, 192));
            $url_pieces = parse_url($config['connection_string']);
            $connection_string = "{$url_pieces['scheme']}://{$config['rpc_user']}:{$config['rpc_password']}@{$url_pieces['host']}:{$url_pieces['port']}";

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

}

