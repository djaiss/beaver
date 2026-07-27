<?php

declare(strict_types=1);

use Symfony\Component\Finder\Finder;

/*
 * A blade echo compiles to a php echo, and php eats the newline that follows a
 * closing tag. In html that is invisible. Inside javascript, an x-data block or a
 * script tag, it glues the next line onto the echo and the whole expression stops
 * parsing, which takes every alpine binding on that component down with it.
 *
 * So an echo that ends a line must be followed by something that continues the
 * expression (a comma, a bracket), never by a fresh statement.
 */
test('a blade echo that ends a javascript line terminates its statement', function () {
    $statementStart = '/^(continue|break|return|const|let|var|if|for|while|throw|delete|this|window|document)\b/';

    $offenders = [];

    foreach (Finder::create()->files()->in(resource_path('views'))->name('*.blade.php') as $file) {
        $lines = explode("\n", (string) file_get_contents($file->getRealPath()));

        foreach ($lines as $number => $line) {
            if (preg_match('/(@js\(.*\)|\{\{.*\}\})\s*$/', $line) !== 1) {
                continue;
            }

            $next = '';
            foreach (array_slice($lines, $number + 1) as $candidate) {
                if (trim($candidate) !== '') {
                    $next = trim($candidate);
                    break;
                }
            }

            if (preg_match($statementStart, $next) !== 1) {
                continue;
            }

            $offenders[] = $file->getRelativePathname().':'.($number + 1).' is followed by "'.$next.'"';
        }
    }

    expect($offenders)->toBe([]);
});
