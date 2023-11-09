<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OptionMeta extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'option_id',
        'key',
        'value',
    ];

    /**
     * Relationship with User.
     *
     * @since 1.0.0
     *
     * @version 1.0.0
     *
     * @author Richard Muvirimi <richard@tyganeutronics.com>
     */
    public function option(): BelongsTo
    {
        return $this->belongsTo(Option::class);
    }
}
