<?php

namespace Paxha\HasManyThroughDeep;

use Illuminate\Database\Query\Grammars\MySqlGrammar;
use RuntimeException;

trait HasEagerLimit
{
    /**
     * Alias to set the "limit" value of the query.
     *
     * @param int $value
     * @return $this
     */
    public function take($value)
    {
        return $this->limit($value);
    }

    /**
     * Set the "limit" value of the query.
     *
     * @param int $value
     * @return $this
     */
    public function limit($value)
    {
        if ($this->farParent->exists) {
            $this->query->limit($value);
        } else {
            if (!class_exists('Paxha\EloquentEagerLimit\Builder')) {
                $message = 'Please install paxha/eloquent-eager-limit to limit eager loading queries.'; // @codeCoverageIgnore

                throw new RuntimeException($message); // @codeCoverageIgnore
            }

            $column = $this->getQualifiedFirstKeyName();

            $grammar = $this->query->getQuery()->getGrammar();

            if ($grammar instanceof MySqlGrammar && $grammar->useLegacyGroupLimit($this->query->getQuery())) {
                $column = 'laravel_through_key';
            }

            $this->query->groupLimit($value, $column);
        }

        return $this;
    }
}
