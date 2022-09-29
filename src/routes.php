<?php

namespace PHPMaker2022\project11;

use Slim\App;
use Slim\Routing\RouteCollectorProxy;

// Handle Routes
return function (App $app) {
    // pronosticador
    $app->map(["GET","POST","OPTIONS"], '/pronosticadorlist[/{ID_ENCUESTA}]', PronosticadorController::class . ':list')->add(PermissionMiddleware::class)->setName('pronosticadorlist-pronosticador-list'); // list
    $app->map(["GET","POST","OPTIONS"], '/pronosticadoradd[/{ID_ENCUESTA}]', PronosticadorController::class . ':add')->add(PermissionMiddleware::class)->setName('pronosticadoradd-pronosticador-add'); // add
    $app->map(["GET","POST","OPTIONS"], '/pronosticadorview[/{ID_ENCUESTA}]', PronosticadorController::class . ':view')->add(PermissionMiddleware::class)->setName('pronosticadorview-pronosticador-view'); // view
    $app->map(["GET","POST","OPTIONS"], '/pronosticadoredit[/{ID_ENCUESTA}]', PronosticadorController::class . ':edit')->add(PermissionMiddleware::class)->setName('pronosticadoredit-pronosticador-edit'); // edit
    $app->map(["GET","POST","OPTIONS"], '/pronosticadordelete[/{ID_ENCUESTA}]', PronosticadorController::class . ':delete')->add(PermissionMiddleware::class)->setName('pronosticadordelete-pronosticador-delete'); // delete
    $app->group(
        '/pronosticador',
        function (RouteCollectorProxy $group) {
            $group->map(["GET","POST","OPTIONS"], '/' . Config("LIST_ACTION") . '[/{ID_ENCUESTA}]', PronosticadorController::class . ':list')->add(PermissionMiddleware::class)->setName('pronosticador/list-pronosticador-list-2'); // list
            $group->map(["GET","POST","OPTIONS"], '/' . Config("ADD_ACTION") . '[/{ID_ENCUESTA}]', PronosticadorController::class . ':add')->add(PermissionMiddleware::class)->setName('pronosticador/add-pronosticador-add-2'); // add
            $group->map(["GET","POST","OPTIONS"], '/' . Config("VIEW_ACTION") . '[/{ID_ENCUESTA}]', PronosticadorController::class . ':view')->add(PermissionMiddleware::class)->setName('pronosticador/view-pronosticador-view-2'); // view
            $group->map(["GET","POST","OPTIONS"], '/' . Config("EDIT_ACTION") . '[/{ID_ENCUESTA}]', PronosticadorController::class . ':edit')->add(PermissionMiddleware::class)->setName('pronosticador/edit-pronosticador-edit-2'); // edit
            $group->map(["GET","POST","OPTIONS"], '/' . Config("DELETE_ACTION") . '[/{ID_ENCUESTA}]', PronosticadorController::class . ':delete')->add(PermissionMiddleware::class)->setName('pronosticador/delete-pronosticador-delete-2'); // delete
        }
    );

    // equipo
    $app->map(["GET","POST","OPTIONS"], '/equipolist[/{ID_EQUIPO}]', EquipoController::class . ':list')->add(PermissionMiddleware::class)->setName('equipolist-equipo-list'); // list
    $app->map(["GET","POST","OPTIONS"], '/equipoadd[/{ID_EQUIPO}]', EquipoController::class . ':add')->add(PermissionMiddleware::class)->setName('equipoadd-equipo-add'); // add
    $app->map(["GET","POST","OPTIONS"], '/equipoaddopt', EquipoController::class . ':addopt')->add(PermissionMiddleware::class)->setName('equipoaddopt-equipo-addopt'); // addopt
    $app->map(["GET","POST","OPTIONS"], '/equipoview[/{ID_EQUIPO}]', EquipoController::class . ':view')->add(PermissionMiddleware::class)->setName('equipoview-equipo-view'); // view
    $app->map(["GET","POST","OPTIONS"], '/equipoedit[/{ID_EQUIPO}]', EquipoController::class . ':edit')->add(PermissionMiddleware::class)->setName('equipoedit-equipo-edit'); // edit
    $app->map(["GET","POST","OPTIONS"], '/equipodelete[/{ID_EQUIPO}]', EquipoController::class . ':delete')->add(PermissionMiddleware::class)->setName('equipodelete-equipo-delete'); // delete
    $app->group(
        '/equipo',
        function (RouteCollectorProxy $group) {
            $group->map(["GET","POST","OPTIONS"], '/' . Config("LIST_ACTION") . '[/{ID_EQUIPO}]', EquipoController::class . ':list')->add(PermissionMiddleware::class)->setName('equipo/list-equipo-list-2'); // list
            $group->map(["GET","POST","OPTIONS"], '/' . Config("ADD_ACTION") . '[/{ID_EQUIPO}]', EquipoController::class . ':add')->add(PermissionMiddleware::class)->setName('equipo/add-equipo-add-2'); // add
            $group->map(["GET","POST","OPTIONS"], '/' . Config("ADDOPT_ACTION") . '', EquipoController::class . ':addopt')->add(PermissionMiddleware::class)->setName('equipo/addopt-equipo-addopt-2'); // addopt
            $group->map(["GET","POST","OPTIONS"], '/' . Config("VIEW_ACTION") . '[/{ID_EQUIPO}]', EquipoController::class . ':view')->add(PermissionMiddleware::class)->setName('equipo/view-equipo-view-2'); // view
            $group->map(["GET","POST","OPTIONS"], '/' . Config("EDIT_ACTION") . '[/{ID_EQUIPO}]', EquipoController::class . ':edit')->add(PermissionMiddleware::class)->setName('equipo/edit-equipo-edit-2'); // edit
            $group->map(["GET","POST","OPTIONS"], '/' . Config("DELETE_ACTION") . '[/{ID_EQUIPO}]', EquipoController::class . ':delete')->add(PermissionMiddleware::class)->setName('equipo/delete-equipo-delete-2'); // delete
        }
    );

    // equipotorneo
    $app->map(["GET","POST","OPTIONS"], '/equipotorneolist[/{ID_EQUIPO_TORNEO}]', EquipotorneoController::class . ':list')->add(PermissionMiddleware::class)->setName('equipotorneolist-equipotorneo-list'); // list
    $app->map(["GET","POST","OPTIONS"], '/equipotorneoadd[/{ID_EQUIPO_TORNEO}]', EquipotorneoController::class . ':add')->add(PermissionMiddleware::class)->setName('equipotorneoadd-equipotorneo-add'); // add
    $app->map(["GET","POST","OPTIONS"], '/equipotorneoaddopt', EquipotorneoController::class . ':addopt')->add(PermissionMiddleware::class)->setName('equipotorneoaddopt-equipotorneo-addopt'); // addopt
    $app->map(["GET","POST","OPTIONS"], '/equipotorneoview[/{ID_EQUIPO_TORNEO}]', EquipotorneoController::class . ':view')->add(PermissionMiddleware::class)->setName('equipotorneoview-equipotorneo-view'); // view
    $app->map(["GET","POST","OPTIONS"], '/equipotorneoedit[/{ID_EQUIPO_TORNEO}]', EquipotorneoController::class . ':edit')->add(PermissionMiddleware::class)->setName('equipotorneoedit-equipotorneo-edit'); // edit
    $app->map(["GET","POST","OPTIONS"], '/equipotorneodelete[/{ID_EQUIPO_TORNEO}]', EquipotorneoController::class . ':delete')->add(PermissionMiddleware::class)->setName('equipotorneodelete-equipotorneo-delete'); // delete
    $app->group(
        '/equipotorneo',
        function (RouteCollectorProxy $group) {
            $group->map(["GET","POST","OPTIONS"], '/' . Config("LIST_ACTION") . '[/{ID_EQUIPO_TORNEO}]', EquipotorneoController::class . ':list')->add(PermissionMiddleware::class)->setName('equipotorneo/list-equipotorneo-list-2'); // list
            $group->map(["GET","POST","OPTIONS"], '/' . Config("ADD_ACTION") . '[/{ID_EQUIPO_TORNEO}]', EquipotorneoController::class . ':add')->add(PermissionMiddleware::class)->setName('equipotorneo/add-equipotorneo-add-2'); // add
            $group->map(["GET","POST","OPTIONS"], '/' . Config("ADDOPT_ACTION") . '', EquipotorneoController::class . ':addopt')->add(PermissionMiddleware::class)->setName('equipotorneo/addopt-equipotorneo-addopt-2'); // addopt
            $group->map(["GET","POST","OPTIONS"], '/' . Config("VIEW_ACTION") . '[/{ID_EQUIPO_TORNEO}]', EquipotorneoController::class . ':view')->add(PermissionMiddleware::class)->setName('equipotorneo/view-equipotorneo-view-2'); // view
            $group->map(["GET","POST","OPTIONS"], '/' . Config("EDIT_ACTION") . '[/{ID_EQUIPO_TORNEO}]', EquipotorneoController::class . ':edit')->add(PermissionMiddleware::class)->setName('equipotorneo/edit-equipotorneo-edit-2'); // edit
            $group->map(["GET","POST","OPTIONS"], '/' . Config("DELETE_ACTION") . '[/{ID_EQUIPO_TORNEO}]', EquipotorneoController::class . ':delete')->add(PermissionMiddleware::class)->setName('equipotorneo/delete-equipotorneo-delete-2'); // delete
        }
    );

    // participante
    $app->map(["GET","POST","OPTIONS"], '/participantelist[/{ID_PARTICIPANTE}]', ParticipanteController::class . ':list')->add(PermissionMiddleware::class)->setName('participantelist-participante-list'); // list
    $app->map(["GET","POST","OPTIONS"], '/participanteadd[/{ID_PARTICIPANTE}]', ParticipanteController::class . ':add')->add(PermissionMiddleware::class)->setName('participanteadd-participante-add'); // add
    $app->map(["GET","POST","OPTIONS"], '/participanteview[/{ID_PARTICIPANTE}]', ParticipanteController::class . ':view')->add(PermissionMiddleware::class)->setName('participanteview-participante-view'); // view
    $app->map(["GET","POST","OPTIONS"], '/participanteedit[/{ID_PARTICIPANTE}]', ParticipanteController::class . ':edit')->add(PermissionMiddleware::class)->setName('participanteedit-participante-edit'); // edit
    $app->map(["GET","POST","OPTIONS"], '/participantedelete[/{ID_PARTICIPANTE}]', ParticipanteController::class . ':delete')->add(PermissionMiddleware::class)->setName('participantedelete-participante-delete'); // delete
    $app->group(
        '/participante',
        function (RouteCollectorProxy $group) {
            $group->map(["GET","POST","OPTIONS"], '/' . Config("LIST_ACTION") . '[/{ID_PARTICIPANTE}]', ParticipanteController::class . ':list')->add(PermissionMiddleware::class)->setName('participante/list-participante-list-2'); // list
            $group->map(["GET","POST","OPTIONS"], '/' . Config("ADD_ACTION") . '[/{ID_PARTICIPANTE}]', ParticipanteController::class . ':add')->add(PermissionMiddleware::class)->setName('participante/add-participante-add-2'); // add
            $group->map(["GET","POST","OPTIONS"], '/' . Config("VIEW_ACTION") . '[/{ID_PARTICIPANTE}]', ParticipanteController::class . ':view')->add(PermissionMiddleware::class)->setName('participante/view-participante-view-2'); // view
            $group->map(["GET","POST","OPTIONS"], '/' . Config("EDIT_ACTION") . '[/{ID_PARTICIPANTE}]', ParticipanteController::class . ':edit')->add(PermissionMiddleware::class)->setName('participante/edit-participante-edit-2'); // edit
            $group->map(["GET","POST","OPTIONS"], '/' . Config("DELETE_ACTION") . '[/{ID_PARTICIPANTE}]', ParticipanteController::class . ':delete')->add(PermissionMiddleware::class)->setName('participante/delete-participante-delete-2'); // delete
        }
    );

    // partidos
    $app->map(["GET","POST","OPTIONS"], '/partidoslist[/{ID_PARTIDO}]', PartidosController::class . ':list')->add(PermissionMiddleware::class)->setName('partidoslist-partidos-list'); // list
    $app->map(["GET","POST","OPTIONS"], '/partidosadd[/{ID_PARTIDO}]', PartidosController::class . ':add')->add(PermissionMiddleware::class)->setName('partidosadd-partidos-add'); // add
    $app->map(["GET","POST","OPTIONS"], '/partidosview[/{ID_PARTIDO}]', PartidosController::class . ':view')->add(PermissionMiddleware::class)->setName('partidosview-partidos-view'); // view
    $app->map(["GET","POST","OPTIONS"], '/partidosedit[/{ID_PARTIDO}]', PartidosController::class . ':edit')->add(PermissionMiddleware::class)->setName('partidosedit-partidos-edit'); // edit
    $app->map(["GET","POST","OPTIONS"], '/partidosdelete[/{ID_PARTIDO}]', PartidosController::class . ':delete')->add(PermissionMiddleware::class)->setName('partidosdelete-partidos-delete'); // delete
    $app->group(
        '/partidos',
        function (RouteCollectorProxy $group) {
            $group->map(["GET","POST","OPTIONS"], '/' . Config("LIST_ACTION") . '[/{ID_PARTIDO}]', PartidosController::class . ':list')->add(PermissionMiddleware::class)->setName('partidos/list-partidos-list-2'); // list
            $group->map(["GET","POST","OPTIONS"], '/' . Config("ADD_ACTION") . '[/{ID_PARTIDO}]', PartidosController::class . ':add')->add(PermissionMiddleware::class)->setName('partidos/add-partidos-add-2'); // add
            $group->map(["GET","POST","OPTIONS"], '/' . Config("VIEW_ACTION") . '[/{ID_PARTIDO}]', PartidosController::class . ':view')->add(PermissionMiddleware::class)->setName('partidos/view-partidos-view-2'); // view
            $group->map(["GET","POST","OPTIONS"], '/' . Config("EDIT_ACTION") . '[/{ID_PARTIDO}]', PartidosController::class . ':edit')->add(PermissionMiddleware::class)->setName('partidos/edit-partidos-edit-2'); // edit
            $group->map(["GET","POST","OPTIONS"], '/' . Config("DELETE_ACTION") . '[/{ID_PARTIDO}]', PartidosController::class . ':delete')->add(PermissionMiddleware::class)->setName('partidos/delete-partidos-delete-2'); // delete
        }
    );

    // torneo
    $app->map(["GET","POST","OPTIONS"], '/torneolist[/{ID_TORNEO}]', TorneoController::class . ':list')->add(PermissionMiddleware::class)->setName('torneolist-torneo-list'); // list
    $app->map(["GET","POST","OPTIONS"], '/torneoadd[/{ID_TORNEO}]', TorneoController::class . ':add')->add(PermissionMiddleware::class)->setName('torneoadd-torneo-add'); // add
    $app->map(["GET","POST","OPTIONS"], '/torneoview[/{ID_TORNEO}]', TorneoController::class . ':view')->add(PermissionMiddleware::class)->setName('torneoview-torneo-view'); // view
    $app->map(["GET","POST","OPTIONS"], '/torneoedit[/{ID_TORNEO}]', TorneoController::class . ':edit')->add(PermissionMiddleware::class)->setName('torneoedit-torneo-edit'); // edit
    $app->map(["GET","POST","OPTIONS"], '/torneodelete[/{ID_TORNEO}]', TorneoController::class . ':delete')->add(PermissionMiddleware::class)->setName('torneodelete-torneo-delete'); // delete
    $app->group(
        '/torneo',
        function (RouteCollectorProxy $group) {
            $group->map(["GET","POST","OPTIONS"], '/' . Config("LIST_ACTION") . '[/{ID_TORNEO}]', TorneoController::class . ':list')->add(PermissionMiddleware::class)->setName('torneo/list-torneo-list-2'); // list
            $group->map(["GET","POST","OPTIONS"], '/' . Config("ADD_ACTION") . '[/{ID_TORNEO}]', TorneoController::class . ':add')->add(PermissionMiddleware::class)->setName('torneo/add-torneo-add-2'); // add
            $group->map(["GET","POST","OPTIONS"], '/' . Config("VIEW_ACTION") . '[/{ID_TORNEO}]', TorneoController::class . ':view')->add(PermissionMiddleware::class)->setName('torneo/view-torneo-view-2'); // view
            $group->map(["GET","POST","OPTIONS"], '/' . Config("EDIT_ACTION") . '[/{ID_TORNEO}]', TorneoController::class . ':edit')->add(PermissionMiddleware::class)->setName('torneo/edit-torneo-edit-2'); // edit
            $group->map(["GET","POST","OPTIONS"], '/' . Config("DELETE_ACTION") . '[/{ID_TORNEO}]', TorneoController::class . ':delete')->add(PermissionMiddleware::class)->setName('torneo/delete-torneo-delete-2'); // delete
        }
    );

    // estadio
    $app->map(["GET","POST","OPTIONS"], '/estadiolist[/{id_estadio}]', EstadioController::class . ':list')->add(PermissionMiddleware::class)->setName('estadiolist-estadio-list'); // list
    $app->map(["GET","POST","OPTIONS"], '/estadioadd[/{id_estadio}]', EstadioController::class . ':add')->add(PermissionMiddleware::class)->setName('estadioadd-estadio-add'); // add
    $app->map(["GET","POST","OPTIONS"], '/estadioaddopt', EstadioController::class . ':addopt')->add(PermissionMiddleware::class)->setName('estadioaddopt-estadio-addopt'); // addopt
    $app->map(["GET","POST","OPTIONS"], '/estadioview[/{id_estadio}]', EstadioController::class . ':view')->add(PermissionMiddleware::class)->setName('estadioview-estadio-view'); // view
    $app->map(["GET","POST","OPTIONS"], '/estadioedit[/{id_estadio}]', EstadioController::class . ':edit')->add(PermissionMiddleware::class)->setName('estadioedit-estadio-edit'); // edit
    $app->map(["GET","POST","OPTIONS"], '/estadiodelete[/{id_estadio}]', EstadioController::class . ':delete')->add(PermissionMiddleware::class)->setName('estadiodelete-estadio-delete'); // delete
    $app->group(
        '/estadio',
        function (RouteCollectorProxy $group) {
            $group->map(["GET","POST","OPTIONS"], '/' . Config("LIST_ACTION") . '[/{id_estadio}]', EstadioController::class . ':list')->add(PermissionMiddleware::class)->setName('estadio/list-estadio-list-2'); // list
            $group->map(["GET","POST","OPTIONS"], '/' . Config("ADD_ACTION") . '[/{id_estadio}]', EstadioController::class . ':add')->add(PermissionMiddleware::class)->setName('estadio/add-estadio-add-2'); // add
            $group->map(["GET","POST","OPTIONS"], '/' . Config("ADDOPT_ACTION") . '', EstadioController::class . ':addopt')->add(PermissionMiddleware::class)->setName('estadio/addopt-estadio-addopt-2'); // addopt
            $group->map(["GET","POST","OPTIONS"], '/' . Config("VIEW_ACTION") . '[/{id_estadio}]', EstadioController::class . ':view')->add(PermissionMiddleware::class)->setName('estadio/view-estadio-view-2'); // view
            $group->map(["GET","POST","OPTIONS"], '/' . Config("EDIT_ACTION") . '[/{id_estadio}]', EstadioController::class . ':edit')->add(PermissionMiddleware::class)->setName('estadio/edit-estadio-edit-2'); // edit
            $group->map(["GET","POST","OPTIONS"], '/' . Config("DELETE_ACTION") . '[/{id_estadio}]', EstadioController::class . ':delete')->add(PermissionMiddleware::class)->setName('estadio/delete-estadio-delete-2'); // delete
        }
    );

    // error
    $app->map(["GET","POST","OPTIONS"], '/error', OthersController::class . ':error')->add(PermissionMiddleware::class)->setName('error');

    // personal_data
    $app->map(["GET","POST","OPTIONS"], '/personaldata', OthersController::class . ':personaldata')->add(PermissionMiddleware::class)->setName('personaldata');

    // login
    $app->map(["GET","POST","OPTIONS"], '/login', OthersController::class . ':login')->add(PermissionMiddleware::class)->setName('login');

    // change_password
    $app->map(["GET","POST","OPTIONS"], '/changepassword', OthersController::class . ':changepassword')->add(PermissionMiddleware::class)->setName('changepassword');

    // logout
    $app->map(["GET","POST","OPTIONS"], '/logout', OthersController::class . ':logout')->add(PermissionMiddleware::class)->setName('logout');

    // Swagger
    $app->get('/' . Config("SWAGGER_ACTION"), OthersController::class . ':swagger')->setName(Config("SWAGGER_ACTION")); // Swagger

    // Index
    $app->get('/[index]', OthersController::class . ':index')->add(PermissionMiddleware::class)->setName('index');

    // Route Action event
    if (function_exists(PROJECT_NAMESPACE . "Route_Action")) {
        if (Route_Action($app) === false) {
            return;
        }
    }

    /**
     * Catch-all route to serve a 404 Not Found page if none of the routes match
     * NOTE: Make sure this route is defined last.
     */
    $app->map(
        ['GET', 'POST', 'PUT', 'DELETE', 'PATCH'],
        '/{routes:.+}',
        function ($request, $response, $params) {
            $error = [
                "statusCode" => "404",
                "error" => [
                    "class" => "text-warning",
                    "type" => Container("language")->phrase("Error"),
                    "description" => str_replace("%p", $params["routes"], Container("language")->phrase("PageNotFound")),
                ],
            ];
            Container("flash")->addMessage("error", $error);
            return $response->withStatus(302)->withHeader("Location", GetUrl("error")); // Redirect to error page
        }
    );
};
