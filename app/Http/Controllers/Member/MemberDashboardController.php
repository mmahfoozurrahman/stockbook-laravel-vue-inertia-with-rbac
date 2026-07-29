<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MemberDashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();

        $bookmarks = $user->bookmarkedBooks()
            ->with('author')
            ->latest('bookmarks.created_at')
            ->limit(6)
            ->get();

        $bookmarkCount = $user->bookmarks()->count();

        $recentPublished = Book::published()
            ->with('author')
            ->latest('published_at')
            ->limit(5)
            ->get();

        return Inertia::render('Member/Dashboard', [
            'stats' => [
                'bookmarks' => $bookmarkCount,
                'thisMonth' => $user->bookmarks()
                    ->where('created_at', '>=', now()->subMonth())
                    ->count(),
            ],
            'bookmarks' => $bookmarks,
            'recentPublished' => $recentPublished,
        ]);
    }
}
