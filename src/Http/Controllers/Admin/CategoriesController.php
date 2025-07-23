<?php

namespace Shopen\Http\Controllers\Admin;

use Shopen\Http\Controller;
use Shopen\Repositories\Category\CategoryRepository;

class CategoriesController extends Controller
{
    public function __construct(private readonly CategoryRepository $categoryRepository)
    {}

    public function index()
    {
        $categories = $this->categoryRepository->getAll();
        return $this->view('admin.category.index', compact('categories'));
    }
}