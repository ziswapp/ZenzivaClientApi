<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\Import\OrderedImportsFixer;
use Symplify\EasyCodingStandard\ValueObject\Option;
use PhpCsFixer\Fixer\ClassNotation\FinalClassFixer;
use PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer;
use PhpCsFixer\Fixer\FunctionNotation\VoidReturnFixer;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set(Option::PATHS, [
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ]);

    $parameters->set(Option::SKIP, [
        PhpCsFixer\Fixer\PhpUnit\PhpUnitStrictFixer::class
    ]);

    $containerConfigurator->import(SetList::STRICT);
    $containerConfigurator->import(SetList::PSR_12);
    $containerConfigurator->import(SetList::CLEAN_CODE);
    $containerConfigurator->import(SetList::COMMON);

    $services = $containerConfigurator->services();

    $services->set(VoidReturnFixer::class);
    $services->set(FinalClassFixer::class);
    $services->set(DeclareStrictTypesFixer::class);
    $services->set(OrderedImportsFixer::class)->call('configure', [['sort_algorithm' => 'length']]);
};
