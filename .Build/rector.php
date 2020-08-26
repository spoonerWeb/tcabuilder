<?php
declare(strict_types=1);

use Rector\Core\Configuration\Option;
use Rector\Set\ValueObject\SetList;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void
{
    // get parameters
    $parameters = $containerConfigurator->parameters();

    $parameters->set(
        Option::PATHS,
        [
            'Classes/'
        ]
    );

    // here we can define, what sets of rules will be applied
    $parameters->set(
        Option::SETS,
        [
            SetList::CODE_QUALITY,
            SetList::DEAD_CODE,
            SetList::PHP_72,
        ]
    );
};
