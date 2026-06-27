<?php

declare(strict_types=1);

namespace App\Markdown\Extension;

use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Event\DocumentPreParsedEvent;
use League\CommonMark\Extension\ExtensionInterface;
use League\CommonMark\Input\MarkdownInput;

class ConfigurationValueExtension implements ExtensionInterface
{
    /**
     * @param  array<string, string>  $values
     */
    public function __construct(private readonly array $values) {}

    public function register(EnvironmentBuilderInterface $environment): void
    {
        $environment->addEventListener(
            DocumentPreParsedEvent::class,
            function (DocumentPreParsedEvent $event): void {
                $markdown = preg_replace_callback(
                    '/\{\{([a-z][a-z0-9_.-]*)\}\}/i',
                    fn (array $matches): string => $this->values[$matches[1]] ?? $matches[0],
                    $event->getMarkdown()->getContent(),
                );

                $event->replaceMarkdown(new MarkdownInput($markdown ?? $event->getMarkdown()->getContent()));
            },
        );
    }
}
