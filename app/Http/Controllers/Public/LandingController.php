<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Category;
use App\Models\Quote;
use Inertia\Inertia;

class LandingController extends Controller
{
    public function __invoke()
    {
        return Inertia::render('Public/Home', [
            'featuredBooks' => Book::with('author')
                ->published()
                ->featured()
                ->latest('published_at')
                ->limit(6)
                ->get(),
            'categories' => Category::orderBy('name')->get(['id', 'name', 'slug']),
            'recentBooks' => Book::with('author')
                ->published()
                ->latest('published_at')
                ->limit(8)
                ->get(),
            'quotes' => Quote::active()
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get(['id', 'body', 'attribution', 'source']),
        ]);
    }
}
