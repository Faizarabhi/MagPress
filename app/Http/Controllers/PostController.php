<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function store(Request $request)
    {
        try{
            $request->validate([
                'title' => 'required|string|max:255',
                'subtitle' => 'nullable|string|max:255',
                'body' => 'required|string',
                'cover_image' => 'nullable|image|max:2048',
                'has_body_images' => 'boolean',
                'body_images_count' => 'integer|min:0',
            ]);

            $post = new Post();
            $post->title = $request->title;
            $post->subtitle = $request->subtitle;
            $post->body = $request->body;
            $post->cover_image = $request->file('cover_image') ? $request->file('cover_image')->store('covers') : null;
            $post->has_body_images = $request->has_body_images ?? false;
            $post->body_images_count = $request->body_images_count ?? 0;
            $post->user_id = auth()->id();
            $post->save();

            return response()->json([
                'status' => 'Post created successfully',
                'post' => $post,
            ])->setStatusCode(201, 'Created');
        }catch(\Throwable $e){
            return response()->json([
                'status' => 'Post creation failed',
                'message' => $e->getMessage(),
            ])->setStatusCode(400, 'Bad Request');
        }
    }
    public function allPosts()
    {
        try {
            $posts = Post::with('user')->where('status', 'accepted')->get();
            return response()->json([
                'status' => 'Posts retrieved successfully',
                'posts' => $posts,
            ])->setStatusCode(200, 'OK');
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'Failed to retrieve posts',
                'message' => $e->getMessage(),
            ])->setStatusCode(500, 'Internal Server Error');
        }
    }
    public function reporterPosts()
    {
        try {
            
            $posts = Post::with('user')->where('user_id', auth()->id())->get();
            return response()->json([
                'status' => 'User posts retrieved successfully',
                'posts' => $posts,
            ])->setStatusCode(200, 'OK');
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'Failed to retrieve user posts',
                'message' => $e->getMessage(),
            ])->setStatusCode(500, 'Internal Server Error');
        }
    }
    public function adminPosts()
    {
        try {
            $posts = Post::with('user')->get();
            return response()->json([
                'status' => 'Posts retrieved successfully',
                'posts' => $posts,
            ])->setStatusCode(200, 'OK');
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'Failed to retrieve posts',
                'message' => $e->getMessage(),
            ])->setStatusCode(500, 'Internal Server Error');
        }
    }

}
