<?php

namespace Dcodegroup\XeroIntegration\Tests\Unit\Data;

use Dcodegroup\XeroIntegration\Data\XeroContactPersonData;

test('can instantiate XeroContactPersonData with all fields', function () {
    $data = new XeroContactPersonData(
        FirstName: 'John',
        LastName: 'Doe',
        EmailAddress: 'john@example.com',
        IncludeInEmails: true,
    );

    expect($data->FirstName)->toBe('John')
        ->and($data->LastName)->toBe('Doe')
        ->and($data->EmailAddress)->toBe('john@example.com')
        ->and($data->IncludeInEmails)->toBeTrue();
});

test('IncludeInEmails defaults to false', function () {
    $data = new XeroContactPersonData;

    expect($data->IncludeInEmails)->toBeFalse();
});

test('can instantiate XeroContactPersonData with null optional fields', function () {
    $data = new XeroContactPersonData(IncludeInEmails: false);

    expect($data->FirstName)->toBeNull()
        ->and($data->LastName)->toBeNull()
        ->and($data->EmailAddress)->toBeNull();
});

test('toXeroArray returns correct keys', function () {
    $data = new XeroContactPersonData(
        FirstName: 'Jane',
        LastName: 'Smith',
        EmailAddress: 'jane@example.com',
        IncludeInEmails: true,
    );

    $array = $data->toXeroArray();

    expect($array)->toHaveKeys(['FirstName', 'LastName', 'EmailAddress', 'IncludeInEmails']);
});

test('toXeroArray returns correct values', function () {
    $data = new XeroContactPersonData(
        FirstName: 'Jane',
        LastName: 'Smith',
        EmailAddress: 'jane@example.com',
        IncludeInEmails: true,
    );

    $array = $data->toXeroArray();

    expect($array['FirstName'])->toBe('Jane')
        ->and($array['LastName'])->toBe('Smith')
        ->and($array['EmailAddress'])->toBe('jane@example.com')
        ->and($array['IncludeInEmails'])->toBeTrue();
});

test('fromXero maps array to XeroContactPersonData', function () {
    $data = XeroContactPersonData::fromXero([
        'FirstName' => 'Bob',
        'LastName' => 'Jones',
        'EmailAddress' => 'bob@example.com',
        'IncludeInEmails' => false,
    ]);

    expect($data)->toBeInstanceOf(XeroContactPersonData::class)
        ->and($data->FirstName)->toBe('Bob')
        ->and($data->LastName)->toBe('Jones')
        ->and($data->EmailAddress)->toBe('bob@example.com')
        ->and($data->IncludeInEmails)->toBeFalse();
});
