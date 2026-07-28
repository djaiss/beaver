<?php

declare(strict_types=1);

use Symfony\Component\Finder\Finder;

/*
 * Everything runs on one of two named queues. "high" is for the handful of things
 * somebody is waiting on: the emails that let them sign in, and the ones that warn
 * them about their own account. "low" is for everything else, above all the audit
 * log, which nobody watches in real time.
 *
 * Nothing runs on a queue called "default". The worker does not listen to one
 * (see docker-compose.yml), so a job that landed there would sit untouched
 * forever, and the failure would be silent.
 */

/**
 * Every dispatch in the source, with the queue it names, if any.
 *
 * @return array<int, array{file: string, line: int, job: string, queue: ?string}>
 */
function dispatchesInSource(): array
{
    $files = Finder::create()->files()->name('*.php')->in([base_path('app'), base_path('routes')]);
    $dispatches = [];

    foreach ($files as $file) {
        $source = $file->getContents();

        preg_match_all('/(\w+)::dispatch(?:AfterResponse)?\s*\(/', $source, $matches, PREG_OFFSET_CAPTURE);

        foreach ($matches[0] as $index => $match) {
            // Walk from the opening bracket to the end of the statement, so a
            // ->onQueue() sitting several lines below is still seen.
            $statement = statementFrom($source, (int) $match[1]);

            preg_match("/onQueue\('([a-z]+)'\)/", $statement, $queue);

            $dispatches[] = [
                'file' => str_replace(base_path().'/', '', $file->getPathname()),
                'line' => substr_count(substr($source, 0, (int) $match[1]), "\n") + 1,
                'job' => $matches[1][$index][0],
                'queue' => $queue[1] ?? null,
            ];
        }
    }

    return $dispatches;
}

/**
 * The source of one statement, from the given offset to its terminating semicolon.
 */
function statementFrom(string $source, int $start): string
{
    $depth = 0;
    $length = strlen($source);

    for ($i = $start; $i < $length; $i++) {
        $character = $source[$i];

        if ($character === '(') {
            $depth++;
        }

        if ($character === ')') {
            $depth--;
        }

        if ($character === ';' && $depth === 0) {
            return substr($source, $start, $i - $start);
        }
    }

    return substr($source, $start);
}

it('dispatches every job onto the high or the low queue, never the default one', function () {
    $offenders = [];

    foreach (dispatchesInSource() as $dispatch) {
        if (in_array($dispatch['queue'], ['high', 'low'], true)) {
            continue;
        }

        $offenders[] = $dispatch['file'].':'.$dispatch['line'].' '.$dispatch['job'].'::dispatch() → '
            .($dispatch['queue'] ?? 'no queue named');
    }

    expect($offenders)->toBe([], 'Every dispatch must say onQueue(\'high\') or onQueue(\'low\'):'.PHP_EOL.implode(PHP_EOL, $offenders));
});

it('keeps high for the emails somebody is actually waiting on', function () {
    $high = array_values(array_filter(
        dispatchesInSource(),
        fn (array $dispatch): bool => $dispatch['queue'] === 'high',
    ));

    // Sign in and security only. Anything else belongs on low: if it grows, the
    // queue stops meaning "urgent" and starts meaning "everything".
    expect(count($high))->toBeLessThanOrEqual(8);

    foreach ($high as $dispatch) {
        expect($dispatch['job'])->toBe('SendEmail', $dispatch['file'].':'.$dispatch['line'].' is on high but is not an email');
    }
});

it('falls back to low rather than default when a queue is somehow not named', function () {
    // A floor under the rule above, so a dispatch that slips through still lands
    // on a queue the worker listens to.
    foreach (['database', 'beanstalkd', 'sqs', 'redis'] as $connection) {
        expect(config('queue.connections.'.$connection.'.queue'))->toBe('low');
    }
});

it('never leaves a mailable to queue itself a second time', function () {
    // Mail::send() with a ShouldQueue mailable queues it rather than sending it,
    // onto a queue of the mailable's choosing. Every mailable here is already
    // inside the SendEmail job or explicitly queued, so that second hop would
    // only lose the Resend id and record the email before it was sent.
    $files = Finder::create()->files()->name('*.php')->in(base_path('app/Mail'));
    $offenders = [];

    foreach ($files as $file) {
        if (str_contains($file->getContents(), 'ShouldQueue')) {
            $offenders[] = $file->getFilename();
        }
    }

    expect($offenders)->toBe([]);
});
