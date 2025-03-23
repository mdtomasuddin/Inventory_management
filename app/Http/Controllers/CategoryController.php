<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function CreateCategory(Request $request)
    {
        $user_id = $request->header('id');

        Category::create([
            "name" => $request->input('name'),
            "user_id" => $user_id
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Category Create Successfully !',
        ]);
    }

    public function CategoryList(Request $request)
    {
        $user_id = $request->header('id');
        $categories = Category::where('user_id', $user_id)->get();
        return $categories;
    }


    public function CategoryById(Request $request)
    {
        $user_id = $request->header('id');
        $categories = Category::where('user_id', $user_id)->where('id', $request->id)->first();
        return $categories;
    }

    public function CategoryUpdate(Request $request)
    {
        $user_id = $request->header('id');
        $id = $request->input('id');
        Category::where('user_id', $user_id)->where('id', $id)->update([
            'name' => $request->input('name'),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Category Create Successfully !',
        ]);
    }

    
    public function CategoryDelete(Request $request, $id)
    {
        $user_id = $request->header('id');
        Category::where('user_id', $user_id)->where('id', $id)->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Category Deleted Successfully !',
        ]);
    }
}
