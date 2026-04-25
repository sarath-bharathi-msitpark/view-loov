<?php

namespace App\Http\Controllers\Web\Admin;
use App\Http\Controllers\Controller;

use App\Models\BlogCategory;
use App\Models\User;
use App\Models\Utility;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BlogCategoryController extends Controller
{

    public function index(Request $request)
    {
        $user = \Auth::user();
    
        if ($user->can('manage blog categories')) {
            $query = BlogCategory::where('created_by', $user->creatorId());
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('short_description', 'LIKE', "%{$search}%");
                });
            }
    
            $categories = $query->paginate(12)->appends($request->all());
    
            return view('admin.blogs.category.index', compact('categories'));
        } else {
            return redirect()->back();
        }


    }

    public function create()
    {
        $user = \Auth::user();
        if ($user->can('manage blog categories')) {
            return view('admin.blogs.category.create');
        } else {
            return redirect()->back();
        }
    }

    public function store(Request $request)
    {

        if (\Auth::user()->can('manage blog categories')) {
            
            $objUser = \Auth::user()->creatorId();

                $validator = \Validator::make(
                    $request->all(), [
                        'name' => 'required|unique:blog_categories',
                        'icon' => 'nullable|file|mimes:jpg,jpeg,png,svg,webp',
                        'short_description' => 'nullable|string',
                        'status' => 'required|integer',
                        'meta_title' => 'nullable|string',
                        'meta_description' => 'nullable|string',
                        'meta_keywords' => 'nullable|string',
                    ]
                );
                
                if ($validator->fails()) {
                    $messages = $validator->getMessageBag();
                    return redirect()->back()->with('error', $messages->first());
                }
                
                $data = $validator->validated();
                
                if ($request->hasFile('icon')) {
                    $dir = "uploads/blogs/categories";
                    $fileName = uniqid() . $request->icon->getClientOriginalName();
                
                    $path = Utility::upload_file($request, 'icon', $fileName, $dir, []);
                    if ($path['flag'] == 1) {
                        $data['icon'] = $fileName;
                    } else {
                        return redirect()->back()->with('error', __($path['msg']));
                    }
                }
                
                $data['slug'] = \Str::slug($request->name);
                $data['created_by'] = $objUser;
                
                BlogCategory::create($data);

                return redirect()->route('general.blog-categories.index')->with('success', __('Successfully created.'));

        } else {
            return redirect()->back();
        }

    }

    public function edit($id)
    {
        $user = \Auth::user();
        if (\Auth::user()->can('manage blog categories')) {
            $category = BlogCategory::findOrFail($id);

            return view('admin.blogs.category.edit', compact('category'));
        } else {
            return redirect()->back();
        }

    }

    public function update(Request $request, $id)
    {
        if (\Auth::user()->can('manage blog categories')) {
            $objUser = \Auth::user()->creatorId();
    
            $validator = \Validator::make(
                $request->all(),
                [
                    'name' => 'required|unique:blog_categories,name,' . $id,
                    'icon' => 'nullable|file|mimes:jpg,jpeg,png,svg,webp',
                    'short_description' => 'nullable|string',
                    'status' => 'required|integer',
                    'meta_title' => 'nullable|string',
                    'meta_description' => 'nullable|string',
                    'meta_keywords' => 'nullable|string',
                ]
            );
    
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }
    
            $data = $validator->validated();
    
            if ($request->hasFile('icon')) {
                $dir = "uploads/blogs/categories";
                $fileName = uniqid() . $request->icon->getClientOriginalName();
    
                $path = Utility::upload_file($request, 'icon', $fileName, $dir, []);
                if ($path['flag'] == 1) {
                    $data['icon'] = $fileName;
                } else {
                    return redirect()->back()->with('error', __($path['msg']));
                }
            }
    
            $data['slug'] = \Str::slug($request->name);
            $data['created_by'] = $objUser;
    
            $blogCategory = BlogCategory::findOrFail($id);
            $blogCategory->update($data);
    
            return redirect()->route('general.blog-categories.index')->with('success', __('Successfully updated.'));
        }
    
        return redirect()->back()->with('error', __('Permission denied.'));
    }


    public function destroy($id)
    {

        if (\Auth::user()->can('manage blog categories')) {

            $blogCategory = BlogCategory::find($id);
            if ($blogCategory->id == 2) {
                return redirect()->back()->with('error', __('You can not delete By default Company'));
            }
            if ($blogCategory) {
                $blogCategory->delete();
                return redirect()->back()->with('success', __('Successfully deleted'));
            } else {
                return redirect()->back()->with('error', __('Something is wrong.'));
            }
        } else {
            return redirect()->back();
        }
    }
}
