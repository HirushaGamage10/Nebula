<?php

namespace App\Helpers;

class RoleHelper
{
    // Define all available roles
    const ROLES = [
        'DGM' => 'Deputy General Manager',
        'Program Administrator (level 01)' => 'Program Administrator (level 01)',
        'Program Administrator (level 02)' => 'Program Administrator (level 02)',
        'Student Counselor' => 'Student Counselor',
        'Librarian' => 'Librarian',
        'Hostel Manager' => 'Hostel Manager',
        'Bursar' => 'Bursar',
        'Project Tutor' => 'Project Tutor',
        'Marketing Manager' => 'Marketing Manager',
        'Developer' => 'Developer',
    ];

    // Define role permissions
    const PERMISSIONS = [
        'DGM' => [
            'dashboard',
            'special.approval',
            'student.list',
            'course.change',
            'payment.dashboard'

        ],
        'Program Administrator (level 01)' => [

            // HOME
            'dashboard',

            // USER MANAGEMENT
            'create.user',
            'user.management',

            // STUDENT MANAGEMENT
            'student.registration',
            'course.registration',
            'eligibility.registration',
            'course.badge',
            'student.other.information',
            'student.list',
            'student.profile',
            'course.change',

            // EXAMS
            'exam.results',
            'student.exam.result.management',
            'exam.results.view.edit',

            // REPEAT STUDENTS
            'repeat.students.management',
            'repeat.students.payment',

            // ATTENDANCE
            'attendance',
            'overall.attendance',

            // CLEARANCE
            'all.clearance.management',
            'hostel.clearance.form.management',
            'library.clearance',
            'project.clearance.management',
            'payment.clearance',

            // ACADEMIC
            'module.creation',
            'module.management',
            'course.management',
            'intake.create',
            'semester.create',
            'semester.registration',
            'timetable',

            // FINANCIAL
            'payment.plan',
            'payment.plan.index',
            'payment',
            'late.payment',
            'payment.discounts',
            'payment.dashboard',
            'payment.summary',
            'misc.payment',
            'payment.showDownloadPage',
            'payment.discount.page',
            'latefee.approval.index',

            // SPECIAL
            'special.approval',

            // FOOTER
            'user.profile'
        ],

        'Program Administrator (level 02)' => [
            'dashboard',
            'intake.create',
            'attendance',
            'timetable',
            'student.other.information',
            'student.profile',
            'exam.results',
            'semester.create',
            'semester.registration',
            'module.management',
            'overall.attendance',
            'student.list',
            'repeat.students.management',
            'course.change',
            'payment.dashboard'

        ],
        'Student Counselor' => [
            'dashboard',
            'student.registration',
            'course.registration',
            'eligibility.registration',
            'payment',
            'late.payment',
            'payment.discounts',
            'student.list',
            'course.change',
            'payment.dashboard',
            'latefee.approval',


        ],
        'Marketing Manager' => [
            'dashboard',
            'payment.plan',
            'student.list',
            'course.change',
            'payment.summary',
            'payment.dashboard'


        ],
        'Librarian' => [
            'dashboard',
            'user.profile',
            'library.clearance'

        ],
        'Hostel Manager' => [
            'dashboard',
            'user.profile',
            'hostel.clearance.form.management'
        ],
        'Bursar' => [
            'dashboard',
            'payment.clearance',
        ],
        'Project Tutor' => [
            'dashboard',
            'user.profile',
            'project.clearance.management',
            'attendance'
            
        ],
        'Developer' => [
            'dashboard',
            'create.user',
            'user.management',
            'course.badge',
            'module.management',
            'course.management',
            'overall.attendance',
            'student.profile',
            'student.other.information',
            'all.clearance',
            'student.list',
            'repeat.students.management',
            'intake.create',
            'attendance',
            'timetable',
            'exam.results',
            'semester.create',
            'semester.registration',
            'student.registration',
            'course.registration',
            'eligibility.registration',
            'payment',
            'late.payment',
            'payment.discounts',
            'payment.plan',
            'user.profile',
            'library.clearance',
            'hostel.clearance.form.management',
            'project.clearance.management',
            'payment.clearance',
            'special.approval',
            'reporting.dashboard',
            'data.export.import',
            'file.upload',
            'file.uploadMultiple',
            'file.download',
            'file.delete',
            'file.deleteMultiple',
            'file.info',
            'file.list',
            'file.storageStats',
            'file.cleanup',
            'repeat.students.management',
            'repeat.students.payment',
            'course.change',
            'payment.dashboard'

        ],
    ];

    /**
     * Check if a user has permission to access a specific route
     */
    public static function hasPermission($userRole, $routeName)
    {
        if (!isset(self::PERMISSIONS[$userRole])) {
            return false;
        }

        return in_array($routeName, self::PERMISSIONS[$userRole]);
    }

    /**
     * Get all permissions for a specific role
     */
    public static function getRolePermissions($role)
    {
        return self::PERMISSIONS[$role] ?? [];
    }

    /**
     * Get all available roles
     */
    public static function getRoles()
    {
        return self::ROLES;
    }

    /**
     * Check if user can access student management features
     */
    public static function canAccessStudentManagement($userRole)
    {
        $studentManagementRoutes = [
            'student.registration',
            'course.registration',
            'eligibility.registration',
            'student.other.information',
            'student.exam.result.management',
            'student.list'
        ];

        foreach ($studentManagementRoutes as $route) {
            if (self::hasPermission($userRole, $route)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user can access clearance features
     */
    public static function canAccessClearance($userRole)
    {
        $clearanceRoutes = [
            'all.clearance.management',
            'student.clearance.form.management',
            'hostel.clearance.form.management',
            'project.clearance.management'
        ];

        foreach ($clearanceRoutes as $route) {
            if (self::hasPermission($userRole, $route)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user can access academic management features
     */
    public static function canAccessAcademicManagement($userRole)
    {
        $academicRoutes = [
            'module.creation',
            'course.management',
            'intake.create',
            'semesters.create',
            'module.management',
            'timetable'
        ];

        foreach ($academicRoutes as $route) {
            if (self::hasPermission($userRole, $route)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user can access attendance features
     */
    public static function canAccessAttendance($userRole)
    {
        return self::hasPermission($userRole, 'attendance') || 
               self::hasPermission($userRole, 'overall.attendance');
    }
} 