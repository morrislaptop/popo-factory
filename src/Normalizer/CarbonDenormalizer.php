<?php

namespace Morrislaptop\PopoFactory\Normalizer;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class CarbonDenormalizer implements DenormalizerInterface
{
    /**
     * @inheritDoc
     */
    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        return new Carbon($data);
    }

    /**
     * @inheritDoc
     */
    public function supportsDenormalization($data, string $type, string $format = null)
    {
        return is_a($type, CarbonInterface::class, true);
    }
}
