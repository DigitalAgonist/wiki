<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Word;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests;
    use ValidatesRequests;

    /**
     * @return HTML with Data
     */
    public function show()
    {
        $articles = Article::all();

        return view('home', ['articles' => $articles]);
    }

    /**
     * @return Collection contains all Articles from DB
     */
    public function load()
    {
        return Article::all();
    }

    /**
     * @param string $pageName is title of page to Wikipedia
     * @return Article is imported Article from DB
     */
    public function importArticle($pageName)
    {
        $article = $this->getWikitext($pageName);

        $words = explode(' ', $article['content']);
        $article['word_quantity'] = count($words);
        $article['size'] = strlen($article['content']);

        $search = Article::insertOrUpdate($article);

        $words = preg_replace('/[^a-zA-Zа-яА-Я0-9-—\s]/ui', '', $article['content']);     // слово-атом — слово с беспробельным «-» или «—» *
        //$words = preg_replace("/[-—]+/ui", ' ', $words);                               // расскоментировать данную  строку для разбиения на атомы даже с разделителями «-» или «—»
        $words = preg_replace("/[\n\r\s,?.!]+/", ' ', $words);                          // иначе получится некоторые технические названия или сокращения разобьются на два слова *
        $words = preg_replace('/\s+/', ' ', $words);                                   // св-ва, кол-во, в-во, чугун-1, вояджер-1, луна-25 *
        $words = preg_replace('/\s—\s/', ' ', $words);
        $words = mb_strtolower($words);
        $words = explode(' ', $words);

        $ratingWords = array_count_values($words);

        foreach ($ratingWords as $word => $quantity) {
            $wordId = Word::findOrCreate($word)->id;

            try {
                $search->words()->attach($wordId, ['quantity' => $quantity]);
            } catch (\Throwable $th) {
                $search->words()->detach($wordId);
                $search->words()->attach($wordId, ['quantity' => $quantity]);
            }
        }

        return $search;
    }

    /**
     * @param string $word
     * @return Collection Articles contains this Word
     */
    public function searchArticles($word)
    {
        $value = Word::where('word_atom', $word)->first();

        if ($value == null) {
            return [
                'error' => '404 Not Found',
            ];
        } else {
            return $value->articles;
        }
    }

    /**
     * @param integer $id
     * @return Article
     */
    public function getArticle($id)
    {
        return Article::find($id);
    }

    /**
     * @param string $pageName
     * @return array array(
     *  'title' => $title,       // title for Article
     *  'link' => $link,        // link for Article
     *  'content' => $content  //  content (plain text) from Article
     * )
     */
    private function getWikitext($pageName)
    {
        $pageName = str_replace(' ', '_', $pageName);
        $link = 'https://ru.wikipedia.org/wiki/'.$pageName;

        $endPoint = 'https://ru.wikipedia.org/w/api.php';
        $params = [
            'action' => 'query',
            'prop' => 'extracts',
            'titles' => $pageName,
            'exlimit' => '1',
            'explaintext' => '1',
            'exsectionformat' => 'plain',
            'format' => 'json',
            'utf8' => '1',
        ];

        $url = $endPoint.'?'.http_build_query($params);
        $curl = curl_init($url);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);

        $output = curl_exec($curl);

        curl_close($curl);

        $result = json_decode($output, true);
        $page = current($result['query']['pages']);
        $article = [
            'title' => $page['title'],
            'link' => $link,
            'content' => $page['extract'],
        ];

        return $article;
    }
}
