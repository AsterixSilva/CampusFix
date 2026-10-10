<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Issue extends Model
{
    protected $fillable = [
        'title',
        'description',
        'category_id',
        'location_id',
        'reporter_id',
        'status',
        'safety_flag',
        'class_blocked',
    ];

    protected function casts(): array
    {
        return [
            'safety_flag' => 'boolean',
            'class_blocked' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class);
    }
}