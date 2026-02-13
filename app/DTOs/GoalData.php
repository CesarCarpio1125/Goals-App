<?php

namespace App\DTOs;

class GoalData
{
    public function __construct(
        public readonly string $title,
        public readonly ?string $description,
        public readonly int $targetValue,
        public readonly int $currentValue,
        public readonly ?\DateTime $deadline,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            title: $data['title'],
            description: $data['description'] ?? null,
            targetValue: (int) $data['target_value'],
            currentValue: (int) ($data['current_value'] ?? 0),
            deadline: isset($data['deadline']) ? new \DateTime($data['deadline']) : null,
        );
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'target_value' => $this->targetValue,
            'current_value' => $this->currentValue,
            'deadline' => $this->deadline?->format('Y-m-d'),
        ];
    }
}
