<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource filtered by category, product, rate, and hidden flag.
     *
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Comment::with(['user:id,name', 'product:id,name']);

        if (!empty($request->category)) {
            $query->whereHas('product', function($q) use ($request){
                $q->where('category_id', $request->category);
            });
        }

        if (!empty($request->product)) {
            $query->where('product_id', $request->product);
        }

        if (!empty($request->rate)) {
            $query->where('rate', $request->rate);
        }

        if ($request->has('hidden') && $request->hidden != null) {
            $query->where('hidden', $request->hidden);
        }

        $comments = $query->whereNull('reply_to_id')->orderBy('id', 'desc')->paginate(20);

        return view('pages.comment.index', compact('comments'));
    }

    /**
     * Remove the specified comment from storage.
     *
     * @param int $id The ID of the comment to be deleted.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id) 
    {
        $comment = Comment::findOrFail($id);

        try {
            $comment->delete();

            return back()->with('success', 'Comment deleted successfully');
        } catch (\Exception $e) {
            \Log::error('Error delete comment: ' . $e->getMessage());
            return back()->with('error', 'Failed to comment!');
        }
    }
}
