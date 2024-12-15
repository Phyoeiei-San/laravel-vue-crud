<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Post;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::all();
    return response()->json(['posts' => $posts], 200);


    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
    $validatedData = $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'required|string',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    ]);

    if ($request->hasFile('image')) {
        $imagePath = $request->file('image')->store('images', 'public');
        $validatedData['image'] = $imagePath;
    }

    $post = Post::create($validatedData);

    return response()->json(['message' => 'Post created successfully', 'post' => $post], 201);
    }



    /**
     * Display the specified resource.
     */
    // public function show(Post $post)
    // {
    //     // $post = Post::where ('id', $id)->first();
    //     $post->first();
    //     return response()->json([
    //         'post'=> $post
    //     ]);
    // }
   // In your PostController

   // Laravel Controller Method
public function show($id) {
    $post = Post::find($id);

    if (!$post) {
        return response()->json(['error' => 'Post not found'], 404);
    }

    return response()->json(['post' => $post], 200);
}



    /**
     * Update the specified resource in storage.
     */


     public function update(Request $request, Post $post)
     {
         $validatedData = $request->validate([
             'title' => 'required|string|max:255',
             'description' => 'required|string',
             'image' => 'nullable',
         ]);

         $post->title = $validatedData['title'];
         $post->description = $validatedData['description'];

        //  Handle Base64 image
         if ($request->has('image') && $request->image) {
             $imageData = $request->image;

             if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $matches)) {
                 $imageType = $matches[1];
                 $imageData = preg_replace('/^data:image\/\w+;base64,/', '', $imageData);
                 $imageData = base64_decode($imageData);

                 $imageName = 'post_' . time() . '.' . $imageType;

                 Storage::disk('public')->put('images/' . $imageName, $imageData);

                 // Delete old image if it exists
                 if ($post->image) {
                     Storage::disk('public')->delete($post->image);
                 }

                 $post->image = 'images/' . $imageName;
             } else {
                 return response()->json(['error' => 'Invalid image data'], 400);
             }
         }

         // Handle FormData image (optional)
         if ($request->hasFile('image')) {
             $imagePath = $request->file('image')->store('images', 'public');

             // Delete old image if it exists
             if ($post->image) {
                 Storage::disk('public')->delete($post->image);
             }

             $post->image = $imagePath;
         }

         $post->save();

         return response()->json(['message' => 'Post updated successfully', 'post' => $post], 200);
     }
//     public function update(Request $request, Post $post)
// {
//     $validatedData = $request->validate([
//         'title' => 'required|string|max:255',
//         'description' => 'required|string',
//         'image' => 'nullable|image|max:2048',
//     ]);

//     $post->update($validatedData);

//     return response()->json(['message' => 'Post updated successfully', 'post' => $post], 200);
// }





    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
    // $post = Post::findOrFail($post);
    $post->delete();

    return response()->json(['message' => 'Post deleted successfully'], 200);
    }

    public function search(Request $request)
    {
        $searchKey = $request->input('key');  // Get search key
        $posts = Post::where('title', 'like', "%$searchKey%")
                     ->orWhere('description', 'like', "%$searchKey%")
                     ->get();

        return response()->json(['posts' => $posts], 200);
    }
}
