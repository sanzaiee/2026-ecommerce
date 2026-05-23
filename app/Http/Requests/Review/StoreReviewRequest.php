<?php

namespace App\Http\Requests\Review;

use App\Domain\Review\DTOs\CreateReviewData;
use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => strip_tags((string) $this->input('name')),
            'comment' => $this->input('comment') ? strip_tags((string) $this->input('comment')) : null,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function toDto(int $productId): CreateReviewData
    {
        return new CreateReviewData(
            productId: $productId,
            name: $this->string('name')->toString(),
            rating: (int) $this->input('rating'),
            comment: $this->input('comment'),
            userId: $this->user()?->id,
        );
    }
}
