<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Trov\Concerns\HasFeaturedImage;
use Trov\Concerns\HasMeta;
use Trov\Concerns\HasPublishedScope;
use Trov\Concerns\Sluggable;

class DiscoveryTopic extends Model
{
    use HasPublishedScope;
    use Sluggable;
    use HasFactory;
    use HasMeta;
    use SoftDeletes;
    use HasFeaturedImage;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'slug',
        'status',
        'excerpt',
        'content',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'indexable' => 'boolean',
        'content' => 'array',
    ];

    protected $with = [
        'meta',
    ];

    public function getBasePath()
    {
        return '/discover/topics';
    }

    public function getPublicUrl()
    {
        return url()->to($this->getBasePath().'/'.$this->slug.'/');
    }

    public function articles()
    {
        return $this->hasMany(DiscoveryArticle::class);
    }
}
