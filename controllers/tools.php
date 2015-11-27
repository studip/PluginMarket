<?php
require_once 'market_controller.php';

class ToolsController extends MarketController
{
    public function sidebar_graphics_generator_action()
    {
        PageLayout::addScript($this->plugin->getPluginURL()."/assets/sidebar/jquery.color.js");
        PageLayout::addScript($this->plugin->getPluginURL()."/assets/sidebar/sidebar_graphics_generator.js");
    }
}