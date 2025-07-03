<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use App\Http\Requests\User\SortedByRequest;
use App\Http\Requests\User\UserCreateRequest;
use App\Http\Requests\User\UserUpdateRequest;
use App\Http\Resources\User\UserDeletedResource;
use App\Http\Resources\User\UserResource;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserController extends Controller
{
    public function index(SortedByRequest $request)
    {
        $orderParam = $request->input('sort_by', 'name');
        $directionParam = $request->input('sort_direction', 'asc');
        $trashed = $request->input('trashed', 'false');

        $query = User::query();

        if ($trashed === 'true') {
            $query = $query->onlyTrashed();
            $users = $query->orderBy($orderParam, $directionParam)->get();
            return UserDeletedResource::collection($users);
        }

        $users = $query->orderBy($orderParam, $directionParam)->get();

        return UserResource::collection($users);
    }

    public function store(UserCreateRequest $request)
    {
        $userData = $request->validated();

        return new UserResource(User::create($userData));
    }

    public function show(User $user)
    {
        return response()->json($user);
    }

    public function update(UserUpdateRequest $request, User $user)
    {
        $validated = $request->validated();

        $user->update($validated);

        return new UserResource($user);
    }

    public function destroy(int $id): JsonResponse
    {
        try {

            $user = User::withTrashed()->findOrFail($id);

            if ($user->trashed()) {

                $user->forceDelete();
                $message = 'Пользователь полностью удалён';
            } else {

                $user->delete();
                $message = 'Пользователь удалён';
            }

            return response()->json(['message' => $message], 200);
        } catch (ModelNotFoundException $e) {

            return response()->json(['message' => 'Пользователь с таким ID не найден'], 404);
        } catch (\Exception $e) {

            return response()->json(['message' => 'Ошибка при удалении пользователя'], 500);
        }
    }

    public function search(Request $request)

    {
        $query = $request->input('query');

        $users = User::where('name', 'ilike', "%{$query}%")
            ->orWhere('phone', 'ilike', "%{$query}%")
            ->get();

        return UserResource::collection($users);
    }
    public function searchTrashedUsers(Request $request)

    {
        $query = $request->input('query');

        $users = User::onlyTrashed()
            ->where(function ($q) use ($query) {
                $q->where('name', 'ilike', "%{$query}%")
                    ->orWhere('phone', 'ilike', "%{$query}%");
            })
            ->get();

        return UserDeletedResource::collection($users);
    }
    public function restore(int $id): JsonResponse
    {
        try {

            $user = User::withTrashed()->findOrFail($id);

            if ($user->trashed()) {
                $user->restore();
                $message = 'Пользователь успешно восстановлен';
                return response()->json(['message' => $message], 200);
            } else {
                return response()->json(['message' => 'Пользователь не был удалён'], 400);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Пользователь с таким ID не найден'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Ошибка при восстановлении пользователя'], 500);
        }
    }
}
