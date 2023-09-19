<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Word extends Model
{
    use HasFactory;

    /**
     * @return Collection articles contains this word
     */
    public function articles()
    {
        return $this->belongsToMany(Article::class)->withPivot('quantity')->orderBy('quantity', 'desc');
    }

    /**
     * @param string $value is Word searched in DB or created
     * @return Word $search is new or searched Word from DB
     */
    public static function findOrCreate($value)
    {
        $search = Word::where('word_atom', $value)->first();

        if ($search == null) {
            return Word::create(['word_atom' => $value]);
        } else {
            return $search;
        }
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'word_atom',
    ];
}
