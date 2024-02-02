<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\category;
use App\Models\TempImage;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rules\Unique;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;




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
            // dd($category);

            // Save Image Here
            if ($request->file('image_id')){
                // $tempImage = TempImage::find($request->image_id);
                // $extArray = explode('.', $tempImage->name);
                // $ext = last($extArray);

                // $newImageName = $category->id.'.'.$ext;
                // $sPath = public_path().'/temp/'.$tempImage->name;
                // $dPath = public_path().'/uploads/category/'.$newImageName;
                // File::copy($sPath,$dPath);

                // Generate Image Thumbnail

                $manager = new ImageManager(new Driver());
                $name_gen = hexdec(Uniqid()).'.'.$request->file('image_id')->getClientOriginalExtension();
                $img = $manager->read($request->file('image_id'));

                // $dPath = public_path().'/uploads/category/thumb'.$newImageName;
                // $img = Image::make($sPath);
                // $img->resize(450,600);
                // $img->save($dPath);

                // $category->image = $newImageName;
                // $category->save();

            }
            //    $category->save();



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

    public function edit(){

    }

    public function update(){

    }

    public function destroy(){

    }
}
