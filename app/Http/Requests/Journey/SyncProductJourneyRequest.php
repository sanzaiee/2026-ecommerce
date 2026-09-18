<?php

namespace App\Http\Requests\Journey;

use App\Domain\Journey\DTOs\SyncProductJourneyData;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;

class SyncProductJourneyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    protected function prepareForValidation(): void
    {
        $steps = $this->input('steps', []);

        if (! is_array($steps)) {
            return;
        }

        foreach ($steps as $index => $step) {
            if (! is_array($step)) {
                continue;
            }

            $steps[$index]['title'] = strip_tags((string) ($step['title'] ?? ''));
            $steps[$index]['text'] = strip_tags((string) ($step['text'] ?? ''));
        }

        $this->merge(['steps' => $steps]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'steps' => ['required', 'array', 'size:'.SyncProductJourneyData::STEP_COUNT],
            'steps.*.title' => ['required', 'string', 'max:120'],
            'steps.*.text' => ['required', 'string', 'max:1000'],
            'steps.*.image' => ['nullable', 'image', 'max:5120'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        $attributes = [];

        for ($i = 0; $i < SyncProductJourneyData::STEP_COUNT; $i++) {
            $step = $i + 1;
            $attributes["steps.{$i}.title"] = "step {$step} title";
            $attributes["steps.{$i}.text"] = "step {$step} description";
            $attributes["steps.{$i}.image"] = "step {$step} image";
        }

        return $attributes;
    }

    public function toDto(int $productId): SyncProductJourneyData
    {
        /** @var list<array{title: string, text: string, image?: UploadedFile|null}> $rawSteps */
        $rawSteps = $this->validated('steps');

        $steps = [];

        foreach ($rawSteps as $index => $step) {
            $image = $this->file("steps.{$index}.image");

            $steps[] = [
                'title' => $step['title'],
                'text' => $step['text'],
                'image' => $image instanceof UploadedFile ? $image : null,
            ];
        }

        return new SyncProductJourneyData(
            productId: $productId,
            steps: $steps,
        );
    }
}
