<?php

function decorate($object)
{
    return new Middleware($object);
}
