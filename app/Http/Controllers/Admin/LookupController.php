<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

abstract class LookupController extends Controller
{
    protected string $model;

    protected string $type;

    protected string $permission;

    protected array $fields;

    public function index()
    {
        $model = $this->model;

        return Inertia::render('Admin/Lookup/Index', [
            'type' => $this->type,
            'fields' => $this->fields,
            'items' => $model::query()
                ->withCount($this->type === 'authors' ? 'books' : ($this->type === 'permissions' ? 'roles' : 'books'))
                ->latest()
                ->paginate(15),
        ]);
    }

    public function store(Request $request)
    {
        $request->user()->hasPermission("$this->permission.create") ?: abort(403);

        $model = $this->model;
        $model::create($this->payload($request));

        return back()->with('success', Str::singular(ucfirst($this->type)).' created.');
    }

    public function update(Request $request, int $id)
    {
        $request->user()->hasPermission("$this->permission.update") ?: abort(403);

        $item = $this->find($id);
        $item->update($this->payload($request, $item));

        return back()->with('success', Str::singular(ucfirst($this->type)).' updated.');
    }

    public function destroy(Request $request, int $id)
    {
        $request->user()->hasPermission("$this->permission.delete") ?: abort(403);

        $this->find($id)->delete();

        return back()->with('success', 'Record deleted.');
    }

    protected function payload(Request $request, ?Model $item = null): array
    {
        $rules = ['name' => ['required', 'string', 'max:255']];

        foreach ($this->fields as $field) {
            if ($field['name'] !== 'name') {
                $rules[$field['name']] = ['nullable', 'string'];
            }
        }

        $data = $request->validate($rules);

        if (in_array($this->type, ['categories', 'tags'])) {
            $data['slug'] = Str::slug($data['name']).($item ? "-$item->id" : '');
        }

        return $data;
    }

    protected function find(int $id): Model
    {
        $model = $this->model;

        return $model::findOrFail($id);
    }
}
