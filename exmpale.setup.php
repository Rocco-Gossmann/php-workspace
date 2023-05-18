<?php
    require_once __DIR__ . "/Setup.php";

    use \de\roccogossmann\php\workspace\Setup;

    echo Setup::create(".")
        ->addLib("lib/de/roccogossmann/php/core")
        ->compile("init.php")

        ? "success"
        : "failed"
    ;

