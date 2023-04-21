<?php

namespace App\Http\Controllers;

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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
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
