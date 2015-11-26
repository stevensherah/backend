<?php

namespace plunner;

use Illuminate\Database\Eloquent\Model;

/**
 * plunner\Group
 *
 * @property integer $id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $name
 * @property string $description
 * @property integer $planner_id
 * @property-read \Illuminate\Database\Eloquent\Collection|\plunner\Employee[] $employees
 * @method static \Illuminate\Database\Query\Builder|\plunner\Group whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\plunner\Group whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\plunner\Group whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\plunner\Group whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\plunner\Group whereDescription($value)
 * @method static \Illuminate\Database\Query\Builder|\plunner\Group wherePlannerId($value)
 * @property integer $company_id
 * @method static \Illuminate\Database\Query\Builder|\plunner\Group whereCompanyId($value)
 */
class Group extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'groups';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'description', 'planner_id'];

    /**
     * @var array
     */
    protected $hidden = ['planner'];

    /**
     * @var array
     */
    protected $appends = ['planner_name'];

    public function getPlannerNameAttribute()
    {
        if(is_object($this->planner) && $this->planner->exists)
            return $this->planner->name;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function employees()
    {
        return $this->belongsToMany(Employee::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function planner()
    {
        return $this->belongsTo(Planner::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
