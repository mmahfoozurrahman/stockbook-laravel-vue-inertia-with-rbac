<?php

namespace Tests\Feature\Member;

use App\Models\Book;
use App\Models\Bookmark;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class BookmarkTest extends TestCase
{
    use RefreshDatabase;

    public function test_bookmark_page_exposes_the_bookmark_id_used_by_the_delete_route(): void
    {
        $user = User::factory()->create();
        $book = Book::create([
            'title' => 'A saved book',
            'status' => 'published',
        ]);
        $bookmark = Bookmark::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);

        $this->actingAs($user)
            ->get(route('member.bookmarks.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Member/Bookmarks/Index')
                ->where('bookmarks.data.0.id', $book->id)
                ->where('bookmarks.data.0.pivot.id', $bookmark->id)
            );
    }

    public function test_user_can_delete_their_bookmark_by_its_id(): void
    {
        $user = User::factory()->create();
        $book = Book::create([
            'title' => 'A saved book',
            'status' => 'published',
        ]);
        $bookmark = Bookmark::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);

        $this->actingAs($user)
            ->from(route('member.bookmarks.index'))
            ->delete(route('member.bookmarks.destroy', $bookmark))
            ->assertRedirect(route('member.bookmarks.index'))
            ->assertSessionHas('success', 'Bookmark removed.');

        $this->assertDatabaseMissing('bookmarks', ['id' => $bookmark->id]);
    }
}
