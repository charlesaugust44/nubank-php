<?php

use CharlesAugust44\NubankPHP\Models\Bill;
use CharlesAugust44\NubankPHP\Models\NubankStatus;
use CharlesAugust44\NubankPHP\Nubank;
use GuzzleHttp\Exception\ClientException;
use PHPUnit\Framework\TestCase;

class NubankTest extends TestCase
{
    private static Nubank $nu;

    protected function setUp(): void
    {
        parent::setUp();
        NubankTest::$nu = new Nubank();
    }

    public function testLogin()
    {
        if (NubankTest::$nu->status === NubankStatus::AUTHORIZED) {
            $this->addToAssertionCount(1);
            return;
        } else {
            $this->assertEquals(NubankStatus::UNAUTHORIZED, NubankTest::$nu->status);
        }

        NubankTest::$nu->login('cpf', 'password');
        NubankTest::$nu->printQRCodeSSID();

        $this->assertEquals(NubankStatus::WAITING_QR, NubankTest::$nu->status);

        for ($tryNumber = 0; $tryNumber < 15; $tryNumber++) {
            sleep(1);

            try {
                NubankTest::$nu->lift();
                break;
            } catch (ClientException $e) {
                if ($e->getCode() === 404) {
                    $this->assertEquals(NubankStatus::WAITING_QR, NubankTest::$nu->status);
                } else {
                    $this->assertEquals(NubankStatus::UNAUTHORIZED, NubankTest::$nu->status);
                    throw $e;
                }
            }
        }

        $this->assertEquals(NubankStatus::AUTHORIZED, NubankTest::$nu->status);
    }

    public function testFetchBill()
    {
        $this->assertEquals(NubankStatus::AUTHORIZED, NubankTest::$nu->status);

        $bills = NubankTest::$nu->fetchBills();
        $bill = null;

        foreach ($bills->bills as $billSummary) {
            echo $billSummary->state . "\n";
            if ($billSummary->state !== Bill::STATE_OPEN) {
                continue;
            }

            $bill = NubankTest::$nu->fetchBillItems($billSummary);
            break;
        }

        $this->assertNotNull($bill);
        $this->assertNotEmpty($bill->line_items);
    }
}
