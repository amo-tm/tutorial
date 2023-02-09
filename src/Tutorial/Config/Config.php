<?php

namespace Tutorial\Config;

class Config
{
    protected string $widgetExample1Id;

    public static function fromGlobals(): self {
        return new self(getenv('TUTORIAL_WIDGET_EXAMPLE_1_ID'));
    }

    /**
     * @param string $widgetExample1Id
     */
    public function __construct(string $widgetExample1Id)
    {
        $this->widgetExample1Id = $widgetExample1Id;
    }

    /**
     * @return string
     */
    public function getWidgetExample1Id(): string
    {
        return $this->widgetExample1Id;
    }
}