<?php

namespace App\Http\Requests\Company;

class UpdateJobRequest extends StoreJobRequest
{
    // Aturan sama dengan store; slug tidak berubah setelah dipublikasikan
    // (dikelola di controller, bukan di request)
}
