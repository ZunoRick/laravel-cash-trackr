<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('shows the login screen', function () {
    $response = $this->get(route('login'));
    $response->assertOk();
});

test('logs in a verified user successfully', function () {
    User::factory()->create([
        'email' => 'juan@correo.com',
        'password' => bcrypt('X7q$mK9_zL2pR'),
        'email_verified_at' => now()
    ]);

    $response = $this->post(route('login.store'), [
        'email' => 'juan@correo.com',
        'password' => 'X7q$mK9_zL2pR',
    ]);

    $response->assertRedirect(route('dashboard'));
    $this->assertAuthenticated();
});

test('does not login with invalid credentials', function () {
    User::factory()->create([
        'email' => 'juan@correo.com',
        'password' => bcrypt('X7q$mK9_zL2pR')
    ]);

    $response = $this->from(route('login'))->post(route('login.store'), [
        'email' => 'juan@correo.com',
        'password' => 'password',
    ]);

    $response->assertRedirect(route('login'));
    $response->assertSessionHas('error', 'Credenciales Incorrectas');

    $this->assertGuest();
});

test('prevent unverified user from accesing dashboard', function () {
    User::factory()->unverified()->create([
        'email' => 'juan@correo.com',
        'password' => bcrypt('X7q$mK9_zL2pR'),
    ]);

    $response = $this->post(route('login.store'), [
        'email' => 'juan@correo.com',
        'password' => 'X7q$mK9_zL2pR',
    ]);

    $response->assertRedirect(route('dashboard'));
    $this->assertAuthenticated();

    $dashboardResponse = $this->get(route('dashboard'));
    $dashboardResponse->assertRedirect(route('verification.notice'));
});

test('does not allow access to dashboard if email is not verified', function () {
    $user = User::factory()->create([
        'email_verified_at' => null,
    ]);

    $response = $this->actingAs($user)->get(route('dashboard'));
    $response->assertRedirect(route('verification.notice'));
});

test('allows access to dashboard if email is verified', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $response = $this->actingAs($user)->get(route('dashboard'));
    $response->assertOk();
});

test('fails login if user does not exist', function () {
    $response = $this->from(route('login'))->post(route('login.store'), [
        'email' => 'noexiste@correo.com',
        'password' => 'mK9_zL2pR'
    ]);
    $response->assertRedirect(route('login'));
    $response->assertSessionHasErrors([
        'email' => 'No encontramos una cuenta con ese email.'
    ]);

    $this->assertGuest();
});
