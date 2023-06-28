<?php

namespace CharlesAugust44\NubankPHP;

use CharlesAugust44\NubankPHP\Models\AppDiscovery;
use CharlesAugust44\NubankPHP\Models\Bill;
use CharlesAugust44\NubankPHP\Models\BillSummary;
use CharlesAugust44\NubankPHP\Models\Discovery;
use CharlesAugust44\NubankPHP\Models\Lift;
use CharlesAugust44\NubankPHP\Models\Login;
use CharlesAugust44\NubankPHP\Models\NubankStatus;
use chillerlan\QRCode\Data\QRMatrix;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Promise\Utils;
use PHPUnit\Logging\Exception;
use PHPUnit\Util\Color;

class Nubank
{
    public NubankStatus $status = NubankStatus::UNAUTHORIZED;


    private const SESSION_FILE = './nubank.json';
    private const SECRET = "yQPeLzoHuJzlMMSAjC-LgNUJdUecx8XO";
    private const BASE_URL = "https://prod-global-webapp-proxy.nubank.com.br";
    private const DISCOVERY_ROUTE = "/api/discovery";
    private const APP_DISCOVERY_ROUTE = "/api/app/discovery";
    private const REQUEST_HEADER = [
        "Content-Type" => "application/json",
        "User-Agent" => "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/53.0.2785.143 Safari/537.36",
        "Accept" => "application/json, text/plain, */*",
        "Origin" => "https://app.nubank.com.br",
        "Host" => "prod-global-webapp-proxy.nubank.com.br",
        "Connection" => "keep-alive"
    ];

    private string $sessionId;
    private Client $client;
    private Discovery $discoveryRoutes;
    private AppDiscovery $appDiscoveryRoutes;
    private ?Login $loginResponse;
    private ?Lift $liftResponse;
    private ?BillSummary $billSummary;

    public function __construct($skipSessionLoad = false)
    {
        $this->client = new Client([
            'base_uri' => self::BASE_URL,
            'headers' => self::REQUEST_HEADER,
            'http_errors' => true
        ]);

        $this->loginResponse = $this->liftResponse = null;
        $this->discovery();

        if ($skipSessionLoad) {
            return;
        }

        $this->loadSession();
    }

    public function login(string $cpf, string $password): void
    {
        $data = [
            'grant_type' => 'password',
            'login' => $cpf,
            'password' => $password,
            'client_id' => 'other.conta',
            'client_secret' => Nubank::SECRET
        ];

        try {
            $loginResponse = $this->client->request('POST', $this->discoveryRoutes->login, [
                'json' => $data
            ]);

            $this->loginResponse = new Login($loginResponse->getBody()->getContents());
            $this->status = NubankStatus::WAITING_QR;
            $this->saveSession();
        } catch (ClientException $e) {
            $this->status = NubankStatus::UNAUTHORIZED;
            throw $e;
        }
    }

    public function lift(string $sessionId = null): void
    {
        if ($sessionId) {
            $this->sessionId = $sessionId;
        }

        if ($this->status !== NubankStatus::WAITING_QR) {
            throw new Exception('Login needs to be done before trying to lift the QRCode!');
        }

        $data = [
            'qr_code_id' => $this->sessionId,
            'type' => "login-webapp"
        ];

        try {
            $liftResponse = $this->client->request('POST', $this->appDiscoveryRoutes->lift, [
                'json' => $data,
                'headers' => $this->getAuthorizedHeader(true)
            ]);
            $this->liftResponse = new Lift($liftResponse->getBody()->getContents());
            $this->status = NubankStatus::AUTHORIZED;
            $this->saveSession();
        } catch (ClientException $e) {
            if ($e->getCode() !== 404) {
                $this->status = NubankStatus::UNAUTHORIZED;
            }
            throw $e;
        }
    }

    public function printQRCodeSSID(): void
    {
        $this->sessionId = $this->generateSSID();
        $options = new QROptions($this->getQROptions());
        $qr = (new QRCode($options))->render($this->sessionId);

        fwrite(STDERR, print_r($qr, TRUE));
    }

    public function getAsciiQRCode(bool $invert = false): string
    {
        $options = new QROptions($this->getQROptions($invert));
        return (new QRCode($options))->render($this->sessionId);
    }

    public function getSSID(): string
    {
        $this->sessionId = $this->generateSSID();
        return $this->sessionId;
    }

    public function fetchBills(): BillSummary
    {
        try {
            $response = $this->client->request('GET', $this->liftResponse->_links['bills_summary']['href'], [
                'headers' => $this->getAuthorizedHeader()
            ]);

            return new BillSummary($response->getBody()->getContents());
        } catch (ClientException $e) {
            if ($e->getCode() === 401) {
                $this->status = NubankStatus::UNAUTHORIZED;
            }
            throw $e;

        }
    }

