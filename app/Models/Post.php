<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    protected $guarded = [];
    public $table = 'posts';
    use softDeletes;
    use HasFactory;

    public function images() {
        return $this->hasMany(Image::class, 'post_id', 'id');
    }
}
