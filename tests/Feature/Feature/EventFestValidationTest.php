<?php

use App\Models\Event;
use App\Models\User;
use App\Services\EventService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

it('validates end date must be after start date when provided', function () {
    Storage::fake('public');

    $user = User::factory()->create([
        'username' => 'fest-organizer',
    ]);

    $startDate = now()->addDays(10)->toDateString();
    $endDate = now()->addDays(8)->toDateString();

    $response = $this
        ->actingAs($user)
        ->from('/profile/events/create')
        ->post('/profile/events', [
            'title' => 'Summer Open Air',
            'content' => 'A two day festival for Armenian rock and metal fans.',
            'country' => 'am',
            'genre' => 'rock',
            'type' => 'event',
            'location' => 'Yerevan, Armenia',
            'cordinates' => [
                'latitude' => 40.1792,
                'longitude' => 44.4991,
            ],
            'start_date' => $startDate,
            'end_date' => $endDate,
            'start_time' => '20:00',
            'poster_file' => UploadedFile::fake()->image('poster.jpg'),
        ]);

    $response
        ->assertRedirect('/profile/events/create')
        ->assertSessionHasErrors(['end_date']);

    expect(Event::query()->count())->toBe(0);
});

it('rejects an event when end date is before start date', function () {
    $startDate = now()->addDays(10)->toDateString();
    $endDate = now()->addDays(8)->toDateString();

    $validator = Validator::make(
        [
            'start_date' => $startDate,
            'end_date' => $endDate,
        ],
        [
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]
    );

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('end_date'))->toBeTrue();
});

it('stores an event with an end date as a festival', function () {
    Storage::fake('public');

    $user = User::factory()->create([
        'username' => 'fest-organizer-2',
    ]);
    $startDate = now()->addDays(10)->toDateString();
    $endDate = now()->addDays(12)->toDateString();

    $this->actingAs($user);

    Event::withoutEvents(function () use ($endDate, $startDate) {
        app(EventService::class)->store([
            'title' => 'Summer Open Air',
            'content' => 'A two day festival for Armenian rock and metal fans.',
            'country' => 'am',
            'genre' => 'rock',
            'type' => 'event',
            'location' => 'Yerevan, Armenia',
            'cordinates' => [
                'latitude' => 40.1792,
                'longitude' => 44.4991,
            ],
            'start_date' => $startDate,
            'end_date' => $endDate,
            'start_time' => '20:00',
        ]);
    });

    $event = Event::query()->first();

    expect($event)
        ->not->toBeNull()
        ->type->toBe('event')
        ->end_date->toDateString()->toBe($endDate);
});
