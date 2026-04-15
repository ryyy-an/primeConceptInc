<?php

/**
 * Renders a standardized grid of admin statistics cards.
 * 
 * @param array $cards Array of card definitions. Each card should have:
 *                     - 'label' (string): Title of the card.
 *                     - 'value' (mixed): Main numeric or string value.
 *                     - 'subtext' (string): Supporting text below value.
 *                     - 'isCritical' (bool): If true, value and subtext will be red.
 *                     - 'animate' (bool): If true, subtext will have animate-pulse.
 *                     - 'indicator' (string): Optional HTML for indicators like status dots.
 * @param int   $cols   Optional. Number of columns in the grid. Default 3.
 * @param int   $width  Optional. Width of each card in px. Default 400.
 */
function render_admin_stats_cards(array $cards, int $cols = 3, int $width = 400) {
    echo '<div class="grid grid-cols-[repeat(' . $cols . ',' . $width . 'px)] justify-center gap-5 w-full">';
    
    foreach ($cards as $card) {
        $label      = $card['label'] ?? 'Unknown';
        $value      = $card['value'] ?? '0';
        $subtext    = $card['subtext'] ?? '';
        $isCritical = $card['isCritical'] ?? false;
        $animate    = $card['animate'] ?? false;
        $indicator  = $card['indicator'] ?? '';

        // Standard logic for values: format if numeric, else use as-is
        $displayValue = is_numeric($value) ? number_format((float)$value) : $value;

        // Styling based on criticality
        $valueClass   = $isCritical ? 'text-red-600' : 'text-gray-800';
        $subtextClass = $isCritical 
            ? 'text-red-600 font-bold uppercase tracking-widest' 
            : 'text-gray-600 font-medium';
        $animateClass = $animate ? 'animate-pulse' : '';

        echo '
        <div class="flex flex-col justify-between bg-white border border-gray-300 rounded-lg shadow-sm h-[180px] p-6 text-left transition-all duration-300 hover:border-red-200 hover:shadow-md group">
            <div class="text-sm uppercase tracking-wide text-gray-500 font-bold group-hover:text-red-500 transition-colors">' . htmlspecialchars((string)$label) . '</div>
            
            <div class="flex items-center gap-3">
                <div class="text-4xl font-bold ' . $valueClass . ' tracking-tighter">' . $displayValue . '</div>
                ' . $indicator . '
            </div>

            <div class="text-xs ' . $subtextClass . ' italic ' . $animateClass . ' leading-relaxed">' . htmlspecialchars((string)$subtext) . '</div>
        </div>';
    }

    echo '</div>';
}
