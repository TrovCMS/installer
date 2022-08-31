<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Trov\Concerns\HasPublishedScope;
use Trov\Concerns\Sluggable;

class Runway extends Model
{
    use HasFactory;
    use HasPublishedScope;
    use Sluggable;
    use SoftDeletes;

    protected string $basePath = '/';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'slug',
        'status',
        'content',
        'has_chat',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'has_chat' => 'boolean',
        'content' => 'array',
    ];

    public function getBasePath()
    {
        return $this->basePath;
    }

    public function getPublicUrl()
    {
        return url()->to($this->getBasePath().'/'.$this->slug.'/');
    }
}
