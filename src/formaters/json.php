<?php

namespace Gendiff\Formaters\Json;

function builderJson($ast)
{
    //return json_encode($ast, JSON_PRETTY_PRINT);
    return json_encode($ast);
}
