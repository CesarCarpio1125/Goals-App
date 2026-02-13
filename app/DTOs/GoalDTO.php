<?php

namespace App\DTOs;

class GoalDTO
{
    public function __construct(
        public readonly string $title,
        public readonly string $description = '',
        public readonly ?string $deadline = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            title: $data['title'] ?? '',
            description: $data['description'] ?? '',
            deadline: $data['deadline'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'target_value' => 100, // Valor por defecto para compatibilidad con BD
            'current_value' => 0,  // Valor por defecto para compatibilidad con BD
            'deadline' => $this->deadline,
            // No incluir hash - se genera automáticamente en el modelo
        ];
    }
}
