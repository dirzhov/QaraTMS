<?php

namespace App\Enums;

enum UserPermission: string
{
    case manage_users = 'manage_users';
    case add_edit_projects = 'add_edit_projects';
    case delete_projects = 'delete_projects';
    case add_edit_repositories = 'add_edit_repositories';
    case delete_repositories = 'delete_repositories';
    case add_edit_test_suites = 'add_edit_test_suites';
    case delete_test_suites = 'delete_test_suites';
    case add_edit_test_cases = 'add_edit_test_cases';
    case delete_test_cases = 'delete_test_cases';
    case add_edit_test_plans = 'add_edit_test_plans';
    case delete_test_plans = 'delete_test_plans';
    case add_edit_test_runs = 'add_edit_test_runs';
    case delete_test_runs = 'delete_test_runs';
    case add_edit_documents = 'add_edit_documents';
    case delete_documents = 'delete_documents';
    case view_automation_runs = 'view_automation_runs';
    case manage_automation_runs = 'manage_automation_runs';
    case add_edit_product_versions = 'add_edit_product_versions';
    case change_review_assignee = 'change_review_assignee';

    public function summary(): string {
        return match ($this) {
            UserPermission::add_edit_projects,
                UserPermission::delete_projects => "Project",
            UserPermission::add_edit_repositories,
                UserPermission::delete_repositories => "Repository",
            UserPermission::add_edit_test_suites,
                UserPermission::delete_test_suites => "Test Suite",
            UserPermission::add_edit_test_cases,
                UserPermission::delete_test_cases => "Test Case",
            UserPermission::add_edit_test_plans,
                UserPermission::delete_test_plans => "Test Plan",
            UserPermission::add_edit_test_runs,
                UserPermission::delete_test_runs => "Test Run",
            UserPermission::add_edit_documents,
                UserPermission::delete_documents => "Document",
            UserPermission::manage_users => "Manage Users",
            UserPermission::view_automation_runs => "View Automation Runs",
            UserPermission::manage_automation_runs => "Manage Automation Runs",
            UserPermission::add_edit_product_versions => "Product Version",
            UserPermission::change_review_assignee => "Change Review Assignee",
        };
    }

}
