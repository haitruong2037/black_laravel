<?php

namespace App\Http\Controllers\User;

use App\Exceptions\Api\InternalServerErrorException;
use App\Http\Controllers\Controller;
use App\Http\Requests\CommentRequest;
use App\Models\Comment;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Store a new comment
     * 
     * @param CommentRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws InternalServerErrorException
     */
    public function store(CommentRequest $request, $productId)
    {
        $userID = Auth::id();

        $product = Product::select('id')->where(['id' => $productId])->where('status', 1)->firstOrFail();

        $data = $request->validated();
        $data['product_id'] = $product->id;
        $data['user_id'] = $userID;

        try {
           Comment::create($data);

           return response()->json([
            'message' => 'Comment created successfully',
        ]);
        } catch (\Exception $e) {
            throw new InternalServerErrorException;
        }
    }

    /**
     * Reply a comment
     * 
     * @param CommentRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws InternalServerErrorException
     */
    public function reply(CommentRequest $request, $productId, $commentId)
    {
        $userID = Auth::id();

        $comment = Comment::findOrFail($commentId);

        $data = [
            'product_id' => $comment->product_id,
            'reply_to_id' => $commentId,
            'user_id' => $userID,
            'content' => $request->content,
        ];

        try {
            $comment = Comment::create($data);

            return response()->json([
                'message' => 'Reply to comment successfully',
            ]);
        } catch (\Exception $e) {
            throw new InternalServerErrorException;
        }
    }

    /**
     * Display list of product's comments
     * 
     * @param string $productId
     * @return \Illuminate\Http\JsonResponse
     * @throws InternalServerErrorException
     */
    public function show(Request $request, string $productId)
    {
        try {
            $query = Comment::with(['user:id,name,image', 'replies' => function ($query) {
                            $query->with(['admin:id,name', 'user:id,name,image']);
                        }])
                        ->whereNull('reply_to_id')
                        ->whereHas('product', function ($query) {
                            $query->where('status', 1);})
                        ->where('product_id', $productId)
                        ->where('hidden', false);

            if (!empty($request->rate) && ($request->rate >=1 && $request->rate <= 5)) {
                $query->where('rate', $request->rate);
            }
            
            $comments =$query->latest()->paginate(10);

            $totalComments = Comment::where('product_id', $productId)->whereNull('reply_to_id')->count();
            $averageRate = Comment::where('product_id', $productId)->whereNull('reply_to_id')->avg('rate');
            $rateCounts = [];
            for ($i = 1; $i <= 5; $i++) {
                $rateCount = Comment::where('product_id', $productId)->where('rate', $i)->whereNull('reply_to_id')->count();
                $rateCounts[$i] = $rateCount;
            }

            return response()->json([
                'comments' => $comments,
                'total_comments' => $totalComments,
                'average_rate' => round($averageRate, 1),
                'rate_counts' => $rateCounts
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to fetch comments', ['error' => $e->getMessage()]);
            throw new InternalServerErrorException;
        }
    }
}
