# Sportscore Pro — Clean Architecture

## Visão Geral

O projeto implementa **Clean Architecture** com **Domain-Driven Design (DDD)**, transformando o acesso aos dados esportivos em uma estrutura profissional, escalável e testável.

## Estrutura de Diretórios

```
src/
├── Domain/                          # Lógica de negócio pura
│   ├── Match/
│   │   ├── Match.php               # Entidade com validações
│   │   └── MatchRepository.php      # Interface de acesso a dados
│   ├── Team/
│   │   ├── Team.php                # Entidade de time
│   │   └── TeamRepository.php       # Interface de repositório
│   ├── Event/
│   │   ├── Event.php               # Eventos de partida (gols, cartões)
│   │   └── EventRepository.php      # Interface de repositório
│   └── Statistics/
│       ├── Statistics.php           # Estatísticas de jogo
│       └── StatisticsRepository.php # Interface de repositório
│
├── Application/                     # Casos de uso (Services)
│   ├── MatchService.php            # Serviço de partidas
│   ├── TeamService.php             # Serviço de times
│   └── StandingsService.php        # Serviço de classificações
│
├── Infrastructure/
│   ├── Database/                   # Implementações de Repositórios
│   │   ├── MatchRepositoryMySQL.php
│   │   ├── TeamRepositoryMySQL.php
│   │   ├── EventRepositoryMySQL.php
│   │   └── StatisticsRepositoryMySQL.php
│   └── Http/
│       ├── Controllers/            # API REST
│       │   ├── MatchController.php
│       │   ├── TeamController.php
│       │   └── StandingsController.php
│       └── Routes.php              # Registro de rotas
│
├── Shared/
│   └── Exceptions/
│       └── DomainException.php     # Exceções de domínio
│
├── Autoloader.php                  # PSR-4 Autoloader
└── Bootstrap.php                   # Inicialização
```

## Camadas da Arquitetura

### 1. Domain (Núcleo de Negócio)

**Responsabilidade:** Lógica de negócio pura, independente de framework.

**Exemplos:**

- **Match.php**: Valida status, scores, calcula vencedor
- **Team.php**: Dados imutáveis do time
- **Event.php**: Eventos com ícones e tipos
- **Statistics.php**: Estatísticas da partida

**Características:**
- Sem dependências externas (salvo PHP puro)
- Validações no construtor
- Métodos de domínio (isLive(), isDraw(), getWinner())
- Conversão para Array (toArray())

**Exemplo:**

```php
$match = new Match(
    id: 15,
    homeTeamId: 1,
    awayTeamId: 2,
    date: new DateTime(),
    status: 'live',
    homeScore: 2,
    awayScore: 1,
    competition: 'Brasileirão'
);

if ($match->isLive()) {
    echo $match->homeScore; // 2
}
```

### 2. Application (Casos de Uso)

**Responsabilidade:** Orquestração entre camadas, lógica de negócio aplicada.

**Services:**

#### MatchService
- `getMatchCenter(id)` → Dados completos da partida
- `getLiveMatches()` → Todas as partidas ao vivo
- `getRecentMatches(limit)` → Partidas recentes
- `getNextMatches(limit)` → Próximas partidas
- `getTeamMatches(teamId)` → Histórico do time
- `updateMatchScore(id, homeScore, awayScore, status)` → Atualizar placar
- `getMatchTimeline(matchId)` → Timeline de eventos
- `getMatchScorers(matchId)` → Marcadores

#### TeamService
- `getTeamProfile(id)` → Perfil do time
- `getTeamBySlug(slug)` → Time por slug
- `getAllTeams()` → Todos os times
- `getTeamStatistics(teamId)` → Estatísticas do time

#### StandingsService
- `getCompetitionStandings(competition)` → Classificação por competição
- `getAllCompetitionsStandings()` → Todas as classificações

### 3. Infrastructure (Detalhes Técnicos)

#### Database (Repositórios MySQL)

Implementam interfaces de repositório e se comunicam com `$wpdb`.

**MatchRepositoryMySQL:**
```php
$repo = new MatchRepositoryMySQL();
$match = $repo->findById(15);
$liveMatches = $repo->findLive();
$repo->save($match);
$repo->update($match);
```

**Métodos comuns:**
- `findById(int)` → Retorna entidade ou null
- `findAll()` → Array de entidades
- `save(Entity)` → Insere
- `update(Entity)` → Atualiza
- `delete(int)` → Deleta

#### HTTP (Controllers REST)

Recebem requisições, delegam ao Service, retornam responses.

**MatchController:**

```
GET /wp-json/sportscore/v1/match/15
GET /wp-json/sportscore/v1/matches/live
GET /wp-json/sportscore/v1/matches/recent?limit=10
GET /wp-json/sportscore/v1/match/15/timeline
PUT /wp-json/sportscore/v1/match/15 { homeScore, awayScore, status }
```

