<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Report - {{ $user->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #1a202c;
            background-color: #ffffff;
        }

        .report-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Header */
        .header {
            background: linear-gradient(135deg, #2d3748 0%, #1a202c 100%);
            color: white;
            padding: 25px;
            text-align: center;
            margin-bottom: 25px;
            border-radius: 8px;
        }

        .header h1 {
            font-size: 24px;
            font-weight: 300;
            margin-bottom: 8px;
        }

        .header .subtitle {
            font-size: 14px;
            opacity: 0.9;
        }

        .header .timestamp {
            font-size: 10px;
            opacity: 0.7;
            margin-top: 10px;
        }

        /* Stats Grid */
        .stats-grid {
            display: table;
            width: 100%;
            margin-bottom: 25px;
            border-collapse: separate;
            border-spacing: 15px 0;
        }

        .stat-card {
            display: table-cell;
            background: #f7fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 15px;
            text-align: center;
            width: 16.66%;
        }

        .stat-card.highlight {
            background: #ebf8ff;
            border-color: #90cdf4;
        }

        .stat-number {
            font-size: 20px;
            font-weight: bold;
            color: #2d3748;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 10px;
            color: #718096;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }

        /* Section Headers */
        .section {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }

        .section-header {
            font-size: 16px;
            font-weight: bold;
            color: #2d3748;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 2px solid #e2e8f0;
        }

        /* Analytics Cards */
        .analytics-grid {
            display: table;
            width: 100%;
            border-collapse: separate;
            border-spacing: 10px 0;
        }

        .analytics-card {
            display: table-cell;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 12px;
            width: 24%;
            vertical-align: top;
        }

        .analytics-card h4 {
            font-size: 11px;
            font-weight: bold;
            color: #4a5568;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .metric-value {
            font-size: 16px;
            font-weight: bold;
            color: #2d3748;
        }

        .metric-label {
            font-size: 9px;
            color: #718096;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Progress Visualization */
        .progress-section {
            background: #f7fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 12px;
            margin-bottom: 15px;
        }

        .progress-title {
            font-size: 12px;
            font-weight: bold;
            color: #4a5568;
            margin-bottom: 8px;
        }

        .progress-bar {
            width: 100%;
            height: 8px;
            background: #edf2f7;
            border-radius: 4px;
            margin-bottom: 4px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            border-radius: 4px;
        }

        .progress-fill.excellent { background: #48bb78; }
        .progress-fill.good { background: #4299e1; }
        .progress-fill.needs-improvement { background: #ed8936; }
        .progress-fill.critical { background: #f56565; }

        .progress-label {
            font-size: 9px;
            color: #718096;
        }

        /* Goals Table */
        .goals-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .goals-table th,
        .goals-table td {
            padding: 8px 6px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
            font-size: 10px;
        }

        .goals-table th {
            background: #f7fafc;
            font-weight: bold;
            color: #4a5568;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 9px;
        }

        .goal-name {
            font-weight: 600;
            color: #2d3748;
        }

        .goal-priority {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 10px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .priority-low { background: #c6f6d5; color: #22543d; }
        .priority-medium { background: #fef5e7; color: #744210; }
        .priority-high { background: #fed7d7; color: #742a2a; }

        .status-completed { color: #38a169; font-weight: 600; }
        .status-in_progress { color: #3182ce; font-weight: 600; }
        .status-pending { color: #718096; }

        /* Motivation Section */
        .motivation-section {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 15px;
            page-break-inside: avoid;
        }

        .motivation-section h3 {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .motivation-message {
            font-size: 11px;
            line-height: 1.5;
            margin-bottom: 10px;
        }

        .productivity-score {
            display: inline-block;
            background: rgba(255,255,255,0.2);
            padding: 8px 12px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }

        /* Insights Section */
        .insights-section {
            background: #f7fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 12px;
            margin-bottom: 15px;
        }

        .insights-section h3 {
            font-size: 12px;
            font-weight: bold;
            color: #4a5568;
            margin-bottom: 10px;
        }

        .insight-item {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 8px;
            margin-bottom: 6px;
            font-size: 10px;
            line-height: 1.4;
        }

        .recommendations-section {
            background: linear-gradient(135deg, #fef5e7 0%, #fed7aa 100%);
            border: 1px solid #f6ad55;
            border-radius: 6px;
            padding: 12px;
            margin-bottom: 15px;
        }

        .recommendations-section h3 {
            font-size: 12px;
            font-weight: bold;
            color: #9c4221;
            margin-bottom: 10px;
        }

        .recommendation-item {
            background: white;
            border-left: 3px solid #f6ad55;
            padding: 8px;
            margin-bottom: 6px;
            font-size: 10px;
            font-weight: 600;
            color: #9c4221;
            line-height: 1.4;
        }

        /* Footer */
        .footer {
            text-align: center;
            padding: 15px;
            border-top: 1px solid #e2e8f0;
            font-size: 10px;
            color: #718096;
        }

        /* Page breaks */
        .page-break {
            page-break-before: always;
        }

        /* Responsive adjustments for PDF */
        @page {
            margin: 0.5in;
            size: A4;
        }
    </style>
</head>
<body>
    <div class="report-container">
        <!-- Header -->
        <div class="header">
            <h1>{{ $user->name }}'s Performance Report</h1>
            <div class="subtitle">Comprehensive Goals & Productivity Analysis</div>
            <div class="timestamp">Generated: {{ $dashboardData['generated_at'] }}</div>
        </div>

        <!-- Key Metrics Overview -->
        <div class="section">
            <div class="section-header">Key Performance Metrics</div>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number">{{ $dashboardData['analytics']['total_goals'] }}</div>
                    <div class="stat-label">Total Goals</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">{{ $dashboardData['analytics']['completed_goals'] }}</div>
                    <div class="stat-label">Completed</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">{{ $dashboardData['analytics']['in_progress_goals'] }}</div>
                    <div class="stat-label">In Progress</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">{{ $dashboardData['analytics']['pending_goals'] }}</div>
                    <div class="stat-label">Pending</div>
                </div>
                <div class="stat-card highlight">
                    <div class="stat-number">{{ number_format($dashboardData['analytics']['avg_completion_velocity'], 1) }}%</div>
                    <div class="stat-label">Daily Velocity</div>
                </div>
                <div class="stat-card highlight">
                    <div class="stat-number">{{ $dashboardData['completion_rate'] }}%</div>
                    <div class="stat-label">Completion Rate</div>
                </div>
            </div>
        </div>

        <!-- Productivity Overview -->
        <div class="section">
            <div class="section-header">Productivity Overview</div>
            <div class="progress-section">
                <div class="progress-title">Overall Productivity Score</div>
                <div class="progress-bar">
                    <div class="progress-fill
                        @if($dashboardData['analytics']['productivity_score'] >= 80) excellent
                        @elseif($dashboardData['analytics']['productivity_score'] >= 60) good
                        @elseif($dashboardData['analytics']['productivity_score'] >= 40) needs-improvement
                        @else critical @endif"
                        style="width: {{ $dashboardData['analytics']['productivity_score'] }}%"></div>
                </div>
                <div class="progress-label">{{ $dashboardData['analytics']['productivity_score'] }}/100 Points</div>
            </div>

            <div class="analytics-grid">
                <div class="analytics-card">
                    <h4>High Priority</h4>
                    <div class="metric-value">{{ $dashboardData['analytics']['high_priority_goals'] }}</div>
                    <div class="metric-label">Goals</div>
                </div>
                <div class="analytics-card">
                    <h4>Overdue</h4>
                    <div class="metric-value">{{ $dashboardData['analytics']['overdue_goals'] }}</div>
                    <div class="metric-label">Goals</div>
                </div>
                <div class="analytics-card">
                    <h4>This Week</h4>
                    <div class="metric-value">{{ $dashboardData['analytics']['goals_created_this_week'] }}</div>
                    <div class="metric-label">Created</div>
                </div>
                <div class="analytics-card">
                    <h4>This Week</h4>
                    <div class="metric-value">{{ $dashboardData['analytics']['goals_completed_this_week'] }}</div>
                    <div class="metric-label">Completed</div>
                </div>
            </div>
        </div>

        <!-- Goals Progress Table -->
        <div class="section">
            <div class="section-header">Goals Progress Details</div>
            <table class="goals-table">
                <thead>
                    <tr>
                        <th style="width: 25%;">Goal</th>
                        <th style="width: 10%;">Priority</th>
                        <th style="width: 10%;">Status</th>
                        <th style="width: 15%;">Progress</th>
                        <th style="width: 10%;">Tasks</th>
                        <th style="width: 15%;">Velocity</th>
                        <th style="width: 15%;">Est. Days</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($dashboardData['goals_with_progress'] as $goal)
                        <tr>
                            <td>
                                <div class="goal-name">{{ Str::limit($goal['title'], 30) }}</div>
                            </td>
                            <td>
                                <span class="goal-priority priority-{{ $goal['priority'] }}">{{ $goal['priority'] }}</span>
                            </td>
                            <td>
                                <span class="status-{{ $goal['status'] }}">
                                    {{ str_replace('_', ' ', $goal['status']) }}
                                </span>
                            </td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 5px;">
                                    <div style="flex: 1; height: 4px; background: #edf2f7; border-radius: 2px;">
                                        <div style="width: {{ $goal['progress'] }}%; height: 100%; background:
                                            @if($goal['progress'] >= 75) #48bb78
                                            @elseif($goal['progress'] >= 25) #4299e1
                                            @else #ed8936 @endif; border-radius: 2px;"></div>
                                    </div>
                                    <span style="font-size: 9px; font-weight: bold;">{{ $goal['progress'] }}%</span>
                                </div>
                            </td>
                            <td>{{ $goal['completed_tasks'] }}/{{ $goal['total_tasks'] }}</td>
                            <td>{{ number_format($goal['completion_velocity'], 1) }}%/day</td>
                            <td>
                                @if($goal['estimated_completion_days'])
                                    {{ $goal['estimated_completion_days'] }}d
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Personalized Motivation -->
        @if(isset($dashboardData['motivation_analysis']))
        <div class="section">
            <div class="section-header">Personalized Performance Analysis</div>

            <div class="motivation-section">
                <h3>
                    @if($dashboardData['motivation_analysis']['type'] === 'excellent')
                        ¡Excelente Rendimiento!
                    @elseif($dashboardData['motivation_analysis']['type'] === 'good')
                        Buen Trabajo Continúa
                    @elseif($dashboardData['motivation_analysis']['type'] === 'needs_improvement')
                        Necesitas Mejorar
                    @elseif($dashboardData['motivation_analysis']['type'] === 'critical')
                        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
                    @else
                        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
                    @endif
                    color: white;">
                    <h4 style="color: white; margin-bottom: 15px;">
                        @if($dashboardData['motivation_analysis']['type'] === 'excellent')
                            ¡EXCELENTE DESEMPEÑO!
                        @elseif($dashboardData['motivation_analysis']['type'] === 'good')
                            BUEN TRABAJO
                        @elseif($dashboardData['motivation_analysis']['type'] === 'needs_improvement')
                            NECESITAS MEJORAR
                        @elseif($dashboardData['motivation_analysis']['type'] === 'critical')
                            ¡ALERTA CRÍTICA!
                        @else
                            ¡SIGUE ADELANTE!
                        @endif
                    </h4>
                    <div class="insight-text" style="color: rgba(255,255,255,0.9); font-size: 14px; font-weight: 600; margin-bottom: 15px;">
                        {{ $dashboardData['motivation_analysis']['message'] }}
                    </div>
                    <div style="background: rgba(255,255,255,0.2); padding: 10px; border-radius: 8px; margin-bottom: 15px;">
                        <div style="font-size: 12px; color: rgba(255,255,255,0.8); margin-bottom: 5px;">Puntuación de Productividad:</div>
                        <div style="font-size: 18px; font-weight: bold; color: white;">{{ $dashboardData['motivation_analysis']['score'] }}/100</div>
                    </div>
                </div>

                <!-- Key Insights -->
                @if(!empty($dashboardData['motivation_analysis']['insights']))
                    <div class="insight-card" style="margin-top: 15px; background: #f8fafc; border: 2px solid #e5e7eb;">
                        <h4 style="color: #374151; margin-bottom: 10px;">Insights Clave de tu Rendimiento</h4>
                        <div style="space-y: 8px;">
                            @foreach($dashboardData['motivation_analysis']['insights'] as $insight)
                                <div style="background: white; padding: 8px 12px; border-radius: 6px; border-left: 3px solid #667eea; font-size: 12px; color: #374151;">
                                    {{ $insight }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Action Recommendations -->
                @if(!empty($dashboardData['motivation_analysis']['recommendations']))
                    <div class="insight-card" style="margin-top: 15px; background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border: 2px solid #f59e0b;">
                        <h4 style="color: #92400e; margin-bottom: 10px;">Recomendaciones de Acción</h4>
                        <div style="space-y: 8px;">
                            @foreach($dashboardData['motivation_analysis']['recommendations'] as $recommendation)
                                <div style="background: white; padding: 8px 12px; border-radius: 6px; border-left: 3px solid #f59e0b; font-size: 12px; color: #92400e; font-weight: 600;">
                                    {{ $recommendation }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endif
        </div>

        <!-- Footer -->
        <div class="footer">
            Advanced Dashboard Report | Goals App | Generated on {{ $dashboardData['generated_at'] }}
        </div>
    </div>
</body>
</html>
