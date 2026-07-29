<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;

class CategoryController extends LookupController
{
    protected string $model = Category::class;

    protected string $type = 'categories';

    protected string $permission = 'categories';

    protected array $fields = [
        ['name' => 'name', 'label' => 'Category name', 'type' => 'text'],
        ['name' => 'description', 'label' => 'Description', 'type' => 'editor'],
    ];
}
