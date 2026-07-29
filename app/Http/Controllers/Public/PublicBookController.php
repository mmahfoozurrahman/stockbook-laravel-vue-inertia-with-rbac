<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PublicBookController extends Controller
{
    public function index(Request $request)
    {
        $books = Book::with(['author', 'categories'])
            ->published()
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%$search%")
                        ->orWhere('isbn', 'like', "%$search%")
                        ->orWhereHas('author', fn ($a) => $a->where('name', 'like', "%$search%"));
                });
            })
            ->when($request->category, fn ($q, $slug) => $q->whereHas('categories', fn ($c) => $c->where('slug', $slug)))
            ->when($request->author, fn ($q, $id) => $q->where('author_id', $id))
            ->latest('published_at')
            ->paginate(12)
            ->withQueryString();

        $bookmarkedIds = Auth::check()
            ? Auth::user()->bookmarks()->pluck('book_id')->all()
            : [];

        return Inertia::render('Public/Books/Index', [
            'books' => $books,
            'categories' => Category::orderBy('name')->get(['id', 'name', 'slug']),
            'authors' => Author::orderBy('name')->get(['id', 'name']),
            'filters' => $request->only('search', 'category', 'author'),
            'bookmarkedIds' => $bookmarkedIds,
        ]);
    }

    public function show(Book $book)
    {
        abort_unless($book->status === 'published', 404);

        $related = Book::with('author')
            ->published()
            ->where('id', '!=', $book->id)
            ->when($book->author_id, fn ($q) => $q->where('author_id', $book->author_id))
            ->latest('published_at')
            ->limit(4)
            ->get();

        $isBookmarked = Auth::check() ? Auth::user()->hasBookmarked($book) : false;

        return Inertia::render('Public/Books/Show', [
            'book' => $book->load(['author', 'categories', 'tags']),
            'related' => $related,
            'isBookmarked' => $isBookmarked,
        ]);
    }
}
