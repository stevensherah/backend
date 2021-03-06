<?php

namespace plunner\Http\Controllers\Employees\Groups;

use Illuminate\Http\Request;
use plunner\Group;
use plunner\Http\Controllers\Controller;
use plunner\Http\Requests;

class GroupsController extends Controller
{
    public function __construct()
    {
        config(['auth.model' => \plunner\Employee::class]);
        config(['jwt.user' => \plunner\Employee::class]);
        $this->middleware('jwt.authandrefresh:mode-en');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $employee = \Auth::user();
        return $employee->groups()->with(['meetings' => function ($query) {
            $query->where('start_time', '=', NULL);
        }])->get();
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //TODO check if start_time = null in authorize
        $group = Group::with('meetings')->findOrFail($id);
        $this->authorize($group);
        return $group;
    }
}
