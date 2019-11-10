<?php

namespace Tests\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Model
{
    use SoftDeletes;

    public function likes()
    {
        return $this->hasManyThroughDeep(Like::class, [Post::class], [null, ['likeable_type', 'likeable_id']]);
    }

    public function permissions()
    {
        return $this->hasManyThroughDeep(Permission::class, ['role_user', Role::class]);
    }

    public function permissionsFromRelations()
    {
        return $this->hasManyThroughDeepFromRelations($this->roles(), (new Role)->permissions());
    }

    public function players()
    {
        return $this->hasManyThroughDeep(self::class, [Club::class, Team::class]);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function tagsFromRelations()
    {
        return $this->hasManyThroughDeepFromRelations($this->posts(), (new Post)->tags());
    }
}
