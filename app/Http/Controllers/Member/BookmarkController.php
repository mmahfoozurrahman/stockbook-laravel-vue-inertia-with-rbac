<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Bookmark;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BookmarkController extends Controller
{
    public function index(Request $request)
    {
        $bookmarks = $request->user()
            ->bookmarkedBooks()
            ->with('author')
            ->withCount('bookmarks')
            ->latest('bookmarks.created_at')
            ->paginate(12);

        return Inertia::render('Member/Bookmarks/Index', [
            'bookmarks' => $bookmarks,
        ]);
    }

    public function toggle(Request $request, Book $book)
    {
        abort_unless($book->status === 'published', 404);

        $user = $request->user();
        $existing = $user->bookmarks()->where('book_id', $book->id)->first();

        if ($existing) {
            $existing->delete();
            $message = 'Removed from your bookmarks.';
        } else {
            $user->bookmarks()->create(['book_id' => $book->id]);
            $message = 'Bookmarked for later.';
        }

        return back()->with('success', $message);
    }

    public function destroy(Request $request, Bookmark $bookmark)
    {
        abort_unless($bookmark->user_id === $request->user()->id, 403);

        $bookmark->delete();

        return back()->with('success', 'Bookmark removed.');
    }
}
