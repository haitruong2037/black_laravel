<?php

namespace App\Livewire\Comment;

use Auth;
use Livewire\Attributes\Validate; 
use App\Models\Comment;
use Livewire\Component;
use Livewire\Attributes\On;

class CommentDetails extends Component
{
    public $selectedCommentId;

    public $commentDetails;

    #[Validate('boolean')]
    public $hidden;

    #[Validate('max:255')]
    public $replyContent;

    /**
     * Fetches and displays details for a specific comment.
     *
     * @param int $id The ID of the comment to view details.
     *
     * @return void
     */
    #[On('view_comment_details')] 
    public function viewCommentDetails($id)
    {
        $this->commentDetails = null;
        $this->selectedCommentId = $id;
        try {
            $this->commentDetails = Comment::with(['admin:id,name', 'user:id,name', 'product:id,name', 'replies'])->findOrFail($id);
            $this->hidden = $this->commentDetails->hidden;
        } catch (\Exception $e) {
            \Log::error('Error fetch comment details '. $e->getMessage());
        }
    }

    /**
     * Update and hidden for the selected comment.
     *
     * @return void
     */
    public function save()
    {
        $this->validate();

        if (!empty($this->replyContent)) {
            $adminId = Auth::guard('admin')->user()->id;
            $data = [
                'product_id' => $this->commentDetails->product_id,
                'reply_to_id' => $this->commentDetails->id,
                'admin_id' => $adminId,
                'content' => $this->replyContent,
            ];
    
            Comment::create($data);

            $this->replyContent = null;
        }

        if ($this->hidden != $this->commentDetails->hidden) {
            Comment::where('id', $this->selectedCommentId)->update(['hidden' => $this->hidden]);
            $this->dispatch('comment-update-hidden', hidden: $this->hidden, commentId: $this->selectedCommentId);
        }

        $this->viewCommentDetails($this->selectedCommentId);
    }

    /**
     * Handles the deletion of a comment reply.
     *
     * @param int $id The ID of the comment reply to delete.
     *
     * @return void
     */
    #[On('handle_delete_comment_reply')]
    public function handleDeleteCommentReply($id)
    {
        try {
            $commentReply = Comment::findOrFail($id);
            $commentReply->delete();
        } catch (\Exception $e) {
            \Log::error('Error fetch comment details ' . $e->getMessage());
        }

        $this->viewCommentDetails($this->selectedCommentId);
    }

    public function render()
    {
        return view('livewire.comment.comment-details', [
            'commentDetails' => $this->commentDetails
        ]);
    }
}
