<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendar Export - {{ $calendarData['month'] }} {{ $calendarData['year'] }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
            line-height: 1.6;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .header h1 {
            color: #2563eb;
            margin: 0;
            font-size: 28px;
        }

        .header .subtitle {
            color: #6b7280;
            font-size: 14px;
            margin-top: 5px;
        }

        .stats {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-bottom: 30px;
            background-color: #f8fafc;
            padding: 15px;
            border-radius: 8px;
        }

        .stat {
            text-align: center;
        }

        .stat .number {
            font-size: 24px;
            font-weight: bold;
            color: #2563eb;
        }

        .stat .label {
            font-size: 12px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .section {
            margin-bottom: 30px;
        }

        .section h2 {
            color: #1f2937;
            font-size: 20px;
            margin-bottom: 15px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 5px;
        }

        .date-group {
            margin-bottom: 20px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            overflow: hidden;
        }

        .date-header {
            background-color: #f8fafc;
            padding: 12px 15px;
            font-weight: bold;
            color: #374151;
            border-bottom: 1px solid #e5e7eb;
        }

        .date-content {
            padding: 15px;
        }

        .item {
            margin-bottom: 12px;
            padding: 10px;
            background-color: #f9fafb;
            border-radius: 6px;
            border-left: 4px solid #2563eb;
        }

        .item.completed {
            border-left-color: #10b981;
            background-color: #f0fdf4;
        }

        .item.overdue {
            border-left-color: #ef4444;
            background-color: #fef2f2;
        }

        .item-title {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .item-meta {
            font-size: 12px;
            color: #6b7280;
        }

        .task-goal {
            color: #6b7280;
            font-style: italic;
        }

        .no-items {
            text-align: center;
            color: #9ca3af;
            font-style: italic;
            padding: 20px;
        }

        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 12px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $user->name }}'s Calendar</h1>
        <div class="subtitle">{{ $calendarData['month'] }} {{ $calendarData['year'] }}</div>
    </div>

    <div class="stats">
        <div class="stat">
            <div class="number">{{ $calendarData['totalGoals'] }}</div>
            <div class="label">Total Goals</div>
        </div>
        <div class="stat">
            <div class="number">{{ $calendarData['totalTasks'] }}</div>
            <div class="label">Total Tasks</div>
        </div>
    </div>

    <div class="section">
        <h2>Goals by Deadline</h2>
        @if($calendarData['goalsByDate']->isEmpty())
            <div class="no-items">No goals with deadlines found.</div>
        @else
            @foreach($calendarData['goalsByDate'] as $date => $dateGoals)
                @if($date)
                    <div class="date-group">
                        <div class="date-header">
                            {{ \Carbon\Carbon::parse($date)->format('l, F j, Y') }}
                        </div>
                        <div class="date-content">
                            @foreach($dateGoals as $goal)
                                <div class="item {{ $goal->status === 'completed' ? 'completed' : ($goal->deadline && $goal->deadline->isPast() ? 'overdue' : '') }}">
                                    <div class="item-title">{{ $goal->title }}</div>
                                    <div class="item-meta">
                                        Status: {{ ucfirst($goal->status) }} |
                                        Progress: {{ $goal->progress }}% |
                                        Priority: {{ ucfirst($goal->priority) }}
                                        @if($goal->description)
                                            | {{ Str::limit($goal->description, 100) }}
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        @endif
    </div>

    <div class="section">
        <h2>Tasks by Deadline</h2>
        @if($calendarData['tasksByDate']->isEmpty())
            <div class="no-items">No tasks with deadlines found.</div>
        @else
            @foreach($calendarData['tasksByDate'] as $date => $dateTasks)
                <div class="date-group">
                    <div class="date-header">
                        {{ \Carbon\Carbon::parse($date)->format('l, F j, Y') }}
                    </div>
                    <div class="date-content">
                        @foreach($dateTasks as $task)
                            <div class="item {{ $task->status === 'completed' ? 'completed' : ($task->deadline && $task->deadline->isPast() ? 'overdue' : '') }}">
                                <div class="item-title">{{ $task->title }}</div>
                                <div class="item-meta">
                                    <span class="task-goal">Goal: {{ $task->goal_title }}</span> |
                                    Status: {{ ucfirst($task->status) }} |
                                    Priority: {{ ucfirst($task->priority) }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    <div class="footer">
        Generated on {{ now()->format('F j, Y \a\t g:i A') }} |
        Goals App Calendar Export
    </div>
</body>
</html>
