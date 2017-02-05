<?php

use hu\doxasoft\phpbackend\Request;
use hu\doxasoft\phpbackend\authentication\Requester;
use hu\doxasoft\phpbackend\authentication\JWTService;
use hu\chrome\gameoflife\tables\TablesRS;

final class TableRsTest extends \PHPUnit_Framework_TestCase {

    protected $obj = null;

    protected function setUp()
    {
        if (!defined('DB_HOST')) {
            define('DB_HOST', 'localhost');
        }
        if (!defined('DB_NAME')) {
            define('DB_NAME', 'gameoflife');
        }
        if (!defined('DB_USER')) {
            define('DB_USER', 'gameoflife');
        }
        if (!defined('DB_PASS')) {
            define('DB_PASS', 'qwe123');
        }
    }

    public function testCalculateNextStepMethod() {
        $this->generateTableRS([0,0,0,
                                0,0,0,
                                0,0,0]);
        $return = $this->obj->handleRequest();
        $expectedStates = [0,0,0,
                           0,0,0,
                           0,0,0];
        for ($i = 0; $i < 3; $i++) {
            for ($j = 0; $j < 3; $j++) {
                $this->assertEquals($return->table[$i]->cells[$j]->state, $expectedStates[$i*3 + $j]);
            }
        }

        $this->generateTableRS([0,0,0,
                                0,1,0,
                                0,0,0]);
        $return = $this->obj->handleRequest();
        $expectedStates = [0,0,0,
                           0,0,0,
                           0,0,0];
        for ($i = 0; $i < 3; $i++) {
            for ($j = 0; $j < 3; $j++) {
                $this->assertEquals($return->table[$i]->cells[$j]->state, $expectedStates[$i*3 + $j]);
            }
        }

        $this->generateTableRS([0,0,0,
                                0,1,1,
                                0,0,0]);
        $return = $this->obj->handleRequest();
        $expectedStates = [0,0,0,
                           0,0,0,
                           0,0,0];
        for ($i = 0; $i < 3; $i++) {
            for ($j = 0; $j < 3; $j++) {
                $this->assertEquals($return->table[$i]->cells[$j]->state, $expectedStates[$i*3 + $j]);
            }
        }

        $this->generateTableRS([0,0,0,
                                1,1,0,
                                0,0,0]);
        $return = $this->obj->handleRequest();
        $expectedStates = [0,0,0,
                           0,0,0,
                           0,0,0];
        for ($i = 0; $i < 3; $i++) {
            for ($j = 0; $j < 3; $j++) {
                $this->assertEquals($return->table[$i]->cells[$j]->state, $expectedStates[$i*3 + $j]);
            }
        }

        $this->generateTableRS([0,0,0,
                                1,1,1,
                                0,0,0]);
        $return = $this->obj->handleRequest();
        $expectedStates = [0,1,0,
                           0,1,0,
                           0,1,0];
        for ($i = 0; $i < 3; $i++) {
            for ($j = 0; $j < 3; $j++) {
                $this->assertEquals($return->table[$i]->cells[$j]->state, $expectedStates[$i*3 + $j]);
            }
        }

        $this->generateTableRS([0,1,0,
                                0,1,0,
                                0,1,0]);
        $return = $this->obj->handleRequest();
        $expectedStates = [0,0,0,
                           1,1,1,
                           0,0,0];
        for ($i = 0; $i < 3; $i++) {
            for ($j = 0; $j < 3; $j++) {
                $this->assertEquals($return->table[$i]->cells[$j]->state, $expectedStates[$i*3 + $j]);
            }
        }

        $this->generateTableRS([1,1,1,
                                0,0,0,
                                0,0,0]);
        $return = $this->obj->handleRequest();
        $expectedStates = [0,1,0,
                           0,1,0,
                           0,0,0];
        for ($i = 0; $i < 3; $i++) {
            for ($j = 0; $j < 3; $j++) {
                $this->assertEquals($return->table[$i]->cells[$j]->state, $expectedStates[$i*3 + $j]);
            }
        }

        $this->generateTableRS([0,0,0,
                                0,0,0,
                                1,1,1]);
        $return = $this->obj->handleRequest();
        $expectedStates = [0,0,0,
                           0,1,0,
                           0,1,0];
        for ($i = 0; $i < 3; $i++) {
            for ($j = 0; $j < 3; $j++) {
                $this->assertEquals($return->table[$i]->cells[$j]->state, $expectedStates[$i*3 + $j]);
            }
        }

        $this->generateTableRS([1,0,0,
                                1,0,0,
                                1,0,0]);
        $return = $this->obj->handleRequest();
        $expectedStates = [0,0,0,
                           1,1,0,
                           0,0,0];
        for ($i = 0; $i < 3; $i++) {
            for ($j = 0; $j < 3; $j++) {
                $this->assertEquals($return->table[$i]->cells[$j]->state, $expectedStates[$i*3 + $j]);
            }
        }

        $this->generateTableRS([0,0,1,
                                0,0,1,
                                0,0,1]);
        $return = $this->obj->handleRequest();
        $expectedStates = [0,0,0,
                           0,1,1,
                           0,0,0];
        for ($i = 0; $i < 3; $i++) {
            for ($j = 0; $j < 3; $j++) {
                $this->assertEquals($return->table[$i]->cells[$j]->state, $expectedStates[$i*3 + $j]);
            }
        }

        $this->generateTableRS([1,0,0,
                                0,1,0,
                                0,0,1]);
        $return = $this->obj->handleRequest();
        $expectedStates = [0,0,0,
                           0,1,0,
                           0,0,0];
        for ($i = 0; $i < 3; $i++) {
            for ($j = 0; $j < 3; $j++) {
                $this->assertEquals($return->table[$i]->cells[$j]->state, $expectedStates[$i*3 + $j]);
            }
        }
    }

