<?php

namespace App\Livewire\Categories;

use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CategoryIndex extends Component
{
    public string $search = '';
    public string $filterType = 'all'; // all, income, expense

    // Modal state
    public bool $showModal = false;
    public bool $showDeleteModal = false;
    public ?int $editingId = null;
    public ?int $deletingId = null;

    // Form fields
    public string $name = '';
    public string $type = 'expense';
    public string $color = '#6366f1';
    public string $icon = 'folder';

    protected function rules(): array
    {
        $uniqueRule = 'unique:categories,name';
        if ($this->editingId) {
            $uniqueRule .= ',' . $this->editingId;
        }

        return [
            'name' => ['required', 'string', 'max:100'],
            'type' => ['required', 'in:income,expense'],
            'color' => ['required', 'string', 'max:7'],
            'icon' => ['required', 'string', 'max:50'],
        ];
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEditModal(int $id): void
    {
        $category = Category::forUser(Auth::id())->findOrFail($id);
        $this->editingId = $category->id;
        $this->name = $category->name;
        $this->type = $category->type;
        $this->color = $category->color;
        $this->icon = $category->icon;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'type' => $this->type,
            'color' => $this->color,
            'icon' => $this->icon,
        ];

        if ($this->editingId) {
            $category = Category::forUser(Auth::id())->findOrFail($this->editingId);
            $category->update($data);
            session()->flash('success', 'Kategori berhasil diperbarui!');
        } else {
            Auth::user()->categories()->create($data);
            session()->flash('success', 'Kategori berhasil ditambahkan!');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingId = $id;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        if ($this->deletingId) {
            $category = Category::forUser(Auth::id())->findOrFail($this->deletingId);
            $category->delete();
            session()->flash('success', 'Kategori berhasil dihapus!');
        }

        $this->showDeleteModal = false;
        $this->deletingId = null;
    }

    public function resetForm(): void
    {
        $this->editingId = null;
        $this->name = '';
        $this->type = 'expense';
        $this->color = '#6366f1';
        $this->icon = 'folder';
        $this->resetValidation();
    }

    public function render()
    {
        $query = Category::forUser(Auth::id());

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        if ($this->filterType !== 'all') {
            $query->where('type', $this->filterType);
        }

        $categories = $query->orderBy('type')->orderBy('name')->get();

        $incomeCategories = $categories->where('type', 'income');
        $expenseCategories = $categories->where('type', 'expense');

        return view('livewire.categories.category-index', [
            'incomeCategories' => $incomeCategories,
            'expenseCategories' => $expenseCategories,
        ])->layout('components.layouts.app', ['header' => 'Kategori', 'title' => 'Kategori']);
    }
}
