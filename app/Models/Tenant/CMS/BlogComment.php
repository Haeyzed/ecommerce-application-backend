<?php

namespace App\Models\Tenant\CMS;

use App\Models\Tenant\Customer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Class BlogComment
 *
 * Represents a user feedback or comment on a blog post.
 *
 * @property int $id The unique identifier of the comment.
 * @property int $blog_post_id The foreign key for the parent blog post.
 * @property int|null $customer_id The foreign key for the customer, if logged in.
 * @property string|null $author_name Name of the author (for guests).
 * @property string|null $author_email Email of the author (for guests).
 * @property string $body The main text of the comment.
 * @property bool $is_approved Whether the comment has been moderated and approved.
 * @property Carbon|null $created_at Timestamp of when the comment was created.
 * @property Carbon|null $updated_at Timestamp of when the comment was last updated.
 * @property-read BlogPost $post The post this comment belongs to.
 * @property-read Customer|null $customer The customer who made the comment.
 *
 * @method static Builder filter(array $filters)
 */
class BlogComment extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'blog_post_id',
        'customer_id',
        'author_name',
        'author_email',
        'body',
        'is_approved',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_approved' => 'bool',
        ];
    }

    /**
     * Get the post that this comment belongs to.
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(BlogPost::class, 'blog_post_id');
    }

    /**
     * Get the customer that made this comment.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Scope a query to apply dynamic filters.
     */
    public function scopeFilter(Builder $query, array $filters): void
    {
        $query->when($filters['search'] ?? null, function (Builder $query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('body', 'like', "%{$search}%")
                    ->orWhere('author_name', 'like', "%{$search}%");
            });
        })
            ->when(isset($filters['is_approved']), function (Builder $query) use ($filters) {
                $query->where('is_approved', $filters['is_approved']);
            });
    }
}
