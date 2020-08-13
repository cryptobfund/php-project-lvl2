<?php

namespace Gendiff\Formatters\Json;

function build($ast)
{
    return json_encode($ast);
}
