<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Content extends Model
{
    use HasFactory;

    const STATUS_PENDING = 'pending';

    const STATUS_PROCESSING = 'processing';

    const STATUS_ABANDONED = 'abandoned';

    const STATUS_PROCESSED = 'processed';

    const TYPE_SITE = 'site';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'type',
        'content',
        'parent',
    ];

    /**
     * Relationship with Customer.
     *
     * @since 1.0.0
     *
     * @version 1.0.0
     *
     * @author Richard Muvirimi <richard@tyganeutronics.com>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship with Content.
     *
     * @since 1.0.0
     *
     * @version 1.0.0
     *
     * @author Richard Muvirimi <richard@tyganeutronics.com>
     */
    public function contents(): HasMany
    {
        return $this->hasMany(Content::class, 'parent', 'id');
    }

    /**
     * Relationship with ContentMeta.
     *
     * @since 1.0.0
     *
     * @version 1.0.0
     *
     * @author Richard Muvirimi <richard@tyganeutronics.com>
     */
    public function contentMetas(): HasMany
    {
        return $this->hasMany(ContentMeta::class);
    }
}
