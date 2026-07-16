<?php
namespace App\Domain\Blog\Services;

use App\DTOs\Blog\CreateBlogDTO;
use App\DTOs\Blog\UpdateBlogDTO;
use App\Core\Repositories\Interfaces\BlogRepositoryInterface;
use App\Models\Blog;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class BlogService
{
    public function __construct(
        protected BlogRepositoryInterface $repository,
        protected ReadingTimeService $readingTimeService,
        protected BlogRevisionService $revisionService
    ) {}

    public function create(CreateBlogDTO $dto): Blog
    {
        $slug = Str::slug($dto->title);
        $count = Blog::where('slug', 'like', $slug . '%')->count();
        if ($count > 0) {
            $slug .= '-' . ($count + 1);
        }

        $readingTime = $this->readingTimeService->calculate($dto->content);

        return $this->repository->create([
            'title' => $dto->title,
            'slug' => $slug,
            'content' => $dto->content,
            'excerpt' => $dto->excerpt,
            'category_id' => $dto->category_id,
            'author_id' => Auth::id(),
            'status' => $dto->status,
            'featured_image' => $dto->featured_image,
            'published_at' => $dto->published_at,
            'reading_time' => $readingTime,
        ]);
    }

    public function update(Blog $blog, UpdateBlogDTO $dto): Blog
    {
        // Automatically save a snapshot revision log history before saving updates!
        $this->revisionService->createRevision($blog);

        $readingTime = $this->readingTimeService->calculate($dto->content);

        $updated = $this->repository->update($blog, [
            'title' => $dto->title,
            'content' => $dto->content,
            'excerpt' => $dto->excerpt,
            'category_id' => $dto->category_id,
            'status' => $dto->status,
            'featured_image' => $dto->featured_image,
            'published_at' => $dto->published_at,
            'reading_time' => $readingTime,
        ]);

        if (!empty($dto->tags)) {
            $updated->tags()->sync($dto->tags);
        }

        if (!empty($dto->related_posts)) {
            $updated->relatedPosts()->sync($dto->related_posts);
        }

        return $updated;
    }
}
