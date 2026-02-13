<?php

namespace App\Services;

use App\Models\User;

class MotivationAnalysisService
{
    public function analyzePerformance(array $analytics, array $stats, int $streak): array
    {
        $motivationType = $this->determineMotivationType($analytics, $stats);
        $message = $this->generateMessage($analytics, $stats, $streak, $motivationType);
        $insights = $this->generateInsights($analytics, $stats, $streak);

        return [
            'type' => $motivationType,
            'message' => $message,
            'insights' => $insights,
            'score' => $analytics['productivity_score'],
            'recommendations' => $this->generateRecommendations($analytics, $stats)
        ];
    }

    private function determineMotivationType(array $analytics, array $stats): string
    {
        $productivityScore = $analytics['productivity_score'];
        $overdueGoals = $analytics['overdue_goals'];
        $completionRate = $stats['completion_rate'] ?? 0;
        $avgVelocity = $analytics['avg_completion_velocity'];

        if ($productivityScore >= 80 && $overdueGoals === 0 && $completionRate >= 75) {
            return 'excellent';
        } elseif ($productivityScore >= 60 && $overdueGoals <= 2) {
            return 'good';
        } elseif ($productivityScore >= 40 || ($overdueGoals > 0 && $overdueGoals <= 3)) {
            return 'needs_improvement';
        } elseif ($productivityScore < 30 || $overdueGoals > 3 || $completionRate < 25) {
            return 'critical';
        } else {
            return 'motivate';
        }
    }

    private function generateMessage(array $analytics, array $stats, int $streak, string $type): string
    {
        $messages = [
            'excellent' => [
                "¡Fantástico! Estás en una racha increíble. Tu puntuación de productividad de {$analytics['productivity_score']}/100 demuestra que eres una máquina de lograr metas. ¡Sigue así!",
                "¡Excelente trabajo! Tu velocidad de completitud del {$analytics['avg_completion_velocity']}% diario es impresionante. Estás en el camino correcto para lograr todo lo que te propongas.",
                "¡Bravo! Tu consistencia es admirable. {$streak} días de racha consecutivos muestran tu compromiso inquebrantable. ¡Eres un ejemplo a seguir!",
                "¡Sobresaliente! Has completado el {$stats['completion_rate']}% de tus metas. Tu enfoque y determinación están dando frutos extraordinarios."
            ],
            'good' => [
                "¡Buen trabajo! Vas por buen camino con tu puntuación de {$analytics['productivity_score']}/100. Un poco más de esfuerzo y llegarás a la excelencia.",
                "¡Vas bien! Tu progreso está siendo constante. Concéntrate en completar esos {$analytics['overdue_goals']} objetivos pendientes y estarás volando.",
                "¡Sigue así! Tu velocidad de {$analytics['avg_completion_velocity']}% diario es sólida. Mantén el momentum y verás resultados aún mejores.",
                "¡Avanzando bien! Has completado el {$stats['completion_rate']}% de tus metas. Cada pequeño paso cuenta en el camino hacia el éxito."
            ],
            'needs_improvement' => [
                "Tienes potencial, pero necesitas más enfoque. Tu puntuación de {$analytics['productivity_score']}/100 indica que puedes hacerlo mejor. ¡Es hora de intensificar tus esfuerzos!",
                "Es momento de reagruparte. Tienes {$analytics['overdue_goals']} metas vencidas que necesitan atención inmediata. No dejes que se acumulen más.",
                "Tu progreso actual del {$stats['completion_rate']}% está por debajo de tu potencial. Imagina lo que podrías lograr si te comprometes al 100%.",
                "¡Despierta! Tu velocidad de completitud del {$analytics['avg_completion_velocity']}% diario es demasiado baja. Es hora de acelerar el ritmo."
            ],
            'critical' => [
                "¡ALERTA ROJA! Tu puntuación de productividad de {$analytics['productivity_score']}/100 es inaceptable. ¿Dónde está tu compromiso? ¡Es hora de actuar AHORA!",
                "¡Esto es crítico! Tienes {$analytics['overdue_goals']} metas vencidas y solo has completado el {$stats['completion_rate']}%. ¿Qué estás esperando?",
                "¡DESPIERTA! Tu velocidad de {$analytics['avg_completion_velocity']}% diario es vergonzosa. Si no cambias ahora, tus metas se convertirán en sueños rotos.",
                "¡URGENTE! Tu rendimiento está en niveles peligrosos. {$analytics['high_priority_goals']} metas de alta prioridad necesitan atención inmediata. ¡Deja de procrastinar!"
            ],
            'motivate' => [
                "¡Tú puedes! Recuerda por qué empezaste este camino. Cada gran logro comenzó con un pequeño paso. ¡Da ese paso hoy!",
                "La consistencia es clave. {$streak} días de racha muestran que tienes la capacidad. ¡Convierte esa capacidad en resultados extraordinarios!",
                "¡No te rindas! Los mejores vienen de atrás. Tu próximo gran logro está justo a la vuelta de la esquina. ¡Sigue empujando!",
                "¡Levántate! Los campeones no se rinden ante los desafíos. Tú tienes el poder de cambiar tu trayectoria. ¡Empieza ahora mismo!"
            ]
        ];

        $typeMessages = $messages[$type] ?? $messages['motivate'];
        return $typeMessages[array_rand($typeMessages)];
    }

