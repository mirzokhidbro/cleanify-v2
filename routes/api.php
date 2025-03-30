<?php

use Slim\App;
use App\Application\Lead\Controller\LeadController;
use App\Application\LeadStatus\Controller\LeadStatusController;
use App\Application\Salary\Controller\SalaryController;
use App\Application\Client\Controller\ClientController;
use App\Application\Employee\Controller\EmployeeController;
use App\Application\OrderItemType\Controller\OrderItemTypeController;

return function (App $app) {
    $app->group("/api/v2", function ($app) {
        $app->group("/lead-status", function ($app) {
            $app->get("", [LeadStatusController::class, "index"]);
            $app->post("", [LeadStatusController::class, "store"]);
            $app->get("/{id}", [LeadStatusController::class, "show"]);
            $app->put("/{id}", [LeadStatusController::class, "update"]);
            $app->delete("/{id}", [LeadStatusController::class, "destroy"]);
        });

        $app->group("/leads", function ($app) {
            $app->post("/add-comment", [LeadController::class, "addComment"]);
            $app->get("", [LeadController::class, "index"]);
            $app->post("", [LeadController::class, "store"]);
            $app->get("/{id}", [LeadController::class, "show"]);
            $app->put("/{id}", [LeadController::class, "update"]);
            $app->delete("/{id}", [LeadController::class, "destroy"]);
        });

        $app->group("/salary", function ($app) {
            $app->get("", [SalaryController::class, "index"]);
            $app->post("", [SalaryController::class, "store"]);
            $app->get("/{id}", [SalaryController::class, "show"]);
            $app->put("/{id}", [SalaryController::class, "update"]);
            $app->delete("/{id}", [SalaryController::class, "delete"]);
        });

        $app->group("/clients", function ($app) {
            $app->delete("/{id}", [ClientController::class, "delete"]);
        });

        $app->group("/employees", function ($app) {
            $app->put("edit/{id}", [EmployeeController::class, "update"]);
            $app->delete("delete/{id}", [EmployeeController::class, "delete"]);
        });

        $app->group("/order-item-type", function ($app) {
            $app->delete("/{id}", [OrderItemTypeController::class, "delete"]);
        });
    });
};
