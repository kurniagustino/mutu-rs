<?php

namespace Tests\Feature;

use App\Models\User; // Tambahkan ini
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase; // Sudah ada dari perbaikan sebelumnya

class ExampleTest extends TestCase
{
    use RefreshDatabase; // <-- TAMBAHKAN BARIS INI!

    public function test_the_application_returns_a_successful_response(): void
    {
        // Kode ini sekarang akan aman karena tabel 'users' ada
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/');

        $response->assertStatus(200);
    }
}