    private function generateInsights(array $analytics, array $stats, int $streak): array
    {
        $insights = [];

        // Completion rate insight
        if ($stats['completion_rate'] >= 75) {
            $insights[] = "Excelente tasa de completitud del {$stats['completion_rate']}%. ¡Mantén este nivel!";
        } elseif ($stats['completion_rate'] >= 50) {
            $insights[] = "Buen progreso con {$stats['completion_rate']}% completado. Puedes mejorar aún más.";
        } else {
            $insights[] = "Solo {$stats['completion_rate']}% completado. Necesitas acelerar el ritmo urgentemente.";
        }

        // Velocity insight
        if ($analytics['avg_completion_velocity'] >= 2.0) {
            $insights[] = "Velocidad impresionante del {$analytics['avg_completion_velocity']}% diario. ¡Eres una máquina!";
        } elseif ($analytics['avg_completion_velocity'] >= 1.0) {
            $insights[] = "Buena velocidad de {$analytics['avg_completion_velocity']}% diario. Mantén el momentum.";
        } else {
            $insights[] = "Velocidad muy baja del {$analytics['avg_completion_velocity']}% diario. Necesitas trabajar más rápido.";
        }

        // Overdue goals insight
        if ($analytics['overdue_goals'] === 0) {
            $insights[] = "¡Perfecto! No tienes metas vencidas. ¡Sigue así!";
        } elseif ($analytics['overdue_goals'] <= 2) {
            $insights[] = "Tienes {$analytics['overdue_goals']} metas vencidas. Resuélvelas pronto.";
        } else {
            $insights[] = "CRÍTICO: {$analytics['overdue_goals']} metas vencidas. ¡Esto es inaceptable!";
        }

        // Streak insight
        if ($streak >= 7) {
            $insights[] = "¡Increíble! {$streak} días de racha consecutivos. ¡Eres imparable!";
        } elseif ($streak >= 3) {
            $insights[] = "Buena racha de {$streak} días. ¡No la rompas!";
        } elseif ($streak === 0) {
            $insights[] = "Racha rota. ¡Es hora de reiniciarla hoy mismo!";
        }

        // High priority goals insight
        if ($analytics['high_priority_goals'] > 0) {
            $insights[] = "Tienes {$analytics['high_priority_goals']} metas de alta prioridad que necesitan atención inmediata.";
        }

        // Weekly activity insight
        $weeklyCreated = $analytics['goals_created_this_week'];
        $weeklyCompleted = $analytics['goals_completed_this_week'];

        if ($weeklyCompleted > $weeklyCreated) {
            $insights[] = "Semana productiva: Completaste {$weeklyCompleted} metas, más que las {$weeklyCreated} creadas.";
        } elseif ($weeklyCreated > $weeklyCompleted) {
            $insights[] = "Semana con backlog: Creaste {$weeklyCreated} metas pero solo completaste {$weeklyCompleted}.";
        }

        return array_slice($insights, 0, 5); // Limit to 5 insights
    }

    private function generateRecommendations(array $analytics, array $stats): array
    {
        $recommendations = [];

        // Priority-based recommendations
        if ($analytics['high_priority_goals'] > 0) {
            $recommendations[] = "Enfócate PRIMERO en tus {$analytics['high_priority_goals']} metas de alta prioridad.";
        }

        if ($analytics['overdue_goals'] > 0) {
            $recommendations[] = "Resuelve tus {$analytics['overdue_goals']} metas vencidas INMEDIATAMENTE.";
        }

        // Performance-based recommendations
        if ($analytics['avg_completion_velocity'] < 1.0) {
            $recommendations[] = "Divide las metas grandes en tareas más pequeñas y manejables.";
            $recommendations[] = "Establece tiempos específicos para trabajar en tus metas diariamente.";
        }

        if ($stats['completion_rate'] < 50) {
            $recommendations[] = "Identifica y elimina distracciones que te impiden avanzar.";
            $recommendations[] = "Crea una lista diaria de 3 tareas prioritarias y complétalas.";
        }

        if ($analytics['productivity_score'] < 60) {
            $recommendations[] = "Analiza qué te está frenando y busca soluciones específicas.";
            $recommendations[] = "Considera pedir ayuda o responsabilidad a alguien de confianza.";
        }

        // Task completion recommendations
        if ($analytics['task_completion_rate'] < 70) {
            $recommendations[] = "Enfócate en completar tareas pequeñas diariamente para ganar momentum.";
        }

        // Time management recommendations
        if ($analytics['goals_created_this_week'] > $analytics['goals_completed_this_week'] * 2) {
            $recommendations[] = "Estás creando más metas de las que completas. Reduce la carga de trabajo.";
        }

        return array_slice($recommendations, 0, 4); // Limit to 4 recommendations
    }
}
