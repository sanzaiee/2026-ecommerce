<?php

use Database\Seeders\CatalogSeeder;

test('the application returns a successful response', function () {
    $this->seed(CatalogSeeder::class);

    $response = $this->get('/');

    $response->assertStatus(200);
});
