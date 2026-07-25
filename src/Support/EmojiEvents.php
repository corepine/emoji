<?php

namespace Corepine\Emoji\Support;

use Corepine\Emoji\Emoji;

class EmojiEvents
{
    public function __construct(
        protected Emoji $emoji,
    ) {
    }

    public function selected(): string
    {
        return $this->emoji->dispatchEvent('selected');
    }

    public function reactionSelected(): string
    {
        return $this->emoji->dispatchEvent('reaction_selected');
    }

    /**
     * @return array<string, string>
     */
    public function all(): array
    {
        return [
            'selected' => $this->selected(),
            'reaction_selected' => $this->reactionSelected(),
        ];
    }
}
