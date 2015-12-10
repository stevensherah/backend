<?php

namespace Employees;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tymon\JWTAuth\Support\testing\ActingAs;

class MeetingsTest extends \TestCase
{
    use DatabaseTransactions, ActingAs;

    private $company, $group, $employee, $planner, $data_non_repeat, $data_repeat;

    public function setUp()
    {
        parent::setUp();
        config(['auth.model' => \plunner\Employee::class]);
        config(['jwt.user' => \plunner\Employee::class]);

        $this->company = \plunner\Company::findOrFail(1);
        $this->employee = $this->company->employees()->with('groups')->first();
        $this->group = $this->employee->groups->first();
        $this->planner = $this->group->planner;

        $this->data_non_repeat= [
            'title' => 'Test non-repeating meeting',
            'description' => 'Errare humanum est!',
            'start_time' => '2015-12-07 12:00:00',
            'end_time' => '2015-12-07 14:00:00',
            'repeat' => '0',
            'is_scheduled' => false,
            'group_id' => $this->group->id,
            'employee_id' => $this->employee->id
        ];
    }


    public function testIndexAllMeetings()
    {
        $response = $this->actingAs($this->employee)->json('GET', '/employees/meetings');

        $response->assertResponseOk();
        $response->seeJsonEquals($this->employee->meetings->toArray());
    }

    public function testErrorIndexNoMeetings()
    {
        $response = $this->json('GET', '/employees/meetings');

        $response->seeStatusCode(401);
    }

    public function testShowNonRepeatingMeeting()
    {
        $this->actingAs($this->planner)->json('POST', '/employees/meetings', $this->data_non_repeat);
        $meeting_id = $this->employee->meetings->first()->id;

        $response = $this->actingAs($this->employee)->json('GET', '/employees/meetings/'.$meeting_id);
        $response->assertResponseOk();
        $response->seeJsonEquals($this->data_non_repeat->toArray());
    }

    public function testShowNonExistingMeeting()
    {
        $test_meeting_id = 0;

        // Find an id of a non existing meeting
        for ($test_meeting_id; $test_meeting_id < $this->employee->meetings->count() + 1; $test_meeting_id++)
        {
            if ($test_meeting_id == !$this->employee->meetings->where("id", $test_meeting_id)->id())
            {
                break;
            }
        }

        $response = $this->actingAs($this->employee)->json('GET', '/employees/meetings/'.$test_meeting_id);
        $response->seeStatusCode(404);
    }


    /*public function testCreateRepeatingMeeting()
    {
        $data = [
            'title' => 'Requirements meeting',
            'description' => 'Discussing the requirements',
            'start_time' => '20.12.2015',
            'end_time' => '02.01.2016',
            'repeat' => '10',
            'repetition_end_time' => '02.05.2016',
            'group_id' => $this->group->id,
        ];

        $response = $this->actingAs($this->planner)->json('POST', '/employees/meetings', $data);

        $response->assertResponseOk();
        $response->seeJson($data);
    }

    public function testShowRepeatingMeeting()
    {
        $data = [
            'title' => 'Requirements meeting',
            'description' => 'Discussing the requirements',
            'start_time' => '20.12.2015',
            'end_time' => '02.01.2016',
            'repeat' => '10',
            'repetition_end_time' => '02.05.2016',
            'group_id' => $this->group->id,
        ];
        $this->actingAs($this->planner)->json('POST', '/employees/meetings', $data);

        $meeting_id = $this->company->employees()->first()->meetings()->first()->id;
        $response = $this->actingAs($this->planner)->json('GET', '/employees/meetings/' . $meeting_id);

        $response->assertResponseOk();
        $response->seeJson($data);
    }

    public function testShowAllMeetingsInMonth()
    {
        $data = [
            'title' => 'Requirements meeting',
            'description' => 'Discussing the requirements',
            'start_time' => '20.12.2015',
            'end_time' => '02.01.2016',
            'repeat' => '10',
            'repetition_end_time' => '02.05.2016',
            'group_id' => $this->group->id,
        ];
        $this->actingAs($this->planner)->json('POST', '/employees/meetings', $data);

        $meeting_id = $this->company->employees()->first()->meetings()->first()->id;
        $response = $this->actingAs($this->planner)->json('GET', '/employees/meetings/' . $meeting_id);

        $response->assertResponseOk();
        $response->seeJson($data);
    }

    */
}
