<?php

namespace Database\Seeders;

use App\Models\Quote;
use Illuminate\Database\Seeder;

class QuoteSeeder extends Seeder
{
    public function run(): void
    {
        $quotes = [
            [
                'body' => 'A reader lives a thousand lives before he dies. The man who never reads lives only one.',
                'attribution' => 'George R.R. Martin',
                'source' => 'A Dance with Dragons',
                'sort_order' => 1,
            ],
            [
                'body' => 'So we beat on, boats against the current, borne back ceaselessly into the past.',
                'attribution' => 'F. Scott Fitzgerald',
                'source' => 'The Great Gatsby',
                'sort_order' => 2,
            ],
            [
                'body' => 'It is our choices, Harry, that show what we truly are, far more than our abilities.',
                'attribution' => 'J.K. Rowling',
                'source' => 'Harry Potter and the Chamber of Secrets',
                'sort_order' => 3,
            ],
            [
                'body' => 'We are all in the gutter, but some of us are looking at the stars.',
                'attribution' => 'Oscar Wilde',
                'source' => 'Lady Windermere’s Fan',
                'sort_order' => 4,
            ],
            [
                'body' => 'The only way out of the labyrinth of suffering is to forgive.',
                'attribution' => 'John Green',
                'source' => 'Looking for Alaska',
                'sort_order' => 5,
            ],
        ];

        foreach ($quotes as $quote) {
            Quote::updateOrCreate(
                ['attribution' => $quote['attribution'], 'source' => $quote['source']],
                $quote + ['is_active' => true],
            );
        }
    }
}
