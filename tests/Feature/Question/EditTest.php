<?php

use App\Models\{Question, User};
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\{assertDatabaseHas, putJson};

it('should be able to edit a question', function () {
    $user     = User::factory()->create();
    $question = Question::factory()->create([
        'user_id' => $user->id,
    ]);

    Sanctum::actingAs($user);

    putJson(route('questions.update', $question), [
        'question' => 'Updateting question?',
    ])->assertOk();

    assertDatabaseHas('questions', [
        'id'       => $question->id,
        'user_id'  => $user->id,
        'question' => 'Updateting question?',
    ]);
});

describe('validation rules', function () {
    test('question::required', function () {
        $user     = User::factory()->create();
        $question = Question::factory()->create([
            'user_id' => $user->id,
        ]);

        Sanctum::actingAs($user);

        putJson(route('questions.update', $question), [
            'question' => '',
        ])
            ->assertJsonValidationErrors([
                'question' => 'required',
            ]);
    });

    test('question::ending with question mark', function () {
        $user     = User::factory()->create();
        $question = Question::factory()->create([
            'user_id' => $user->id,
        ]);

        Sanctum::actingAs($user);

        putJson(route('questions.update', $question), [
            'question' => 'Question shold have a mark',
        ])
            ->assertJsonValidationErrors([
                'question' => 'The question should end with question mark (?).',
            ]);
    });

    test('question::min characters should be 10', function () {
        $user     = User::factory()->create();
        $question = Question::factory()->create([
            'user_id' => $user->id,
        ]);

        Sanctum::actingAs($user);

        putJson(route('questions.update', $question), [
            'question' => 'Question?',
        ])
            ->assertJsonValidationErrors([
                'question' => 'least 10 characters',
            ]);
    });

    test('question::should be unique', function () {
        $user     = User::factory()->create();
        $question = Question::factory()->create([
            'user_id' => $user->id,
        ]);

        Question::factory()->create([
            'question' => 'Jeremias question?',
            'status'   => 'draft',
            'user_id'  => $user->id,
        ]);

        Sanctum::actingAs($user);

        putJson(route('questions.update', $question), [
            'question' => 'Jeremias question?',
        ])
            ->assertJsonValidationErrors([
                'question' => 'already been taken',
            ]);

    });

    test('question::should be unique only if id is different', function () {
        $user     = User::factory()->create();
        $question = Question::factory()->create([
            'question' => 'Jeremias question?',
            'user_id'  => $user->id,
        ]);

        Sanctum::actingAs($user);

        putJson(route('questions.update', $question), [
            'question' => 'Jeremias question?',
        ])
        ->assertOk();

    });

});

describe('security', function () {
    test('only the person who create the question can update him', function () {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $question = Question::factory()->create([
            'user_id' => $user1->id,
        ]);

        Sanctum::actingAs($user2);

        putJson(route('questions.update', $question), [
            'question' => 'updating the question?',
        ])->assertForbidden();

        assertDatabaseHas('questions', [
            'id'       => $question->id,
            'question' => $question->question,
        ]);
    });
});
