<?php

namespace Tests\Models;

class Country extends Model
{
    public function comment()
    {
        return $this->hasOneThroughDeep(Comment::class, [User::class, Post::class])->withDefault();
    }

    public function commentFromRelations()
    {
        return $this->hasOneThroughDeepFromRelations($this->posts(), (new Post)->comments());
    }

    public function comments()
    {
        return $this->hasManyThroughDeep(Comment::class, [User::class, Post::class]);
    }

    public function commentsFromRelations()
    {
        return $this->hasManyThroughDeepFromRelations([$this->posts(), (new Post)->comments()]);
    }

    public function permissions()
    {
        return $this->hasManyThroughDeep(Permission::class, [User::class, 'role_user', Role::class]);
    }

    public function permissionsFromRelations()
    {
        return $this->hasManyThroughDeepFromRelations($this->permissions());
    }

    public function permissionsWithPivotAlias()
    {
        return $this->hasManyThroughDeep(Permission::class, [User::class, RoleUser::class.' as alias', Role::class]);
    }

    public function permissionsWithPivotAliasFromRelations()
    {
        return $this->hasManyThroughDeepFromRelations($this->permissionsWithPivotAlias());
    }

    public function posts()
    {
        return $this->hasManyThrough(Post::class, User::class);
    }

    public function roles()
    {
        return $this->hasManyThroughDeep(Role::class, [User::class, 'role_user']);
    }
}
