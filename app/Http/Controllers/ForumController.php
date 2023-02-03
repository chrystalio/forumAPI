<?php

namespace App\Http\Controllers;

use App\Models\Forum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;


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
        $validator = Validator::make($request->all(), $this->getValidationAttribute());

        //if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //get user
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
        $validator = Validator::make($request->all(), $this->getValidationAttribute());
        //if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = $this->getAuthUser();

        Forum::findOrFail($id)->update([
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
        //
    }

    private function getValidationAttribute(): array
    {
        return [
            'title' => 'required|string|min:5|max:255',
            'body' => 'required|min:10|max:255',
            'category' => 'required|sometimes',
            'slug' => 'unique:forums'
        ];
    }

    private function getAuthUser()
    {
        try {
            return auth()->user();
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Not authorized, You must login first'
            ], 404);
        }
    }
}
