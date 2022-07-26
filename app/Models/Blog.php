<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Blog extends Model
{
    use HasFactory;

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        // 'user_id' => Auth::id(),
    ];

    protected $fillable = [
        'title',
        'description',
        'is_published',
    ];

    /**
     * Get the user associated with the blog.
     */
    public function user()
    {
        return $this->hasOne(User::class,'id','user_id');
    }

    /**
     * The has Many Relationship
     *
     * @var array
     */
    public function comments()
    {
        return $this->hasMany(Comment::class)->whereNull('parent_id');
    }

    /**
     * The has Many Relationship
     *
     * @var array
     */
    public function totalComments()
    {
        return $this->hasMany(Comment::class);
    }
}
