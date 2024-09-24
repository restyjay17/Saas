<?php

namespace Tests\Unit;

// use PHPUnit\Framework\TestCase;
use Tests\TestCase;

class LogsTest extends TestCase
{
    public function test_get_log_users()
    {
        $response = $this->call('GET', '/api/logs/users');

        $response->assertStatus(200);
    }


    public function test_get_logs()
    {
        $response = $this->call('GET', '/api/logs');

        $response->assertStatus(200);
    }
}
