<?php
/**
 * Helpers do tema — funções reutilizáveis nos templates
 */

/**
 * Formata data de partida para exibição (pt-BR)
 */
function sse_format_match_date( string $dateStr, string $format = 'd/m H:i' ): string {
    try {
        $dt = new \DateTimeImmutable( $dateStr, new \DateTimeZone( 'America/Sao_Paulo' ) );
        return $dt->format( $format );
    } catch ( \Throwable $e ) {
        return $dateStr;
    }
}

/**
 * Retorna badge HTML de status
 */
function sse_status_badge( string $status ): string {
    $labels = [
        'live'      => 'Ao Vivo',
        'finished'  => 'Encerrado',
        'scheduled' => 'Agendado',
        'postponed' => 'Adiado',
    ];
    $label = $labels[ $status ] ?? ucfirst( $status );
    return sprintf(
        '<span class="match-status-badge %s">%s</span>',
        esc_attr( $status ),
        esc_html( $label )
    );
}

/**
 * Ícone de evento da timeline
 */
function sse_event_icon( string $type ): string {
    return match ( strtolower( $type ) ) {
        'goal', 'normal goal'     => '⚽',
        'own goal'                => '🥅',
        'penalty'                 => '🎯',
        'missed penalty'          => '❌',
        'yellow card'             => '🟨',
        'red card'                => '🟥',
        'yellowred card'          => '🟧',
        'substitution'            => '🔄',
        'var'                     => '📺',
        default                   => '•',
    };
}

/**
 * Verifica se o plugin Engine está ativo
 */
function sse_engine_active(): bool {
    return function_exists( 'add_action' )
        && defined( 'SSE_VERSION' );
}

/**
 * Aviso de dependência se engine não estiver ativo
 */
function sse_engine_notice(): void {
    if ( ! sse_engine_active() ) {
        echo '<div class="sse-engine-notice">'
            . '<p>⚠️ Plugin <strong>Sportscore Engine</strong> não está ativo. '
            . 'Ative-o para carregar dados ao vivo.</p>'
            . '</div>';
    }
}
