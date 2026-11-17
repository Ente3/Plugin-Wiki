<?php

namespace Azuriom\Plugin\Wiki\Policies;

use Azuriom\Models\User;
use Azuriom\Plugin\Wiki\Models\Category;
use Illuminate\Auth\Access\HandlesAuthorization;

class CategoryPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the category.
     */
    public function view(?User $user, Category $category): bool
    {
        if ($user?->can('wiki.admin')) {
            return true;
        }

        if (! $category->is_enabled) {
            return false;
        }

        if ($category->parent !== null && ! $this->view($user, $category->parent)) {
            return false;
        }

        if ($category->roles === null) {
            return true;
        }

        return $user !== null && $category->hasRole($user->role);
    }
}
