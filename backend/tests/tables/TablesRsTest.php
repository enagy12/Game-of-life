<?php

use hu\doxasoft\phpbackend\Request;
use hu\doxasoft\phpbackend\authentication\Requester;
use hu\doxasoft\phpbackend\authentication\JWTService;
use hu\chrome\gameoflife\tables\TablesRS;

final class TableRsTest extends \PHPUnit_Framework_TestCase {

    protected $obj = null;

    protected function setUp()
    {
        define('DB_HOST', 'localhost');
        define('DB_NAME', 'gameoflife');
        define('DB_USER', 'gameoflife');
        define('DB_PASS', 'qwe123');

//        $_SERVER['REQUEST_METHOD'] = 'GET';
//        $_SERVER['REQUEST_URI'] = '/api/table/';
//
//        $requester = new Requester(new JWTService(), 'Sz87WpLupMnnROfwoRAQRYamy1u4TLLwubJg/CM8D3s=');
//        $req = new Request();
//        $dao = $this->getMockBuilder('hu\chrome\gameoflife\tables\TablesDAO')
//            ->setMethods(array('getAll'))
//            ->getMock();
//
//        $dao->expects($this->any())
//            ->method('getAll')
//            ->will($this->returnValue(['id'=>1, 'table_name'=>'DoublePi', 'table_data'=>'']));
//        $this->obj = new TablesRS($requester, $req, $dao);
    }

    public function testCalculateNextStepMethod()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REQUEST_URI'] = '/api/table/next';
        $_SERVER['SCRIPT_NAME'] = '/api/table/';

        $requester = new Requester(new JWTService(), 'Sz87WpLupMnnROfwoRAQRYamy1u4TLLwubJg/CM8D3s=');
        //$req = new Request();
        $req = $this->getMockBuilder('hu\doxasoft\phpbackend\Request')
            ->setMethods(array('hasPayload', 'getPayload'))
            ->getMock();
        $req->expects($this->any())
            ->method('hasPayload')
            ->will($this->returnValue(true));
//        $req->expects($this->any())
//            ->method('getPayload')
//            ->will(
//                $this->returnValue(
//                    {
//                        "table": []
//                    }
//                )
//            );

        $dao = $this->getMockBuilder('hu\chrome\gameoflife\tables\TablesDAO')
            ->getMock();

        $this->obj = new TablesRS($requester, $req, $dao);

        $return = $this->obj->handleRequest();
        var_dump($return);
        $this->assertEquals(1, 1);
    }
}