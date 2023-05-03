<?php

namespace Szogyenyid\Phocus;

use Exception;
use ReflectionMethod;
use ReflectionParameter;

/**
 * An abstract class to be inherited by all views
 */
abstract class AbstractView
{
    /**
     * The absolute path to the template file to be used
     *
     * @var string
     */
    private string $template;
    /**
     * The parameters to pass to the template
     *
     * @var array<string,mixed>
     */
    private array $params = array();

    /**
     * Creates a new view
     *
     * @param string $template  The absolute path to the template file.
     * @param mixed  ...$params The parameters to pass to the template with the given name.
     */
    public function __construct(string $template, mixed ...$params)
    {
        $this->template = $template;
        $methodParams = self::getMethodParams();
        for (
            $abstractViewParameterIterator = 0;
            $abstractViewParameterIterator < count($methodParams);
            $abstractViewParameterIterator++
        ) {
            $parName = $methodParams[$abstractViewParameterIterator]->name;
            $parVal = $params[$abstractViewParameterIterator];
            $this->params[$parName] = $parVal;
        }
    }

    /**
     * Renders the view
     *
     * @return void
     */
    public function render(): void
    {
        foreach ($this->params as $parName => $parVal) {
            $$parName = $parVal;
        }
        include $this->template;
    }

    /**
     * Return the view as a string. Useful for e-mail bodies, and other views that need to be used as a string.
     *
     * @param string $template  The absolute path to the template file.
     * @param mixed  ...$params The parameters to pass to the template with the given name.
     * @return string The resulting view as a string
     */
    public static function toString(string $template, mixed ...$params): string
    {
        ob_start();
        $methodParams = self::getMethodParams();
        for (
            $abstractViewParameterIterator = 0;
            $abstractViewParameterIterator < count($methodParams);
            $abstractViewParameterIterator++
        ) {
            $parName = $methodParams[$abstractViewParameterIterator]->name;
            $parVal = $params[$abstractViewParameterIterator];
            $$parName = $parVal;
        }
        include $template;
        $obOut = ob_get_clean();
        if (is_bool($obOut)) {
            return "";
        }
        return $obOut;
    }
    /**
     * Returns the parameters of the method that called the constructor
     *
     * @return array<ReflectionParameter> The parameters of the method that called the constructor.
     * @throws Exception If the class that inherited from AbstractView is not a subclass of a class.
     */
    private static function getMethodParams(): array
    {
        $trace = debug_backtrace()[2];
        if (empty($trace['class'])) {
            throw new Exception("AbstractView inherited from illegal class.");
        }
        $methodParams = array_values(array_filter(
            (new ReflectionMethod(
                $trace['class'],
                $trace['function']
            ))->getParameters(),
            function ($reflParam) {
                return $reflParam->getName() != "template";
            }
        ));
        return $methodParams;
    }
}
