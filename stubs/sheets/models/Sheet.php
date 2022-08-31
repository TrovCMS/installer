<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Trov\Concerns\HasAuthor;
use Trov\Concerns\HasMeta;
use Trov\Concerns\HasPublishedScope;
use Trov\Concerns\Sluggable;

class Sheet extends Model
{
    use HasPublishedScope;
    use Sluggable;
    use HasFactory;
    use HasMeta;
    use HasAuthor;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'slug',
        'status',
        'author_id',
        'type',
        'content',
        'published_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'indexable' => 'boolean',
        'published_at' => 'datetime',
        'content' => 'array',
    ];

    protected $with = [
        'meta',
    ];

    public function getBasePath()
    {
        return '/'.$this->type;
    }

    public function getPublicUrl()
    {
        return url()->to($this->getBasePath().'/'.$this->slug.'/');
    }
}