    public function fetchBillItems(Bill $bill): Bill
    {
        try {
            $response = $this->client->request('GET', $bill->_links->self->href, [
                'headers' => $this->getAuthorizedHeader()
            ]);

            return new Bill($response->getBody()->getContents());
        } catch (ClientException $e) {
            if ($e->getCode() === 401) {
                $this->status = NubankStatus::UNAUTHORIZED;
            }
            throw $e;
        }
    }

    public function getBillSummary(): ?BillSummary
    {
        return $this->billSummary;
    }

    private function loadSession(): void
    {
        if (!file_exists(self::SESSION_FILE)) {
            $this->status = NubankStatus::UNAUTHORIZED;
            return;
        }

        $serialized = file_get_contents(self::SESSION_FILE);
        $data = json_decode($serialized);

        if ($data->liftResponse) {
            $this->liftResponse = new Lift($data->liftResponse);
        }

        if ($data->loginResponse) {
            $this->loginResponse = new Login($data->loginResponse);
        }

        if ($this->loginResponse && $this->liftResponse) {
            $this->status = NubankStatus::SESSION_LOADED;
        } elseif ($this->loginResponse) {
            $this->status = NubankStatus::WAITING_QR;
            return;
        }

        $this->billSummary = $this->fetchBills();

        if ($this->status === NubankStatus::SESSION_LOADED && $this->billSummary) {
            $this->status = NubankStatus::AUTHORIZED;
        }
    }

    private function saveSession(): void
    {
        file_put_contents(self::SESSION_FILE, json_encode([
            'liftResponse' => $this->liftResponse,
            'loginResponse' => $this->loginResponse
        ]));
    }

    private function discovery(): void
    {
        $promises = [
            $this->client->requestAsync('GET', self::DISCOVERY_ROUTE),
            $this->client->requestAsync('GET', self::APP_DISCOVERY_ROUTE),
        ];

        $responses = Utils::settle(Utils::unwrap($promises))->wait();

        $this->discoveryRoutes = new Discovery($responses[0]['value']->getBody()->getContents());
        $this->appDiscoveryRoutes = new AppDiscovery($responses[1]['value']->getBody()->getContents());
    }

    private function getQROptions(bool $invert = false): array
    {
        $black = $invert ? '  ' : '██';
        $white = $invert ? '██' : '  ';

        return [
            'version' => 3,
            'outputType' => QRCode::OUTPUT_STRING_TEXT,
            'eccLevel' => QRCode::ECC_L,
            'eol' => Color::colorize('reset', "\x00\n"),
            'moduleValues' => [
                // light
                QRMatrix::M_NULL => $black, // 0
                QRMatrix::M_DATA => $black, // 4
                QRMatrix::M_FINDER => $black, // 6
                QRMatrix::M_SEPARATOR => $black, // 8
                QRMatrix::M_ALIGNMENT => $black, // 10
                QRMatrix::M_TIMING => $black, // 12
                QRMatrix::M_FORMAT => $black, // 14
                QRMatrix::M_VERSION => $black, // 16
                QRMatrix::M_QUIETZONE => $black, // 18
                QRMatrix::M_LOGO => $black, // 20
                QRMatrix::M_TEST => $black, // 255
                // dark
                QRMatrix::M_DARKMODULE << 8 => $white,  // 512
                QRMatrix::M_DATA << 8 => $white,  // 1024
                QRMatrix::M_FINDER << 8 => $white,  // 1536
                QRMatrix::M_ALIGNMENT << 8 => $white,  // 2560
                QRMatrix::M_TIMING << 8 => $white,  // 3072
                QRMatrix::M_FORMAT << 8 => $white,  // 3584
                QRMatrix::M_VERSION << 8 => $white,  // 4096
                QRMatrix::M_FINDER_DOT << 8 => $white,  // 5632
                QRMatrix::M_TEST << 8 => $white,  // 65280
            ],
        ];
    }

    private function getAuthorizedHeader(bool $useLoginToken = false): array
    {
        $token = $useLoginToken ? $this->loginResponse->access_token : $this->liftResponse->access_token;

        return [
            ...self::REQUEST_HEADER,
            "Authorization" => "Bearer " . $token
        ];
    }

    private function generateSSID(): string
    {
        $rand = function () {
            return dechex((int)(16 * mt_rand(0, mt_getrandmax()) / (mt_getrandmax() + 1)));
        };

        return preg_replace_callback('/x/', $rand, 'xxxxxxxx-xxxx-4xxx-8xxx-xxxxxxxxxxxx');
    }
}
