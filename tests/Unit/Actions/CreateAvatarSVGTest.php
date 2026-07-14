<?php

declare(strict_types=1);
use App\Actions\CreateAvatarSVG;

it('creates an svg avatar', function () {
    $svg = new CreateAvatarSVG('JD')->render();

    $this->assertStringContainsString('<svg', $svg);
});