    public function testAvailableTablesFromFilesMethod() {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/api/table/';
        $_SERVER['SCRIPT_NAME'] = '/api/table/';
        $requester = new Requester(new JWTService(), 'Sz87WpLupMnnROfwoRAQRYamy1u4TLLwubJg/CM8D3s=');

        $req = new Request();
        $dao = $this->getMockBuilder('hu\chrome\gameoflife\tables\TablesDAO')
            ->setMethods(array('getAll'))
            ->getMock();

        $dao->expects($this->any())
            ->method('getAll')
            ->will($this->returnValue([]));
        $this->obj = new TablesRS($requester, $req, $dao);

        $return = $this->obj->handleRequest();
        $this->assertGreaterThan(0, sizeof($return));
    }

    public function testGetTablesMethod() {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/api/table/';
        $_SERVER['SCRIPT_NAME'] = '/api/table/';
        $requester = new Requester(new JWTService(), 'Sz87WpLupMnnROfwoRAQRYamy1u4TLLwubJg/CM8D3s=');

        $req = new Request();
        $dao = $this->getMockBuilder('hu\chrome\gameoflife\tables\TablesDAO')
            ->setMethods(array('getAll'))
            ->getMock();

        $dao->expects($this->any())
            ->method('getAll')
            ->will($this->returnValue([]));
        $this->obj = new TablesRS($requester, $req, $dao);

        $returnWithoutDB = $this->obj->handleRequest();

        $dao = $this->getMockBuilder('hu\chrome\gameoflife\tables\TablesDAO')
            ->setMethods(array('getAll'))
            ->getMock();

        $dao->expects($this->any())
            ->method('getAll')
            ->will($this->returnValue(['id'=>1, 'table_name'=>'DoublePi', 'table_data'=>'']));
        $this->obj = new TablesRS($requester, $req, $dao);

        $returnWithDB = $this->obj->handleRequest();

        $this->assertGreaterThan(sizeof($returnWithoutDB), sizeof($returnWithDB));
    }

    private function generateTableRS($states) {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REQUEST_URI'] = '/api/table/next';
        $_SERVER['SCRIPT_NAME'] = '/api/table/';
        $requester = new Requester(new JWTService(), 'Sz87WpLupMnnROfwoRAQRYamy1u4TLLwubJg/CM8D3s=');

        $testData = new \stdClass();
        $testData->table = [];
        for ($i = 0; $i < 3; $i++) {
            $row = new \stdClass();
            $row->cells = [];
            for ($j = 0; $j < 3; $j++) {
                $cell = new \stdClass();
                $cell->row = $i;
                $cell->column = $j;
                $cell->state = $states[$i*3 + $j];

                $row->cells[] = $cell;
            }
            $testData->table[] = $row;
        }

        $req = $this->getMockBuilder('hu\doxasoft\phpbackend\Request')
            ->setMethods(array('hasPayload', 'getPayload'))
            ->getMock();
        $req->expects($this->any())
            ->method('hasPayload')
            ->will($this->returnValue(true));
        $req->expects($this->any())
            ->method('getPayload')
            ->will($this->returnValue($testData));

        $dao = $this->getMockBuilder('hu\chrome\gameoflife\tables\TablesDAO')
            ->getMock();

        $this->obj = new TablesRS($requester, $req, $dao);
    }
}