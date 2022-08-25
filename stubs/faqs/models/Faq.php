<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Tags\HasTags;
use Trov\Concerns\HasMeta;
use Trov\Concerns\HasPublishedScope;

class Faq extends Model
{
    use HasFactory;
    use HasTags;
    use HasSlug;
    use HasPublishedScope;
    use HasMeta;
    use SoftDeletes;

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('question')
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate();
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'question',
        'slug',
        'status',
        'answer',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
    ];

    protected $with = [
        'meta',
    ];

    public function getBasePath()
    {
        return '/faqs/';
    }

    public function getPublicUrl()
    {
        return url()->to($this->getBasePath() . $this->slug . '/');
    }
}
