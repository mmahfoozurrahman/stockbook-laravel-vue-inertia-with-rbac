<?php

namespace App\Http\Controllers\Admin;

use App\Models\Book;
use App\Models\User;
use Inertia\Inertia;

class DashboardController extends AdminController
{
    public function __invoke()
    {
        $recent = Book::with('author')->latest()->limit(6)->get();

        return Inertia::render('Admin/Dashboard', [
            'metrics' => [
                'books' => Book::count(),
                'published' => Book::where('status', 'published')->count(),
                'users' => User::count(),
                'lowStock' => Book::where('stock', '<=', 3)->count(),
            ],
            'recentBooks' => $recent,
            'topBooks' => Book::with('author')->orderByDesc('stock')->limit(5)->get(),
        ]);
    }
}
