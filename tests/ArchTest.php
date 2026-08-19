<?php

arch()->preset()->php();
arch()->preset()->laravel();
arch()->preset()->security();

arch('it will not use debugging functions')
    ->expect(['dd', 'dump', 'ray'])
    ->each->not->toBeUsed();

arch('all data classes will be suffixed with Data')
    ->expect('Dcodegroup\XeroIntegration\Data')
    ->classes()
    ->toHaveSuffix('Data');

arch('all data classes to extend AbstractXeroData')
    ->expect('Dcodegroup\XeroIntegration\Data')
    ->classes()
    ->toExtend('Dcodegroup\XeroIntegration\Data\AbstractXeroData')
    ->ignoring('Dcodegroup\XeroIntegration\Data\AbstractXeroData');

arch('all files in Enum folder are string backed enums')
    ->expect('Dcodegroup\XeroIntegration\Enums')
    ->toBeEnums()
    ->toBeStringBackedEnums();

arch('all enums to be suffixed with Enum')
    ->expect('Dcodegroup\XeroIntegration\Enums')
    ->enums()
    ->toHaveSuffix('Enum');

arch('all commands to be suffixed with Command')
    ->expect('Dcodegroup\XeroIntegration\Commands')
    ->classes()
    ->toHaveSuffix('Command');

arch('all controllers to be suffixed with Controller')
    ->expect('Dcodegroup\XeroIntegration\Http\Controllers')
    ->classes()
    ->toHaveSuffix('Controller');

arch('all files in Traits folder to be traits')
    ->expect('Dcodegroup\XeroIntegration\**\Traits')
    ->toBeTraits();

arch('all files in Contracts folder to be interfaces')
    ->expect('Dcodegroup\XeroIntegration\**\Contracts')
    ->toBeInterfaces();
