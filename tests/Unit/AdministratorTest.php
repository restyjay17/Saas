<?php

namespace Tests\Unit;

//use PHPUnit\Framework\TestCase;
use Tests\TestCase;

class AdministratorTest extends TestCase
{
    public function test_get_administrators()
    {
        $response = $this->call('GET', '/api/administrators?page=1');

        $response->assertStatus(200);
    }


    public function test_save_administrator()
    {
        $response = $this->call('POST', '/api/administrator', [
            'uname' => 'loremipsum',
            'pword' => 'loremipsum',
            'name' => 'Lorem Ipsum',
            'email' => 'lorem.ipsum@email.com',
            'status' => 1
        ]);

        $response->assertStatus(200);
    }


    public function test_update_administrator()
    {
        $response = $this->call('POST', '/api/administrator', [
            'target' => 5,
            'uname' => 'loremipsum2',
            'pword' => 'loremipsum2',
            'name' => 'Lorem Ipsum 2',
            'email' => 'lorem.ipsum.2@email.com',
            'status' => 2
        ]);

        $response->assertStatus(200);
    }


    public function test_get_administrator_details()
    {
        $response = $this->call('GET', '/api/administrator/5');

        $response->assertStatus(200);
    }


    public function test_delete_administrator()
    {
        $response = $this->call('DELETE', '/api/administrator/5');

        $response->assertStatus(200);
    }
}
