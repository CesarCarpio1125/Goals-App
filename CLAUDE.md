# Goals Journal App

Stack:
- Laravel 11
- Inertia
- Vue 3 (Composition API)
- Pinia
- Tailwind
- Docker (Sail)

Global Architecture Rules:
- Controllers must remain thin
- Business logic must live in Services
- Validation must use Form Requests
- Follow SOLID principles
- Use Repository pattern if logic grows
- No duplicated logic

Frontend Architecture:
- Atomic Design structure
- Composition API only
- Global state handled with Pinia
- Components must be reusable
- UI must be modern, dynamic and clean

Refactoring Rule:
Always suggest improvements if code can be simplified,
decoupled, optimized or made more maintainable.

Backend Structure:

app/
├── Http/
│    ├── Controllers/
│    ├── Requests/
├── Services/
├── Actions/
├── Repositories/
├── DTOs/

Frontend Structure:

resources/js/
├── components/
│    ├── atoms/
│    ├── molecules/
│    ├── organisms/
│    ├── templates/
├── pages/
├── stores/
├── composables/

Naming Conventions:
- Services must end with "Service"
- Actions must end with "Action"
- Repositories must end with "Repository"
- Vue components use PascalCase
- Composables use "useSomething"
- Pinia stores use "useXStore"

Core Features:
- Users can create goals
- Each goal has:
    - title
    - description
    - target_value
    - current_value
    - deadline
- Goals calculate percentage automatically
- Journal entries belong to goals
- Dashboard shows:
    - Progress %
    - Motivational message
    - Active goals
    - Completed goals

Backend Flow Rules:

- Controller → FormRequest → Service → Repository → Model
- Controllers must never contain business logic
- Services orchestrate domain logic
- Repositories handle database access only
- Models must not contain heavy business logic
- All calculations must be handled inside Services

Domain Logic Rules:

- Goal percentage must be calculated as:
  (current_value / target_value) * 100

- Percentage must never exceed 100%
- If current_value >= target_value:
  status = completed
- If deadline passed and not completed:
  status = overdue
- Otherwise:
  status = active

- Motivational message must depend on percentage:
  0–30% → encourage start
  31–70% → motivate consistency
  71–99% → push to finish
  100% → celebration message

State Management Rules:

- Goal percentage must be reactive
- Dashboard must compute derived state using getters
- Avoid duplicating state between components
- Use composables for reusable business logic
- Pinia stores must be modular

Performance Rules:

- Avoid N+1 queries
- Use eager loading where necessary
- Avoid unnecessary watchers in Vue
- Avoid deeply nested components
- Optimize reactivity

Inertia Rules:

- Controllers must return Inertia responses
- Only required data should be sent to frontend
- Use API Resources for shaping data
- Avoid sending full models
