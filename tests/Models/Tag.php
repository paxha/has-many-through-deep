<?php

namespace Tests\Models;

class Tag extends Model
{
    public function comments()
    {
        return $this->hasManyThroughDeep(
            Comment::class,
            ['taggables', Post::class],
            [null, 'id'],
            [null, ['taggable_type', 'taggable_id']]
        );
    }

    public function commentsFromRelations()
    {
        return $this->hasManyThroughDeepFromRelations($this->posts(), (new Post)->comments());
    }

    public function posts()
    {
        return $this->morphedByMany(Post::class, 'taggable');
    }
}
