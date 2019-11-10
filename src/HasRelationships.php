<?php

namespace Paxha\HasManyThroughDeep;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Str;

trait HasRelationships
{
    use ConcatenatesRelationships;

    /**
     * Define a has-many-deep relationship.
     *
     * @param string $related
     * @param array $through
     * @param array $foreignKeys
     * @param array $localKeys
     * @return HasManyThroughDeep
     */
    public function hasManyThroughDeep($related, array $through, array $foreignKeys = [], array $localKeys = [])
    {
        return $this->newHasManyThroughDeep(...$this->hasOneOrManyThroughDeep($related, $through, $foreignKeys, $localKeys));
    }

    /**
     * Define a has-many-deep relationship from existing relationships.
     *
     * @param Relation ...$relations
     * @return HasManyThroughDeep
     */
    public function hasManyThroughDeepFromRelations(...$relations)
    {
        return $this->hasManyThroughDeep(...$this->hasOneOrManyThroughDeepFromRelations($relations));
    }

    /**
     * Define a has-one-deep relationship.
     *
     * @param string $related
     * @param array $through
     * @param array $foreignKeys
     * @param array $localKeys
     * @return HasOneThroughDeep
     */
    public function hasOneThroughDeep($related, array $through, array $foreignKeys = [], array $localKeys = [])
    {
        return $this->newHasOneThroughDeep(...$this->hasOneOrManyThroughDeep($related, $through, $foreignKeys, $localKeys));
    }

    /**
     * Define a has-one-deep relationship from existing relationships.
     *
     * @param Relation ...$relations
     * @return HasOneThroughDeep
     */
    public function hasOneThroughDeepFromRelations(...$relations)
    {
        return $this->hasOneThroughDeep(...$this->hasOneOrManyThroughDeepFromRelations($relations));
    }

    /**
     * Prepare a has-one-deep or has-many-deep relationship.
     *
     * @param string $related
     * @param array $through
     * @param array $foreignKeys
     * @param array $localKeys
     * @return array
     */
    protected function hasOneOrManyThroughDeep($related, array $through, array $foreignKeys, array $localKeys)
    {
        /**
         *
         * @var Model $relatedInstance
         */
        $relatedInstance = $this->newRelatedInstance($related);

        $throughParents = $this->hasOneOrManyThroughDeepParents($through);

        $foreignKeys = $this->hasOneOrManyThroughDeepForeignKeys($relatedInstance, $throughParents, $foreignKeys);

        $localKeys = $this->hasOneOrManyThroughDeepLocalKeys($relatedInstance, $throughParents, $localKeys);

        return [$relatedInstance->newQuery(), $this, $throughParents, $foreignKeys, $localKeys];
    }

    /**
     * Prepare the through parents for a has-one-deep or has-many-deep relationship.
     *
     * @param array $through
     * @return array
     */
    protected function hasOneOrManyThroughDeepParents(array $through)
    {
        return array_map(function ($class) {
            $segments = preg_split('/\s+as\s+/i', $class);

            $instance = Str::contains($segments[0], '\\')
                ? new $segments[0]
                : (new Pivot)->setTable($segments[0]);

            if (isset($segments[1])) {
                $instance->setTable($instance->getTable() . ' as ' . $segments[1]);
            }

            return $instance;
        }, $through);
    }

    /**
     * Prepare the foreign keys for a has-one-deep or has-many-deep relationship.
     *
     * @param Model $related
     * @param Model[] $throughParents
     * @param array $foreignKeys
     * @return array
     */
    protected function hasOneOrManyThroughDeepForeignKeys(Model $related, array $throughParents, array $foreignKeys)
    {
        foreach (array_merge([$this], $throughParents) as $i => $instance) {
            /** @var Model $instance */
            if (!isset($foreignKeys[$i])) {
                if ($instance instanceof Pivot) {
                    $foreignKeys[$i] = ($throughParents[$i] ?? $related)->getKeyName();
                } else {
                    $foreignKeys[$i] = $instance->getForeignKey();
                }
            }
        }

        return $foreignKeys;
    }

    /**
     * Prepare the local keys for a has-one-deep or has-many-deep relationship.
     *
     * @param Model $related
     * @param Model[] $throughParents
     * @param array $localKeys
     * @return array
     */
    protected function hasOneOrManyThroughDeepLocalKeys(Model $related, array $throughParents, array $localKeys)
    {
        foreach (array_merge([$this], $throughParents) as $i => $instance) {
            /**
             *
             * @var Model $instance
             */
            if (!isset($localKeys[$i])) {
                if ($instance instanceof Pivot) {
                    $localKeys[$i] = ($throughParents[$i] ?? $related)->getForeignKey();
                } else {
                    $localKeys[$i] = $instance->getKeyName();
                }
            }
        }

        return $localKeys;
    }

    /**
     * Instantiate a new HasManyDeep relationship.
     *
     * @param Builder $query
     * @param Model $farParent
     * @param Model[] $throughParents
     * @param array $foreignKeys
     * @param array $localKeys
     * @return HasManyThroughDeep
     */
    protected function newHasManyThroughDeep(Builder $query, Model $farParent, array $throughParents, array $foreignKeys, array $localKeys)
    {
        return new HasManyThroughDeep($query, $farParent, $throughParents, $foreignKeys, $localKeys);
    }

    /**
     * Instantiate a new HasOneThroughDeep relationship.
     *
     * @param Builder $query
     * @param Model $farParent
     * @param Model[] $throughParents
     * @param array $foreignKeys
     * @param array $localKeys
     * @return HasOneThroughDeep
     */
    protected function newHasOneThroughDeep(Builder $query, Model $farParent, array $throughParents, array $foreignKeys, array $localKeys)
    {
        return new HasOneThroughDeep($query, $farParent, $throughParents, $foreignKeys, $localKeys);
    }
}
