<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $books = Book::with(['author', 'categories', 'tags'])
            ->when($request->search, fn ($q, $s) => $q->where(
                fn ($q) => $q->where('title', 'like', "%$s%")->orWhere('isbn', 'like', "%$s%")
            ))
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return Inertia::render('Admin/Books/Index', [
            'books' => $books,
            'authors' => Author::orderBy('name')->get(['id', 'name']),
            'categories' => Category::orderBy('name')->get(['id', 'name']),
            'tags' => Tag::orderBy('name')->get(['id', 'name']),
            'filters' => $request->only('search', 'status'),
        ]);
    }

    public function store(Request $request)
    {
        $request->user()->hasPermission('books.create') ?: abort(403);
        $book = Book::create($this->validated($request));
        $this->syncRelationsAndCover($request, $book);

        return back()->with('success', 'Book added to the catalog.');
    }

    public function update(Request $request, Book $book)
    {
        $request->user()->hasPermission('books.update') ?: abort(403);
        $book->update($this->validated($request, $book));
        $this->syncRelationsAndCover($request, $book);

        return back()->with('success', 'Book details updated.');
    }

    public function destroy(Request $request, Book $book)
    {
        $request->user()->hasPermission('books.delete') ?: abort(403);

        if ($book->cover_image) {
            Storage::disk('public')->delete($book->cover_image);
        }

        $book->delete();

        return back()->with('success', 'Book removed from the catalog.');
    }

    private function validated(Request $request, ?Book $book = null): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'isbn' => ['nullable', 'string', 'max:64', Rule::unique('books')->ignore($book)],
            'description' => ['nullable', 'string'],
            'published_at' => ['nullable', 'date'],
            'status' => ['required', Rule::in(['draft', 'published', 'archived'])],
            'stock' => ['required', 'integer', 'min:0'],
            'is_featured' => ['boolean'],
            'author_id' => ['nullable', 'exists:authors,id'],
            'cover' => ['nullable', 'image', 'max:3072'],
            'categories' => ['array'],
            'categories.*' => ['exists:categories,id'],
            'tags' => ['array'],
            'tags.*' => ['exists:tags,id'],
        ]);
    }

    private function syncRelationsAndCover(Request $request, Book $book): void
    {
        $book->categories()->sync($request->input('categories', []));
        $book->tags()->sync($request->input('tags', []));
        $book->update(['is_featured' => $request->boolean('is_featured')]);

        if ($request->hasFile('cover')) {
            if ($book->cover_image) {
                Storage::disk('public')->delete($book->cover_image);
            }

            $book->update([
                'cover_image' => $request->file('cover')->store('covers', 'public'),
            ]);
        }
    }
}
