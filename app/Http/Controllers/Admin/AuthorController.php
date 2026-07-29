<?php

namespace App\Http\Controllers\Admin;

use App\Models\Author;

class AuthorController extends LookupController
{
    protected string $model = Author::class;

    protected string $type = 'authors';

    protected string $permission = 'authors';

    protected array $fields = [
        ['name' => 'name', 'label' => 'Author name', 'type' => 'text'],
        ['name' => 'bio', 'label' => 'Biography', 'type' => 'editor'],
    ];
}
