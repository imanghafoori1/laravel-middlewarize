<?php


namespace Imanghafoori\Middlewarize\Tests\helpers;


use Imanghafoori\Middlewarize\Middlewarable;

class MockClass
{
    use Middlewarable;

    public function find($id)
    {
        return $id;
    }

    public static function static_find($id)
    {
        return $id;
    }

    /**
     * @throws \Exception
     */
    public function faily()
    {
        throw new \Exception('Oh my God');
    }

    /**
     * @throws \Exception
     */
    public static function static_faily()
    {
        throw new \Exception('Oh my God');
    }
}