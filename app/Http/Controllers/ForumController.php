<?php

namespace App\Http\Controllers;

use App\Models\Forum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use PHPOpenSourceSaver\JWTAuth\Exceptions\UserNotDefinedException;


class ForumController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        return Forum::with('user:id,username')->get();
    }


    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $this->validateRequest();
        $user = $this->getAuthUser();

        $user->forums()->create([
            'title' => request('title'),
            'body' => request('body'),
            'slug' => \Str::slug(request('title'), '-') . '-' . \Str::random(5),
            'category' => request('category'),
        ]);

        //return response JSON if success posted
        return response()->json([
            'success' => true,
            'message' => 'Successfully posted'
        ], 201);
    }

    public function show($id)
    {
        return Forum::with('user:id,username')->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $this->validateRequest();
        $user = $this->getAuthUser();

        try {
            $forum = Forum::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Post not found'
            ], 404);
        }

        if ($user->id !== $forum->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to update this post'
            ], 403);
        }

        $forum->update([
            'title' => request('title'),
            'body' => request('body'),
            'category' => request('category'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Successfully updated'
        ], 201);

    }

    public function destroy($id)
    {
        $user = $this->getAuthUser();

        try {
            $forum = Forum::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Post not found'
            ], 404);
        }

        if ($user->id !== $forum->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to delete this post'
            ], 403);
        }

        $forum->delete();

        return response()->json([
            'success' => true,
            'message' => 'Successfully deleted'
        ], 201);
    }

    private function getAuthUser(){
        try {
            return auth()->user();
        } catch (UserNotDefinedException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Not authorized, You must login first'
            ], 403);
        }
    }

    private function validateRequest()
    {
        $validator = Validator::make(request()->all(), [
            'title' => 'required|string|min:5|max:255',
            'body' => 'required|min:10|max:255',
            'category' => 'required|sometimes',
            'slug' => 'unique:forums'
        ]);

        if ($validator->fails()) {
            response()->json($validator->errors(), 422)->send();
            exit;
        }
    }
}
