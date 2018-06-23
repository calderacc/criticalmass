<?php

namespace Criticalmass\Bundle\AppBundle\Criticalmass\Markdown;

use cebe\markdown\Parser;

use cebe\markdown\block as block;
use cebe\markdown\inline as inline;

class CriticalMarkdown extends Parser
{
    use block\HtmlTrait {
        parseInlineHtml as private;
    }

    use block\ListTrait {
        identifyUl as protected identifyBUl;
        consumeUl as protected consumeBUl;
    }

    use block\QuoteTrait;

    use block\RuleTrait {
        identifyHr as protected identifyAHr;
        consumeHr as protected consumeAHr;
    }

    use inline\CodeTrait;
    use inline\EmphStrongTrait;
    use inline\LinkTrait;

    use inline\StrikeoutTrait;
    use inline\UrlLinkTrait;

    protected $html5 = true;

    /**
     * @param string $text
     */
    protected function renderText($text): string
    {
        $br = $this->html5 ? "<br>\n" : "<br />\n";

        return strtr($text[1], ["  \n" => $br, "\n" => $br]);
    }
}
