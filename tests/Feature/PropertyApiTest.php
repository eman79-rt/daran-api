<?php

namespace Tests\Feature;

use App\Models\Agency;
use App\Models\Property;
use App\Models\Province;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PropertyApiTest extends TestCase
{
    use RefreshDatabase;

    private function property(Agency $agency, Province $province, array $overrides = []): Property
    {
        return Property::create(array_merge([
            'agency_id' => $agency->id,
            'province_id' => $province->id,
            'title' => 'Test property',
            'description' => 'Description',
            'type' => 'apartment',
            'offer_type' => 'sale',
            'status' => 'available',
            'price_amount' => 100,
            'currency' => 'USD',
            'address' => 'Test address',
            'area_sqm' => 50,
            'owner_phone' => 'secret-phone',
            'approved_at' => now(),
        ], $overrides));
    }

    public function test_v1_and_v2_return_different_price_shapes(): void
    {
        $agency = Agency::create(['name' => 'Agency A']);
        $province = Province::create(['name' => 'Province A']);
        $property = $this->property($agency, $province);

        $this->getJson('/api/v1/properties/'.$property->id)
            ->assertOk()->assertJsonPath('data.price', 100);
        $this->getJson('/api/v2/properties/'.$property->id)
            ->assertOk()->assertJsonPath('data.price.amount', 100)
            ->assertJsonPath('data.price.currency', 'USD');
    }

    public function test_private_fields_never_appear_and_phone_is_hidden_from_guests(): void
    {
        $agency = Agency::create(['name' => 'Agency A']);
        $province = Province::create(['name' => 'Province A']);
        $property = $this->property($agency, $province, [
            'commission_percent' => 8,
            'internal_notes' => 'private note',
        ]);

        $this->getJson('/api/v1/properties/'.$property->id)
            ->assertOk()
            ->assertJsonMissing(['contact_phone' => 'secret-phone'])
            ->assertJsonMissing(['internal_notes' => 'private note'])
            ->assertJsonMissing(['commission_percent' => 8]);
    }

    public function test_office_can_see_its_pending_property_but_cannot_edit_another_office_property(): void
    {
        $agencyA = Agency::create(['name' => 'Agency A']);
        $agencyB = Agency::create(['name' => 'Agency B']);
        $province = Province::create(['name' => 'Province A']);
        $user = User::create(['name' => 'Agent A', 'email' => 'agent-a@example.test', 'password' => 'password', 'agency_id' => $agencyA->id]);
        $ownPending = $this->property($agencyA, $province, ['status' => 'pending', 'approved_at' => null]);
        $foreignPublic = $this->property($agencyB, $province);

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/properties/'.$ownPending->id)->assertOk();
        $this->putJson('/api/v1/properties/'.$foreignPublic->id, ['title' => 'Changed title'])
            ->assertForbidden();
    }
}
