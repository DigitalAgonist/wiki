<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    /**
     * @return Collection words from this Article
     */
    public function words()
    {
        return $this->belongsToMany(Word::class)->withPivot('quantity');
    }

    /**
     * @param string $article is title of page to Wikipedia
     * @return Article $search is new or searched Article from DB
     */
    public static function insertOrUpdate($article)
    {
        $search = Article::where('title', $article['title'])->first();

        if ($search == null) {
            return Article::create($article);
        } else {
            $search->link = $article['link'];
            $search->size = $article['size'];
            $search->word_quantity = $article['word_quantity'];
            $search->content = $article['content'];
            $search->update();

            return $search;
        }
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'link',
        'size',
        'word_quantity',
        'content',
    ];
}
