<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class EscapeLikeTest extends TestCase
{
    public function test_escapes_percent(): void
    {
        $this->assertSame('50\%', escapeLike('50%'));
    }

    public function test_escapes_underscore(): void
    {
        $this->assertSame('file\_name', escapeLike('file_name'));
    }

    public function test_escapes_backslash(): void
    {
        $this->assertSame('C:\\\\path', escapeLike('C:\\path'));
    }

    public function test_normal_string_unchanged(): void
    {
        $this->assertSame('laravel', escapeLike('laravel'));
    }
}
