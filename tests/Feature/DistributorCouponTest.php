<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Distributor;
use App\Models\DistributorCoupon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

class DistributorCouponTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_returns_error_if_sales_value_is_below_1000()
    {
        $distributor = Distributor::factory()->create();

        $response = $this->postJson(route('coupons.store'), [
            'distributor_id' => $distributor->id,
            'sales_value'    => 500, // below threshold
            'area_name'      => 'Test Area',
            'expired_at'     => now()->addDays(7)->toDateString(),
        ]);

        $response->assertStatus(400)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Sales value must be at least 1000 to generate a coupon.',
                 ]);
    }

    /** @test */
    public function it_creates_coupons_when_sales_value_is_valid()
    {
        $distributor = Distributor::factory()->create();

        $response = $this->postJson(route('coupons.store'), [
            'distributor_id' => $distributor->id,
            'sales_value'    => 2000, // should generate 2 coupons logically
            'area_name'      => 'Cairo Zone',
            'expired_at'     => Carbon::now()->addDays(10)->toDateString(),
            'points'         => 100,
        ]);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Coupon created successfully',
                 ]);

        $this->assertDatabaseHas('distributor_coupons', [
            'distributor_id' => $distributor->id,
            'area_name'      => 'Cairo Zone',
        ]);
    }

    /** @test */
    public function it_validates_required_fields()
    {
        $response = $this->postJson(route('coupons.store'), []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors([
                     'distributor_id',
                     'sales_value',
                     'area_name',
                     'expired_at',
                 ]);
    }

    /** @test */
    public function it_returns_all_coupons_with_their_distributors()
    {
        $distributor = Distributor::factory()->create();
        DistributorCoupon::factory()->create(['distributor_id' => $distributor->id]);

        $response = $this->getJson(route('coupons.index'));

        $response->assertOk()
                 ->assertJsonStructure([
                     '*' => ['id', 'distributor_id', 'area_name', 'sales_value', 'code', 'status' , 'points']
                 ]);
    }
}
