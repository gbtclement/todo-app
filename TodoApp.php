<?php

class TodoApp
{
    private array $todos = [];
    private int $nextId = 1;

    public function addTodo(string $title): array
    {
        if (empty(trim($title))) {
            throw new InvalidArgumentException("Le titre ne peut pas être vide");
        }
        

        $todo = [
            'id' => $this->nextId++,
            'title' => trim($title),
            'completed' => false,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $this->todos[] = $todo;
        return $todo;
    }

    public function getTodos(): array
    {
        return $this->todos;
    }

    public function getTodo(int $id): ?array
    {
        foreach ($this->todos as $todo) {
            if ($todo['id'] === $id) {
                return $todo;
            }
        }
        return null;
    }

    public function completeTodo(int $id): bool
    {
        foreach ($this->todos as &$todo) {
            if ($todo['id'] === $id) {
                $todo['completed'] = true;
                return true;
            }
        }
        return false;
    }

    public function deleteTodo(int $id): bool
    {
        foreach ($this->todos as $index => $todo) {
            if ($todo['id'] === $id) {
                unset($this->todos[$index]);
                $this->todos = array_values($this->todos); // Réindexer
                return true;
            }
        }
        return false;
    }

    public function getCompletedTodos(): array
    {
        return array_filter($this->todos, fn($todo) => $todo['completed']);
    }

    public function getPendingTodos(): array
    {
        return array_filter($this->todos, fn($todo) => !$todo['completed']);
    }

    public function getStats(): array
    {
        $total = count($this->todos);
        $completed = count($this->getCompletedTodos());
        $pending = count($this->getPendingTodos());

        return [
            'total' => $total,
            'completed' => $completed,
            'pending' => $pending,
            'completion_rate' => $total > 0 ? round(($completed / $total) * 100, 2) : 0
        ];
    }
}