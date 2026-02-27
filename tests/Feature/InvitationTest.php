<?php

namespace Tests\Feature;

use App\Models\Colocation;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvitationTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_send_invitation()
    {
        $owner = User::factory()->create();
        $colocation = Colocation::create(['name' => 'Test Coloc', 'status' => 'active', 'owner_id' => $owner->id]);
        $colocation->members()->attach($owner->id, ['role' => 'owner', 'joined_at' => now()]);

        $response = $this->actingAs($owner)->post(route('colocations.invite', $colocation), [
            'email' => 'newmember@example.com',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('invitations', [
            'colocation_id' => $colocation->id,
            'email' => 'newmember@example.com',
        ]);
    }

    public function test_member_cannot_send_invitation()
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        $colocation = Colocation::create(['name' => 'Test Coloc', 'status' => 'active', 'owner_id' => $owner->id]);
        $colocation->members()->attach($owner->id, ['role' => 'owner', 'joined_at' => now()]);
        $colocation->members()->attach($member->id, ['role' => 'member', 'joined_at' => now()]);

        $response = $this->actingAs($member)->post(route('colocations.invite', $colocation), [
            'email' => 'newmember@example.com',
        ]);

        $response->assertStatus(403);
    }

    public function test_cannot_invite_user_with_active_colocation()
    {
        $owner = User::factory()->create();
        $existingMember = User::factory()->create(['email' => 'existing@example.com']);
        
        $colocation1 = Colocation::create(['name' => 'Coloc 1', 'status' => 'active', 'owner_id' => $owner->id]);
        $colocation1->members()->attach($owner->id, ['role' => 'owner', 'joined_at' => now()]);
        
        $colocation2 = Colocation::create(['name' => 'Coloc 2', 'status' => 'active', 'owner_id' => $existingMember->id]);
        $colocation2->members()->attach($existingMember->id, ['role' => 'owner', 'joined_at' => now()]);

        $response = $this->actingAs($owner)->post(route('colocations.invite', $colocation1), [
            'email' => 'existing@example.com',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_invitation_page_can_be_viewed()
    {
        $owner = User::factory()->create();
        $colocation = Colocation::create(['name' => 'Test Coloc', 'status' => 'active', 'owner_id' => $owner->id]);
        
        $invitation = Invitation::create([
            'colocation_id' => $colocation->id,
            'email' => 'test@example.com',
            'token' => 'test-token-123',
            'expires_at' => now()->addDays(7),
        ]);

        $response = $this->get(route('invitations.show', $invitation->token));

        $response->assertStatus(200);
        $response->assertSee($colocation->name);
    }

    public function test_expired_invitation_cannot_be_viewed()
    {
        $owner = User::factory()->create();
        $colocation = Colocation::create(['name' => 'Test Coloc', 'status' => 'active', 'owner_id' => $owner->id]);
        
        $invitation = Invitation::create([
            'colocation_id' => $colocation->id,
            'email' => 'test@example.com',
            'token' => 'test-token-123',
            'expires_at' => now()->subDays(1),
        ]);

        $response = $this->get(route('invitations.show', $invitation->token));

        $response->assertRedirect('/');
        $response->assertSessionHas('error');
    }

    public function test_user_can_accept_invitation()
    {
        $owner = User::factory()->create();
        $newUser = User::factory()->create(['email' => 'newuser@example.com']);
        $colocation = Colocation::create(['name' => 'Test Coloc', 'status' => 'active', 'owner_id' => $owner->id]);
        $colocation->members()->attach($owner->id, ['role' => 'owner', 'joined_at' => now()]);
        
        $invitation = Invitation::create([
            'colocation_id' => $colocation->id,
            'email' => 'newuser@example.com',
            'token' => 'test-token-123',
            'expires_at' => now()->addDays(7),
        ]);

        $response = $this->actingAs($newUser)->post(route('invitations.accept', $invitation->token));

        $response->assertRedirect(route('colocations.index'));
        $this->assertTrue($colocation->members()->where('user_id', $newUser->id)->exists());
        $this->assertNotNull($invitation->fresh()->accepted_at);
    }

    public function test_user_with_active_colocation_cannot_accept_invitation()
    {
        $owner = User::factory()->create();
        $existingMember = User::factory()->create();
        
        $colocation1 = Colocation::create(['name' => 'Coloc 1', 'status' => 'active', 'owner_id' => $owner->id]);
        $colocation1->members()->attach($owner->id, ['role' => 'owner', 'joined_at' => now()]);
        
        $colocation2 = Colocation::create(['name' => 'Coloc 2', 'status' => 'active', 'owner_id' => $existingMember->id]);
        $colocation2->members()->attach($existingMember->id, ['role' => 'owner', 'joined_at' => now()]);
        
        $invitation = Invitation::create([
            'colocation_id' => $colocation1->id,
            'email' => $existingMember->email,
            'token' => 'test-token-123',
            'expires_at' => now()->addDays(7),
        ]);

        $response = $this->actingAs($existingMember)->post(route('invitations.accept', $invitation->token));

        $response->assertRedirect(route('colocations.index'));
        $response->assertSessionHas('error');
    }

    public function test_accepted_invitation_cannot_be_used_again()
    {
        $owner = User::factory()->create();
        $newUser = User::factory()->create(['email' => 'newuser@example.com']);
        $colocation = Colocation::create(['name' => 'Test Coloc', 'status' => 'active', 'owner_id' => $owner->id]);
        $colocation->members()->attach($owner->id, ['role' => 'owner', 'joined_at' => now()]);
        
        $invitation = Invitation::create([
            'colocation_id' => $colocation->id,
            'email' => 'newuser@example.com',
            'token' => 'test-token-123',
            'expires_at' => now()->addDays(7),
            'accepted_at' => now(),
        ]);

        $response = $this->actingAs($newUser)->post(route('invitations.accept', $invitation->token));

        $response->assertRedirect('/');
        $response->assertSessionHas('error');
    }

    public function test_user_who_left_can_rejoin_via_invitation()
    {
        $owner = User::factory()->create();
        $returningUser = User::factory()->create(['email' => 'returning@example.com']);
        $colocation = Colocation::create(['name' => 'Test Coloc', 'status' => 'active', 'owner_id' => $owner->id]);
        $colocation->members()->attach($owner->id, ['role' => 'owner', 'joined_at' => now()]);
        $colocation->members()->attach($returningUser->id, ['role' => 'member', 'joined_at' => now()->subDays(10), 'left_at' => now()->subDays(5)]);
        
        $invitation = Invitation::create([
            'colocation_id' => $colocation->id,
            'email' => 'returning@example.com',
            'token' => 'test-token-123',
            'expires_at' => now()->addDays(7),
        ]);

        $response = $this->actingAs($returningUser)->post(route('invitations.accept', $invitation->token));

        $response->assertRedirect(route('colocations.index'));
        
        $membership = $colocation->members()->where('user_id', $returningUser->id)->first();
        $this->assertNull($membership->pivot->left_at);
    }

    public function test_invitation_requires_valid_email()
    {
        $owner = User::factory()->create();
        $colocation = Colocation::create(['name' => 'Test Coloc', 'status' => 'active', 'owner_id' => $owner->id]);
        $colocation->members()->attach($owner->id, ['role' => 'owner', 'joined_at' => now()]);

        $response = $this->actingAs($owner)->post(route('colocations.invite', $colocation), [
            'email' => 'invalid-email',
        ]);

        $response->assertSessionHasErrors(['email']);
    }
}
