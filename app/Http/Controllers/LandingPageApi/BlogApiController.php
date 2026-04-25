<?php

namespace App\Http\Controllers\LandingPageApi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\Utility;

class BlogApiController extends Controller
{
    public function categories(Request $request)
    {
        $logoPath = Utility::get_file('uploads/blogs/categories/');
        $search = $request->input('search');

        $categoriesQuery = BlogCategory::select('id', 'name', 'slug', 'icon', 'short_description')
            ->where('status', 1);

        if ($search) {
            $categoriesQuery->where('name', 'like', "%{$search}%");
        }

        $categories = $categoriesQuery->latest()->get()->map(function ($cat) use ($logoPath) {
            return [
                'id' => $cat->id,
                'name' => $cat->name,
                'slug' => $cat->slug,
                'short_description' => $cat->short_description,
                'icon' => $logoPath . $cat->icon,
            ];
        });

        $popularBlogs = Blog::with(['category'])
            ->select('id', 'title', 'slug', 'category_id')
            ->where('status', 1)
            ->orderByDesc('views')
            ->take(5)
            ->get();

        return response()->json([
            'success' => true,
            'categories' => $categories,
            'popularBlogs' => $popularBlogs
        ]);
    }

    public function categoryDetails(Request $request, $slug)
    {
        $category = BlogCategory::where('slug', $slug)->where('status', 1)->first();
    
        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found.'
            ], 404);
        }
    
        $logoPath = Utility::get_file('uploads/blogs/categories/');
        $category->icon = $logoPath . $category->icon;
    
        $query = Blog::with(['category'])->select('id', 'title', 'slug', 'description', 'category_id')
            ->where('category_id', $category->id)
            ->where('status', 1);
    
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }
    
        $blogs = $query->orderByDesc('views')
            // ->take(10)
            ->get();
    
        return response()->json([
            'success' => true,
            'category' => $category,
            'blogs' => $blogs
        ]);
    }


    public function blogDetails($category_slug, $blog_slug)
    {
        $category = BlogCategory::where('slug', $category_slug)
            ->where('status', 1)
            ->first();

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found.'
            ],404);
        }

        $blog = Blog::where('slug', $blog_slug)
            ->where('category_id', $category->id)
            ->where('status', 1)
            ->first();

        if (!$blog) {
            return response()->json([
                'success' => false,
                'message' => 'Blog not found.'
            ],404);
        }

        if ($category->icon) {
            $logoPath = Utility::get_file('uploads/blogs/categories/');
            $category->icon = $logoPath . $category->icon;
        }

        $relatedBlogs = Blog::with(['category'])
            ->select('id', 'title', 'slug', 'category_id')
            ->where('category_id', $category->id)
            ->where('status', 1)
            ->orderByDesc('views')
            ->take(10)
            ->get();

        return response()->json([
            'success' => true,
            'blog' => $blog,
            'category' => $category,
            'related_blogs' => $relatedBlogs
        ]);
    }
}

