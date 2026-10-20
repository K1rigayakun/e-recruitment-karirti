<?php

namespace Tests\Feature;

use Tests\TestCase;

class StyleguideTest extends TestCase
{
    public function test_styleguide_is_available_in_local(): void
    {
        $this->withoutVite();
        $this->app['env'] = 'local';

        $this->get('/styleguide')
            ->assertOk()
            ->assertSee('Sistem desain', false);
    }

    public function test_styleguide_is_hidden_in_production(): void
    {
        $this->withoutVite();
        $this->app['env'] = 'production';

        $this->get('/styleguide')->assertNotFound();
    }
}
