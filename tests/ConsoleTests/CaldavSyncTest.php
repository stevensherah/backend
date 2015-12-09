<?php

namespace ConsoleTests;

use Illuminate\Foundation\Testing\DatabaseTransactions;

class EmployeesAuthTest extends \TestCase
{
    use DatabaseTransactions;

    public function testPerformSyncForeground()
    {
        if(!$this->doConsole())
            return;
        $status = \Artisan::call('sync:caldav');
        $this->assertEquals(0, $status);
    }

    public function testPerformSyncBackground()
    {
        if(!$this->doConsole())
            return;
        $status = \Artisan::call('sync:caldav', ['--background' => true]);
        $this->assertEquals(0, $status);
    }

    public function testError()
    {
        if(!$this->doConsole())
            return;
        \Artisan::call('sync:caldav');
        $company = \plunner\Company::whereEmail('testInit@test.com')->firstOrFail();
        $employee = $company->employees()->whereEmail('testEmp@test.com')->firstOrFail();
        $calendar = $employee->calendars()->whereName('errors')->firstOrFail()->caldav;
        $this->assertNotEquals('', $calendar->sync_errors);
    }

    //TODO test a correct sync
}
