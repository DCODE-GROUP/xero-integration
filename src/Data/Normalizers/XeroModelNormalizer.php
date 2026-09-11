<?php

namespace Dcodegroup\XeroIntegration\Data\Normalizers;

use Spatie\LaravelData\Normalizers\Normalizer;
use XeroPHP\Remote\Model as XeroModel;

class XeroModelNormalizer implements Normalizer
{
    public function normalize(mixed $value): ?array
    {
        if (! $value instanceof XeroModel) {
            return null;
        }

        $stringArray = $value->toStringArray();
        $dataArray = [];
        foreach ($stringArray as $key => $item) {
            $dataArray[$key] = $value->{$key};
        }

        return $dataArray;
    }
}
