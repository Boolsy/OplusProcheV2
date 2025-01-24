<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;


    protected $fillable = [
        'text', 'difficulty', 'category_id', 'image_url', 'created_at', 'updated_at',
        'corrected_by', 'deleted',
    ];

    public function correctedBy()
    {
        return $this->belongsTo(User::class, 'corrected_by');
    }

    public function category() {
        return $this->belongsTo(Category::class);
    }

    public function responses() {
        return $this->hasMany(Response::class);
    }

    public function votes() {
        return $this->hasMany(Vote::class);
    }
}
