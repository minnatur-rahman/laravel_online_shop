<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\category;
use App\Models\TempImage;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManagerStatic as Image;


class CategoryController extends Controller
{
    public function index(Request $request){
        $categories = category::latest();

        if (!empty($request->get('keyword'))){
            $categories = $categories->where('name', 'like','%'.$request->get('keyword').'%');
        }

        $categories = $categories->paginate(10);

        return view('admin.category.list', compact('categories'));
    }

    public function create(){
        return view('admin.category.create');
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'slug' => 'required|unique:categories',
        ]);

        if ($validator->passes()){

            $category = new Category();
            $category->name = $request->name;
            $category->slug = $request->slug;
            $category->status = $request->status;
            $category->save();
            // Save Image Here
            if(!empty($request->image_id)){
                $tempImage = TempImage::find($request->image_id);
                $extArray =  explode('.'.$tempImage->name);
                $ext = last($extArray);

                $newImageName = $category->id.'.'.$ext;
                $sPath = public_path().'/temp/'.$tempImage->name;
                $dPath = public_path().'/uploads/category/'.$newImageName;
                File::copy($sPath,$dPath);

                $category->image = $newImageName;
                $category->save();
            }



                  // Path to the original image
                $originalImagePath = public_path('upload/category/'.$new_name);

                  // Path to save the thumbnail
                $thumbnailPath = public_path('upload/category/thumb'.$new_name);

                   // Create thumbnail
                Image::make($originalImagePath)
                ->fit(100, 100) // Adjust dimensions as needed
                ->save($thumbnailPath);

    // Optionally, return a response or do something else with the thumbnail


                $category->image = $newImageName;
                $category->save();

            }

            $request->session()->flash('success', 'Category add successfully');

            return response()->json([
                'status' => true,
                'message' => 'Category add successfully'
            ]);

        }else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function edit($categoryId, Request $request){
        $category = Category::find($categoryId);
        if (empty($category)){
            return redirect()->route('categories.index');
        }

       return view('admin.category.edit', compact('category'));
    }

    public function update($categoryId,  Request $request){

    }

    public function destroy(){

    }
}
