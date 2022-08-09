<?php

namespace App\Http\Controllers;
use App\Models\Blog;
use Illuminate\Http\Request;
use Response;
class BlogController extends Controller
{
    

    public function importblogs()
    {
        return view('import_blog');
    }

    public function storeblogs(Request $request)
    {
        try {
            if($file = $request->file('file')){
                $name = $file->getClientOriginalName();
                if($file->move('storage/blogs', $name)){
                    $blog = new Blog();
                    $blog->title = $request->title;
                    $blog->author = $request->author;
                    $blog->tags = $request->tags;
                    $blog->file = $name;
                    if($request->image){
                        $blog->image = $request->image;
                    }
                    else{
                        $blog->image = "storage/blog.png";
                    }
                    $blog->description = $request->description;
                    $blog->save();
                    return redirect()->route('import-blogs');

                }
            }
            return redirect()->back();
            
        } catch (\Exception $error) {
            return $error->getMessage();
        }


    }


    public function getBlogList(){

        $allblogs = Blog::all();
        return view('allblogs', compact('allblogs'));

    }

    public function showBlog($id){
        try{
            $blog = Blog::where('id', $id)->first();
            $pathToFile = "storage/blogs/".$blog->file;
            return response()->file($pathToFile);
        }
        catch (\Exception $error) {
            return "Sorry Blog doesnot exists.";
        }


    }


}
