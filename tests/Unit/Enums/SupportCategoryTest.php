<?php

declare(strict_types=1);

use App\Enums\SupportCategory;

it('carries the copy the screen needs for every category', function () {
    foreach (SupportCategory::cases() as $category) {
        expect($category->label())->not->toBeEmpty()
            ->and($category->prompt())->not->toBeEmpty()
            ->and($category->icon())->not->toBeEmpty()
            ->and($category->heading())->not->toBeEmpty()
            ->and($category->paragraphs())->not->toBeEmpty();
    }
});

/*
 * There is no subscription and nothing renews: self hosting is free and the managed
 * hosting is a single payment. The billing intro used to ask about a subscription and
 * a renewal, which invented a billing model the product does not have.
 */
it('does not imply a recurring charge in the billing intro', function () {
    $copy = implode(' ', SupportCategory::Billing->paragraphs());

    expect($copy)->toContain('no subscription')
        ->and($copy)->toContain('one-time payment')
        ->and($copy)->not->toContain('your subscription')
        ->and($copy)->not->toContain('a renewal');
});
