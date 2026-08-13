<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Motorcycle;
use App\Models\Route;
use App\Models\RouteReview;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Regressions de seguretat. Cada test d'aquest fitxer cobreix un forat real
 * que hi havia: accés a dades d'altres usuaris només sabent-ne l'ID.
 */
class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    private function verifiedUser(): User
    {
        return User::factory()->create(['email_verified_at' => now()]);
    }

    // ── Quilòmetres d'una moto d'un altre usuari ────────────────────────────

    public function test_offline_sync_cannot_add_km_to_another_users_motorcycle(): void
    {
        $victim = $this->verifiedUser();
        $attacker = $this->verifiedUser();

        $victimMoto = Motorcycle::factory()->for($victim)->create(['current_km' => 1000]);

        $response = $this->actingAs($attacker)->postJson(route('routes.sync-offline'), [
            'title'         => 'Ruta robada',
            'distance_km'   => 500,
            'waypoints'     => [['lat' => 41.38, 'lng' => 2.16], ['lat' => 41.39, 'lng' => 2.17]],
            'created_at'    => now()->toDateTimeString(),
            'motorcycle_id' => $victimMoto->id,
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors('motorcycle_id');
        $this->assertEquals(1000, $victimMoto->fresh()->current_km);
    }

    public function test_trip_cannot_add_km_to_another_users_motorcycle(): void
    {
        $victim = $this->verifiedUser();
        $attacker = $this->verifiedUser();

        $victimMoto = Motorcycle::factory()->for($victim)->create(['current_km' => 2000]);

        $this->actingAs($attacker)->postJson(route('trips.store'), [
            'distance_km'   => 300,
            'started_at'    => now()->toDateTimeString(),
            'waypoints'     => [['lat' => 41.38, 'lng' => 2.16], ['lat' => 41.39, 'lng' => 2.17]],
            'motorcycle_id' => $victimMoto->id,
        ])->assertStatus(422)->assertJsonValidationErrors('motorcycle_id');

        $this->assertEquals(2000, $victimMoto->fresh()->current_km);
    }

    public function test_route_cannot_be_created_against_another_users_motorcycle(): void
    {
        $victim = $this->verifiedUser();
        $attacker = $this->verifiedUser();

        $victimMoto = Motorcycle::factory()->for($victim)->create(['current_km' => 500]);

        $this->actingAs($attacker)->post(route('routes.store'), [
            'title'         => 'Ruta',
            'difficulty'    => 'easy',
            'geo_json'      => json_encode(['type' => 'LineString', 'coordinates' => [[2.16, 41.38]]]),
            'motorcycle_id' => $victimMoto->id,
        ])->assertSessionHasErrors('motorcycle_id');

        $this->assertEquals(500, $victimMoto->fresh()->current_km);
    }

    // ── Rutes privades ──────────────────────────────────────────────────────

    public function test_private_route_of_another_user_cannot_be_cloned(): void
    {
        $owner = $this->verifiedUser();
        $attacker = $this->verifiedUser();

        $private = Route::factory()->for($owner)->create(['is_public' => false]);

        $this->actingAs($attacker)
            ->post(route('routes.clone', $private))
            ->assertForbidden();

        $this->assertDatabaseCount('routes', 1);
    }

    public function test_public_route_can_still_be_cloned(): void
    {
        $owner = $this->verifiedUser();
        $cloner = $this->verifiedUser();

        $public = Route::factory()->for($owner)->create(['is_public' => true]);

        $this->actingAs($cloner)->post(route('routes.clone', $public))->assertRedirect();

        $this->assertDatabaseCount('routes', 2);
        $copy = Route::where('user_id', $cloner->id)->firstOrFail();
        // Una còpia és un traçat per rodar, no el registre de l'altre usuari.
        $this->assertNull($copy->motorcycle_id);
        $this->assertFalse((bool) $copy->is_recorded);
        $this->assertFalse((bool) $copy->is_public);
    }

    public function test_private_route_cannot_be_reviewed(): void
    {
        $owner = $this->verifiedUser();
        $attacker = $this->verifiedUser();

        $private = Route::factory()->for($owner)->create(['is_public' => false]);

        $this->actingAs($attacker)
            ->post(route('routes.reviews.store', $private), ['rating' => 1, 'comment' => 'spam'])
            ->assertForbidden();

        $this->assertEquals(0, RouteReview::count());
    }

    // ── Quedades privades ───────────────────────────────────────────────────

    public function test_private_event_cannot_be_joined_by_a_stranger(): void
    {
        $organizer = $this->verifiedUser();
        $attacker = $this->verifiedUser();

        $event = Event::factory()->for($organizer, 'organizer')->create(['is_public' => false]);

        $this->actingAs($attacker)->post(route('events.join', $event))->assertForbidden();

        $this->assertEquals(0, $event->participants()->count());
    }

    public function test_event_join_respects_the_participant_limit(): void
    {
        $organizer = $this->verifiedUser();
        $event = Event::factory()->for($organizer, 'organizer')->create([
            'is_public'        => true,
            'max_participants' => 2,
        ]);

        foreach ([$this->verifiedUser(), $this->verifiedUser()] as $participant) {
            $event->participants()->attach($participant->id, ['status' => 'confirmed']);
        }

        $latecomer = $this->verifiedUser();
        $this->actingAs($latecomer)
            ->post(route('events.join', $event))
            ->assertSessionHasErrors('join');

        $this->assertEquals(2, $event->participants()->count());
    }

    // ── Integritat del comptaquilòmetres ────────────────────────────────────

    public function test_deleting_a_gps_trip_rolls_back_the_odometer(): void
    {
        $user = $this->verifiedUser();
        $moto = Motorcycle::factory()->for($user)->create(['current_km' => 1000]);

        $this->actingAs($user)->postJson(route('trips.store'), [
            'distance_km'   => 120,
            'started_at'    => now()->toDateTimeString(),
            'waypoints'     => [['lat' => 41.38, 'lng' => 2.16], ['lat' => 41.39, 'lng' => 2.17]],
            'motorcycle_id' => $moto->id,
        ])->assertOk();

        $this->assertEquals(1120, $moto->fresh()->current_km);

        $trip = Trip::where('user_id', $user->id)->firstOrFail();
        $this->actingAs($user)->delete(route('trips.destroy', $trip));

        $this->assertEquals(1000, $moto->fresh()->current_km);
    }

    // ── Dades sensibles compartides amb el frontend ──────────────────────────

    public function test_push_token_and_google_id_are_never_serialised(): void
    {
        $user = User::factory()->create([
            'fcm_token' => 'super-secret-device-token',
            'google_id' => '1234567890',
        ]);

        $serialised = $user->toArray();

        $this->assertArrayNotHasKey('fcm_token', $serialised);
        $this->assertArrayNotHasKey('google_id', $serialised);
        // El frontend només necessita saber si pot demanar la contrasenya.
        $this->assertTrue($serialised['has_password']);
    }

    /**
     * Qui es registra amb correu i després entra amb Google té password I
     * google_id. El formulari i el backend han de coincidir en què demanen,
     * o el compte queda impossible d'esborrar.
     */
    public function test_account_linked_to_google_still_deletes_with_its_password(): void
    {
        $user = User::factory()->create(['google_id' => 'linked-123']);

        $this->assertTrue($user->toArray()['has_password']);

        $this->actingAs($user)
            ->delete(route('profile.destroy'), ['password' => 'password'])
            ->assertRedirect('/');

        $this->assertGuest();
        $this->assertNull($user->fresh());
    }

    public function test_role_cannot_be_escalated_through_mass_assignment(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $user->fill(['role' => 'admin', 'name' => 'Nou nom']);
        $user->save();

        $this->assertSame('user', $user->fresh()->role);
        $this->assertSame('Nou nom', $user->fresh()->name);
    }

    // ── Esborrat de compte amb Google ───────────────────────────────────────

    public function test_google_only_user_can_delete_account_by_confirming_email(): void
    {
        $user = User::factory()->create([
            'password'  => null,
            'google_id' => 'abc123',
        ]);

        $this->actingAs($user)
            ->delete(route('profile.destroy'), ['confirm_email' => $user->email])
            ->assertRedirect('/');

        $this->assertGuest();
        $this->assertNull($user->fresh());
    }

    public function test_google_only_user_cannot_delete_account_with_a_wrong_email(): void
    {
        $user = User::factory()->create([
            'password'  => null,
            'google_id' => 'abc123',
        ]);

        $this->actingAs($user)
            ->delete(route('profile.destroy'), ['confirm_email' => 'algu@altre.com'])
            ->assertSessionHasErrors('confirm_email');

        $this->assertNotNull($user->fresh());
    }
}
