<?php declare(strict_types=1);

namespace App\Criticalmass\TextParser\Embedder;

use League\CommonMark\Inline\Element\Link;

interface EmbedderInterface
{
    public function processEmbedsInLink(Link $link): Link;
}