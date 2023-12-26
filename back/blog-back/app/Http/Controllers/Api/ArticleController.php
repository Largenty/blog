<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //$articles = Article::orderBy('created_at', 'desc')->paginate(10);

        // return response()->json($articles);

        $articles = Article::with('user')->orderBy('created_at', 'desc')->paginate(10);

        return ArticleResource::collection($articles);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            "title" => "required|string|max:255",
            "description" => "required|string"
        ]);

        $data = array_merge($validatedData, ['user_id' => 1]);

        $article = Article::create($data);

        // return response()->json($article);

        return new ArticleResource($article);
    }

    /**
     * Display the specified resource.
     */
    public function show(Article $article)
    {
        // return response()->json($article);

        return new ArticleResource($article);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Article $article)
    {
        $validatedData = $request->validate([
            "title" => "sometimes|string|max:255",
            "description" => "sometimes|string"
        ]);

        $data = array_merge($validatedData, ['user_id' => 1]);

        $article = Article::findOrFail($article->id);

        $article->update($data);

        // return response()->json($article);

        return new ArticleResource($article);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Article $article)
    {
        $article->delete();
        return response(status: 204);
    }
}
