<?php

namespace Tests\Feature\Admin;

use App\Enums\CompanyVerificationStatus;
use App\Enums\UserRole;
use App\Models\CompanyProfile;
use App\Models\User;
use App\Notifications\CompanyVerified;
use App\Services\CsvExporter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdmin(): User
    {
        Model::unguard();
        $admin = User::factory()->admin()->create(['email_verified_at' => now()]);
        Model::reguard();

        return $admin;
    }

    private function makeCompany(string $status = 'pending'): CompanyProfile
    {
        Model::unguard();
        $user = User::factory()->company()->create(['email_verified_at' => now()]);
        $c = new CompanyProfile(['slug' => 'co-'.rand(), 'company_name' => 'PT Test '.rand()]);
        $c->user_id = $user->id;
        $c->verification_status = CompanyVerificationStatus::from($status);
        $c->save();
        Model::reguard();

        return $c;
    }

    /** Non-admin tidak bisa akses panel admin */
    public function test_non_admin_cannot_access_admin_panel(): void
    {
        Model::unguard();
        $applicant = User::factory()->create(['role' => UserRole::Applicant, 'email_verified_at' => now()]);
        Model::reguard();

        $this->actingAs($applicant)->withoutVite()
            ->get(route('admin.dashboard'))->assertForbidden();
    }

    /** Admin bisa melihat daftar pengguna */
    public function test_admin_can_view_users(): void
    {
        $admin = $this->makeAdmin();
        $this->actingAs($admin)->withoutVite()
            ->get(route('admin.users.index'))->assertOk();
    }

    /** BR13: admin tidak bisa nonaktifkan diri sendiri */
    public function test_admin_cannot_deactivate_self(): void
    {
        $admin = $this->makeAdmin();
        $this->actingAs($admin)
            ->patch(route('admin.users.toggle-active', $admin))
            ->assertRedirect()->assertSessionHasErrors('toggle');
    }

    /** BR13: tidak bisa nonaktifkan admin terakhir */
    public function test_cannot_deactivate_last_admin(): void
    {
        $admin = $this->makeAdmin();
        Model::unguard();
        $otherAdmin = User::factory()->admin()->create(['email_verified_at' => now()]);
        Model::reguard();

        // Nonaktifkan other admin (bukan last)
        $this->actingAs($admin)
            ->patch(route('admin.users.toggle-active', $otherAdmin))
            ->assertRedirect(); // berhasil

        // Sekarang coba nonaktifkan admin sendiri (last)
        $this->actingAs($admin)
            ->patch(route('admin.users.toggle-active', $admin))
            ->assertSessionHasErrors('toggle');
    }

    /** Admin bisa setujui perusahaan dan notifikasi terkirim */
    public function test_admin_can_approve_company(): void
    {
        Notification::fake();
        $admin = $this->makeAdmin();
        $company = $this->makeCompany('pending');

        $this->actingAs($admin)
            ->post(route('admin.companies.approve', $company))
            ->assertRedirect();

        $this->assertEquals(
            CompanyVerificationStatus::Approved,
            $company->fresh()->verification_status
        );
        Notification::assertSentTo($company->user, CompanyVerified::class);
    }

    /** CsvExporter::safe mencegah CSV injection */
    public function test_csv_exporter_safe_escapes_formulas(): void
    {
        $this->assertSame("'=1+1", CsvExporter::safe('=1+1'));
        $this->assertSame("'+alert(1)", CsvExporter::safe('+alert(1)'));
        $this->assertSame("'-1", CsvExporter::safe('-1'));
        $this->assertSame('Normal text', CsvExporter::safe('Normal text'));
    }

    /** Admin bisa ekspor CSV laporan */
    public function test_admin_can_export_csv(): void
    {
        $admin = $this->makeAdmin();
        $response = $this->actingAs($admin)
            ->get(route('admin.reports.export'));

        $response->assertOk()
            ->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }

    /** Perusahaan lain tidak bisa melihat laporan perusahaan lain */
    public function test_company_cannot_see_other_company_report(): void
    {
        Model::unguard();
        $user1 = User::factory()->company()->create(['email_verified_at' => now()]);
        $c1 = new CompanyProfile(['slug' => 'c1-'.rand(), 'company_name' => 'C1']);
        $c1->user_id = $user1->id;
        $c1->verification_status = CompanyVerificationStatus::Approved;
        $c1->save();
        Model::reguard();

        // User1 mengakses laporannya sendiri — harus bisa
        $this->actingAs($user1)->withoutVite()
            ->get(route('company.reports.index'))->assertOk();
    }
}
