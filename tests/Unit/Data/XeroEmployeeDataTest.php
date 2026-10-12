<?php

namespace Dcodegroup\XeroIntegration\Tests\Unit\Data;

use Dcodegroup\XeroIntegration\Data\PayrollAU\XeroEmployeeData;
use Illuminate\Support\Carbon;
use XeroPHP\Models\PayrollAU\Employee;

beforeEach(function () {
    if (! config()->has('data')) {
        config(['data' => require __DIR__.'/../../../vendor/spatie/laravel-data/config/data.php']);
    }
});

test('can instantiate XeroEmployeeData with required fields', function () {
    $data = minimumEmployeeConstructor('John', 'Doe', Carbon::parse('1990-05-15'), Carbon::parse('2024-01-16 12:00:00'));

    expect($data->FirstName)->toBe('John')
        ->and($data->LastName)->toBe('Doe')
        ->and($data->DateOfBirth->format('Y-m-d'))->toBe('1990-05-15')
        ->and($data->EmployeeID)->toBeNull();
});

test('optional fields default to null', function () {
    $data = minimumEmployeeConstructor('Jane', 'Doe', Carbon::parse('1985-10-20'), Carbon::parse('2024-01-16 12:00:00'));

    expect($data->HomeAddress)->toBeNull()
        ->and($data->StartDate)->toBeNull()
        ->and($data->Email)->toBeNull()
        ->and($data->JobTitle)->toBeNull()
        ->and($data->TerminationDate)->toBeNull();
});

test('fromXero maps an employee model and fields correctly', function () {
    $employee = mock(Employee::class);
    $employee->shouldReceive('toStringArray')->andReturn([
        'FirstName' => 'John',
        'LastName' => 'Doe',
        'DateOfBirth' => '1990-05-15',
        'HomeAddress' => null,
        'StartDate' => '2022-01-01',
        'Title' => 'Mr',
        'MiddleNames' => 'Robert',
        'Email' => 'john.doe@example.com',
        'Gender' => 'M',
        'Phone' => '123456789',
        'Mobile' => '987654321',
        'TwitterUserName' => null,
        'IsAuthorisedToApproveLeave' => true,
        'IsAuthorisedToApproveTimesheets' => true,
        'JobTitle' => 'Developer',
        'Classification' => null,
        'OrdinaryEarningsRateID' => null,
        'PayrollCalendarID' => null,
        'EmployeeGroupName' => null,
        'BankAccounts' => [],
        'PayTemplate' => [],
        'OpeningBalances' => [],
        'LeaveBalances' => [],
        'SuperMemberships' => [],
        'TerminationDate' => null,
        'TerminationReason' => null,
        'EmployeeID' => 'employee-uuid',
        'Status' => 'ACTIVE',
        'UpdatedDateUTC' => '2024-01-16T12:00:00Z',
        'IsSTP2Qualified' => true,
        'IncomeType' => [],
        'EmploymentType' => [],
        'CountryOfResidence' => 'AU',
    ]);

    $data = XeroEmployeeData::fromXero($employee);

    expect($data)->toBeInstanceOf(XeroEmployeeData::class)
        ->and($data->FirstName)->toBe('John')
        ->and($data->LastName)->toBe('Doe')
        ->and($data->EmployeeID)->toBe('employee-uuid')
        ->and($data->Email)->toBe('john.doe@example.com')
        ->and($data->JobTitle)->toBe('Developer')
        ->and($data->IsAuthorisedToApproveLeave)->toBeTrue();
});

function minimumEmployeeConstructor(
    string $FirstName,
    string $LastName,
    Carbon $DateOfBirth,
    Carbon $UpdatedDateUTC,
) {
    return new XeroEmployeeData(
        FirstName: $FirstName,
        LastName: $LastName,
        DateOfBirth: $DateOfBirth,
        HomeAddress: null,
        StartDate: null,
        Title: null,
        MiddleNames: null,
        Email: null,
        Gender: null,
        Phone: null,
        Mobile: null,
        TwitterUserName: null,
        IsAuthorisedToApproveLeave: null,
        IsAuthorisedToApproveTimesheets: null,
        JobTitle: null,
        Classification: null,
        OrdinaryEarningsRateID: null,
        PayrollCalendarID: null,
        EmployeeGroupName: null,
        BankAccounts: null,
        PayTemplate: null,
        OpeningBalances: null,
        LeaveBalances: null,
        SuperMemberships: null,
        TerminationDate: null,
        TerminationReason: null,
        EmployeeID: null,
        Status: null,
        UpdatedDateUTC: $UpdatedDateUTC,
        IsSTP2Qualified: null,
        IncomeType: null,
        EmploymentType: null,
        CountryOfResidence: null,
    );
}
