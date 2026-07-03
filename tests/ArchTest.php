<?php

arch()->preset()->laravel();
arch()->preset()->security();

arch('it will not use debugging functions')
    ->expect(['dd', 'dump', 'ray'])
    ->each->not->toBeUsed();

arch('all data classes will be suffixed with Data')
    ->expect('DcodeGroup\XeroIntegration\Data')
    ->classes()
    ->toHaveSuffix('Data');

arch('all data classes to extend AbstractXeroData')
    ->expect('DcodeGroup\XeroIntegration\Data')
    ->classes()
    ->toExtend('DcodeGroup\XeroIntegration\Data\AbstractXeroData')
    ->ignoring('DcodeGroup\XeroIntegration\Data\AbstractXeroData');

arch('all files in Enum folder are string backed enums')
    ->expect('DcodeGroup\XeroIntegration\Enums')
    ->toBeEnums()
    ->toBeStringBackedEnums();

arch('all enums to be suffixed with Enum')
    ->expect('DcodeGroup\XeroIntegration\Enums')
    ->enums()
    ->toHaveSuffix('Enum');

arch('all commands to be suffixed with Command')
    ->expect('DcodeGroup\XeroIntegration\Commands')
    ->classes()
    ->toHaveSuffix('Command');

arch('all controllers to be suffixed with Controller')
    ->expect('DcodeGroup\XeroIntegration\Http\Controllers')
    ->classes()
    ->toHaveSuffix('Controller');

arch('all files in Traits folder to be traits')
    ->expect('DcodeGroup\XeroIntegration\**\Traits')
    ->toBeTraits();

arch('all files in Contracts folder to be interfaces')
    ->expect('DcodeGroup\XeroIntegration\**\Contracts')
    ->toBeInterfaces();
