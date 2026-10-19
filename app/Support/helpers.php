<?php

if (! function_exists('escapeLike')) {
    /**
     * Escape karakter khusus untuk LIKE query.
     * Pakai: where("title", "like", "%" . escapeLike($q) . "%")
     */
    function escapeLike(string $value): string
    {
        return str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $value);
    }
}

if (! function_exists('is_role')) {
    /**
     * Cek apakah pengguna yang sedang login memiliki role tertentu.
     */
    function is_role(string $role): bool
    {
        return auth()->check() && auth()->user()->role?->value === $role;
    }
}
