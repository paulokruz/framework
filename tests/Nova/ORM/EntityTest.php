<?php


namespace Nova\ORM;

use App\Modules\Demo\Models\Entities\Car;

class EntityTest extends \PHPUnit_Framework_TestCase
{
    public function testBasic()
    {
        $car = new Car();

        $cols = Structure::getTableColumns($car);

        $this->assertEquals(4, count($cols));
    }
}
