<?php

use Slim\Factory\ServerRequestCreatorFactory;

return function () {
    $serverRequestCreator = ServerRequestCreatorFactory::create();
    return $serverRequestCreator->createServerRequestFromGlobals();
};