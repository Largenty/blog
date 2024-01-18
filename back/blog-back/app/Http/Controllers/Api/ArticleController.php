<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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

        Log::info(auth()->user());
        error_log('Données validées');

        $data = array_merge($validatedData, ['user_id' => Auth::id()]);

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

        // $data = array_merge($validatedData, ['user_id' => auth()->id()]);

        if ($article->user_id !== auth()->id()) {
            return response()->json(['error' => 'Action non autorisée'], 403);
        }

        $article = Article::findOrFail($article->id);

        $article->update($validatedData);

        // return response()->json($article);

        return new ArticleResource($article);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Article $article)
    {
        if ($article->user_id !== auth()->id()) {
            return response()->json(['error' => 'Action non autorisée'], 403);
        }

        $article->delete();
        return response(status: 204);
    }
}
