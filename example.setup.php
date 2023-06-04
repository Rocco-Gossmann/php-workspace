<?php
    require_once __DIR__ . "/Setup.php";

    use rogoss\workspace\Setup;

    echo Setup::create(".")
        ->addLib("lib/de/rogoss/php/core")
        ->compile("init.php")

        ? "success"
        : "failed"
    ;
