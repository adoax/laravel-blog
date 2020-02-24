<?php

namespace App\Policies;

use App\Category;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class CategoryPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any categories.
     *
     * @param \App\User $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the category.
     *
     * @param \App\User $user
     * @param \App\Category $category
     * @return mixed
     */
    public function view(User $user, Category $category)
    {
        //
    }

    /**
     * Determine whether the user can create categories.
     *
     * @param \App\User $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the category.
     *
     * @param \App\User $user
     * @param \App\Category $category
     * @return mixed
     */
    public function update(User $user, Category $category)
    {
        //
    }

    /**
     * Determine whether the user can delete the category.
     *
     * @param \App\User $user
     * @param \App\Category $category
     * @return mixed
     */
    public function delete(User $user, Category $category)
    {
        return count($category->posts) === 0   ? Response::allow() : Response::deny('Vous ne pouvez pas supprimez cette category');
    }

    /**
     * Determine whether the user can restore the category.
     *
     * @param \App\User $user
     * @param \App\Category $category
     * @return mixed
     */
    public function restore(User $user, Category $category)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the category.
     *
     * @param \App\User $user
     * @param \App\Category $category
     * @return mixed
     */
    public function forceDelete(User $user, Category $category)
    {
        //
    }
}
