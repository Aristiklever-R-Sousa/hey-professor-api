<?php

namespace App\Http\Requests\Question;

use App\Rules\{OnlyAsDraft, WithQuestionMark};
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

/**
 * @property-read string $question
 */

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /** @var Question $question */
        $question = $this->route()->question; // @phpstan-ignore-line

        return Gate::allows('update', $question);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var \App\Models\Question $question */
        $question = $this->route()->question; // @phpstan-ignore-line

        return [
            'question' => [
                'required',
                new WithQuestionMark(),
                new OnlyAsDraft($question),
                'min:10',
                // 'unique:questions,'.$this->route()->question->id,
                Rule::unique('questions')->ignore($question->id),
            ],
        ];
    }
}
