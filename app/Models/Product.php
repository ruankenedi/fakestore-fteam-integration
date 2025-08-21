<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Product extends Model
{
    use HasFactory;


    protected $fillable = [
        'category_id',
        'external_id',
        'title',
        'description',
        'price',
        'image_url',
        'raw'
    ];


    protected $casts = [
        'price' => 'decimal:2',
        'raw' => 'array',
    ];


    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
