<?php

namespace Tests\Feature\Applicant;

use App\Enums\UserRole;
use App\Models\ApplicantProfile;
use App\Models\PortfolioProject;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortfolioTest extends TestCase
{
    use RefreshDatabase;

    private function makeApplicantWithProfile(): array
    {
        Model::unguard();
        $user = User::factory()->create([
            'role' => UserRole::Applicant,
            'email_verified_at' => now(),
        ]);
        $profile = new ApplicantProfile(['slug' => 'ap-'.rand()]);
        $profile->user_id = $user->id;
        $profile->save();
        Model::reguard();

        return [$user, $profile];
    }

    private function validProjectData(): array
    {
        return [
            'title' => 'Sistem Informasi',
            'description' => 'Deskripsi project yang valid.',
            'github_url' => 'https://github.com/user/repo',
            'tech_stack' => ['Laravel', 'PHP'],
        ];
    }

    public function test_applicant_can_create_project(): void
    {
        [$user] = $this->makeApplicantWithProfile();

        $this->actingAs($user)
            ->post(route('applicant.projects.store'), $this->validProjectData())
            ->assertRedirect(route('applicant.projects.index'));

        $this->assertDatabaseHas('portfolio_projects', ['title' => 'Sistem Informasi']);
    }

    public function test_project_limit_enforced(): void
    {
        [$user, $profile] = $this->makeApplicantWithProfile();

        // Buat 6 proyek (max)
        Model::unguard();
        for ($i = 0; $i < 6; $i++) {
            $p = new PortfolioProject([
                'title' => "Project $i", 'description' => 'Desc',
                'github_url' => "https://github.com/u/r$i",
                'tech_stack' => ['PHP'], 'sort_order' => $i,
            ]);
            $p->applicant_profile_id = $profile->id;
            $p->save();
        }
        Model::reguard();

        $this->actingAs($user)
            ->post(route('applicant.projects.store'), $this->validProjectData())
            ->assertSessionHasErrors('limit');
    }

    public function test_other_applicant_cannot_edit_project(): void
    {
        [$owner, $profile] = $this->makeApplicantWithProfile();
        [$other] = $this->makeApplicantWithProfile();

        Model::unguard();
        $project = new PortfolioProject([
            'title' => 'P', 'description' => 'D',
            'github_url' => 'https://github.com/u/r',
            'tech_stack' => ['PHP'], 'sort_order' => 0,
        ]);
        $project->applicant_profile_id = $profile->id;
        $project->save();
        Model::reguard();

        $this->actingAs($other)
            ->put(route('applicant.projects.update', $project), $this->validProjectData())
            ->assertForbidden();
    }

    public function test_portfolio_public_page_shows_public_profile(): void
    {
        [$user, $profile] = $this->makeApplicantWithProfile();
        $profile->is_public = true;
        $profile->save();

        $this->withoutVite()
            ->get(route('portfolios.show', $profile->slug))
            ->assertOk();
    }

    public function test_portfolio_non_public_returns_404(): void
    {
        [$user, $profile] = $this->makeApplicantWithProfile();
        // is_public default false

        $this->withoutVite()
            ->get(route('portfolios.show', $profile->slug))
            ->assertNotFound();
    }

    public function test_portfolio_public_does_not_expose_contact(): void
    {
        [$user, $profile] = $this->makeApplicantWithProfile();
        $profile->is_public = true;
        $profile->phone = '081234567890';
        $profile->nim = '1234567890';
        $profile->save();

        $response = $this->withoutVite()->get(route('portfolios.show', $profile->slug));
        $response->assertDontSee('081234567890');
        $response->assertDontSee('1234567890');
    }
}
