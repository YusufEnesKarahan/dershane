<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogComment;
use App\Domain\Blog\Actions\ApproveCommentAction;
use App\Domain\Blog\Actions\RejectCommentAction;
use App\Domain\Blog\Actions\SpamCommentAction;
use App\Core\Repositories\Interfaces\CommentRepositoryInterface;
use Illuminate\Http\Request;

class BlogCommentController extends Controller
{
    public function __construct(protected CommentRepositoryInterface $repository) {}

    public function index()
    {
        $this->authorize('viewAny', BlogComment::class);

        $comments = $this->repository->paginate(15);

        return view('admin.blogs.comments', compact('comments'));
    }

    public function approve(BlogComment $comment, ApproveCommentAction $action)
    {
        $this->authorize('update', BlogComment::class);

        $action->execute($comment);

        return redirect()->back()->with('success', 'Comment approved successfully.');
    }

    public function reject(BlogComment $comment, RejectCommentAction $action)
    {
        $this->authorize('update', BlogComment::class);

        $action->execute($comment);

        return redirect()->back()->with('success', 'Comment rejected successfully.');
    }

    public function spam(BlogComment $comment, SpamCommentAction $action)
    {
        $this->authorize('update', BlogComment::class);

        $action->execute($comment);

        return redirect()->back()->with('success', 'Comment marked as spam.');
    }

    public function destroy(BlogComment $comment)
    {
        $this->authorize('delete', BlogComment::class);

        $comment->delete();

        return redirect()->back()->with('success', 'Comment deleted successfully.');
    }
}
