<?php

namespace Database\Seeders;

use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Tag;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $groups = ['books', 'authors', 'categories', 'tags', 'users', 'roles'];
        foreach ($groups as $group) {
            foreach (['view', 'create', 'update', 'delete'] as $action) {
                Permission::firstOrCreate(['slug' => "$group.$action"], ['name' => ucfirst($action).' '.ucfirst($group), 'group' => $group]);
            }
        }
        $all = Permission::all();
        $super = Role::create(['name' => 'Super Admin', 'slug' => 'super-admin', 'description' => 'Full workspace administration']);
        $editor = Role::create(['name' => 'Editor', 'slug' => 'editor', 'description' => 'Manage the library catalog']);
        $member = Role::create(['name' => 'Member', 'slug' => 'user', 'description' => 'Browse the catalog']);
        $super->permissions()->sync($all);
        $editor->permissions()->sync($all->whereIn('group', ['books','authors','categories','tags'])->pluck('id'));
        $member->permissions()->sync($all->where('slug', 'books.view')->pluck('id'));

        $admin = User::create(['name' => 'Avery Morgan', 'email' => 'admin@booklist.test', 'password' => 'password']);
        $admin->giveRole($super);
        $editorUser = User::create(['name' => 'Mina Patel', 'email' => 'editor@booklist.test', 'password' => 'password']);
        $editorUser->giveRole($editor);

        $authors = collect([
            ['name'=>'Kazuo Ishiguro','bio'=>'Nobel Prize-winning novelist exploring memory, time, and responsibility.'],
            ['name'=>'Ursula K. Le Guin','bio'=>'Visionary author of speculative fiction and thoughtful social worlds.'],
            ['name'=>'James Baldwin','bio'=>'American writer and essayist with a singular moral voice.'],
        ])->map(fn ($data) => Author::create($data));
        $fiction = Category::create(['name'=>'Literary Fiction','slug'=>'literary-fiction','description'=>'Distinctive contemporary and classic fiction.']);
        $essays = Category::create(['name'=>'Essays','slug'=>'essays','description'=>'Ideas, criticism, and cultural reflection.']);
        $staff = Tag::create(['name'=>'Staff pick','slug'=>'staff-pick']);
        $classic = Tag::create(['name'=>'Modern classic','slug'=>'modern-classic']);

        collect([
            ['title' => 'Klara and the Sun', 'isbn' => '9780571364886', 'author_id' => $authors[0]->id, 'status' => 'published', 'stock' => 8, 'published_at' => '2021-03-02', 'is_featured' => true],
            ['title' => 'The Left Hand of Darkness', 'isbn' => '9780441478125', 'author_id' => $authors[1]->id, 'status' => 'published', 'stock' => 2, 'published_at' => '1969-03-01', 'is_featured' => true],
            ['title' => 'The Fire Next Time', 'isbn' => '9780679744726', 'author_id' => $authors[2]->id, 'status' => 'published', 'stock' => 5, 'published_at' => '1963-01-31', 'is_featured' => true],
            ['title' => 'The Buried Giant', 'isbn' => '9780307455796', 'author_id' => $authors[0]->id, 'status' => 'draft', 'stock' => 0, 'published_at' => '2015-03-03'],
        ])->each(function ($data, $index) use ($fiction, $essays, $staff, $classic) {
            $book = Book::create($data);
            $book->categories()->attach($index === 2 ? $essays : $fiction);
            $book->tags()->attach($index % 2 ? $classic : $staff);
        });

        $this->call(QuoteSeeder::class);
    }
}
