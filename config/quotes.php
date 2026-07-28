<?php

declare(strict_types=1);

/*
 * The quote on the sign in and sign up screens, picked at random.
 *
 * Each one is invented, and each one is about collecting: what somebody owns,
 * what it is worth, what condition it is in, and who borrowed it and never gave
 * it back. Keep it that way. A quote about documenting decisions or tracking
 * relationships belongs to some other product.
 *
 * The `file` names an avatar in public/images/marketing/quotes, so a new
 * character needs both a .webp and a @2x.webp before it can go in here.
 */

return [
    // The Office quotes
    [
        'sentence' => 'I own the largest collection of anything in this office. I have not counted it. That is what makes it art.',
        'character' => 'Michael Scott',
        'file' => 'michael',
        'tv_show' => 'The Office',
        'season_episode' => 'Season 3, Episode 4',
        'description' => 'Michael discovering that a collection nobody has catalogued is technically just a room.',
    ],
    [
        'sentence' => 'Dwight has catalogued all four hundred of his beets. I have catalogued one stapler. Condition: suspended in gelatin.',
        'character' => 'Jim Halpert',
        'file' => 'jim',
        'tv_show' => 'The Office',
        'season_episode' => 'Season 5, Episode 9',
        'description' => 'Jim proving that a collection of one, properly documented, still counts.',
    ],
    [
        'sentence' => 'A collection you have not catalogued is not a collection. It is a pile. My beets are catalogued. Your beets are a pile.',
        'character' => 'Dwight Schrute',
        'file' => 'dwight',
        'tv_show' => 'The Office',
        'season_episode' => 'Season 4, Episode 13',
        'description' => 'Dwight drawing the only distinction he considers important.',
    ],

    // Friends quotes
    [
        'sentence' => 'At last, somewhere to record that it is two hundred million years old, and that my sister still calls it a rock.',
        'character' => 'Ross Geller',
        'file' => 'ross',
        'tv_show' => 'Friends',
        'season_episode' => 'Season 6, Episode 17',
        'description' => 'Ross finally getting the provenance field he has waited his whole life for.',
    ],
    [
        'sentence' => 'So it tells me where all my stuff is? Because I lent Chandler a sandwich in 1998 and I would like it back.',
        'character' => 'Joey Tribbiani',
        'file' => 'joey',
        'tv_show' => 'Friends',
        'season_episode' => 'Season 5, Episode 8',
        'description' => 'Joey discovering loan tracking, and immediately misusing it.',
    ],
    [
        'sentence' => 'Could this BE any more catalogued? Everything I own is searchable now, which really draws attention to how much of it is ducks.',
        'character' => 'Chandler Bing',
        'file' => 'chandler',
        'tv_show' => 'Friends',
        'season_episode' => 'Season 4, Episode 14',
        'description' => 'Chandler learning that visibility and dignity are not the same thing.',
    ],
    [
        'sentence' => 'It turns out forty-two handbags is not a wardrobe. It is a collection, it has an estimated value, and I feel wonderful.',
        'character' => 'Rachel Green',
        'file' => 'rachel',
        'tv_show' => 'Friends',
        'season_episode' => 'Season 7, Episode 6',
        'description' => 'Rachel reframing a spending habit as an asset class.',
    ],

    // How I Met Your Mother quotes
    [
        'sentence' => 'Suits: catalogued. Condition: legendary. Estimated value: more than your car. Location: every wardrobe I own.',
        'character' => 'Barney Stinson',
        'file' => 'barney',
        'tv_show' => 'How I Met Your Mother',
        'season_episode' => 'Season 3, Episode 10',
        'description' => 'Barney using every field on the item screen at once, correctly.',
    ],
];
