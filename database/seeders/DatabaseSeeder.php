<?php

namespace Database\Seeders;

use App\Models\Agency;
use App\Models\Property;
use App\Models\Province;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        if (Property::query()->withoutGlobalScopes()->exists()) {
            return;
        }

        $provinces = collect(['دمشق', 'حلب', 'حمص', 'حماة', 'اللاذقية', 'طرطوس', 'إدلب', 'درعا'])
            ->map(fn (string $name) => Province::firstOrCreate(['name' => $name]));

        $agencies = collect(range(1, 10))->map(function (int $i): Agency {
            $agency = Agency::firstOrCreate(
                ['email' => "agency{$i}@eman-dawara.test"],
                ['name' => "مكتب عقاري {$i}", 'phone' => "+9639000000{$i}"]
            );
            $user = User::firstOrCreate(
                ['email' => "agent{$i}@eman-dawara.test"],
                ['name' => "موظف {$i}", 'password' => Hash::make('password'), 'agency_id' => $agency->id]
            );
            $user->tokens()->firstOrCreate(
                ['name' => 'demo-token'],
                ['token' => hash('sha256', 'eman-dawara-demo-token-'.$i), 'abilities' => ['*']]
            );

            return $agency;
        });

        $titles = ['شقة واسعة بإطلالة', 'محل تجاري في موقع حيوي', 'أرض سكنية'];
        Property::withoutEvents(function () use ($agencies, $provinces, $titles): void {
            foreach (range(1, 3000) as $i) {
                $property = Property::create([
                    'agency_id' => $agencies->random()->id,
                    'province_id' => $provinces->random()->id,
                    'title' => $titles[$i % 3]." رقم {$i}",
                    'description' => 'عقار تجريبي لبيانات المهمة رقم '.$i,
                    'type' => ['apartment', 'shop', 'land'][$i % 3],
                    'offer_type' => $i % 2 ? 'sale' : 'rent',
                    'status' => $i % 20 === 0 ? 'pending' : 'available',
                    'price_amount' => random_int(15000, 500000),
                    'currency' => ['USD', 'EUR', 'SYP', 'TRY'][$i % 4],
                    'address' => 'شارع تجريبي، مبنى '.($i % 100),
                    'bedrooms' => $i % 4,
                    'bathrooms' => 1,
                    'area_sqm' => random_int(40, 300),
                    'owner_phone' => '+963911000000',
                    'commission_percent' => 2.5,
                    'internal_notes' => 'ملاحظة داخلية سرية',
                    'is_featured' => $i % 30 === 0,
                    'approved_at' => $i % 20 === 0 ? null : now()->subDays(random_int(0, 365)),
                ]);
                $property->media()->create([
                    'path' => 'https://example.test/property/'.$i.'.jpg',
                    'collection' => 'property',
                ]);
            }
        });
    }
}
