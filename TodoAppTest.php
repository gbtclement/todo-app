<?php

use PHPUnit\Framework\TestCase;

require_once 'TodoApp.php';

class TodoAppTest extends TestCase
{
    private TodoApp $todoApp;

    protected function setUp(): void
    {
        $this->todoApp = new TodoApp();
    }

    public function testAddTodo()
    {
        $todo = $this->todoApp->addTodo('Test task');
        
        $this->assertEquals(1, $todo['id']);
        $this->assertEquals('Test task', $todo['title']);
        $this->assertFalse($todo['completed']);
        $this->assertNotEmpty($todo['created_at']);
    }

    public function testAddEmptyTodoThrowsException()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Le titre ne peut pas être vide");
        
        $this->todoApp->addTodo('');
    }

    public function testAddWhitespaceTodoThrowsException()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Le titre ne peut pas être vide");
        
        $this->todoApp->addTodo('   ');
    }

    public function testGetTodos()
    {
        $this->assertCount(0, $this->todoApp->getTodos());
        
        $this->todoApp->addTodo('Task 1');
        $this->todoApp->addTodo('Task 2');
        
        $todos = $this->todoApp->getTodos();
        $this->assertCount(2, $todos);
        $this->assertEquals('Task 1', $todos[0]['title']);
        $this->assertEquals('Task 2', $todos[1]['title']);
    }

    public function testGetTodoById()
    {
        $todo = $this->todoApp->addTodo('Test task');
        
        $foundTodo = $this->todoApp->getTodo($todo['id']);
        $this->assertNotNull($foundTodo);
        $this->assertEquals('Test task', $foundTodo['title']);
        
        $notFound = $this->todoApp->getTodo(999);
        $this->assertNull($notFound);
    }

    public function testCompleteTodo()
    {
        $todo = $this->todoApp->addTodo('Task to complete');
        
        $result = $this->todoApp->completeTodo($todo['id']);
        $this->assertTrue($result);
        
        $updatedTodo = $this->todoApp->getTodo($todo['id']);
        $this->assertTrue($updatedTodo['completed']);
        
        // Test completing non-existent todo
        $result = $this->todoApp->completeTodo(999);
        $this->assertFalse($result);
    }

    public function testDeleteTodo()
    {
        $todo = $this->todoApp->addTodo('Task to delete');
        
        $result = $this->todoApp->deleteTodo($todo['id']);
        $this->assertTrue($result);
        
        $deletedTodo = $this->todoApp->getTodo($todo['id']);
        $this->assertNull($deletedTodo);
        
        // Test deleting non-existent todo
        $result = $this->todoApp->deleteTodo(999);
        $this->assertFalse($result);
    }

    public function testGetCompletedTodos()
    {
        $todo1 = $this->todoApp->addTodo('Task 1');
        $todo2 = $this->todoApp->addTodo('Task 2');
        $todo3 = $this->todoApp->addTodo('Task 3');
        
        $this->todoApp->completeTodo($todo1['id']);
        $this->todoApp->completeTodo($todo3['id']);
        
        $completed = $this->todoApp->getCompletedTodos();
        $this->assertCount(2, $completed);
        
        foreach ($completed as $todo) {
            $this->assertTrue($todo['completed']);
        }
    }

    public function testGetPendingTodos()
    {
        $todo1 = $this->todoApp->addTodo('Task 1');
        $todo2 = $this->todoApp->addTodo('Task 2');
        $todo3 = $this->todoApp->addTodo('Task 3');
        
        $this->todoApp->completeTodo($todo2['id']);
        
        $pending = $this->todoApp->getPendingTodos();
        $this->assertCount(2, $pending);
        
        foreach ($pending as $todo) {
            $this->assertFalse($todo['completed']);
        }
    }

    public function testGetStats()
    {
        // Test avec aucune tâche
        $stats = $this->todoApp->getStats();
        $this->assertEquals(0, $stats['total']);
        $this->assertEquals(0, $stats['completed']);
        $this->assertEquals(0, $stats['pending']);
        $this->assertEquals(0, $stats['completion_rate']);
        
        // Test avec quelques tâches
        $todo1 = $this->todoApp->addTodo('Task 1');
        $todo2 = $this->todoApp->addTodo('Task 2');
        $todo3 = $this->todoApp->addTodo('Task 3');
        $todo4 = $this->todoApp->addTodo('Task 4');
        
        $this->todoApp->completeTodo($todo1['id']);
        $this->todoApp->completeTodo($todo3['id']);
        
        $stats = $this->todoApp->getStats();
        $this->assertEquals(4, $stats['total']);
        $this->assertEquals(2, $stats['completed']);
        $this->assertEquals(2, $stats['pending']);
        $this->assertEquals(50.0, $stats['completion_rate']);
    }

    public function testMultipleOperations()
    {
        // Test d'un scénario complet
        $todo1 = $this->todoApp->addTodo('Buy groceries');
        $todo2 = $this->todoApp->addTodo('Walk the dog');
        $todo3 = $this->todoApp->addTodo('Do homework');
        
        $this->assertCount(3, $this->todoApp->getTodos());
        
        $this->todoApp->completeTodo($todo1['id']);
        $this->assertCount(1, $this->todoApp->getCompletedTodos());
        $this->assertCount(2, $this->todoApp->getPendingTodos());
        
        $this->todoApp->deleteTodo($todo2['id']);
        $this->assertCount(2, $this->todoApp->getTodos());
        $this->assertNull($this->todoApp->getTodo($todo2['id']));
        
        $stats = $this->todoApp->getStats();
        $this->assertEquals(2, $stats['total']);
        $this->assertEquals(1, $stats['completed']);
        $this->assertEquals(1, $stats['pending']);
        $this->assertEquals(50.0, $stats['completion_rate']);
    }
}