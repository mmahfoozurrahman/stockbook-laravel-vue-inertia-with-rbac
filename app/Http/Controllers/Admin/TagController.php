<?php

namespace App\Http\Controllers\Admin;

use App\Models\Tag;

class TagController extends LookupController
{
    protected string $model = Tag::class;

    protected string $type = 'tags';

    protected string $permission = 'tags';

    protected array $fields = [
        ['name' => 'name', 'label' => 'Tag name', 'type' => 'text'],
    ];
}
