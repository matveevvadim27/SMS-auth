<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Article\ArticleCreateRequest;
use App\Http\Requests\Article\ArticleUpdateRequest;
use App\Http\Requests\Article\SortedByRequest;
use App\Http\Resources\Article\ArticleDeletedResource;
use App\Http\Resources\Article\ArticleResource;
use Illuminate\Http\Request;
use App\Models\Article;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ArticleController extends Controller
{

    public function index(SortedByRequest $request)
    {
        $orderParam = $request->input('sort_by', 'name');
        $directionParam = $request->input('sort_direction', 'asc');
        $trashed = $request->input('trashed', 'false');

        $query = Article::query();

        if ($trashed === 'true') {
            $query = $query->onlyTrashed();
            $articles = $query->orderBy($orderParam, $directionParam)->get();
            return ArticleDeletedResource::collection($articles);
        }

        $articles = $query->orderBy($orderParam, $directionParam)->get();

        return ArticleResource::collection($articles);
    }


    public function store(ArticleCreateRequest $request)
    {
        $atricleData = $request->validated();

        return new ArticleResource(Article::create($atricleData));
    }


    public function show(Article $article)
    {
        return response()->json($article);
    }

    public function update(ArticleUpdateRequest $request, Article $article)
    {
        $validated = $request->validate();

        $article->update($validated);

        return new ArticleResource($article);
    }

    public function destroy(int $id): JsonResponse
    {
        try {

            $article = Article::withTrashed()->findOrFail($id);

            if ($article->trashed()) {

                $article->forceDelete();
                $message = 'Статья полностью удалена';
            } else {

                $article->delete();
                $message = 'Статья удалена';
            }
            return response()->json(['message' => $message], 200);
        } catch (ModelNotFoundException $e) {

            return response()->json(['message' => 'Статья с таким ID не найдена'], 404);
        } catch (\Exception $e) {

            return response()->json(['message' => 'Ошибка при удалении статьи'], 500);
        }
    }
    public function search(Request $request)

    {
        $query = $request->input('query');

        $article = Article::where('name', 'ilike', "%{$query}%")
            ->orWhere('phone', 'ilike', "%{$query}%")
            ->get();

        return ArticleResource::collection($article);
    }
    public function searchTrashedArticles(Request $request)

    {
        $query = $request->input('query');

        $article = Article::onlyTrashed()
            ->where(function ($q) use ($query) {
                $q->where('name', 'ilike', "%{$query}%")
                    ->orWhere('phone', 'ilike', "%{$query}%");
            })
            ->get();

        return ArticleDeletedResource::collection($article);
    }
    public function restore(int $id): JsonResponse
    {
        try {

            $article = Article::withTrashed()->findOrFail($id);

            if ($article->trashed()) {
                $article->restore();
                $message = 'Cтатья успешно восстановлена';
                return response()->json(['message' => $message], 200);
            } else {
                return response()->json(['message' => 'Статья не был удалёна'], 400);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Статьяс таким ID не найдена'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Ошибка при восстановлении статьи'], 500);
        }
    }
}
