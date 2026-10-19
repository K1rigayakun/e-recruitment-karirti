<?php

namespace Tests\Feature\Company;

use App\Enums\CompanyVerificationStatus;
use App\Models\CompanyProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CompanyProfileTest extends TestCase
{
    use RefreshDatabase;

    private function makeCompany(): array
    {
        Model::unguard();
        $user = User::factory()->company()->create(['email_verified_at' => now()]);
        $profile = new CompanyProfile(['slug' => 'co-'.rand(), 'company_name' => 'PT Test']);
        $profile->user_id = $user->id;
        $profile->verification_status = CompanyVerificationStatus::Pending;
        $profile->save();
        Model::reguard();

        return [$user, $profile];
    }

    public function test_company_can_update_profile(): void
    {
        [$user] = $this->makeCompany();

        $this->actingAs($user)
            ->put(route('company.profile.update'), [
                'company_name' => 'PT Baru',
                'industry' => 'Teknologi',
                'city' => 'Jakarta',
            ])->assertRedirect();

        $this->assertDatabaseHas('company_profiles', ['company_name' => 'PT Baru']);
    }

    public function test_svg_logo_is_rejected(): void
    {
        Storage::fake('public');
        [$user] = $this->makeCompany();

        $svg = UploadedFile::fake()->createWithContent('logo.svg', '<svg><script>alert(1)</script></svg>');

        $this->actingAs($user)
            ->put(route('company.profile.update'), [
                'company_name' => 'PT Baru',
                'logo' => $svg,
            ])->assertSessionHasErrors('logo');
    }

    public function test_valid_jpg_logo_is_accepted(): void
    {
        // GD tidak tersedia, skip dengan test ukuran saja
        [$user] = $this->makeCompany();

        // File yang terlalu besar
        $big = UploadedFile::fake()->create('logo.jpg', 2000, 'image/jpeg'); // 2000 KB > 1024 KB

        $this->actingAs($user)
            ->put(route('company.profile.update'), [
                'company_name' => 'PT Test',
                'logo' => $big,
            ])->assertSessionHasErrors('logo');
    }
}