**TeamController:**

```
GET /wp-json/sportscore/v1/team/1
GET /wp-json/sportscore/v1/team/slug/cruzeiro
GET /wp-json/sportscore/v1/teams
GET /wp-json/sportscore/v1/team/1/statistics
```

**StandingsController:**

```
GET /wp-json/sportscore/v1/standings/brasileirao
GET /wp-json/sportscore/v1/standings
```

## Fluxo de Dados

### Exemplo: Obter Match Center Completo

```
Browser
    ↓
GET /wp-json/sportscore/v1/match/15
    ↓
MatchController::show()
    ↓
MatchService::getMatchCenter(15)
    ↓
MatchRepositoryMySQL::findById(15) → Match entity
EventRepositoryMySQL::findByMatchId(15) → [Event, Event, ...]
StatisticsRepositoryMySQL::findByMatchId(15) → [Statistics, ...]
    ↓
Service retorna array com:
{
    match: { ... },
    events: [ ... ],
    statistics: [ ... ],
    isLive: true,
    winner: null
}
    ↓
WP_REST_Response (JSON)
    ↓
Browser (React/Frontend)
```

## Princípios SOLID Aplicados

### S — Single Responsibility
- `Match.php` só valida e calcula lógica de match
- `MatchService` só orquestra casos de uso
- `MatchRepositoryMySQL` só persiste

### O — Open/Closed
- Aberto para extensão (novo `StatisticsService`)
- Fechado para modificação (interfaces imutáveis)

### L — Liskov Substitution
- Qualquer `MatchRepository` pode substituir `MatchRepositoryMySQL`

### I — Interface Segregation
- `MatchRepository` apenas define métodos essenciais
- Não força métodos desnecessários

### D — Dependency Inversion
- Services recebem interfaces, não implementações
- Facilita testes com Mocks

## Exemplo de Uso Prático

### 1. Obter dados de uma partida ao vivo

```php
use Sportscore\Application\MatchService;
use Sportscore\Infrastructure\Database\MatchRepositoryMySQL;
use Sportscore\Infrastructure\Database\EventRepositoryMySQL;
use Sportscore\Infrastructure\Database\StatisticsRepositoryMySQL;

$service = new MatchService(
    new MatchRepositoryMySQL(),
    new EventRepositoryMySQL(),
    new StatisticsRepositoryMySQL(),
);

$matchCenter = $service->getMatchCenter(15);

echo $matchCenter['match']['homeScore']; // 2
echo count($matchCenter['events']);       // 45
```

### 2. No JavaScript (Frontend)

```javascript
const response = await fetch('/wp-json/sportscore/v1/match/15');
const data = await response.json();

console.log(data.match); // { id: 15, homeTeamId: 1, ... }
console.log(data.events); // [ { type: 'goal', minute: 12 }, ... ]
```

### 3. Atualizar placar

```php
$service->updateMatchScore(
    id: 15,
    homeScore: 3,
    awayScore: 1,
    status: 'live'
);
```

## Tratamento de Erros

Todas as camadas usam **DomainException**:

```php
throw DomainException::notFound('Match', 15);
throw DomainException::invalidStatus('invalid_status');
throw DomainException::invalidScore(-1);
```

Controllers tratam exceções:

```php
try {
    $data = $service->getMatchCenter($id);
    return new WP_REST_Response($data, 200);
} catch (DomainException $e) {
    return new WP_REST_Response(['error' => $e->getMessage()], 404);
} catch (\Throwable $e) {
    return new WP_REST_Response(['error' => 'Internal server error'], 500);
}
```

## Vantagens dessa Arquitetura

| Aspecto | Benefício |
|---------|-----------|
| **Testabilidade** | Lógica isolada, sem framework, fácil de mockar |
| **Manutenibilidade** | Código organizado, separação de responsabilidades |
| **Escalabilidade** | Adicionar novos serviços sem impactar existentes |
| **Reutilização** | Services podem ser usados em diferentes contextos |
| **Type Safety** | Strong typing em PHP 8+ |
| **DDD** | Ubiquitous language (Match, Team, Event são conceitos claros) |

## Próximos Passos

1. **Testes Automatizados**
   - Unit tests para Entities (validações)
   - Integration tests para Repositories
   - API tests para Controllers

2. **Cache**
   - Cache standings com transients
   - Cache de matches ao vivo

3. **WebSocket/Real-time**
   - Atualização ao vivo de placar com Pusher ou similar

4. **Analytics**
   - Eventos de usuário
   - Métricas de acesso

5. **Admin Dashboard**
   - Gerenciar partidas
   - Gerenciar times
   - Inserir eventos

6. **Player Management**
   - Entidade Player
   - Estatísticas por jogador
   - Histórico de confrontos
