<?php

/**
 *
 */
class ShowGameSettingsPage extends AbstractAdminPage
{
    public function __construct()
    {
        parent::__construct();
    }

    public function show(): void
    {
        $this->display('page.gameSettings.default.tpl');
    }

    public function rapidFire(): void
    {
        $this->display('page.gameSettings.rapidFire.tpl');
    }
}
