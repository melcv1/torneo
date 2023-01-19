<?php

namespace PHPMaker2022\project11;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Container\ContainerInterface;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\FetchMode;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

// Filter for 'Last Month' (example)
function GetLastMonthFilter($FldExpression, $dbid = 0)
{
    $today = getdate();
    $lastmonth = mktime(0, 0, 0, $today['mon'] - 1, 1, $today['year']);
    $val = date("Y|m", $lastmonth);
    $wrk = $FldExpression . " BETWEEN " .
        QuotedValue(DateValue("month", $val, 1, $dbid), DATATYPE_DATE, $dbid) .
        " AND " .
        QuotedValue(DateValue("month", $val, 2, $dbid), DATATYPE_DATE, $dbid);
    return $wrk;
}

// Filter for 'Starts With A' (example)
function GetStartsWithAFilter($FldExpression, $dbid = 0)
{
    return $FldExpression . Like("'A%'", $dbid);
}

// Global user functions

// Database Connecting event
function Database_Connecting(&$info)
{
    // Example:
    //var_dump($info);
    //if ($info["id"] == "DB" && IsLocal()) { // Testing on local PC
    //    $info["host"] = "locahost";
    //    $info["user"] = "root";
    //    $info["pass"] = "";
    //}
}

// Database Connected event
function Database_Connected(&$conn)
{
    // Example:
    //if ($conn->info["id"] == "DB") {
    //    $conn->executeQuery("Your SQL");
    //}
}

function MenuItem_Adding($item)
{
    //var_dump($item);
    // Return false if menu item not allowed
    return true;
}

function Menu_Rendering($menu)
{
    // Change menu items here
}

function Menu_Rendered($menu)
{
    // Clean up here
}

// Page Loading event
function Page_Loading()
{
    //Log("Page Loading");
}

// Page Rendering event
function Page_Rendering()
{
    //Log("Page Rendering");
}

// Page Unloaded event
function Page_Unloaded()
{
    //Log("Page Unloaded");
}

// AuditTrail Inserting event
function AuditTrail_Inserting(&$rsnew)
{
    //var_dump($rsnew);
    return true;
}

// Personal Data Downloading event
function PersonalData_Downloading(&$row)
{
    //Log("PersonalData Downloading");
}

// Personal Data Deleted event
function PersonalData_Deleted($row)
{
    //Log("PersonalData Deleted");
}

// Route Action event
function Route_Action($app)
{
    // Example:
    // $app->get('/myaction', function ($request, $response, $args) {
    //    return $response->withJson(["name" => "myaction"]); // Note: Always return Psr\Http\Message\ResponseInterface object
    // });
    // $app->get('/myaction2', function ($request, $response, $args) {
    //    return $response->withJson(["name" => "myaction2"]); // Note: Always return Psr\Http\Message\ResponseInterface object
    // });
}

