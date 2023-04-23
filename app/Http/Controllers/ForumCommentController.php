<?php

namespace App\Http\Controllers;

use App\Models\ForumComment;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class ForumCommentController extends Controller
{
    use AuthUserTrait;

    public function __construct()
    {
        $this->middleware('jwt.verify');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request, $forumId): JsonResponse
    {
        $this->validateRequest();
        $user = $this->getAuthUser();

        $user->forumsComments()->create([
            'body' => request('body'),
            'forum_id' => $forumId,
        ]);

        //return response JSON if success posted
        return response()->json([
            'success' => true,
            'message' => 'Comment Successfully Added'
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  int  $id
     * @return JsonResponse
     */
    public function update(Request $request, $forumId, $commentId)
    {
        $this->validateRequest();
        try {
            $forumComment = ForumComment::findOrFail($commentId);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Forum not found'
            ], 404);
        }

        try {
            $this->checkOwnership($forumComment->user_id);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 403);
        }

        $forumComment->update([
            'body' => request('body'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Comment Successfully updated'
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function destroy($forumId, $commentId)
    {
        try {
            $forumComment = ForumComment::findOrFail($commentId);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Post not found'
            ], 404);
        }

        try {
            $this->checkOwnership($forumComment->user_id);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to delete this comment'
            ], 403);
        }
        $forumComment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Successfully deleted'
        ], 201);
    }

    private function validateRequest(): void
    {
        $validator = Validator::make(request()->all(), [
            'body' => 'required|min:10|max:255',
        ]);

        if ($validator->fails()) {
            response()->json($validator->errors(), 422)->send();
            exit;
        }
    }
}
