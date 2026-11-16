# Changelog

All notable changes to `xero-integration` will be documented in this file.

## v0.0.16 - 2026-11-16

### What's Changed

* feat: add Payroll AU 2.0 timesheets, earnings rates and payroll calendars by @jny986 in https://github.com/DCODE-GROUP/xero-integration/pull/25
  * Sync payroll timesheets to Xero AU via a new Saloon gateway (Payroll AU 2.0)
  * New `XeroEarningsRateData` and `XeroPayrollCalendarData` with per-tenant caching (`xero-integration.cache.*`)
  * Fix `XeroEmployeeData` mapping to `EMPLOYEE_AU`
  * Add Laravel 10 support
  * CI: ignore specific Laravel 11 advisories instead of disabling audit blocking
  
* Update CHANGELOG

#### Breaking changes

* `XeroTimesheetData` and `XeroTimesheetLineData` properties renamed to the 2.0 camelCase names
* `forPeriod()` now takes a `payrollCalendarId`
* `fromXero()` now throws for timesheets, lines, earnings rates and calendars
* `attachBinary()` and the xero-php `list()`/`$searchFields` dedupe no longer apply to timesheets
* Updating changed lines on an Approved timesheet reverts it to Draft unless `status` is explicitly Approved
* Adds `saloonphp/saloon` and `saloonphp/laravel-plugin` dependencies

**Full Changelog**: https://github.com/DCODE-GROUP/xero-integration/compare/v0.0.15...v0.0.16

## v0.0.15 - 2026-10-23

### What's Changed

* Remove offending properties by @kathdcode in https://github.com/DCODE-GROUP/xero-integration/pull/23

**Full Changelog**: https://github.com/DCODE-GROUP/xero-integration/compare/v0.0.14...v0.0.15

## v0.0.14 - 2026-10-23

### What's Changed

* Fix contact data mapping by @kathdcode in https://github.com/DCODE-GROUP/xero-integration/pull/22

**Full Changelog**: https://github.com/DCODE-GROUP/xero-integration/compare/v0.0.13...v0.0.14

## v0.0.13 - 2026-10-21

### What's Changed

* Fix token refresh race conditions by @kathdcode in https://github.com/DCODE-GROUP/xero-integration/pull/21

**Full Changelog**: https://github.com/DCODE-GROUP/xero-integration/compare/v0.0.12...v0.0.13

## v0.0.12 - 2026-10-14

### What's Changed

* Added employee DTO by @Barry-B-Benson6 in https://github.com/DCODE-GROUP/xero-integration/pull/20

### New Contributors

* @Barry-B-Benson6 made their first contribution in https://github.com/DCODE-GROUP/xero-integration/pull/20

**Full Changelog**: https://github.com/DCODE-GROUP/xero-integration/compare/v0.0.11...v0.0.12

## v0.0.11 - 2026-10-09

### What's Changed

* Fix record identification by @kathdcode in https://github.com/DCODE-GROUP/xero-integration/pull/19

**Full Changelog**: https://github.com/DCODE-GROUP/xero-integration/compare/v0.0.10...v0.0.11

## v0.0.10 - 2026-10-08

### What's Changed

* Fix inadvertently returning the wrong Xero object to sync to by @kathdcode in https://github.com/DCODE-GROUP/xero-integration/pull/18

**Full Changelog**: https://github.com/DCODE-GROUP/xero-integration/compare/v0.0.9...v0.0.10

## v0.0.9 - 2026-10-07

### What's Changed

* Add DTOs for purchase orders by @kathdcode in https://github.com/DCODE-GROUP/xero-integration/pull/17

**Full Changelog**: https://github.com/DCODE-GROUP/xero-integration/compare/v0.0.8...v0.0.9

## v0.0.8 - 2026-10-06

### What's Changed

* fix/laravel-10-compat by @Gavindecode in https://github.com/DCODE-GROUP/xero-integration/pull/16

**Full Changelog**: https://github.com/DCODE-GROUP/xero-integration/compare/v0.0.6...v0.0.8

## v0.0.7 - 2026-10-06

### What's Changed

* feature/WEL-35-update-xero-integration-package-to-support-laravel-10 by @Gavindecode in https://github.com/DCODE-GROUP/xero-integration/pull/15

**Full Changelog**: https://github.com/DCODE-GROUP/xero-integration/compare/v0.0.5...v0.0.7

## v 0.0.6 - 2026-10-05

### What's Changed

* feature/WEL-35-update-xero-integration-package-to-support-laravel-10 by @Gavindecode in https://github.com/DCODE-GROUP/xero-integration/pull/15

**Full Changelog**: https://github.com/DCODE-GROUP/xero-integration/compare/v0.0.5...0.0.6

## v0.0.5 - 2026-09-11

### What's Changed

* Feature/add items by @kathdcode in https://github.com/DCODE-GROUP/xero-integration/pull/14

**Full Changelog**: https://github.com/DCODE-GROUP/xero-integration/compare/v0.0.4...v0.0.5

## v0.0.4 - 2026-09-11

### What's Changed

* Feature/eloquent style static queries by @kathdcode in https://github.com/DCODE-GROUP/xero-integration/pull/13

**Full Changelog**: https://github.com/DCODE-GROUP/xero-integration/compare/v0.0.3...v0.0.4

## v0.0.3 - 2026-09-09

### What's Changed

* Defer config exceptions until we actually try to use the config by @kathdcode in https://github.com/DCODE-GROUP/xero-integration/pull/12

**Full Changelog**: https://github.com/DCODE-GROUP/xero-integration/compare/v0.0.2...v0.0.3
