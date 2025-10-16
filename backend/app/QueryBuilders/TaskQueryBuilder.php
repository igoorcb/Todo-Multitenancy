<?php

namespace App\QueryBuilders;

use Illuminate\Database\Eloquent\Builder;

class TaskQueryBuilder
{
    public function __construct(
        private Builder $query
    ) {}

    public function applyFilters(array $filters): self
    {
        if (isset($filters['status'])) {
            $this->query->where('status', $filters['status']);
        }

        if (isset($filters['priority'])) {
            $this->query->where('priority', $filters['priority']);
        }

        if (isset($filters['user_id'])) {
            $this->query->where('user_id', $filters['user_id']);
        }

        if (isset($filters['search'])) {
            $this->query->where(function ($q) use ($filters) {
                $q->where('title', 'like', "%{$filters['search']}%")
                  ->orWhere('description', 'like', "%{$filters['search']}%");
            });
        }

        if (isset($filters['due_date_from'])) {
            $this->query->where('due_date', '>=', $filters['due_date_from']);
        }

        if (isset($filters['due_date_to'])) {
            $this->query->where('due_date', '<=', $filters['due_date_to']);
        }

        return $this;
    }

    public function get()
    {
        return $this->query;
    }
}
