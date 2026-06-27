<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\CreateAvatarSVG;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CreateAvatarSVGTest extends TestCase
{
    #[Test]
    public function it_creates_an_svg_avatar(): void
    {
        $svg = new CreateAvatarSVG('JD')->render();

        $this->assertStringContainsString('<svg', $svg);
    }
}
