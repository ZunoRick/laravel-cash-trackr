<?php

use App\Models\User;
use App\Notifications\VerifyEmail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;

uses(RefreshDatabase::class);

test('shows the registration screen', function () {
    $response = $this->get(route('register'));
    $response->assertOk();
    $response->assertStatus(200);
    $response->assertSee('Crear cuenta');
    $response->assertSee('Registrarme');

    $response->assertSeeInOrder([
        'Crear cuenta',
        'Registrarme'
    ]);
});

test('registers a new user as unverified and dispatches the registered event', function () {
    Event::fake();

    $response = $this->post(route('register.store'), [
        'name' => 'Juan Pérez',
        'email' => 'juan@correo.com',
        'password' => 'X7q$mK9_zL2pR',
        'password_confirmation' => 'X7q$mK9_zL2pR'
    ]);

    dump(session('errors')?->getBag('default')->all());

    $response->assertRedirect(route('verification.notice'));
    $user = User::where('email', 'juan@correo.com')->first();

    expect($user)->not->toBeNull();
    expect($user->name)->toBe('Juan Pérez');
    expect($user->email)->toBe('juan@correo.com');
    expect($user->hasVerifiedEmail())->toBeFalse();

    Event::assertDispatched(Registered::class);
});

test('Should validate required fields when the request body is empty', function () {
    $response = $this->post(route('register.store'), []);
    $response->assertSessionHasErrors([
        'name',
        'email',
        'password'
    ]);
});

test('Prevents duplicate email addresses', function () {
    User::factory()->create([
        'email' => 'juan@correo.com',
    ]);

    $response = $this->post(route('register.store'), [
        'name' => 'Juan Pérez',
        'email' => 'juan@correo.com',
        'password' => 'X7q$mK9_zL2pR',
        'password_confirmation' => 'X7q$mK9_zL2pR'
    ]);

    $response->assertRedirect();

    $response->assertSessionHasErrors([
        'email' => 'Este correo ya está registrado'
    ]);
});

test('Sends the verification email notification after resgistration', function () {
    Notification::fake();

    $response = $this->post(route('register.store'), [
        'name' => 'Juan Pérez',
        'email' => 'juan@correo.com',
        'password' => 'X7q$mK9_zL2pR',
        'password_confirmation' => 'X7q$mK9_zL2pR'
    ]);

    dump(session('errors')?->getBag('default')->all());

    $user = User::where('email', 'juan@correo.com')->first();

    Notification::assertSentTo($user, VerifyEmail::class);
});

test('verifies the user email from a signed verification link', function () {
    $user = User::factory()->unverified()->create();

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        [
            'id' => $user->id,
            'hash' => sha1($user->email),
        ]
    );

    $response = $this->actingAs($user)->get($verificationUrl);
    $response->assertRedirect(route('dashboard'));
    expect($user->hasVerifiedEmail())->toBeTrue();
});

test('does not allow an unverified user to access the dashboard', function () {
    $user = User::factory()->unverified()->create();

    $response = $this->actingAs($user)->get(route('dashboard'));
    $response->assertRedirect(route('verification.notice'));
});

test('allows a unverified user to access the dashboard', function () {
    $user = User::factory()->create([
        'email_verified_at' => now()
    ]);

    $response = $this->actingAs($user)->get(route('dashboard'));
    $response->assertOk();
});
