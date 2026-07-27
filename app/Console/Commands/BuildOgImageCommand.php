<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Imagick;
use ImagickPixel;

/**
 * Draws the card social networks show when a public page is shared:
 * public/images/og/default.png, 1200x630.
 *
 * The card is the brand mark and the wordmark on the dark background the footer
 * already uses, and it is built from the same SVG paths as the logo components
 * rather than from a separate design file, so it cannot drift from them. No text
 * is drawn, which keeps the command free of any font dependency: the title and
 * the description of the page travel next to the image in the Open Graph tags.
 *
 * The PNG is committed. Run this again after changing the logo.
 */
class BuildOgImageCommand extends Command
{
    protected $signature = 'kollek:build-og-image';

    protected $description = 'Draw the default Open Graph card into public/images/og/default.png';

    private const WIDTH = 1200;

    private const HEIGHT = 630;

    private const BACKGROUND = '#101010';

    public function handle(): int
    {
        if (! extension_loaded('imagick')) {
            $this->error('The imagick extension is required to draw the card.');

            return self::FAILURE;
        }

        $path = public_path('images/og/default.png');

        if (! is_dir(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        $this->line('Drawing the card…');

        $card = new Imagick;
        $card->setBackgroundColor(new ImagickPixel('transparent'));
        $card->readImageBlob($this->svg());
        $card->setImageFormat('png');
        $card->writeImage($path);
        $card->clear();

        $this->info('Wrote '.$path.' at '.self::WIDTH.'x'.self::HEIGHT.'.');

        return self::SUCCESS;
    }

    /**
     * The whole card as one SVG document. The mark keeps its own rounded tile so
     * it reads the same here as it does in the interface; the wordmark is the
     * path from resources/views/components/wordmark.blade.php, drawn in white.
     */
    private function svg(): string
    {
        $markSize = 168;
        $wordmarkHeight = 96;
        $wordmarkWidth = (int) round($wordmarkHeight * 2658 / 712);
        $gap = 44;

        $blockWidth = $markSize + $gap + $wordmarkWidth;
        $left = (self::WIDTH - $blockWidth) / 2;
        $top = (self::HEIGHT - $markSize) / 2;
        $wordmarkTop = (self::HEIGHT - $wordmarkHeight) / 2;

        $markScale = $markSize / 1024;
        $wordmarkScale = $wordmarkHeight / 712;

        return <<<SVG
            <svg xmlns="http://www.w3.org/2000/svg" width="{$this->width()}" height="{$this->height()}" viewBox="0 0 {$this->width()} {$this->height()}">
                <rect width="{$this->width()}" height="{$this->height()}" fill="{$this->background()}"/>
                <g transform="translate({$left} {$top}) scale({$markScale})">
                    <rect x="16" y="16" width="992" height="992" rx="132" fill="#ffffff"/>
                    <g fill="#101010">
                        <path d="M346,294.1 L346,706 L429,706 L429,588.8 L484,527.6 L601.2,706 L699.8,706 L542.1,472.6 L695.7,294.1 L594,294.1 L480.9,426.9 L429,499.5 L429,294.1 Z"/>
                        <path d="M493.3,760 H454.9 A13.5,13.5 0 0 0 441.4,773.5 V816 A13.5,13.5 0 0 0 454.9,829.5 H493.3 Z"/>
                        <path d="M502.7,760 H568 A13.5,13.5 0 0 1 581.5,773.5 V816 A13.5,13.5 0 0 1 568,829.5 H502.7 Z"/>
                    </g>
                </g>
                <g transform="translate({$this->wordmarkLeft($left, $markSize, $gap)} {$wordmarkTop}) scale({$wordmarkScale})" fill="#ffffff">
                    <path d="M0 700V0H135V274.2L388.3 0H554.7L254.5 319.7L562.2 700H395.4L135 381.9V700ZM800.7 712Q728.9 712 671.1 678.8Q613.3 645.7 579.7 587Q546 528.4 546 452Q546 375.3 579.9 316.6Q613.8 257.8 671.7 224.9Q729.6 192 801.4 192Q873.2 192 931 224.9Q988.7 257.8 1022.4 316.6Q1056.1 375.3 1056.1 452Q1056.1 528.7 1022.2 587.2Q988.3 645.7 930.4 678.8Q872.5 712 800.7 712ZM800.4 598.3Q833.1 598.3 859.5 582.1Q886 565.9 902.2 533.3Q918.5 500.8 918.5 452.1Q918.5 403 902.4 370.3Q886.4 337.7 859.9 321.7Q833.4 305.7 801.8 305.7Q770 305.7 743.1 321.7Q716.1 337.7 699.8 370.3Q683.6 403 683.6 452.1Q683.6 500.8 699.8 533.3Q716.1 565.9 742.6 582.1Q769.2 598.3 800.4 598.3ZM1126 700V0H1261V700ZM1356 700V0H1491V700ZM1820.2 712Q1744 712 1685.4 679.8Q1626.9 647.6 1594 590Q1561 532.3 1561 456.7Q1561 378.9 1593.5 319.4Q1625.9 259.9 1684.4 226Q1742.8 192 1820.5 192Q1894.7 192 1951.1 224.2Q2007.4 256.4 2039.3 311.6Q2071.1 366.7 2071.1 436.9Q2071.1 446.9 2070.8 459.3Q2070.5 471.7 2068.9 485.1H1657.4V402.5H1934.1Q1931.1 354.9 1899.5 326.8Q1867.8 298.6 1820.8 298.6Q1786 298.6 1756.9 314.1Q1727.9 329.6 1710.9 361.1Q1694 392.7 1694 441.3V470.5Q1694 510.8 1710 540.4Q1725.9 569.9 1754.6 586Q1783.3 602.1 1819.1 602.1Q1855.8 602.1 1880.9 585.8Q1905.9 569.4 1919 542.6H2056.5Q2042.3 590.4 2009.1 628.7Q1975.9 667.1 1927.8 689.5Q1879.8 712 1820.2 712ZM2658 700H2523V381.9L2262.6 700H2095.8L2403.5 319.7L2103.3 0H2269.7L2523 274.2V0H2658Z"/>
                </g>
            </svg>
            SVG;
    }

    private function wordmarkLeft(float $left, int $markSize, int $gap): float
    {
        return $left + $markSize + $gap;
    }

    private function width(): int
    {
        return self::WIDTH;
    }

    private function height(): int
    {
        return self::HEIGHT;
    }

    private function background(): string
    {
        return self::BACKGROUND;
    }
}
