<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Collection;

class Photo extends Model {
    protected $table = 'photos';
    protected $fillable = ['collection_id', 'file_path'];

    public function collection() {
        return $this->belongsTo(Collection::class, 'collection_id');
    }
}