function Api_Action($app) {
   $GLOBALS["Conn"] = $GLOBALS["Conn"] ?? getConnection();
 $app->get('/v1/equipos', function ($request, $response, $args) {
        $myArray="todo ok";
         $response = $response->withJson(ExecuteRows("SELECT a.NOM_EQUIPO_CORTO, a.NOM_EQUIPO_LARGO, a.ESCUDO_EQUIPO, b.GRUPO FROM equipotorneo as b INNER JOIN equipo as a ON a.id_equipo=b.ID_EQUIPO"));
        return $response;
    });
    $app->get('/v1/equipos/torneo/{ID_TORNEO}', function ($request, $response, $args) {
    $ID_TORNEO = $args["ID_TORNEO"] ?? null; // Get the input value
         if ($ID_TORNEO !== null) {
            $response = $response->withJson(ExecuteRows("SELECT a.NOM_EQUIPO_CORTO, a.NOM_EQUIPO_LARGO, a.ESCUDO_EQUIPO, b.GRUPO FROM equipotorneo as b INNER JOIN equipo as a ON a.id_equipo=b.ID_EQUIPO WHERE b.ID_TORNEO = '" . AdjustSql($ID_TORNEO) . "'"));
        }    
        return $response;
    });
      $app->post('/v1/agregar', function ($request, $response, $args) {
      	$p2 = json_encode($request->getParsedBody());
        $p= json_decode($p2);
        $conn = Conn();
          $conn = getConnection();
           $consulta = $conn->prepare("insert into encuesta(ID_PARTICIPANTE,GRUPO, EQUIPO, POSICION) values (:ID_PARTICIPANTE,:GRUPO,:EQUIPO,:POSICION)");
           $estado = $consulta->execute(array(':ID_PARTICIPANTE' => $p->ID_PARTICIPANTE, ':GRUPO' => $p->GRUPO, ':EQUIPO' => $p->EQUIPO, ':POSICION' => $p->POSICION));
           if ($estado) {
            $response = $response->withJson( json_encode(array('mensaje' => 'Datos insertados correctamente. ')));
          } else {
            $response = $response->withJson( json_encode(array('mensaje' => 'Fallo ')));
             }    
           return $response;
    });
    $app->get('/v1/equipo/{NOM_EQUIPO_CORTO}', function ($request, $response, $args) {
        $NOM_EQUIPO_CORTO = $args["NOM_EQUIPO_CORTO"] ?? null; // Get the input value
        if ($NOM_EQUIPO_CORTO !== null) {
            $response = $response->withJson(ExecuteRows("SELECT a.NOM_EQUIPO_CORTO, a.NOM_EQUIPO_LARGO, a.ESCUDO_EQUIPO, b.GRUPO FROM equipotorneo as b INNER JOIN equipo as a ON a.id_equipo=b.ID_EQUIPO WHERE a.NOM_EQUIPO_CORTO = '" . AdjustSql($NOM_EQUIPO_CORTO) . "'"));
        }    
        return $response;
    });
    $app->get('/v1/tabla', function ($request, $response, $args) {
        $myArray="todo ok";
         $response = $response->withJson(ExecuteRows("SELECT a.NOM_EQUIPO_CORTO, a.NOM_EQUIPO_LARGO, a.ESCUDO_EQUIPO, b.PARTIDOS_JUGADOS, b.PARTIDOS_GANADOS, b.PARTIDOS_EMPATADOS, b.PARTIDOS_PERDIDOS, b.GF, b.GC, b.GD FROM equipotorneo as b INNER JOIN equipo as a ON a.id_equipo=b.ID_EQUIPO;"));
        return $response;
    });
      $app->get('/v1/tabla/{ID_TORNEO}', function ($request, $response, $args) {
        $ID_TORNEO = $args["ID_TORNEO"] ?? null; // Get the input value
        if ($ID_TORNEO !== null) {
            $response = $response->withJson(ExecuteRows("SELECT a.NOM_EQUIPO_CORTO, a.NOM_EQUIPO_LARGO, a.ESCUDO_EQUIPO, b.PARTIDOS_JUGADOS, b.PARTIDOS_GANADOS, b.PARTIDOS_EMPATADOS, b.PARTIDOS_PERDIDOS, b.GF, b.GC, b.GD FROM equipotorneo as b INNER JOIN equipo as a ON a.id_equipo=b.ID_EQUIPO WHERE b.ID_TORNEO = '" . AdjustSql($ID_TORNEO) . "'"));
        }    
        return $response;
    });
    $app->get('/v1/tabla/{ID_TORNEO}/{GRUPO}', function ($request, $response, $args) {
        $ID_TORNEO = $args["ID_TORNEO"] ?? null; // Get the input value
        $GRUPO = $args["GRUPO"] ?? null; 
        if ($ID_TORNEO !== null) {
            $response = $response->withJson(ExecuteRows("SELECT a.NOM_EQUIPO_CORTO, a.NOM_EQUIPO_LARGO, a.ESCUDO_EQUIPO, b.PARTIDOS_JUGADOS, b.PARTIDOS_GANADOS, b.PARTIDOS_EMPATADOS, b.PARTIDOS_PERDIDOS, b.GF, b.GC, b.GD FROM equipotorneo as b INNER JOIN equipo as a ON a.id_equipo=b.ID_EQUIPO WHERE b.ID_TORNEO = '" . AdjustSql($ID_TORNEO) . "' AND  b.GRUPO = '" . AdjustSql($GRUPO) . "'"));
        }    
        return $response;
    });
    $app->get('/v1/partidos/{ID_TORNEO}', function ($request, $response, $args) {
        $ID_TORNEO = $args["ID_TORNEO"] ?? null; // Get the input value
        if ($ID_TORNEO !== null) {
            $response = $response->withJson(ExecuteRows("SELECT * FROM partidos WHERE ID_TORNEO = '" . AdjustSql($ID_TORNEO) . "'"));
        }    
        return $response;
    });
    $app->get('/v1/partidos/{ID_TORNEO}/{GRUPO}', function ($request, $response, $args) {
        $ID_TORNEO = $args["ID_TORNEO"] ?? null; // Get the input value
        $GRUPO = $args["GRUPO"] ?? null; 
        if ($ID_TORNEO !== null) {
            $response = $response->withJson(ExecuteRows("SELECT * FROM partidos as a INNER JOIN equipotorneo as b ON a.equipo_local = b.ID_EQUIPO WHERE a.ID_TORNEO='" . AdjustSql($ID_TORNEO) . "' AND b.ID_TORNEO='" . AdjustSql($ID_TORNEO) . "' AND b.GRUPO='" . AdjustSql($GRUPO) . "'  "));
        }    
        return $response;
    });
    $app->get('/v1/equipos/{ID_EQUIPO}', function ($request, $response, $args) {
        $ID_EQUIPO = $args["ID_EQUIPO"] ?? null; // Get the input value
        if ($ID_EQUIPO !== null) {
            $response = $response->withJson(ExecuteRows("SELECT a.NOM_EQUIPO_CORTO, a.NOM_EQUIPO_LARGO, a.ESCUDO_EQUIPO, b.GRUPO FROM equipotorneo as b INNER JOIN equipo as a ON a.id_equipo=b.ID_EQUIPO WHERE a.ID_EQUIPO = '" . AdjustSql($ID_EQUIPO) . "'"));
        }    
        return $response;
    });
      $app->get('/v1/jugadores', function ($request, $response, $args) {
        $myArray="todo ok";
       /*  $response = $response->withJson(ExecuteRows("SELECT a.NOM_EQUIPO_LARGO, b.nombre_jugador, b.votos_jugador, b.imagen_jugador, b.posicion 
         FROM jugador as b 
         JOIN jugadorequipo as c
         ON b.id_jugador=c.id_jugador 
         JOIN equipo as a 
         ON a.id_equipo=c.id_equipo;"));*/
          $response = $response->withJson(ExecuteRows("SELECT * from jugador;"));
        return $response;
    });

    $app->get('/v1/goleadores', function ($request, $response, $args) {
        $myArray="todo ok";
      
        $response = $response->withJson(ExecuteRows("SELECT a.NOMBRE_JUGADOR, c.NOMBRE_EQUIPO_LARGO, e.GOLES         FROM jugador as a         JOIN jugadorequipo as e ON a.id_jugador = e.id_jugador         JOIN equipotorneo as b ON e.id_equipo = b.id_equipo_torneo         JOIN equipo as c ON b.id_equipo = c.id_equipo;"));
        return $response;
    });

     $app->get('/v1/pronostica', function ($request, $response, $args) {
        $myArray="todo ok";
          $response = $response->withJson(ExecuteRows("SELECT a.NOM_EQUIPO_CORTO, a.NOM_EQUIPO_LARGO, a.ESCUDO_EQUIPO, b.GRUPO, c.posicion, c.numeracion FROM equipotorneo as b INNER JOIN equipo as a ON a.id_equipo=b.ID_EQUIPO INNER JOIN pronosticador as c ON b.ID_EQUIPO_TORNEO = c.ID_EQUIPOTORNEO;"));
        return $response;
    });
    $app->get('/v1/votos/{ID_JUGADOR}', function ($request, $response, $args) {
        $ID_EQUIPO = $args["ID_JUGADOR"] ?? null; // Get the input value
        if ($ID_EQUIPO !== null) {
            $response = $response->withJson(ExecuteRows("SELECT votos_jugador from jugador WHERE ID_JUGADOR= '" . AdjustSql($ID_EQUIPO) . "'"));
        }    
        return $response;
    });
    $app->post('/v1/agregar/voto', function ($request, $response, $args) {
        $p2 = json_encode($request->getParsedBody());
      $p= json_decode($p2);
      $conn = Conn();
        $conn = getConnection();
         $consulta = $conn->prepare("insert into jugador(votos_jugador) values (:votos_jugador) WHERE id_jugador= :id_jugador");
         $estado = $consulta->execute(array(':votos_jugador' => $p->votos_jugador, ':id_jugador' => $p->id_jugador));
         if ($estado) {
          $response = $response->withJson( json_encode(array('mensaje' => 'Datos insertados correctamente. ')));
        } else {
          $response = $response->withJson( json_encode(array('mensaje' => 'Fallo ')));
           }    
         return $response;
  });
  $app->get('/v1/sumar/voto/{ID_JUGADOR}', function ($request, $response, $args) {
    $dato="hola";
        $ID_EQUIPO = $args["ID_JUGADOR"] ?? null; // Get the input value
        if ($ID_EQUIPO !== null) {
            $sql_local = "
            UPDATE jugador
            SET
           votos_jugador =votos_jugador + 1 
           WHERE ID_JUGADOR ='". $ID_EQUIPO. "';
           ";
            Execute($sql_local);
       } 
        $response = $response->withJson( json_encode(array('mensaje' => 'Datos insertados correctamente. ')));
        return $response;
    });
$app->get('/v1/restar/voto/{ID_JUGADOR}', function ($request, $response, $args) {
        $dato="hola";
            $ID_EQUIPO = $args["ID_JUGADOR"] ?? null; // Get the input value
            if ($ID_EQUIPO !== null) {
                $sql_local = "
                UPDATE jugador
                SET
               votos_jugador =votos_jugador - 1 
               WHERE ID_JUGADOR ='". $ID_EQUIPO. "';
               ";
                Execute($sql_local);
           } 
            $response = $response->withJson( json_encode(array('mensaje' => 'Datos eliminados correctamente. ')));
            return $response;
        });
}

// Container Build event
function Container_Build($builder)
{
    // Example:
    // $builder->addDefinitions([
    //    "myservice" => function (ContainerInterface $c) {
    //        // your code to provide the service, e.g.
    //        return new MyService();
    //    },
    //    "myservice2" => function (ContainerInterface $c) {
    //        // your code to provide the service, e.g.
    //        return new MyService2();
    //    }
    // ]);
}
