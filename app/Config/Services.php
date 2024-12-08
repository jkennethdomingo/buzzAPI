<?php

namespace Config;

use CodeIgniter\Config\BaseService;

use App\Models\{
    BuzzerStateModel,
    LogsModel,
    ScoresModel,
    UserModel,
    SectionsModel,
    ActivitiesModel,
    UserActivitiesModel,
    AttendanceModel
};

/**
 * Services Configuration file.
 *
 * Services are simply other classes/libraries that the system uses
 * to do its job. This is used by CodeIgniter to allow the core of the
 * framework to be swapped out easily without affecting the usage within
 * the rest of your application.
 *
 * This file holds any application-specific services, or service overrides
 * that you might need. An example has been included with the general
 * method format you should use for your service methods. For more examples,
 * see the core Services file at system/Config/Services.php.
 */
class Services extends BaseService
{
    /*
     * public static function example($getShared = true)
     * {
     *     if ($getShared) {
     *         return static::getSharedInstance('example');
     *     }
     *
     *     return new \CodeIgniter\Example();
     * }
     */

    public static function buzzerStateModel($getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('buzzerStateModel');
        }

        return new BuzzerStateModel();
    }

    public static function logsModel($getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('logsModel');
        }

        return new LogsModel();
    }

    public static function scoresModel($getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('scoresModel');
        }

        return new ScoresModel();
    }

    public static function userModel($getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('userModel');
        }

        return new UserModel();
    }

    public static function sectionsModel($getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('sectionsModel');
        }

        return new SectionsModel();
    }

    public static function activitiesModel($getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('activitiesModel');
        }

        return new ActivitiesModel();
    }

    public static function userActivitiesModel($getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('userActivitiesModel');
        }

        return new UserActivitiesModel();
    }

    public static function attendanceModel($getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('attendanceModel');
        }

        return new AttendanceModel();
    }
}
