<?php

namespace App\Tests;

class UrlCheckerTest extends AuthentificationHelper
{
///////////////////////////////////////////////////////
// dont forget to update db !!
// 1) change .env database test_admin_events
// 2) migrate
// or copy db
////////////////////////////////////////////////////// 
    /**
     * @dataProvider urlProvider
     */
    public function testPageIsSuccessful($url)
    {
       
    	
        $this->client->request('GET', $url);
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function urlProvider()
    {
        // yield ['/']; if only one online, redirection
        yield ['/admin'];

        yield ['/rouen-2018'];

        yield ['/admin/event/'];
        yield ['/admin/event/75'];
        yield ['/admin/event/new/1-1'];
        yield ['/admin/event/new/2-1'];
        yield ['/admin/event/75/edit'];
        yield ['/admin/event/75/add-organization'];
        yield ['/admin/event/place'];
        yield ['/admin/event/change-manager/1'];
        yield ['/admin/event/see-recall/1'];

        yield ['/admin/event-type/'];
        yield ['/admin/event-type/edit/1'];

        yield ['/admin/place/'];
        yield ['/admin/place/new'];
        yield ['/admin/place/1'];
        yield ['/admin/place/1/edit'];

        yield ['/admin/organization/'];
        yield ['/admin/organization/new'];
        yield ['/admin/organization/2'];
        yield ['/admin/organization/2/edit'];

        yield ['/admin/user'];
        yield ['/admin/user/new/l4m'];
        yield ['/admin/user/new/rh'];
        yield ['/admin/user/new/exposant/10'];
        yield ['/admin/user/2/edit'];  //l4m user france
        yield ['/admin/user/40/edit'];  //exposant user raclette   

        yield ['/admin/participation/2'];
        yield ['/admin/participation/2/edit'];
        yield ['/admin/participation/stand-numbers/75'];
        //yield ['/admin/event/show/1'];
        //...
    }
}
