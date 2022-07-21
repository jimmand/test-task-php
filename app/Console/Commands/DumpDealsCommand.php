<?php

namespace App\Console\Commands;

use AmoCRM\Client\AmoCRMApiClient;
use App\Services\Lead\Service;
use Illuminate\Console\Command;
use League\OAuth2\Client\Token\AccessToken;

class DumpDealsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leads:dump';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Выгружает сделки в бд';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $client_id = env('CLIENT_ID');
        $secret = env('SECRET');
        $token = env('ACCESS_TOKEN');
        $domain = env('DOMAIN');

        $access_token = new AccessToken([
            'access_token' => $token,
            'expires' => 2147483647,
        ]);
        $apiClient = new AmoCRMApiClient($client_id, $secret, '');
        $apiClient->setAccessToken($access_token)
            ->setAccountBaseDomain($domain);

        $leadService = new Service($apiClient);
        print "ожидайте\n";
        $leadService->dump();
        print "выгрузка завершена\n";

        return 0;
    }
}
