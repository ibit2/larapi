<?php

namespace Tests\Unit;

use App\Services\SmsService;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SmsServiceTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testSend()
    {


            $smsService = new  SmsService();
            $smsService->send('18065048384');
            $this->assertTrue(true);




    }
}
