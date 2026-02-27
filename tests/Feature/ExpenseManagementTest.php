<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Colocation;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpenseManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'CategorySeeder']);
    }

    /** @test */
    public function owner_can_create_expense()
    {
        $owner = User::factory()->create();
        $colocation = Colocation::create(['name' => 'Test Coloc', 'status' => 'active', 'owner_id' => $owner->id]);
        $colocation->members()->attach($owner->id, ['role' => 'owner', 'joined_at' => now()]);
        $category = Category::first();

        $response = $this->actingAs($owner)->post(route('expenses.store', $colocation), [
            'user_id' => $owner->id,
            'category_id' => $category->id,
            'description' => 'Test expense',
            'amount' => 100.50,
            'date' => now()->format('Y-m-d'),
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('expenses', [
            'colocation_id' => $colocation->id,
            'description' => 'Test expense',
            'amount' => 100.50,
        ]);
    }

    /** @test */
    public function member_cannot_create_expense()
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        $colocation = Colocation::create(['name' => 'Test Coloc', 'status' => 'active', 'owner_id' => $owner->id]);
        $colocation->members()->attach($owner->id, ['role' => 'owner', 'joined_at' => now()]);
        $colocation->members()->attach($member->id, ['role' => 'member', 'joined_at' => now()]);
        $category = Category::first();

        $response = $this->actingAs($member)->post(route('expenses.store', $colocation), [
            'user_id' => $member->id,
            'category_id' => $category->id,
            'description' => 'Test expense',
            'amount' => 100.50,
            'date' => now()->format('Y-m-d'),
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('expenses', ['description' => 'Test expense']);
    }

    /** @test */
    public function owner_can_delete_expense()
    {
        $owner = User::factory()->create();
        $colocation = Colocation::create(['name' => 'Test Coloc', 'status' => 'active', 'owner_id' => $owner->id]);
        $colocation->members()->attach($owner->id, ['role' => 'owner', 'joined_at' => now()]);
        $category = Category::first();
        
        $expense = Expense::create([
            'colocation_id' => $colocation->id,
            'user_id' => $owner->id,
            'category_id' => $category->id,
            'description' => 'Test expense',
            'amount' => 100.50,
            'date' => now(),
        ]);

        $response = $this->actingAs($owner)->delete(route('expenses.destroy', [$colocation, $expense]));

        $response->assertRedirect();
        $this->assertDatabaseMissing('expenses', ['id' => $expense->id]);
    }

    /** @test */
    public function member_cannot_delete_expense()
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        $colocation = Colocation::create(['name' => 'Test Coloc', 'status' => 'active', 'owner_id' => $owner->id]);
        $colocation->members()->attach($owner->id, ['role' => 'owner', 'joined_at' => now()]);
        $colocation->members()->attach($member->id, ['role' => 'member', 'joined_at' => now()]);
        $category = Category::first();
        
        $expense = Expense::create([
            'colocation_id' => $colocation->id,
            'user_id' => $member->id,
            'category_id' => $category->id,
            'description' => 'Test expense',
            'amount' => 100.50,
            'date' => now(),
        ]);

        $response = $this->actingAs($member)->delete(route('expenses.destroy', [$colocation, $expense]));

        $response->assertStatus(403);
        $this->assertDatabaseHas('expenses', ['id' => $expense->id]);
    }

    /** @test */
    public function expense_requires_valid_data()
    {
        $owner = User::factory()->create();
        $colocation = Colocation::create(['name' => 'Test Coloc', 'status' => 'active', 'owner_id' => $owner->id]);
        $colocation->members()->attach($owner->id, ['role' => 'owner', 'joined_at' => now()]);

        $response = $this->actingAs($owner)->post(route('expenses.store', $colocation), [
            'user_id' => '',
            'category_id' => '',
            'description' => '',
            'amount' => '',
            'date' => '',
        ]);

        $response->assertSessionHasErrors(['user_id', 'category_id', 'description', 'amount', 'date']);
    }

    /** @test */
    public function expense_amount_must_be_positive()
    {
        $owner = User::factory()->create();
        $colocation = Colocation::create(['name' => 'Test Coloc', 'status' => 'active', 'owner_id' => $owner->id]);
        $colocation->members()->attach($owner->id, ['role' => 'owner', 'joined_at' => now()]);
        $category = Category::first();

        $response = $this->actingAs($owner)->post(route('expenses.store', $colocation), [
            'user_id' => $owner->id,
            'category_id' => $category->id,
            'description' => 'Test expense',
            'amount' => -50,
            'date' => now()->format('Y-m-d'),
        ]);

        $response->assertSessionHasErrors(['amount']);
    }

    /** @test */
    public function expenses_are_filtered_by_month()
    {
        $owner = User::factory()->create();
        $colocation = Colocation::create(['name' => 'Test Coloc', 'status' => 'active', 'owner_id' => $owner->id]);
        $colocation->members()->attach($owner->id, ['role' => 'owner', 'joined_at' => now()]);
        $category = Category::first();

        Expense::create([
            'colocation_id' => $colocation->id,
            'user_id' => $owner->id,
            'category_id' => $category->id,
            'description' => 'January expense',
            'amount' => 100,
            'date' => '2026-01-15',
        ]);

        Expense::create([
            'colocation_id' => $colocation->id,
            'user_id' => $owner->id,
            'category_id' => $category->id,
            'description' => 'February expense',
            'amount' => 200,
            'date' => '2026-02-15',
        ]);

        $response = $this->actingAs($owner)->get(route('colocations.show', $colocation) . '?month=2026-01');

        $response->assertSee('January expense');
        $response->assertDontSee('February expense');
    }

    /** @test */
    public function statistics_show_correct_totals_by_category()
    {
        $owner = User::factory()->create();
        $colocation = Colocation::create(['name' => 'Test Coloc', 'status' => 'active', 'owner_id' => $owner->id]);
        $colocation->members()->attach($owner->id, ['role' => 'owner', 'joined_at' => now()]);
        $category1 = Category::first();
        $category2 = Category::skip(1)->first();

        Expense::create([
            'colocation_id' => $colocation->id,
            'user_id' => $owner->id,
            'category_id' => $category1->id,
            'description' => 'Expense 1',
            'amount' => 100,
            'date' => now(),
        ]);

        Expense::create([
            'colocation_id' => $colocation->id,
            'user_id' => $owner->id,
            'category_id' => $category1->id,
            'description' => 'Expense 2',
            'amount' => 50,
            'date' => now(),
        ]);

        Expense::create([
            'colocation_id' => $colocation->id,
            'user_id' => $owner->id,
            'category_id' => $category2->id,
            'description' => 'Expense 3',
            'amount' => 75,
            'date' => now(),
        ]);

        $response = $this->actingAs($owner)->get(route('colocations.show', $colocation));

        $response->assertSee('150.00');
        $response->assertSee('75.00');
        $response->assertSee('225.00');
    }

    /** @test */
    public function only_active_members_are_shown()
    {
        $owner = User::factory()->create();
        $activeMember = User::factory()->create(['name' => 'Active Member']);
        $leftMember = User::factory()->create(['name' => 'Left Member']);
        
        $colocation = Colocation::create(['name' => 'Test Coloc', 'status' => 'active', 'owner_id' => $owner->id]);
        $colocation->members()->attach($owner->id, ['role' => 'owner', 'joined_at' => now()]);
        $colocation->members()->attach($activeMember->id, ['role' => 'member', 'joined_at' => now()]);
        $colocation->members()->attach($leftMember->id, ['role' => 'member', 'joined_at' => now()->subDays(10), 'left_at' => now()->subDays(5)]);

        $response = $this->actingAs($owner)->get(route('colocations.show', $colocation));

        $response->assertSee('Active Member');
        $response->assertDontSee('Left Member');
    }

    /** @test */
    public function owner_can_assign_expense_to_any_member()
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        $colocation = Colocation::create(['name' => 'Test Coloc', 'status' => 'active', 'owner_id' => $owner->id]);
        $colocation->members()->attach($owner->id, ['role' => 'owner', 'joined_at' => now()]);
        $colocation->members()->attach($member->id, ['role' => 'member', 'joined_at' => now()]);
        $category = Category::first();

        $response = $this->actingAs($owner)->post(route('expenses.store', $colocation), [
            'user_id' => $member->id,
            'category_id' => $category->id,
            'description' => 'Member expense',
            'amount' => 100.50,
            'date' => now()->format('Y-m-d'),
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('expenses', [
            'user_id' => $member->id,
            'description' => 'Member expense',
        ]);
    }
}
