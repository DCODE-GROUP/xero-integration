<?php

namespace Dcodegroup\XeroIntegration\Tests\Unit\Models;

use Dcodegroup\XeroIntegration\Models\XeroRecord;

test('can create xero record using factory', function () {
    $record = XeroRecord::factory()->create();

    expect($record)->toBeInstanceOf(XeroRecord::class)
        ->and($record->xero_id)->not->toBeNull()
        ->and($record->recordable_type)->not->toBeNull()
        ->and($record->recordable_id)->not->toBeNull();
});

test('xero record has correct fillable attributes', function () {
    $record = new XeroRecord;

    expect($record->getFillable())->toBe(['recordable_id', 'recordable_type', 'xero_id']);
});

test('can update xero record', function () {
    $record = XeroRecord::factory()->create();
    $newXeroId = 'updated-xero-id-123';

    $record->update(['xero_id' => $newXeroId]);

    expect($record->fresh()->xero_id)->toBe($newXeroId);
});

test('can delete xero record', function () {
    $record = XeroRecord::factory()->create();
    $id = $record->id;

    $record->delete();

    expect(XeroRecord::find($id))->toBeNull();
});

test('xero record uses soft deletes', function () {
    $record = XeroRecord::factory()->create();
    $id = $record->id;

    $record->delete();

    expect(XeroRecord::withTrashed()->find($id))->not->toBeNull()
        ->and(XeroRecord::withTrashed()->find($id)->deleted_at)->not->toBeNull();
});

test('can restore soft deleted xero record', function () {
    $record = XeroRecord::factory()->create();
    $id = $record->id;

    $record->delete();
    XeroRecord::withTrashed()->find($id)->restore();

    expect(XeroRecord::find($id))->not->toBeNull();
});

test('xero record has recordable polymorphic relation', function () {
    $record = new XeroRecord;

    expect(method_exists($record, 'recordable'))->toBeTrue();
});

test('can retrieve xero record by xero id', function () {
    $record = XeroRecord::factory()->create(['xero_id' => 'test-xero-id-abc']);

    $found = XeroRecord::where('xero_id', 'test-xero-id-abc')->first();

    expect($found)->not->toBeNull()
        ->and($found->id)->toBe($record->id);
});

test('xero record has timestamps', function () {
    $record = XeroRecord::factory()->create();

    expect($record->created_at)->not->toBeNull()
        ->and($record->updated_at)->not->toBeNull();
});
