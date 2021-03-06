<?php

use Illuminate\Database\Seeder;

class TestsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        self::company();
        self::makeDataKnown();

    }

    static private function makeDataKnown()
    {
        //create company
        $company = [
            'name' => 'testInit',
            'email' => 'testInit@test.com',
            'password' => bcrypt('test'),
            'remember_token' => str_random(10),
        ];
        $company = plunner\Company::create($company);

        //create employees
        self::employees($company);
        $employee = new \plunner\Employee([
            'name' => 'testEmp',
            'email' => 'testEmp@test.com',
            'password' => bcrypt('test'),
            'remember_token' => str_random(10),
        ]);
        $company->employees()->save($employee);
        self::calendars($employee);

        //create groups
        self::groups($company, $company->employees->toArray());

        //add caldav
        $calendar = $employee->calendars()->create(['name' => 'errors']);
        $calendar->caldav()->create(['url'=>'https://example.com', 'username'=>'caldav.test@plunner.com', 'password'=>Crypt::encrypt('wrong'), 'calendar_name' => 'test']);
        $examples = env('CALDAV_EXAMPLES', '[]');
        $examples = json_decode($examples, true);
        foreach($examples as $example) {
            $example['password'] = Crypt::encrypt($example['password']);
            $employee->calendars()->create(['name' => 'caldavTes'])->caldav()->create($example);
        }
        //TODO seed caldavs for all users
    }

    static private function company()
    {
        factory(plunner\Company::class, 10)->create()->each(function ($company) {
            self::employees($company);
            self::groups($company, $company->employees->toArray());
        });
    }

    static private function employees($company)
    {
        factory(plunner\Employee::class, 3)->make()->each(function ($employee) use ($company) {
            $company->employees()->save($employee);
            self::calendars($employee);
        });
    }

    static private function calendars($employee)
    {
        factory(plunner\Calendar::class, 3)->make()->each(function ($calendar) use($employee){
            $employee->calendars()->save($calendar);
            self::timeslots($calendar);
        });
    }

    static private function timeslots($calendar)
    {
        factory(plunner\Timeslot::class, 3)->make()->each(function ($timeslot) use($calendar){
            $calendar->timeslots()->save($timeslot);
        });
    }

    static private function timeslotsMeeting($meeting)
    {
        factory(plunner\MeetingTimeslot::class, 3)->make()->each(function ($timeslot) use($meeting){
            $meeting->timeslots()->save($timeslot);
        });
    }

    static private function meetings($group)
    {
        factory(plunner\Meeting::class, 3)->make()->each(function ($meeting) use($group){
            $group->meetings()->save($meeting);
            self::timeslotsMeeting($meeting);
        });
    }

    static private function groups($company, $employees)
    {
        factory(plunner\Group::class, 4)->make()->each(function ($group) use ($company, $employees) {
            $employeeSubsetIndices = array_rand($employees, rand(1, 3)); // 1 to 3 random members in each team
            $employeeSubsetIndices = is_array($employeeSubsetIndices) ? $employeeSubsetIndices : [$employeeSubsetIndices];
            $employeeSubset = array_map(function ($index) use ($company) {
                return $company->employees[$index];
            }, $employeeSubsetIndices);

            $plannerIndex = array_rand($employeeSubset);
            $employeePlanner = $company->employees[$plannerIndex];

            /**
             * @var $group \plunner\Group
             */
            $group->planner_id = $employeePlanner->id;
            $company->groups()->save($group);

            array_map(function ($employee) use ($group) {
                $group->employees()->save($employee);
            }, $employeeSubset);
            self::meetings($group);
        });
    }
}
