<?php

namespace App\Http\Controllers\Web\Admin;
use App\Http\Controllers\Controller;

use App\Models\BlogCategory;
use App\Models\User;
use App\Models\Blog;
use App\Models\Utility;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        if (\Auth::user()->can('manage blogs')) {
            $userId = \Auth::user()->creatorId();
            $query = Blog::where('created_by', $userId);

            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'LIKE', "%{$search}%")
                      ->orWhere('description', 'LIKE', "%{$search}%");
                });
            }

            $blogs = $query->latest()->paginate(12)->appends($request->all());

            return view('admin.blogs.index', compact('blogs'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        if (\Auth::user()->can('manage blogs')) {
            $userId = \Auth::user()->creatorId();
            $categories = BlogCategory::where('created_by', $userId)->pluck('name', 'id')->prepend('Select', '');
            return view('admin.blogs.create', compact('categories'));
        } else {
            return redirect()->back();
        }
    }

    public function store(Request $request)
    {
        if (\Auth::user()->can('manage blogs')) {
            $userId = \Auth::user()->creatorId();

            $validator = \Validator::make($request->all(), [
                'category_id' => 'required|exists:blog_categories,id',
                'title' => 'required|string|max:255|unique:blogs,title',
                'description' => 'required|string',
                'status' => 'required|integer',
                'meta_title' => 'nullable|string',
                'meta_description' => 'nullable|string',
                'meta_keywords' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->with('error', $validator->errors()->first());
            }

            $data = $validator->validated();

            $data['slug'] = \Str::slug($request->title);
            $data['created_by'] = $userId;

            Blog::create($data);

            return redirect()->route('general.blogs.index')->with('success', __('Blog created successfully.'));
        }

        return redirect()->back()->with('error', __('Permission denied.'));
    }

    public function edit($id)
    {
        if (\Auth::user()->can('manage blogs')) {
            $blog = Blog::findOrFail($id);
            $userId = \Auth::user()->creatorId();
            $categories = BlogCategory::where('created_by', $userId)->pluck('name', 'id')->prepend('Select', '');
            return view('admin.blogs.edit', compact('blog', 'categories'));
        }

        return redirect()->back();
    }

    public function update(Request $request, $id)
    {
        if (\Auth::user()->can('manage blogs')) {
            $userId = \Auth::user()->creatorId();

            $validator = \Validator::make($request->all(), [
                'category_id' => 'required|exists:blog_categories,id',
                'title' => 'required|string|max:255|unique:blogs,title,'.$id,
                'description' => 'required|string',
                'status' => 'required|integer',
                'meta_title' => 'nullable|string',
                'meta_description' => 'nullable|string',
                'meta_keywords' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->with('error', $validator->errors()->first());
            }

            $data = $validator->validated();

            $data['slug'] = \Str::slug($request->title);
            $data['created_by'] = $userId;

            $blog = Blog::findOrFail($id);
            $blog->update($data);

            return redirect()->route('general.blogs.index')->with('success', __('Blog updated successfully.'));
        }

        return redirect()->back()->with('error', __('Permission denied.'));
    }

    public function destroy($id)
    {
        if (\Auth::user()->can('manage blogs')) {
            $blog = Blog::find($id);
            if ($blog) {
                $blog->delete();
                return redirect()->back()->with('success', __('Blog deleted successfully.'));
            }
            return redirect()->back()->with('error', __('Blog not found.'));
        }

        return redirect()->back()->with('error', __('Permission denied.'));
    }
}

