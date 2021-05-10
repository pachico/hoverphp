<?php

declare(strict_types=1);

namespace Pachico\HoverPHPUTest;

use JsonSchema\Constraints\Constraint;
use JsonSchema\Validator;
use Pachico\HoverPHP\Entity\Simulation;
use PHPUnit\Framework\TestCase;

abstract class AbstractTestCase extends TestCase
{
    protected string $simulationSchemaPath = __DIR__ . '/../../resources/hoverfly/schema/simulation.json';

    protected function assertSimulationCompliesWithSchema(Simulation $simulation)
    {
        // Arrange
        $validator = new Validator();

        $simulationAsObj = json_decode(json_encode($simulation), false);

        $validator->validate(
            $simulationAsObj,
            (object)['$ref' => 'file://' . $this->simulationSchemaPath],
            Constraint::CHECK_MODE_TYPE_CAST
        );

        $errorMessage = '';
        $isValid = $validator->isValid();
        if (!$isValid) {
            $errorMessage .= "JSON does not validate. Violations:\n";
            foreach ($validator->getErrors() as $error) {
                $errorMessage .= sprintf("[%s] %s\n", $error['property'], $error['message']);
            }
        }

        $this->assertTrue($isValid, $errorMessage);
    }
}
