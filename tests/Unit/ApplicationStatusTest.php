<?php

namespace Tests\Unit;

use App\Enums\ApplicationStatus;
use PHPUnit\Framework\TestCase;

class ApplicationStatusTest extends TestCase
{
    /** Semua status final tidak bisa transisi ke manapun. */
    public function test_final_statuses_have_no_allowed_next(): void
    {
        foreach ([ApplicationStatus::Accepted, ApplicationStatus::Rejected, ApplicationStatus::Withdrawn] as $status) {
            $this->assertEmpty($status->allowedNext(), "{$status->value} seharusnya final");
            $this->assertTrue($status->isFinal());
        }
    }

    /** Status non-final tidak final. */
    public function test_non_final_statuses(): void
    {
        foreach ([ApplicationStatus::Applied, ApplicationStatus::Viewed, ApplicationStatus::Interview] as $status) {
            $this->assertFalse($status->isFinal(), "{$status->value} seharusnya bukan final");
        }
    }

    /** Matriks transisi: applied bisa ke viewed. */
    public function test_applied_can_move_to_viewed(): void
    {
        $this->assertTrue(ApplicationStatus::Applied->canMoveTo(ApplicationStatus::Viewed));
    }

    /** Interview hanya bisa ke accepted, rejected, withdrawn. */
    public function test_interview_transitions(): void
    {
        $allowed = ApplicationStatus::Interview->allowedNext();
        $this->assertContains(ApplicationStatus::Accepted, $allowed);
        $this->assertContains(ApplicationStatus::Rejected, $allowed);
        $this->assertContains(ApplicationStatus::Withdrawn, $allowed);
        $this->assertNotContains(ApplicationStatus::Applied, $allowed);
        $this->assertNotContains(ApplicationStatus::Viewed, $allowed);
    }

    /** accepted tidak bisa ke rejected (status final). */
    public function test_accepted_cannot_move_to_rejected(): void
    {
        $this->assertFalse(ApplicationStatus::Accepted->canMoveTo(ApplicationStatus::Rejected));
    }
}
