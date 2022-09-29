<?php

namespace PHPMaker2022\project11;

use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\FetchMode;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * Table class for partidos
 */
class Partidos extends DbTable
{
    protected $SqlFrom = "";
    protected $SqlSelect = null;
    protected $SqlSelectList = null;
    protected $SqlWhere = "";
    protected $SqlGroupBy = "";
    protected $SqlHaving = "";
    protected $SqlOrderBy = "";
    public $UseSessionForListSql = true;

    // Column CSS classes
    public $LeftColumnClass = "col-sm-2 col-form-label ew-label";
    public $RightColumnClass = "col-sm-10";
    public $OffsetColumnClass = "col-sm-10 offset-sm-2";
    public $TableLeftColumnClass = "w-col-2";

    // Export
    public $ExportDoc;

    // Fields
    public $ID_TORNEO;
    public $equipo_local;
    public $equipo_visitante;
    public $ID_PARTIDO;
    public $FECHA_PARTIDO;
    public $HORA_PARTIDO;
    public $ESTADIO;
    public $CIUDAD_PARTIDO;
    public $PAIS_PARTIDO;
    public $GOLES_LOCAL;
    public $GOLES_VISITANTE;
    public $GOLES_EXTRA_EQUIPO1;
    public $GOLES_EXTRA_EQUIPO2;
    public $NOTA_PARTIDO;
    public $RESUMEN_PARTIDO;
    public $ESTADO_PARTIDO;
    public $crea_dato;
    public $modifica_dato;
    public $usuario_dato;
    public $automatico;
    public $actualizado;

    // Page ID
    public $PageID = ""; // To be overridden by subclass

    // Constructor
    public function __construct()
    {
        global $Language, $CurrentLanguage, $CurrentLocale;
        parent::__construct();

        // Language object
        $Language = Container("language");
        $this->TableVar = 'partidos';
        $this->TableName = 'partidos';
        $this->TableType = 'TABLE';

        // Update Table
        $this->UpdateTable = "`partidos`";
        $this->Dbid = 'DB';
        $this->ExportAll = true;
        $this->ExportPageBreakCount = 0; // Page break per every n record (PDF only)
        $this->ExportPageOrientation = "portrait"; // Page orientation (PDF only)
        $this->ExportPageSize = "a4"; // Page size (PDF only)
        $this->ExportExcelPageOrientation = ""; // Page orientation (PhpSpreadsheet only)
        $this->ExportExcelPageSize = ""; // Page size (PhpSpreadsheet only)
        $this->ExportWordVersion = 12; // Word version (PHPWord only)
        $this->ExportWordPageOrientation = "portrait"; // Page orientation (PHPWord only)
        $this->ExportWordPageSize = "A4"; // Page orientation (PHPWord only)
        $this->ExportWordColumnWidth = null; // Cell width (PHPWord only)
        $this->DetailAdd = false; // Allow detail add
        $this->DetailEdit = false; // Allow detail edit
        $this->DetailView = false; // Allow detail view
        $this->ShowMultipleDetails = false; // Show multiple details
        $this->GridAddRowCount = 5;
        $this->AllowAddDeleteRow = true; // Allow add/delete row
        $this->UserIDAllowSecurity = Config("DEFAULT_USER_ID_ALLOW_SECURITY"); // Default User ID allowed permissions
        $this->BasicSearch = new BasicSearch($this->TableVar);

        // ID_TORNEO
        $this->ID_TORNEO = new DbField(
            'partidos',
            'partidos',
            'x_ID_TORNEO',
            'ID_TORNEO',
            '`ID_TORNEO`',
            '`ID_TORNEO`',
            3,
            11,
            -1,
            false,
            '`ID_TORNEO`',
            false,
            false,
            false,
            'FORMATTED TEXT',
            'SELECT'
        );
        $this->ID_TORNEO->InputTextType = "text";
        $this->ID_TORNEO->UsePleaseSelect = true; // Use PleaseSelect by default
        $this->ID_TORNEO->PleaseSelectText = $Language->phrase("PleaseSelect"); // "PleaseSelect" text
        $this->ID_TORNEO->UseFilter = true; // Table header filter
        switch ($CurrentLanguage) {
            case "en-US":
                $this->ID_TORNEO->Lookup = new Lookup('ID_TORNEO', 'torneo', true, 'ID_TORNEO', ["NOM_TORNEO_CORTO","","",""], [], ["x_equipo_local","x_equipo_visitante","x_ESTADIO"], [], [], [], [], '', '', "`NOM_TORNEO_CORTO`");
                break;
            default:
                $this->ID_TORNEO->Lookup = new Lookup('ID_TORNEO', 'torneo', true, 'ID_TORNEO', ["NOM_TORNEO_CORTO","","",""], [], ["x_equipo_local","x_equipo_visitante","x_ESTADIO"], [], [], [], [], '', '', "`NOM_TORNEO_CORTO`");
                break;
        }
        $this->ID_TORNEO->DefaultErrorMessage = $Language->phrase("IncorrectInteger");
        $this->Fields['ID_TORNEO'] = &$this->ID_TORNEO;

        // equipo_local
        $this->equipo_local = new DbField(
            'partidos',
            'partidos',
            'x_equipo_local',
            'equipo_local',
            '`equipo_local`',
            '`equipo_local`',
            3,
            11,
            -1,
            false,
            '`EV__equipo_local`',
            true,
            true,
            true,
            'FORMATTED TEXT',
            'SELECT'
        );
        $this->equipo_local->InputTextType = "text";
        $this->equipo_local->UsePleaseSelect = true; // Use PleaseSelect by default
        $this->equipo_local->PleaseSelectText = $Language->phrase("PleaseSelect"); // "PleaseSelect" text
        switch ($CurrentLanguage) {
            case "en-US":
                $this->equipo_local->Lookup = new Lookup('equipo_local', 'equipotorneo', false, 'ID_EQUIPO', ["ID_EQUIPO","","",""], ["x_ID_TORNEO"], ["x_PAIS_PARTIDO"], ["ID_TORNEO"], ["x_ID_TORNEO"], [], [], '', '', "`ID_EQUIPO`");
                break;
            default:
                $this->equipo_local->Lookup = new Lookup('equipo_local', 'equipotorneo', false, 'ID_EQUIPO', ["ID_EQUIPO","","",""], ["x_ID_TORNEO"], ["x_PAIS_PARTIDO"], ["ID_TORNEO"], ["x_ID_TORNEO"], [], [], '', '', "`ID_EQUIPO`");
                break;
        }
        $this->equipo_local->DefaultErrorMessage = $Language->phrase("IncorrectInteger");
        $this->Fields['equipo_local'] = &$this->equipo_local;

        // equipo_visitante
        $this->equipo_visitante = new DbField(
            'partidos',
            'partidos',
            'x_equipo_visitante',
            'equipo_visitante',
            '`equipo_visitante`',
            '`equipo_visitante`',
            3,
            11,
            -1,
            false,
            '`EV__equipo_visitante`',
            true,
            true,
            true,
            'FORMATTED TEXT',
            'SELECT'
        );
        $this->equipo_visitante->InputTextType = "text";
        $this->equipo_visitante->UsePleaseSelect = true; // Use PleaseSelect by default
        $this->equipo_visitante->PleaseSelectText = $Language->phrase("PleaseSelect"); // "PleaseSelect" text
        switch ($CurrentLanguage) {
            case "en-US":
                $this->equipo_visitante->Lookup = new Lookup('equipo_visitante', 'equipotorneo', false, 'ID_EQUIPO', ["ID_EQUIPO","","",""], ["x_ID_TORNEO"], [], ["ID_TORNEO"], ["x_ID_TORNEO"], [], [], '', '', "`ID_EQUIPO`");
                break;
            default:
                $this->equipo_visitante->Lookup = new Lookup('equipo_visitante', 'equipotorneo', false, 'ID_EQUIPO', ["ID_EQUIPO","","",""], ["x_ID_TORNEO"], [], ["ID_TORNEO"], ["x_ID_TORNEO"], [], [], '', '', "`ID_EQUIPO`");
                break;
        }
        $this->equipo_visitante->DefaultErrorMessage = $Language->phrase("IncorrectInteger");
        $this->Fields['equipo_visitante'] = &$this->equipo_visitante;

        // ID_PARTIDO
        $this->ID_PARTIDO = new DbField(
            'partidos',
            'partidos',
            'x_ID_PARTIDO',
            'ID_PARTIDO',
            '`ID_PARTIDO`',
            '`ID_PARTIDO`',
            3,
            11,
            -1,
            false,
            '`ID_PARTIDO`',
            false,
            false,
            false,
            'FORMATTED TEXT',
            'NO'
        );
        $this->ID_PARTIDO->InputTextType = "text";
        $this->ID_PARTIDO->IsAutoIncrement = true; // Autoincrement field
        $this->ID_PARTIDO->IsPrimaryKey = true; // Primary key field
        $this->ID_PARTIDO->DefaultErrorMessage = $Language->phrase("IncorrectInteger");
        $this->Fields['ID_PARTIDO'] = &$this->ID_PARTIDO;

        // FECHA_PARTIDO
        $this->FECHA_PARTIDO = new DbField(
            'partidos',
            'partidos',
            'x_FECHA_PARTIDO',
            'FECHA_PARTIDO',
            '`FECHA_PARTIDO`',
            CastDateFieldForLike("`FECHA_PARTIDO`", 14, "DB"),
            133,
            10,
            14,
            false,
            '`FECHA_PARTIDO`',
            false,
            false,
            false,
            'FORMATTED TEXT',
            'TEXT'
        );
        $this->FECHA_PARTIDO->InputTextType = "text";
        $this->FECHA_PARTIDO->DefaultErrorMessage = str_replace("%s", DateFormat(14), $Language->phrase("IncorrectDate"));
        $this->Fields['FECHA_PARTIDO'] = &$this->FECHA_PARTIDO;

        // HORA_PARTIDO
        $this->HORA_PARTIDO = new DbField(
            'partidos',
            'partidos',
            'x_HORA_PARTIDO',
            'HORA_PARTIDO',
            '`HORA_PARTIDO`',
            CastDateFieldForLike("`HORA_PARTIDO`", 3, "DB"),
            134,
            10,
            3,
            false,
            '`HORA_PARTIDO`',
            false,
            false,
            false,
            'FORMATTED TEXT',
            'TEXT'
        );
        $this->HORA_PARTIDO->InputTextType = "text";
        $this->HORA_PARTIDO->DefaultErrorMessage = str_replace("%s", DateFormat(3), $Language->phrase("IncorrectTime"));
        $this->Fields['HORA_PARTIDO'] = &$this->HORA_PARTIDO;

        // ESTADIO
        $this->ESTADIO = new DbField(
            'partidos',
            'partidos',
            'x_ESTADIO',
            'ESTADIO',
            '`ESTADIO`',
            '`ESTADIO`',
            3,
            11,
            -1,
            false,
            '`ESTADIO`',
            false,
            false,
            false,
            'FORMATTED TEXT',
            'SELECT'
        );
        $this->ESTADIO->InputTextType = "text";
        $this->ESTADIO->UsePleaseSelect = true; // Use PleaseSelect by default
        $this->ESTADIO->PleaseSelectText = $Language->phrase("PleaseSelect"); // "PleaseSelect" text
        $this->ESTADIO->UseFilter = true; // Table header filter
        switch ($CurrentLanguage) {
            case "en-US":
                $this->ESTADIO->Lookup = new Lookup('ESTADIO', 'estadio', true, 'id_estadio', ["nombre_estadio","","",""], ["x_ID_TORNEO"], [], ["id_torneo"], ["x_id_torneo"], [], [], '', '', "`nombre_estadio`");
                break;
            default:
                $this->ESTADIO->Lookup = new Lookup('ESTADIO', 'estadio', true, 'id_estadio', ["nombre_estadio","","",""], ["x_ID_TORNEO"], [], ["id_torneo"], ["x_id_torneo"], [], [], '', '', "`nombre_estadio`");
                break;
        }
        $this->ESTADIO->DefaultErrorMessage = $Language->phrase("IncorrectInteger");
        $this->Fields['ESTADIO'] = &$this->ESTADIO;

        // CIUDAD_PARTIDO
        $this->CIUDAD_PARTIDO = new DbField(
            'partidos',
            'partidos',
            'x_CIUDAD_PARTIDO',
            'CIUDAD_PARTIDO',
            '`CIUDAD_PARTIDO`',
            '`CIUDAD_PARTIDO`',
            201,
            256,
            -1,
            false,
            '`CIUDAD_PARTIDO`',
            false,
            false,
            false,
            'FORMATTED TEXT',
            'TEXTAREA'
        );
        $this->CIUDAD_PARTIDO->InputTextType = "text";
        $this->Fields['CIUDAD_PARTIDO'] = &$this->CIUDAD_PARTIDO;

        // PAIS_PARTIDO
        $this->PAIS_PARTIDO = new DbField(
            'partidos',
            'partidos',
            'x_PAIS_PARTIDO',
            'PAIS_PARTIDO',
            '`PAIS_PARTIDO`',
            '`PAIS_PARTIDO`',
            201,
            256,
            -1,
            false,
            '`PAIS_PARTIDO`',
            false,
            false,
            false,
            'FORMATTED TEXT',
            'SELECT'
        );
        $this->PAIS_PARTIDO->InputTextType = "text";
        $this->PAIS_PARTIDO->UsePleaseSelect = true; // Use PleaseSelect by default
        $this->PAIS_PARTIDO->PleaseSelectText = $Language->phrase("PleaseSelect"); // "PleaseSelect" text
        switch ($CurrentLanguage) {
            case "en-US":
                $this->PAIS_PARTIDO->Lookup = new Lookup('PAIS_PARTIDO', 'equipo', false, 'PAIS_EQUIPO', ["PAIS_EQUIPO","","",""], ["x_equipo_local"], [], ["ID_EQUIPO"], ["x_ID_EQUIPO"], [], [], '', '', "`PAIS_EQUIPO`");
                break;
            default:
                $this->PAIS_PARTIDO->Lookup = new Lookup('PAIS_PARTIDO', 'equipo', false, 'PAIS_EQUIPO', ["PAIS_EQUIPO","","",""], ["x_equipo_local"], [], ["ID_EQUIPO"], ["x_ID_EQUIPO"], [], [], '', '', "`PAIS_EQUIPO`");
                break;
        }
        $this->Fields['PAIS_PARTIDO'] = &$this->PAIS_PARTIDO;

        // GOLES_LOCAL
        $this->GOLES_LOCAL = new DbField(
            'partidos',
            'partidos',
            'x_GOLES_LOCAL',
            'GOLES_LOCAL',
            '`GOLES_LOCAL`',
            '`GOLES_LOCAL`',
            3,
            11,
            -1,
            false,
            '`GOLES_LOCAL`',
            false,
            false,
            false,
            'FORMATTED TEXT',
            'TEXT'
        );
        $this->GOLES_LOCAL->InputTextType = "text";
        $this->GOLES_LOCAL->DefaultErrorMessage = $Language->phrase("IncorrectInteger");
        $this->Fields['GOLES_LOCAL'] = &$this->GOLES_LOCAL;

        // GOLES_VISITANTE
        $this->GOLES_VISITANTE = new DbField(
            'partidos',
            'partidos',
            'x_GOLES_VISITANTE',
            'GOLES_VISITANTE',
            '`GOLES_VISITANTE`',
            '`GOLES_VISITANTE`',
            3,
            11,
            -1,
            false,
            '`GOLES_VISITANTE`',
            false,
            false,
            false,
            'FORMATTED TEXT',
            'TEXT'
        );
        $this->GOLES_VISITANTE->InputTextType = "text";
        $this->GOLES_VISITANTE->DefaultErrorMessage = $Language->phrase("IncorrectInteger");
        $this->Fields['GOLES_VISITANTE'] = &$this->GOLES_VISITANTE;

        // GOLES_EXTRA_EQUIPO1
        $this->GOLES_EXTRA_EQUIPO1 = new DbField(
            'partidos',
            'partidos',
            'x_GOLES_EXTRA_EQUIPO1',
            'GOLES_EXTRA_EQUIPO1',
            '`GOLES_EXTRA_EQUIPO1`',
            '`GOLES_EXTRA_EQUIPO1`',
            3,
            11,
            -1,
            false,
            '`GOLES_EXTRA_EQUIPO1`',
            false,
            false,
            false,
            'FORMATTED TEXT',
            'TEXT'
        );
        $this->GOLES_EXTRA_EQUIPO1->InputTextType = "text";
        $this->GOLES_EXTRA_EQUIPO1->DefaultErrorMessage = $Language->phrase("IncorrectInteger");
        $this->Fields['GOLES_EXTRA_EQUIPO1'] = &$this->GOLES_EXTRA_EQUIPO1;

        // GOLES_EXTRA_EQUIPO2
        $this->GOLES_EXTRA_EQUIPO2 = new DbField(
            'partidos',
            'partidos',
            'x_GOLES_EXTRA_EQUIPO2',
            'GOLES_EXTRA_EQUIPO2',
            '`GOLES_EXTRA_EQUIPO2`',
            '`GOLES_EXTRA_EQUIPO2`',
            3,
            11,
            -1,
            false,
            '`GOLES_EXTRA_EQUIPO2`',
            false,
            false,
            false,
            'FORMATTED TEXT',
            'TEXT'
        );
        $this->GOLES_EXTRA_EQUIPO2->InputTextType = "text";
        $this->GOLES_EXTRA_EQUIPO2->DefaultErrorMessage = $Language->phrase("IncorrectInteger");
        $this->Fields['GOLES_EXTRA_EQUIPO2'] = &$this->GOLES_EXTRA_EQUIPO2;

        // NOTA_PARTIDO
        $this->NOTA_PARTIDO = new DbField(
            'partidos',
            'partidos',
            'x_NOTA_PARTIDO',
            'NOTA_PARTIDO',
            '`NOTA_PARTIDO`',
            '`NOTA_PARTIDO`',
            201,
            65535,
            -1,
            false,
            '`NOTA_PARTIDO`',
            false,
            false,
            false,
            'FORMATTED TEXT',
            'TEXTAREA'
        );
        $this->NOTA_PARTIDO->InputTextType = "text";
        $this->Fields['NOTA_PARTIDO'] = &$this->NOTA_PARTIDO;

        // RESUMEN_PARTIDO
        $this->RESUMEN_PARTIDO = new DbField(
            'partidos',
            'partidos',
            'x_RESUMEN_PARTIDO',
            'RESUMEN_PARTIDO',
            '`RESUMEN_PARTIDO`',
            '`RESUMEN_PARTIDO`',
            201,
            65535,
            -1,
            false,
            '`RESUMEN_PARTIDO`',
            false,
            false,
            false,
            'FORMATTED TEXT',
            'TEXTAREA'
        );
        $this->RESUMEN_PARTIDO->InputTextType = "text";
        $this->Fields['RESUMEN_PARTIDO'] = &$this->RESUMEN_PARTIDO;

        // ESTADO_PARTIDO
        $this->ESTADO_PARTIDO = new DbField(
            'partidos',
            'partidos',
            'x_ESTADO_PARTIDO',
            'ESTADO_PARTIDO',
            '`ESTADO_PARTIDO`',
            '`ESTADO_PARTIDO`',
            201,
            256,
            -1,
            false,
            '`ESTADO_PARTIDO`',
            false,
            false,
            false,
            'FORMATTED TEXT',
            'SELECT'
        );
        $this->ESTADO_PARTIDO->InputTextType = "text";
        $this->ESTADO_PARTIDO->UsePleaseSelect = true; // Use PleaseSelect by default
        $this->ESTADO_PARTIDO->PleaseSelectText = $Language->phrase("PleaseSelect"); // "PleaseSelect" text
        switch ($CurrentLanguage) {
            case "en-US":
                $this->ESTADO_PARTIDO->Lookup = new Lookup('ESTADO_PARTIDO', 'partidos', false, '', ["","","",""], [], [], [], [], [], [], '', '', "");
                break;
            default:
                $this->ESTADO_PARTIDO->Lookup = new Lookup('ESTADO_PARTIDO', 'partidos', false, '', ["","","",""], [], [], [], [], [], [], '', '', "");
                break;
        }
        $this->ESTADO_PARTIDO->OptionCount = 4;
        $this->Fields['ESTADO_PARTIDO'] = &$this->ESTADO_PARTIDO;

        // crea_dato
        $this->crea_dato = new DbField(
            'partidos',
            'partidos',
            'x_crea_dato',
            'crea_dato',
            '`crea_dato`',
            CastDateFieldForLike("`crea_dato`", 15, "DB"),
            135,
            19,
            15,
            false,
            '`crea_dato`',
            false,
            false,
            false,
            'FORMATTED TEXT',
            'TEXT'
        );
        $this->crea_dato->InputTextType = "text";
        $this->crea_dato->DefaultErrorMessage = str_replace("%s", DateFormat(15), $Language->phrase("IncorrectDate"));
        $this->Fields['crea_dato'] = &$this->crea_dato;

        // modifica_dato
        $this->modifica_dato = new DbField(
            'partidos',
            'partidos',
            'x_modifica_dato',
            'modifica_dato',
            '`modifica_dato`',
            CastDateFieldForLike("`modifica_dato`", 15, "DB"),
            135,
            19,
            15,
            false,
            '`modifica_dato`',
            false,
            false,
            false,
            'FORMATTED TEXT',
            'TEXT'
        );
        $this->modifica_dato->InputTextType = "text";
        $this->modifica_dato->DefaultErrorMessage = str_replace("%s", DateFormat(15), $Language->phrase("IncorrectDate"));
        $this->Fields['modifica_dato'] = &$this->modifica_dato;

        // usuario_dato
        $this->usuario_dato = new DbField(
            'partidos',
            'partidos',
            'x_usuario_dato',
            'usuario_dato',
            '`usuario_dato`',
            '`usuario_dato`',
            201,
            256,
            -1,
            false,
            '`usuario_dato`',
            false,
            false,
            false,
            'FORMATTED TEXT',
            'TEXT'
        );
        $this->usuario_dato->InputTextType = "text";
        $this->Fields['usuario_dato'] = &$this->usuario_dato;

        // automatico
        $this->automatico = new DbField(
            'partidos',
            'partidos',
            'x_automatico',
            'automatico',
            '`automatico`',
            '`automatico`',
            16,
            1,
            -1,
            false,
            '`automatico`',
            false,
            false,
            false,
            'FORMATTED TEXT',
            'CHECKBOX'
        );
        $this->automatico->InputTextType = "text";
        $this->automatico->DataType = DATATYPE_BOOLEAN;
        switch ($CurrentLanguage) {
            case "en-US":
                $this->automatico->Lookup = new Lookup('automatico', 'partidos', false, '', ["","","",""], [], [], [], [], [], [], '', '', "");
                break;
            default:
                $this->automatico->Lookup = new Lookup('automatico', 'partidos', false, '', ["","","",""], [], [], [], [], [], [], '', '', "");
                break;
        }
        $this->automatico->OptionCount = 2;
        $this->automatico->DefaultErrorMessage = $Language->phrase("IncorrectField");
        $this->Fields['automatico'] = &$this->automatico;

        // actualizado
        $this->actualizado = new DbField(
            'partidos',
            'partidos',
            'x_actualizado',
            'actualizado',
            '`actualizado`',
            '`actualizado`',
            201,
            256,
            -1,
            false,
            '`actualizado`',
            false,
            false,
            false,
            'FORMATTED TEXT',
            'HIDDEN'
        );
        $this->actualizado->InputTextType = "text";
        $this->Fields['actualizado'] = &$this->actualizado;

        // Add Doctrine Cache
        $this->Cache = new ArrayCache();
        $this->CacheProfile = new \Doctrine\DBAL\Cache\QueryCacheProfile(0, $this->TableVar);
    }

    // Field Visibility
    public function getFieldVisibility($fldParm)
    {
        global $Security;
        return $this->$fldParm->Visible; // Returns original value
    }

    // Set left column class (must be predefined col-*-* classes of Bootstrap grid system)
    public function setLeftColumnClass($class)
    {
        if (preg_match('/^col\-(\w+)\-(\d+)$/', $class, $match)) {
            $this->LeftColumnClass = $class . " col-form-label ew-label";
            $this->RightColumnClass = "col-" . $match[1] . "-" . strval(12 - (int)$match[2]);
            $this->OffsetColumnClass = $this->RightColumnClass . " " . str_replace("col-", "offset-", $class);
            $this->TableLeftColumnClass = preg_replace('/^col-\w+-(\d+)$/', "w-col-$1", $class); // Change to w-col-*
        }
    }

    // Single column sort
    public function updateSort(&$fld)
    {
        if ($this->CurrentOrder == $fld->Name) {
            $sortField = $fld->Expression;
            $lastSort = $fld->getSort();
            if (in_array($this->CurrentOrderType, ["ASC", "DESC", "NO"])) {
                $curSort = $this->CurrentOrderType;
            } else {
                $curSort = $lastSort;
            }
            $orderBy = in_array($curSort, ["ASC", "DESC"]) ? $sortField . " " . $curSort : "";
            $this->setSessionOrderBy($orderBy); // Save to Session
            $sortFieldList = ($fld->VirtualExpression != "") ? $fld->VirtualExpression : $sortField;
            $orderBy = in_array($curSort, ["ASC", "DESC"]) ? $sortFieldList . " " . $curSort : "";
            $this->setSessionOrderByList($orderBy); // Save to Session
        }
    }

    // Update field sort
    public function updateFieldSort()
    {
        $orderBy = $this->useVirtualFields() ? $this->getSessionOrderByList() : $this->getSessionOrderBy(); // Get ORDER BY from Session
        $flds = GetSortFields($orderBy);
        foreach ($this->Fields as $field) {
            $fldSort = "";
            foreach ($flds as $fld) {
                if ($fld[0] == $field->Expression || $fld[0] == $field->VirtualExpression) {
                    $fldSort = $fld[1];
                }
            }
            $field->setSort($fldSort);
        }
    }

    // Session ORDER BY for List page
    public function getSessionOrderByList()
    {
        return Session(PROJECT_NAME . "_" . $this->TableVar . "_" . Config("TABLE_ORDER_BY_LIST"));
    }

    public function setSessionOrderByList($v)
    {
        $_SESSION[PROJECT_NAME . "_" . $this->TableVar . "_" . Config("TABLE_ORDER_BY_LIST")] = $v;
    }

    // Table level SQL
    public function getSqlFrom() // From
    {
        return ($this->SqlFrom != "") ? $this->SqlFrom : "`partidos`";
    }

    public function sqlFrom() // For backward compatibility
    {
        return $this->getSqlFrom();
    }

    public function setSqlFrom($v)
    {
        $this->SqlFrom = $v;
    }

    public function getSqlSelect() // Select
    {
        return $this->SqlSelect ?? $this->getQueryBuilder()->select("*");
    }

    public function sqlSelect() // For backward compatibility
    {
        return $this->getSqlSelect();
    }

    public function setSqlSelect($v)
    {
        $this->SqlSelect = $v;
    }

    public function getSqlSelectList() // Select for List page
    {
        if ($this->SqlSelectList) {
            return $this->SqlSelectList;
        }
        $from = "(SELECT *, (SELECT `ID_EQUIPO` FROM `equipotorneo` `TMP_LOOKUPTABLE` WHERE `TMP_LOOKUPTABLE`.`ID_EQUIPO` = `partidos`.`equipo_local` LIMIT 1) AS `EV__equipo_local`, (SELECT `ID_EQUIPO` FROM `equipotorneo` `TMP_LOOKUPTABLE` WHERE `TMP_LOOKUPTABLE`.`ID_EQUIPO` = `partidos`.`equipo_visitante` LIMIT 1) AS `EV__equipo_visitante` FROM `partidos`)";
        return $from . " `TMP_TABLE`";
    }

    public function sqlSelectList() // For backward compatibility
    {
        return $this->getSqlSelectList();
    }

    public function setSqlSelectList($v)
    {
        $this->SqlSelectList = $v;
    }

    public function getSqlWhere() // Where
    {
        $where = ($this->SqlWhere != "") ? $this->SqlWhere : "";
        $this->DefaultFilter = "";
        AddFilter($where, $this->DefaultFilter);
        return $where;
    }

    public function sqlWhere() // For backward compatibility
    {
        return $this->getSqlWhere();
    }

    public function setSqlWhere($v)
    {
        $this->SqlWhere = $v;
    }

    public function getSqlGroupBy() // Group By
    {
        return ($this->SqlGroupBy != "") ? $this->SqlGroupBy : "";
    }

    public function sqlGroupBy() // For backward compatibility
    {
        return $this->getSqlGroupBy();
    }

    public function setSqlGroupBy($v)
    {
        $this->SqlGroupBy = $v;
    }

    public function getSqlHaving() // Having
    {
        return ($this->SqlHaving != "") ? $this->SqlHaving : "";
    }

    public function sqlHaving() // For backward compatibility
    {
        return $this->getSqlHaving();
    }

    public function setSqlHaving($v)
    {
        $this->SqlHaving = $v;
    }

    public function getSqlOrderBy() // Order By
    {
        return ($this->SqlOrderBy != "") ? $this->SqlOrderBy : "";
    }

    public function sqlOrderBy() // For backward compatibility
    {
        return $this->getSqlOrderBy();
    }

    public function setSqlOrderBy($v)
    {
        $this->SqlOrderBy = $v;
    }

    // Apply User ID filters
    public function applyUserIDFilters($filter, $id = "")
    {
        return $filter;
    }

    // Check if User ID security allows view all
    public function userIDAllow($id = "")
    {
        $allow = $this->UserIDAllowSecurity;
        switch ($id) {
            case "add":
            case "copy":
            case "gridadd":
            case "register":
            case "addopt":
                return (($allow & 1) == 1);
            case "edit":
            case "gridedit":
            case "update":
            case "changepassword":
            case "resetpassword":
                return (($allow & 4) == 4);
            case "delete":
                return (($allow & 2) == 2);
            case "view":
                return (($allow & 32) == 32);
            case "search":
                return (($allow & 64) == 64);
            case "lookup":
                return (($allow & 256) == 256);
            default:
                return (($allow & 8) == 8);
        }
    }

    /**
     * Get record count
     *
     * @param string|QueryBuilder $sql SQL or QueryBuilder
     * @param mixed $c Connection
     * @return int
     */
    public function getRecordCount($sql, $c = null)
    {
        $cnt = -1;
        $rs = null;
        if ($sql instanceof QueryBuilder) { // Query builder
            $sqlwrk = clone $sql;
            $sqlwrk = $sqlwrk->resetQueryPart("orderBy")->getSQL();
        } else {
            $sqlwrk = $sql;
        }
        $pattern = '/^SELECT\s([\s\S]+)\sFROM\s/i';
        // Skip Custom View / SubQuery / SELECT DISTINCT / ORDER BY
        if (
            ($this->TableType == 'TABLE' || $this->TableType == 'VIEW' || $this->TableType == 'LINKTABLE') &&
            preg_match($pattern, $sqlwrk) && !preg_match('/\(\s*(SELECT[^)]+)\)/i', $sqlwrk) &&
            !preg_match('/^\s*select\s+distinct\s+/i', $sqlwrk) && !preg_match('/\s+order\s+by\s+/i', $sqlwrk)
        ) {
            $sqlwrk = "SELECT COUNT(*) FROM " . preg_replace($pattern, "", $sqlwrk);
        } else {
            $sqlwrk = "SELECT COUNT(*) FROM (" . $sqlwrk . ") COUNT_TABLE";
        }
        $conn = $c ?? $this->getConnection();
        $cnt = $conn->fetchOne($sqlwrk);
        if ($cnt !== false) {
            return (int)$cnt;
        }

        // Unable to get count by SELECT COUNT(*), execute the SQL to get record count directly
        return ExecuteRecordCount($sql, $conn);
    }

    // Get SQL
    public function getSql($where, $orderBy = "")
    {
        return $this->buildSelectSql(
            $this->getSqlSelect(),
            $this->getSqlFrom(),
            $this->getSqlWhere(),
            $this->getSqlGroupBy(),
            $this->getSqlHaving(),
            $this->getSqlOrderBy(),
            $where,
            $orderBy
        )->getSQL();
    }

    // Table SQL
    public function getCurrentSql()
    {
        $filter = $this->CurrentFilter;
        $filter = $this->applyUserIDFilters($filter);
        $sort = $this->getSessionOrderBy();
        return $this->getSql($filter, $sort);
    }

    /**
     * Table SQL with List page filter
     *
     * @return QueryBuilder
     */
    public function getListSql()
    {
        $filter = $this->UseSessionForListSql ? $this->getSessionWhere() : "";
        AddFilter($filter, $this->CurrentFilter);
        $filter = $this->applyUserIDFilters($filter);
        $this->recordsetSelecting($filter);
        if ($this->useVirtualFields()) {
            $select = "*";
            $from = $this->getSqlSelectList();
            $sort = $this->UseSessionForListSql ? $this->getSessionOrderByList() : "";
        } else {
            $select = $this->getSqlSelect();
            $from = $this->getSqlFrom();
            $sort = $this->UseSessionForListSql ? $this->getSessionOrderBy() : "";
        }
        $this->Sort = $sort;
        return $this->buildSelectSql(
            $select,
            $from,
            $this->getSqlWhere(),
            $this->getSqlGroupBy(),
            $this->getSqlHaving(),
            $this->getSqlOrderBy(),
            $filter,
            $sort
        );
    }

    // Get ORDER BY clause
    public function getOrderBy()
    {
        $orderBy = $this->getSqlOrderBy();
        $sort = ($this->useVirtualFields()) ? $this->getSessionOrderByList() : $this->getSessionOrderBy();
        if ($orderBy != "" && $sort != "") {
            $orderBy .= ", " . $sort;
        } elseif ($sort != "") {
            $orderBy = $sort;
        }
        return $orderBy;
    }

    // Check if virtual fields is used in SQL
    protected function useVirtualFields()
    {
        $where = $this->UseSessionForListSql ? $this->getSessionWhere() : $this->CurrentFilter;
        $orderBy = $this->UseSessionForListSql ? $this->getSessionOrderByList() : "";
        if ($where != "") {
            $where = " " . str_replace(["(", ")"], ["", ""], $where) . " ";
        }
        if ($orderBy != "") {
            $orderBy = " " . str_replace(["(", ")"], ["", ""], $orderBy) . " ";
        }
        if (
            $this->equipo_local->AdvancedSearch->SearchValue != "" ||
            $this->equipo_local->AdvancedSearch->SearchValue2 != "" ||
            ContainsString($where, " " . $this->equipo_local->VirtualExpression . " ")
        ) {
            return true;
        }
        if (ContainsString($orderBy, " " . $this->equipo_local->VirtualExpression . " ")) {
            return true;
        }
        if (
            $this->equipo_visitante->AdvancedSearch->SearchValue != "" ||
            $this->equipo_visitante->AdvancedSearch->SearchValue2 != "" ||
            ContainsString($where, " " . $this->equipo_visitante->VirtualExpression . " ")
        ) {
            return true;
        }
        if (ContainsString($orderBy, " " . $this->equipo_visitante->VirtualExpression . " ")) {
            return true;
        }
        return false;
    }

    // Get record count based on filter (for detail record count in master table pages)
    public function loadRecordCount($filter)
    {
        $origFilter = $this->CurrentFilter;
        $this->CurrentFilter = $filter;
        $this->recordsetSelecting($this->CurrentFilter);
        $select = $this->TableType == 'CUSTOMVIEW' ? $this->getSqlSelect() : $this->getQueryBuilder()->select("*");
        $groupBy = $this->TableType == 'CUSTOMVIEW' ? $this->getSqlGroupBy() : "";
        $having = $this->TableType == 'CUSTOMVIEW' ? $this->getSqlHaving() : "";
        $sql = $this->buildSelectSql($select, $this->getSqlFrom(), $this->getSqlWhere(), $groupBy, $having, "", $this->CurrentFilter, "");
        $cnt = $this->getRecordCount($sql);
        $this->CurrentFilter = $origFilter;
        return $cnt;
    }

    // Get record count (for current List page)
    public function listRecordCount()
    {
        $filter = $this->getSessionWhere();
        AddFilter($filter, $this->CurrentFilter);
        $filter = $this->applyUserIDFilters($filter);
        $this->recordsetSelecting($filter);
        $select = $this->TableType == 'CUSTOMVIEW' ? $this->getSqlSelect() : $this->getQueryBuilder()->select("*");
        $groupBy = $this->TableType == 'CUSTOMVIEW' ? $this->getSqlGroupBy() : "";
        $having = $this->TableType == 'CUSTOMVIEW' ? $this->getSqlHaving() : "";
        if ($this->useVirtualFields()) {
            $sql = $this->buildSelectSql("*", $this->getSqlSelectList(), $this->getSqlWhere(), $groupBy, $having, "", $filter, "");
        } else {
            $sql = $this->buildSelectSql($select, $this->getSqlFrom(), $this->getSqlWhere(), $groupBy, $having, "", $filter, "");
        }
        $cnt = $this->getRecordCount($sql);
        return $cnt;
    }

    /**
     * INSERT statement
     *
     * @param mixed $rs
     * @return QueryBuilder
     */
    public function insertSql(&$rs)
    {
        $queryBuilder = $this->getQueryBuilder();
        $queryBuilder->insert($this->UpdateTable);
        foreach ($rs as $name => $value) {
            if (!isset($this->Fields[$name]) || $this->Fields[$name]->IsCustom) {
                continue;
            }
            $type = GetParameterType($this->Fields[$name], $value, $this->Dbid);
            $queryBuilder->setValue($this->Fields[$name]->Expression, $queryBuilder->createPositionalParameter($value, $type));
        }
        return $queryBuilder;
    }

    // Insert
    public function insert(&$rs)
    {
        $conn = $this->getConnection();
        $success = $this->insertSql($rs)->execute();
        if ($success) {
            // Get insert id if necessary
            $this->ID_PARTIDO->setDbValue($conn->lastInsertId());
            $rs['ID_PARTIDO'] = $this->ID_PARTIDO->DbValue;
        }
        return $success;
    }

    /**
     * UPDATE statement
     *
     * @param array $rs Data to be updated
     * @param string|array $where WHERE clause
     * @param string $curfilter Filter
     * @return QueryBuilder
     */
    public function updateSql(&$rs, $where = "", $curfilter = true)
    {
        $queryBuilder = $this->getQueryBuilder();
        $queryBuilder->update($this->UpdateTable);
        foreach ($rs as $name => $value) {
            if (!isset($this->Fields[$name]) || $this->Fields[$name]->IsCustom || $this->Fields[$name]->IsAutoIncrement) {
                continue;
            }
            $type = GetParameterType($this->Fields[$name], $value, $this->Dbid);
            $queryBuilder->set($this->Fields[$name]->Expression, $queryBuilder->createPositionalParameter($value, $type));
        }
        $filter = ($curfilter) ? $this->CurrentFilter : "";
        if (is_array($where)) {
            $where = $this->arrayToFilter($where);
        }
        AddFilter($filter, $where);
        if ($filter != "") {
            $queryBuilder->where($filter);
        }
        return $queryBuilder;
    }

    // Update
    public function update(&$rs, $where = "", $rsold = null, $curfilter = true)
    {
        // If no field is updated, execute may return 0. Treat as success
        $success = $this->updateSql($rs, $where, $curfilter)->execute();
        $success = ($success > 0) ? $success : true;
        return $success;
    }

    /**
     * DELETE statement
     *
     * @param array $rs Key values
     * @param string|array $where WHERE clause
     * @param string $curfilter Filter
     * @return QueryBuilder
     */
    public function deleteSql(&$rs, $where = "", $curfilter = true)
    {
        $queryBuilder = $this->getQueryBuilder();
        $queryBuilder->delete($this->UpdateTable);
        if (is_array($where)) {
            $where = $this->arrayToFilter($where);
        }
        if ($rs) {
            if (array_key_exists('ID_PARTIDO', $rs)) {
                AddFilter($where, QuotedName('ID_PARTIDO', $this->Dbid) . '=' . QuotedValue($rs['ID_PARTIDO'], $this->ID_PARTIDO->DataType, $this->Dbid));
            }
        }
        $filter = ($curfilter) ? $this->CurrentFilter : "";
        AddFilter($filter, $where);
        return $queryBuilder->where($filter != "" ? $filter : "0=1");
    }

    // Delete
    public function delete(&$rs, $where = "", $curfilter = false)
    {
        $success = true;
        if ($success) {
            $success = $this->deleteSql($rs, $where, $curfilter)->execute();
        }
        return $success;
    }

    // Load DbValue from recordset or array
    protected function loadDbValues($row)
    {
        if (!is_array($row)) {
            return;
        }
        $this->ID_TORNEO->DbValue = $row['ID_TORNEO'];
        $this->equipo_local->DbValue = $row['equipo_local'];
        $this->equipo_visitante->DbValue = $row['equipo_visitante'];
        $this->ID_PARTIDO->DbValue = $row['ID_PARTIDO'];
        $this->FECHA_PARTIDO->DbValue = $row['FECHA_PARTIDO'];
        $this->HORA_PARTIDO->DbValue = $row['HORA_PARTIDO'];
        $this->ESTADIO->DbValue = $row['ESTADIO'];
        $this->CIUDAD_PARTIDO->DbValue = $row['CIUDAD_PARTIDO'];
        $this->PAIS_PARTIDO->DbValue = $row['PAIS_PARTIDO'];
        $this->GOLES_LOCAL->DbValue = $row['GOLES_LOCAL'];
        $this->GOLES_VISITANTE->DbValue = $row['GOLES_VISITANTE'];
        $this->GOLES_EXTRA_EQUIPO1->DbValue = $row['GOLES_EXTRA_EQUIPO1'];
        $this->GOLES_EXTRA_EQUIPO2->DbValue = $row['GOLES_EXTRA_EQUIPO2'];
        $this->NOTA_PARTIDO->DbValue = $row['NOTA_PARTIDO'];
        $this->RESUMEN_PARTIDO->DbValue = $row['RESUMEN_PARTIDO'];
        $this->ESTADO_PARTIDO->DbValue = $row['ESTADO_PARTIDO'];
        $this->crea_dato->DbValue = $row['crea_dato'];
        $this->modifica_dato->DbValue = $row['modifica_dato'];
        $this->usuario_dato->DbValue = $row['usuario_dato'];
        $this->automatico->DbValue = $row['automatico'];
        $this->actualizado->DbValue = $row['actualizado'];
    }

    // Delete uploaded files
    public function deleteUploadedFiles($row)
    {
        $this->loadDbValues($row);
    }

    // Record filter WHERE clause
    protected function sqlKeyFilter()
    {
        return "`ID_PARTIDO` = @ID_PARTIDO@";
    }

    // Get Key
    public function getKey($current = false)
    {
        $keys = [];
        $val = $current ? $this->ID_PARTIDO->CurrentValue : $this->ID_PARTIDO->OldValue;
        if (EmptyValue($val)) {
            return "";
        } else {
            $keys[] = $val;
        }
        return implode(Config("COMPOSITE_KEY_SEPARATOR"), $keys);
    }

    // Set Key
    public function setKey($key, $current = false)
    {
        $this->OldKey = strval($key);
        $keys = explode(Config("COMPOSITE_KEY_SEPARATOR"), $this->OldKey);
        if (count($keys) == 1) {
            if ($current) {
                $this->ID_PARTIDO->CurrentValue = $keys[0];
            } else {
                $this->ID_PARTIDO->OldValue = $keys[0];
            }
        }
    }

    // Get record filter
    public function getRecordFilter($row = null)
    {
        $keyFilter = $this->sqlKeyFilter();
        if (is_array($row)) {
            $val = array_key_exists('ID_PARTIDO', $row) ? $row['ID_PARTIDO'] : null;
        } else {
            $val = $this->ID_PARTIDO->OldValue !== null ? $this->ID_PARTIDO->OldValue : $this->ID_PARTIDO->CurrentValue;
        }
        if (!is_numeric($val)) {
            return "0=1"; // Invalid key
        }
        if ($val === null) {
            return "0=1"; // Invalid key
        } else {
            $keyFilter = str_replace("@ID_PARTIDO@", AdjustSql($val, $this->Dbid), $keyFilter); // Replace key value
        }
        return $keyFilter;
    }

    // Return page URL
    public function getReturnUrl()
    {
        $referUrl = ReferUrl();
        $referPageName = ReferPageName();
        $name = PROJECT_NAME . "_" . $this->TableVar . "_" . Config("TABLE_RETURN_URL");
        // Get referer URL automatically
        if ($referUrl != "" && $referPageName != CurrentPageName() && $referPageName != "login") { // Referer not same page or login page
            $_SESSION[$name] = $referUrl; // Save to Session
        }
        return $_SESSION[$name] ?? GetUrl("partidoslist");
    }

    // Set return page URL
    public function setReturnUrl($v)
    {
        $_SESSION[PROJECT_NAME . "_" . $this->TableVar . "_" . Config("TABLE_RETURN_URL")] = $v;
    }

    // Get modal caption
    public function getModalCaption($pageName)
    {
        global $Language;
        if ($pageName == "partidosview") {
            return $Language->phrase("View");
        } elseif ($pageName == "partidosedit") {
            return $Language->phrase("Edit");
        } elseif ($pageName == "partidosadd") {
            return $Language->phrase("Add");
        } else {
            return "";
        }
    }

    // API page name
    public function getApiPageName($action)
    {
        switch (strtolower($action)) {
            case Config("API_VIEW_ACTION"):
                return "PartidosView";
            case Config("API_ADD_ACTION"):
                return "PartidosAdd";
            case Config("API_EDIT_ACTION"):
                return "PartidosEdit";
            case Config("API_DELETE_ACTION"):
                return "PartidosDelete";
            case Config("API_LIST_ACTION"):
                return "PartidosList";
            default:
                return "";
        }
    }

    // List URL
    public function getListUrl()
    {
        return "partidoslist";
    }

    // View URL
    public function getViewUrl($parm = "")
    {
        if ($parm != "") {
            $url = $this->keyUrl("partidosview", $this->getUrlParm($parm));
        } else {
            $url = $this->keyUrl("partidosview", $this->getUrlParm(Config("TABLE_SHOW_DETAIL") . "="));
        }
        return $this->addMasterUrl($url);
    }

    // Add URL
    public function getAddUrl($parm = "")
    {
        if ($parm != "") {
            $url = "partidosadd?" . $this->getUrlParm($parm);
        } else {
            $url = "partidosadd";
        }
        return $this->addMasterUrl($url);
    }

    // Edit URL
    public function getEditUrl($parm = "")
    {
        $url = $this->keyUrl("partidosedit", $this->getUrlParm($parm));
        return $this->addMasterUrl($url);
    }

    // Inline edit URL
    public function getInlineEditUrl()
    {
        $url = $this->keyUrl(CurrentPageName(), $this->getUrlParm("action=edit"));
        return $this->addMasterUrl($url);
    }

    // Copy URL
    public function getCopyUrl($parm = "")
    {
        $url = $this->keyUrl("partidosadd", $this->getUrlParm($parm));
        return $this->addMasterUrl($url);
    }

    // Inline copy URL
    public function getInlineCopyUrl()
    {
        $url = $this->keyUrl(CurrentPageName(), $this->getUrlParm("action=copy"));
        return $this->addMasterUrl($url);
    }

    // Delete URL
    public function getDeleteUrl()
    {
        return $this->keyUrl("partidosdelete", $this->getUrlParm());
    }

    // Add master url
    public function addMasterUrl($url)
    {
        return $url;
    }

    public function keyToJson($htmlEncode = false)
    {
        $json = "";
        $json .= "\"ID_PARTIDO\":" . JsonEncode($this->ID_PARTIDO->CurrentValue, "number");
        $json = "{" . $json . "}";
        if ($htmlEncode) {
            $json = HtmlEncode($json);
        }
        return $json;
    }

    // Add key value to URL
    public function keyUrl($url, $parm = "")
    {
        if ($this->ID_PARTIDO->CurrentValue !== null) {
            $url .= "/" . $this->encodeKeyValue($this->ID_PARTIDO->CurrentValue);
        } else {
            return "javascript:ew.alert(ew.language.phrase('InvalidRecord'));";
        }
        if ($parm != "") {
            $url .= "?" . $parm;
        }
        return $url;
    }

    // Render sort
    public function renderFieldHeader($fld)
    {
        global $Security, $Language;
        $sortUrl = "";
        $attrs = "";
        if ($fld->Sortable) {
            $sortUrl = $this->sortUrl($fld);
            $attrs = ' role="button" data-sort-url="' . $sortUrl . '" data-sort-type="1"';
        }
        $html = '<div class="ew-table-header-caption"' . $attrs . '>' . $fld->caption() . '</div>';
        if ($sortUrl) {
            $html .= '<div class="ew-table-header-sort">' . $fld->getSortIcon() . '</div>';
        }
        if ($fld->UseFilter && $Security->canSearch()) {
            $html .= '<div class="ew-filter-dropdown-btn" data-ew-action="filter" data-table="' . $fld->TableVar . '" data-field="' . $fld->FieldVar .
                '"><div class="ew-table-header-filter" role="button" aria-haspopup="true">' . $Language->phrase("Filter") . '</div></div>';
        }
        $html = '<div class="ew-table-header-btn">' . $html . '</div>';
        if ($this->UseCustomTemplate) {
            $scriptId = str_replace("{id}", $fld->TableVar . "_" . $fld->Param, "tpc_{id}");
            $html = '<template id="' . $scriptId . '">' . $html . '</template>';
        }
        return $html;
    }

    // Sort URL
    public function sortUrl($fld)
    {
        if (
            $this->CurrentAction || $this->isExport() ||
            in_array($fld->Type, [128, 204, 205])
        ) { // Unsortable data type
                return "";
        } elseif ($fld->Sortable) {
            $urlParm = $this->getUrlParm("order=" . urlencode($fld->Name) . "&amp;ordertype=" . $fld->getNextSort());
            return $this->addMasterUrl(CurrentPageName() . "?" . $urlParm);
        } else {
            return "";
        }
    }

    // Get record keys from Post/Get/Session
    public function getRecordKeys()
    {
        $arKeys = [];
        $arKey = [];
        if (Param("key_m") !== null) {
            $arKeys = Param("key_m");
            $cnt = count($arKeys);
        } else {
            if (($keyValue = Param("ID_PARTIDO") ?? Route("ID_PARTIDO")) !== null) {
                $arKeys[] = $keyValue;
            } elseif (IsApi() && (($keyValue = Key(0) ?? Route(2)) !== null)) {
                $arKeys[] = $keyValue;
            } else {
                $arKeys = null; // Do not setup
            }

            //return $arKeys; // Do not return yet, so the values will also be checked by the following code
        }
        // Check keys
        $ar = [];
        if (is_array($arKeys)) {
            foreach ($arKeys as $key) {
                if (!is_numeric($key)) {
                    continue;
                }
                $ar[] = $key;
            }
        }
        return $ar;
    }

    // Get filter from record keys
    public function getFilterFromRecordKeys($setCurrent = true)
    {
        $arKeys = $this->getRecordKeys();
        $keyFilter = "";
        foreach ($arKeys as $key) {
            if ($keyFilter != "") {
                $keyFilter .= " OR ";
            }
            if ($setCurrent) {
                $this->ID_PARTIDO->CurrentValue = $key;
            } else {
                $this->ID_PARTIDO->OldValue = $key;
            }
            $keyFilter .= "(" . $this->getRecordFilter() . ")";
        }
        return $keyFilter;
    }

    // Load recordset based on filter
    public function loadRs($filter)
    {
        $sql = $this->getSql($filter); // Set up filter (WHERE Clause)
        $conn = $this->getConnection();
        return $conn->executeQuery($sql);
    }

    // Load row values from record
    public function loadListRowValues(&$rs)
    {
        if (is_array($rs)) {
            $row = $rs;
        } elseif ($rs && property_exists($rs, "fields")) { // Recordset
            $row = $rs->fields;
        } else {
            return;
        }
        $this->ID_TORNEO->setDbValue($row['ID_TORNEO']);
        $this->equipo_local->setDbValue($row['equipo_local']);
        $this->equipo_visitante->setDbValue($row['equipo_visitante']);
        $this->ID_PARTIDO->setDbValue($row['ID_PARTIDO']);
        $this->FECHA_PARTIDO->setDbValue($row['FECHA_PARTIDO']);
        $this->HORA_PARTIDO->setDbValue($row['HORA_PARTIDO']);
        $this->ESTADIO->setDbValue($row['ESTADIO']);
        $this->CIUDAD_PARTIDO->setDbValue($row['CIUDAD_PARTIDO']);
        $this->PAIS_PARTIDO->setDbValue($row['PAIS_PARTIDO']);
        $this->GOLES_LOCAL->setDbValue($row['GOLES_LOCAL']);
        $this->GOLES_VISITANTE->setDbValue($row['GOLES_VISITANTE']);
        $this->GOLES_EXTRA_EQUIPO1->setDbValue($row['GOLES_EXTRA_EQUIPO1']);
        $this->GOLES_EXTRA_EQUIPO2->setDbValue($row['GOLES_EXTRA_EQUIPO2']);
        $this->NOTA_PARTIDO->setDbValue($row['NOTA_PARTIDO']);
        $this->RESUMEN_PARTIDO->setDbValue($row['RESUMEN_PARTIDO']);
        $this->ESTADO_PARTIDO->setDbValue($row['ESTADO_PARTIDO']);
        $this->crea_dato->setDbValue($row['crea_dato']);
        $this->modifica_dato->setDbValue($row['modifica_dato']);
        $this->usuario_dato->setDbValue($row['usuario_dato']);
        $this->automatico->setDbValue($row['automatico']);
        $this->actualizado->setDbValue($row['actualizado']);
    }

    // Render list row values
    public function renderListRow()
    {
        global $Security, $CurrentLanguage, $Language;

        // Call Row Rendering event
        $this->rowRendering();

        // Common render codes

        // ID_TORNEO

        // equipo_local

        // equipo_visitante

        // ID_PARTIDO

        // FECHA_PARTIDO

        // HORA_PARTIDO

        // ESTADIO

        // CIUDAD_PARTIDO

        // PAIS_PARTIDO

        // GOLES_LOCAL

        // GOLES_VISITANTE

        // GOLES_EXTRA_EQUIPO1

        // GOLES_EXTRA_EQUIPO2

        // NOTA_PARTIDO

        // RESUMEN_PARTIDO

        // ESTADO_PARTIDO

        // crea_dato

        // modifica_dato

        // usuario_dato

        // automatico

        // actualizado

        // ID_TORNEO
        $curVal = strval($this->ID_TORNEO->CurrentValue);
        if ($curVal != "") {
            $this->ID_TORNEO->ViewValue = $this->ID_TORNEO->lookupCacheOption($curVal);
            if ($this->ID_TORNEO->ViewValue === null) { // Lookup from database
                $filterWrk = "`ID_TORNEO`" . SearchString("=", $curVal, DATATYPE_NUMBER, "");
                $sqlWrk = $this->ID_TORNEO->Lookup->getSql(false, $filterWrk, '', $this, true, true);
                $conn = Conn();
                $config = $conn->getConfiguration();
                $config->setResultCacheImpl($this->Cache);
                $rswrk = $conn->executeCacheQuery($sqlWrk, [], [], $this->CacheProfile)->fetchAll();
                $ari = count($rswrk);
                if ($ari > 0) { // Lookup values found
                    $arwrk = $this->ID_TORNEO->Lookup->renderViewRow($rswrk[0]);
                    $this->ID_TORNEO->ViewValue = $this->ID_TORNEO->displayValue($arwrk);
                } else {
                    $this->ID_TORNEO->ViewValue = FormatNumber($this->ID_TORNEO->CurrentValue, $this->ID_TORNEO->formatPattern());
                }
            }
        } else {
            $this->ID_TORNEO->ViewValue = null;
        }
        $this->ID_TORNEO->ViewCustomAttributes = "";

        // equipo_local
        if ($this->equipo_local->VirtualValue != "") {
            $this->equipo_local->ViewValue = $this->equipo_local->VirtualValue;
        } else {
            $curVal = strval($this->equipo_local->CurrentValue);
            if ($curVal != "") {
                $this->equipo_local->ViewValue = $this->equipo_local->lookupCacheOption($curVal);
                if ($this->equipo_local->ViewValue === null) { // Lookup from database
                    $filterWrk = "`ID_EQUIPO`" . SearchString("=", $curVal, DATATYPE_NUMBER, "");
                    $sqlWrk = $this->equipo_local->Lookup->getSql(false, $filterWrk, '', $this, true, true);
                    $conn = Conn();
                    $config = $conn->getConfiguration();
                    $config->setResultCacheImpl($this->Cache);
                    $rswrk = $conn->executeCacheQuery($sqlWrk, [], [], $this->CacheProfile)->fetchAll();
                    $ari = count($rswrk);
                    if ($ari > 0) { // Lookup values found
                        $arwrk = $this->equipo_local->Lookup->renderViewRow($rswrk[0]);
                        $this->equipo_local->ViewValue = $this->equipo_local->displayValue($arwrk);
                    } else {
                        $this->equipo_local->ViewValue = FormatNumber($this->equipo_local->CurrentValue, $this->equipo_local->formatPattern());
                    }
                }
            } else {
                $this->equipo_local->ViewValue = null;
            }
        }
        $this->equipo_local->ViewCustomAttributes = "";

        // equipo_visitante
        if ($this->equipo_visitante->VirtualValue != "") {
            $this->equipo_visitante->ViewValue = $this->equipo_visitante->VirtualValue;
        } else {
            $curVal = strval($this->equipo_visitante->CurrentValue);
            if ($curVal != "") {
                $this->equipo_visitante->ViewValue = $this->equipo_visitante->lookupCacheOption($curVal);
                if ($this->equipo_visitante->ViewValue === null) { // Lookup from database
                    $filterWrk = "`ID_EQUIPO`" . SearchString("=", $curVal, DATATYPE_NUMBER, "");
                    $sqlWrk = $this->equipo_visitante->Lookup->getSql(false, $filterWrk, '', $this, true, true);
                    $conn = Conn();
                    $config = $conn->getConfiguration();
                    $config->setResultCacheImpl($this->Cache);
                    $rswrk = $conn->executeCacheQuery($sqlWrk, [], [], $this->CacheProfile)->fetchAll();
                    $ari = count($rswrk);
                    if ($ari > 0) { // Lookup values found
                        $arwrk = $this->equipo_visitante->Lookup->renderViewRow($rswrk[0]);
                        $this->equipo_visitante->ViewValue = $this->equipo_visitante->displayValue($arwrk);
                    } else {
                        $this->equipo_visitante->ViewValue = FormatNumber($this->equipo_visitante->CurrentValue, $this->equipo_visitante->formatPattern());
                    }
                }
            } else {
                $this->equipo_visitante->ViewValue = null;
            }
        }
        $this->equipo_visitante->ViewCustomAttributes = "";

        // ID_PARTIDO
        $this->ID_PARTIDO->ViewValue = $this->ID_PARTIDO->CurrentValue;
        $this->ID_PARTIDO->ViewCustomAttributes = "";

        // FECHA_PARTIDO
        $this->FECHA_PARTIDO->ViewValue = $this->FECHA_PARTIDO->CurrentValue;
        $this->FECHA_PARTIDO->ViewValue = FormatDateTime($this->FECHA_PARTIDO->ViewValue, $this->FECHA_PARTIDO->formatPattern());
        $this->FECHA_PARTIDO->ViewCustomAttributes = "";

        // HORA_PARTIDO
        $this->HORA_PARTIDO->ViewValue = $this->HORA_PARTIDO->CurrentValue;
        $this->HORA_PARTIDO->ViewValue = FormatDateTime($this->HORA_PARTIDO->ViewValue, $this->HORA_PARTIDO->formatPattern());
        $this->HORA_PARTIDO->ViewCustomAttributes = "";

        // ESTADIO
        $curVal = strval($this->ESTADIO->CurrentValue);
        if ($curVal != "") {
            $this->ESTADIO->ViewValue = $this->ESTADIO->lookupCacheOption($curVal);
            if ($this->ESTADIO->ViewValue === null) { // Lookup from database
                $filterWrk = "`id_estadio`" . SearchString("=", $curVal, DATATYPE_NUMBER, "");
                $sqlWrk = $this->ESTADIO->Lookup->getSql(false, $filterWrk, '', $this, true, true);
                $conn = Conn();
                $config = $conn->getConfiguration();
                $config->setResultCacheImpl($this->Cache);
                $rswrk = $conn->executeCacheQuery($sqlWrk, [], [], $this->CacheProfile)->fetchAll();
                $ari = count($rswrk);
                if ($ari > 0) { // Lookup values found
                    $arwrk = $this->ESTADIO->Lookup->renderViewRow($rswrk[0]);
                    $this->ESTADIO->ViewValue = $this->ESTADIO->displayValue($arwrk);
                } else {
                    $this->ESTADIO->ViewValue = FormatNumber($this->ESTADIO->CurrentValue, $this->ESTADIO->formatPattern());
                }
            }
        } else {
            $this->ESTADIO->ViewValue = null;
        }
        $this->ESTADIO->ViewCustomAttributes = "";

        // CIUDAD_PARTIDO
        $this->CIUDAD_PARTIDO->ViewValue = $this->CIUDAD_PARTIDO->CurrentValue;
        $this->CIUDAD_PARTIDO->ViewCustomAttributes = "";

        // PAIS_PARTIDO
        $curVal = strval($this->PAIS_PARTIDO->CurrentValue);
        if ($curVal != "") {
            $this->PAIS_PARTIDO->ViewValue = $this->PAIS_PARTIDO->lookupCacheOption($curVal);
            if ($this->PAIS_PARTIDO->ViewValue === null) { // Lookup from database
                $filterWrk = "`PAIS_EQUIPO`" . SearchString("=", $curVal, DATATYPE_MEMO, "");
                $sqlWrk = $this->PAIS_PARTIDO->Lookup->getSql(false, $filterWrk, '', $this, true, true);
                $conn = Conn();
                $config = $conn->getConfiguration();
                $config->setResultCacheImpl($this->Cache);
                $rswrk = $conn->executeCacheQuery($sqlWrk, [], [], $this->CacheProfile)->fetchAll();
                $ari = count($rswrk);
                if ($ari > 0) { // Lookup values found
                    $arwrk = $this->PAIS_PARTIDO->Lookup->renderViewRow($rswrk[0]);
                    $this->PAIS_PARTIDO->ViewValue = $this->PAIS_PARTIDO->displayValue($arwrk);
                } else {
                    $this->PAIS_PARTIDO->ViewValue = $this->PAIS_PARTIDO->CurrentValue;
                }
            }
        } else {
            $this->PAIS_PARTIDO->ViewValue = null;
        }
        $this->PAIS_PARTIDO->ViewCustomAttributes = "";

        // GOLES_LOCAL
        $this->GOLES_LOCAL->ViewValue = $this->GOLES_LOCAL->CurrentValue;
        $this->GOLES_LOCAL->ViewValue = FormatNumber($this->GOLES_LOCAL->ViewValue, $this->GOLES_LOCAL->formatPattern());
        $this->GOLES_LOCAL->ViewCustomAttributes = "";

        // GOLES_VISITANTE
        $this->GOLES_VISITANTE->ViewValue = $this->GOLES_VISITANTE->CurrentValue;
        $this->GOLES_VISITANTE->ViewValue = FormatNumber($this->GOLES_VISITANTE->ViewValue, $this->GOLES_VISITANTE->formatPattern());
        $this->GOLES_VISITANTE->ViewCustomAttributes = "";

        // GOLES_EXTRA_EQUIPO1
        $this->GOLES_EXTRA_EQUIPO1->ViewValue = $this->GOLES_EXTRA_EQUIPO1->CurrentValue;
        $this->GOLES_EXTRA_EQUIPO1->ViewValue = FormatNumber($this->GOLES_EXTRA_EQUIPO1->ViewValue, $this->GOLES_EXTRA_EQUIPO1->formatPattern());
        $this->GOLES_EXTRA_EQUIPO1->ViewCustomAttributes = "";

        // GOLES_EXTRA_EQUIPO2
        $this->GOLES_EXTRA_EQUIPO2->ViewValue = $this->GOLES_EXTRA_EQUIPO2->CurrentValue;
        $this->GOLES_EXTRA_EQUIPO2->ViewValue = FormatNumber($this->GOLES_EXTRA_EQUIPO2->ViewValue, $this->GOLES_EXTRA_EQUIPO2->formatPattern());
        $this->GOLES_EXTRA_EQUIPO2->ViewCustomAttributes = "";

        // NOTA_PARTIDO
        $this->NOTA_PARTIDO->ViewValue = $this->NOTA_PARTIDO->CurrentValue;
        $this->NOTA_PARTIDO->ViewCustomAttributes = "";

        // RESUMEN_PARTIDO
        $this->RESUMEN_PARTIDO->ViewValue = $this->RESUMEN_PARTIDO->CurrentValue;
        $this->RESUMEN_PARTIDO->ViewCustomAttributes = "";

        // ESTADO_PARTIDO
        if (strval($this->ESTADO_PARTIDO->CurrentValue) != "") {
            $this->ESTADO_PARTIDO->ViewValue = $this->ESTADO_PARTIDO->optionCaption($this->ESTADO_PARTIDO->CurrentValue);
        } else {
            $this->ESTADO_PARTIDO->ViewValue = null;
        }
        $this->ESTADO_PARTIDO->ViewCustomAttributes = "";

        // crea_dato
        $this->crea_dato->ViewValue = $this->crea_dato->CurrentValue;
        $this->crea_dato->ViewValue = FormatDateTime($this->crea_dato->ViewValue, $this->crea_dato->formatPattern());
        $this->crea_dato->ViewCustomAttributes = "";

        // modifica_dato
        $this->modifica_dato->ViewValue = $this->modifica_dato->CurrentValue;
        $this->modifica_dato->ViewValue = FormatDateTime($this->modifica_dato->ViewValue, $this->modifica_dato->formatPattern());
        $this->modifica_dato->ViewCustomAttributes = "";

        // usuario_dato
        $this->usuario_dato->ViewValue = $this->usuario_dato->CurrentValue;
        $this->usuario_dato->ViewCustomAttributes = "";

        // automatico
        if (ConvertToBool($this->automatico->CurrentValue)) {
            $this->automatico->ViewValue = $this->automatico->tagCaption(1) != "" ? $this->automatico->tagCaption(1) : "Yes";
        } else {
            $this->automatico->ViewValue = $this->automatico->tagCaption(2) != "" ? $this->automatico->tagCaption(2) : "No";
        }
        $this->automatico->ViewCustomAttributes = "";

        // actualizado
        $this->actualizado->ViewValue = $this->actualizado->CurrentValue;
        $this->actualizado->ViewCustomAttributes = "";

        // ID_TORNEO
        $this->ID_TORNEO->LinkCustomAttributes = "";
        $this->ID_TORNEO->HrefValue = "";
        $this->ID_TORNEO->TooltipValue = "";

        // equipo_local
        $this->equipo_local->LinkCustomAttributes = "";
        $this->equipo_local->HrefValue = "";
        $this->equipo_local->TooltipValue = "";

        // equipo_visitante
        $this->equipo_visitante->LinkCustomAttributes = "";
        $this->equipo_visitante->HrefValue = "";
        $this->equipo_visitante->TooltipValue = "";

        // ID_PARTIDO
        $this->ID_PARTIDO->LinkCustomAttributes = "";
        $this->ID_PARTIDO->HrefValue = "";
        $this->ID_PARTIDO->TooltipValue = "";

        // FECHA_PARTIDO
        $this->FECHA_PARTIDO->LinkCustomAttributes = "";
        $this->FECHA_PARTIDO->HrefValue = "";
        $this->FECHA_PARTIDO->TooltipValue = "";

        // HORA_PARTIDO
        $this->HORA_PARTIDO->LinkCustomAttributes = "";
        $this->HORA_PARTIDO->HrefValue = "";
        $this->HORA_PARTIDO->TooltipValue = "";

        // ESTADIO
        $this->ESTADIO->LinkCustomAttributes = "";
        $this->ESTADIO->HrefValue = "";
        $this->ESTADIO->TooltipValue = "";

        // CIUDAD_PARTIDO
        $this->CIUDAD_PARTIDO->LinkCustomAttributes = "";
        $this->CIUDAD_PARTIDO->HrefValue = "";
        $this->CIUDAD_PARTIDO->TooltipValue = "";

        // PAIS_PARTIDO
        $this->PAIS_PARTIDO->LinkCustomAttributes = "";
        $this->PAIS_PARTIDO->HrefValue = "";
        $this->PAIS_PARTIDO->TooltipValue = "";

        // GOLES_LOCAL
        $this->GOLES_LOCAL->LinkCustomAttributes = "";
        $this->GOLES_LOCAL->HrefValue = "";
        $this->GOLES_LOCAL->TooltipValue = "";

        // GOLES_VISITANTE
        $this->GOLES_VISITANTE->LinkCustomAttributes = "";
        $this->GOLES_VISITANTE->HrefValue = "";
        $this->GOLES_VISITANTE->TooltipValue = "";

        // GOLES_EXTRA_EQUIPO1
        $this->GOLES_EXTRA_EQUIPO1->LinkCustomAttributes = "";
        $this->GOLES_EXTRA_EQUIPO1->HrefValue = "";
        $this->GOLES_EXTRA_EQUIPO1->TooltipValue = "";

        // GOLES_EXTRA_EQUIPO2
        $this->GOLES_EXTRA_EQUIPO2->LinkCustomAttributes = "";
        $this->GOLES_EXTRA_EQUIPO2->HrefValue = "";
        $this->GOLES_EXTRA_EQUIPO2->TooltipValue = "";

        // NOTA_PARTIDO
        $this->NOTA_PARTIDO->LinkCustomAttributes = "";
        $this->NOTA_PARTIDO->HrefValue = "";
        $this->NOTA_PARTIDO->TooltipValue = "";

        // RESUMEN_PARTIDO
        $this->RESUMEN_PARTIDO->LinkCustomAttributes = "";
        $this->RESUMEN_PARTIDO->HrefValue = "";
        $this->RESUMEN_PARTIDO->TooltipValue = "";

        // ESTADO_PARTIDO
        $this->ESTADO_PARTIDO->LinkCustomAttributes = "";
        $this->ESTADO_PARTIDO->HrefValue = "";
        $this->ESTADO_PARTIDO->TooltipValue = "";

        // crea_dato
        $this->crea_dato->LinkCustomAttributes = "";
        $this->crea_dato->HrefValue = "";
        $this->crea_dato->TooltipValue = "";

        // modifica_dato
        $this->modifica_dato->LinkCustomAttributes = "";
        $this->modifica_dato->HrefValue = "";
        $this->modifica_dato->TooltipValue = "";

        // usuario_dato
        $this->usuario_dato->LinkCustomAttributes = "";
        $this->usuario_dato->HrefValue = "";
        $this->usuario_dato->TooltipValue = "";

        // automatico
        $this->automatico->LinkCustomAttributes = "";
        $this->automatico->HrefValue = "";
        $this->automatico->TooltipValue = "";

        // actualizado
        $this->actualizado->LinkCustomAttributes = "";
        $this->actualizado->HrefValue = "";
        $this->actualizado->TooltipValue = "";

        // Call Row Rendered event
        $this->rowRendered();

        // Save data for Custom Template
        $this->Rows[] = $this->customTemplateFieldValues();
    }

    // Render edit row values
    public function renderEditRow()
    {
        global $Security, $CurrentLanguage, $Language;

        // Call Row Rendering event
        $this->rowRendering();

        // ID_TORNEO
        $this->ID_TORNEO->setupEditAttributes();
        $this->ID_TORNEO->EditCustomAttributes = "";
        $curVal = trim(strval($this->ID_TORNEO->CurrentValue));
        if ($curVal != "") {
            $this->ID_TORNEO->ViewValue = $this->ID_TORNEO->lookupCacheOption($curVal);
        } else {
            $this->ID_TORNEO->ViewValue = $this->ID_TORNEO->Lookup !== null && is_array($this->ID_TORNEO->lookupOptions()) ? $curVal : null;
        }
        if ($this->ID_TORNEO->ViewValue !== null) { // Load from cache
            $this->ID_TORNEO->EditValue = array_values($this->ID_TORNEO->lookupOptions());
        } else { // Lookup from database
            $filterWrk = "";
            $sqlWrk = $this->ID_TORNEO->Lookup->getSql(true, $filterWrk, '', $this, false, true);
            $conn = Conn();
            $config = $conn->getConfiguration();
            $config->setResultCacheImpl($this->Cache);
            $rswrk = $conn->executeCacheQuery($sqlWrk, [], [], $this->CacheProfile)->fetchAll();
            $ari = count($rswrk);
            $arwrk = $rswrk;
            $this->ID_TORNEO->EditValue = $arwrk;
        }
        $this->ID_TORNEO->PlaceHolder = RemoveHtml($this->ID_TORNEO->caption());

        // equipo_local
        $this->equipo_local->setupEditAttributes();
        $this->equipo_local->EditCustomAttributes = "";
        $this->equipo_local->PlaceHolder = RemoveHtml($this->equipo_local->caption());

        // equipo_visitante
        $this->equipo_visitante->setupEditAttributes();
        $this->equipo_visitante->EditCustomAttributes = "";
        $this->equipo_visitante->PlaceHolder = RemoveHtml($this->equipo_visitante->caption());

        // ID_PARTIDO
        $this->ID_PARTIDO->setupEditAttributes();
        $this->ID_PARTIDO->EditCustomAttributes = "";
        $this->ID_PARTIDO->EditValue = $this->ID_PARTIDO->CurrentValue;
        $this->ID_PARTIDO->ViewCustomAttributes = "";

        // FECHA_PARTIDO
        $this->FECHA_PARTIDO->setupEditAttributes();
        $this->FECHA_PARTIDO->EditCustomAttributes = "";
        $this->FECHA_PARTIDO->EditValue = FormatDateTime($this->FECHA_PARTIDO->CurrentValue, $this->FECHA_PARTIDO->formatPattern());
        $this->FECHA_PARTIDO->PlaceHolder = RemoveHtml($this->FECHA_PARTIDO->caption());

        // HORA_PARTIDO
        $this->HORA_PARTIDO->setupEditAttributes();
        $this->HORA_PARTIDO->EditCustomAttributes = "";
        $this->HORA_PARTIDO->EditValue = FormatDateTime($this->HORA_PARTIDO->CurrentValue, $this->HORA_PARTIDO->formatPattern());
        $this->HORA_PARTIDO->PlaceHolder = RemoveHtml($this->HORA_PARTIDO->caption());

        // ESTADIO
        $this->ESTADIO->setupEditAttributes();
        $this->ESTADIO->EditCustomAttributes = "";
        $curVal = trim(strval($this->ESTADIO->CurrentValue));
        if ($curVal != "") {
            $this->ESTADIO->ViewValue = $this->ESTADIO->lookupCacheOption($curVal);
        } else {
            $this->ESTADIO->ViewValue = $this->ESTADIO->Lookup !== null && is_array($this->ESTADIO->lookupOptions()) ? $curVal : null;
        }
        if ($this->ESTADIO->ViewValue !== null) { // Load from cache
            $this->ESTADIO->EditValue = array_values($this->ESTADIO->lookupOptions());
        } else { // Lookup from database
            $filterWrk = "";
            $sqlWrk = $this->ESTADIO->Lookup->getSql(true, $filterWrk, '', $this, false, true);
            $conn = Conn();
            $config = $conn->getConfiguration();
            $config->setResultCacheImpl($this->Cache);
            $rswrk = $conn->executeCacheQuery($sqlWrk, [], [], $this->CacheProfile)->fetchAll();
            $ari = count($rswrk);
            $arwrk = $rswrk;
            $this->ESTADIO->EditValue = $arwrk;
        }
        $this->ESTADIO->PlaceHolder = RemoveHtml($this->ESTADIO->caption());

        // CIUDAD_PARTIDO
        $this->CIUDAD_PARTIDO->setupEditAttributes();
        $this->CIUDAD_PARTIDO->EditCustomAttributes = "";
        $this->CIUDAD_PARTIDO->EditValue = $this->CIUDAD_PARTIDO->CurrentValue;
        $this->CIUDAD_PARTIDO->PlaceHolder = RemoveHtml($this->CIUDAD_PARTIDO->caption());

        // PAIS_PARTIDO
        $this->PAIS_PARTIDO->setupEditAttributes();
        $this->PAIS_PARTIDO->EditCustomAttributes = "";
        $this->PAIS_PARTIDO->PlaceHolder = RemoveHtml($this->PAIS_PARTIDO->caption());

        // GOLES_LOCAL
        $this->GOLES_LOCAL->setupEditAttributes();
        $this->GOLES_LOCAL->EditCustomAttributes = "";
        $this->GOLES_LOCAL->EditValue = $this->GOLES_LOCAL->CurrentValue;
        $this->GOLES_LOCAL->PlaceHolder = RemoveHtml($this->GOLES_LOCAL->caption());
        if (strval($this->GOLES_LOCAL->EditValue) != "" && is_numeric($this->GOLES_LOCAL->EditValue)) {
            $this->GOLES_LOCAL->EditValue = FormatNumber($this->GOLES_LOCAL->EditValue, null);
        }

        // GOLES_VISITANTE
        $this->GOLES_VISITANTE->setupEditAttributes();
        $this->GOLES_VISITANTE->EditCustomAttributes = "";
        $this->GOLES_VISITANTE->EditValue = $this->GOLES_VISITANTE->CurrentValue;
        $this->GOLES_VISITANTE->PlaceHolder = RemoveHtml($this->GOLES_VISITANTE->caption());
        if (strval($this->GOLES_VISITANTE->EditValue) != "" && is_numeric($this->GOLES_VISITANTE->EditValue)) {
            $this->GOLES_VISITANTE->EditValue = FormatNumber($this->GOLES_VISITANTE->EditValue, null);
        }

        // GOLES_EXTRA_EQUIPO1
        $this->GOLES_EXTRA_EQUIPO1->setupEditAttributes();
        $this->GOLES_EXTRA_EQUIPO1->EditCustomAttributes = "";
        $this->GOLES_EXTRA_EQUIPO1->EditValue = $this->GOLES_EXTRA_EQUIPO1->CurrentValue;
        $this->GOLES_EXTRA_EQUIPO1->PlaceHolder = RemoveHtml($this->GOLES_EXTRA_EQUIPO1->caption());
        if (strval($this->GOLES_EXTRA_EQUIPO1->EditValue) != "" && is_numeric($this->GOLES_EXTRA_EQUIPO1->EditValue)) {
            $this->GOLES_EXTRA_EQUIPO1->EditValue = FormatNumber($this->GOLES_EXTRA_EQUIPO1->EditValue, null);
        }

        // GOLES_EXTRA_EQUIPO2
        $this->GOLES_EXTRA_EQUIPO2->setupEditAttributes();
        $this->GOLES_EXTRA_EQUIPO2->EditCustomAttributes = "";
        $this->GOLES_EXTRA_EQUIPO2->EditValue = $this->GOLES_EXTRA_EQUIPO2->CurrentValue;
        $this->GOLES_EXTRA_EQUIPO2->PlaceHolder = RemoveHtml($this->GOLES_EXTRA_EQUIPO2->caption());
        if (strval($this->GOLES_EXTRA_EQUIPO2->EditValue) != "" && is_numeric($this->GOLES_EXTRA_EQUIPO2->EditValue)) {
            $this->GOLES_EXTRA_EQUIPO2->EditValue = FormatNumber($this->GOLES_EXTRA_EQUIPO2->EditValue, null);
        }

        // NOTA_PARTIDO
        $this->NOTA_PARTIDO->setupEditAttributes();
        $this->NOTA_PARTIDO->EditCustomAttributes = "";
        $this->NOTA_PARTIDO->EditValue = $this->NOTA_PARTIDO->CurrentValue;
        $this->NOTA_PARTIDO->PlaceHolder = RemoveHtml($this->NOTA_PARTIDO->caption());

        // RESUMEN_PARTIDO
        $this->RESUMEN_PARTIDO->setupEditAttributes();
        $this->RESUMEN_PARTIDO->EditCustomAttributes = "";
        $this->RESUMEN_PARTIDO->EditValue = $this->RESUMEN_PARTIDO->CurrentValue;
        $this->RESUMEN_PARTIDO->PlaceHolder = RemoveHtml($this->RESUMEN_PARTIDO->caption());

        // ESTADO_PARTIDO
        $this->ESTADO_PARTIDO->setupEditAttributes();
        $this->ESTADO_PARTIDO->EditCustomAttributes = "";
        $this->ESTADO_PARTIDO->EditValue = $this->ESTADO_PARTIDO->options(true);
        $this->ESTADO_PARTIDO->PlaceHolder = RemoveHtml($this->ESTADO_PARTIDO->caption());

        // crea_dato
        $this->crea_dato->setupEditAttributes();
        $this->crea_dato->EditCustomAttributes = "";
        $this->crea_dato->EditValue = $this->crea_dato->CurrentValue;
        $this->crea_dato->EditValue = FormatDateTime($this->crea_dato->EditValue, $this->crea_dato->formatPattern());
        $this->crea_dato->ViewCustomAttributes = "";

        // modifica_dato
        $this->modifica_dato->setupEditAttributes();
        $this->modifica_dato->EditCustomAttributes = "";
        $this->modifica_dato->EditValue = $this->modifica_dato->CurrentValue;
        $this->modifica_dato->EditValue = FormatDateTime($this->modifica_dato->EditValue, $this->modifica_dato->formatPattern());
        $this->modifica_dato->ViewCustomAttributes = "";

        // usuario_dato

        // automatico
        $this->automatico->EditCustomAttributes = "";
        $this->automatico->EditValue = $this->automatico->options(false);
        $this->automatico->PlaceHolder = RemoveHtml($this->automatico->caption());

        // actualizado
        $this->actualizado->setupEditAttributes();
        $this->actualizado->EditCustomAttributes = "";

        // Call Row Rendered event
        $this->rowRendered();
    }

    // Aggregate list row values
    public function aggregateListRowValues()
    {
    }

    // Aggregate list row (for rendering)
    public function aggregateListRow()
    {
        // Call Row Rendered event
        $this->rowRendered();
    }

    // Export data in HTML/CSV/Word/Excel/Email/PDF format
    public function exportDocument($doc, $recordset, $startRec = 1, $stopRec = 1, $exportPageType = "")
    {
        if (!$recordset || !$doc) {
            return;
        }
        if (!$doc->ExportCustom) {
            // Write header
            $doc->exportTableHeader();
            if ($doc->Horizontal) { // Horizontal format, write header
                $doc->beginExportRow();
                if ($exportPageType == "view") {
                    $doc->exportCaption($this->ID_TORNEO);
                    $doc->exportCaption($this->equipo_local);
                    $doc->exportCaption($this->equipo_visitante);
                    $doc->exportCaption($this->ID_PARTIDO);
                    $doc->exportCaption($this->FECHA_PARTIDO);
                    $doc->exportCaption($this->HORA_PARTIDO);
                    $doc->exportCaption($this->ESTADIO);
                    $doc->exportCaption($this->CIUDAD_PARTIDO);
                    $doc->exportCaption($this->PAIS_PARTIDO);
                    $doc->exportCaption($this->GOLES_LOCAL);
                    $doc->exportCaption($this->GOLES_VISITANTE);
                    $doc->exportCaption($this->GOLES_EXTRA_EQUIPO1);
                    $doc->exportCaption($this->GOLES_EXTRA_EQUIPO2);
                    $doc->exportCaption($this->NOTA_PARTIDO);
                    $doc->exportCaption($this->RESUMEN_PARTIDO);
                    $doc->exportCaption($this->ESTADO_PARTIDO);
                    $doc->exportCaption($this->crea_dato);
                    $doc->exportCaption($this->modifica_dato);
                    $doc->exportCaption($this->usuario_dato);
                    $doc->exportCaption($this->automatico);
                    $doc->exportCaption($this->actualizado);
                } else {
                    $doc->exportCaption($this->equipo_local);
                    $doc->exportCaption($this->equipo_visitante);
                    $doc->exportCaption($this->ID_PARTIDO);
                    $doc->exportCaption($this->GOLES_LOCAL);
                    $doc->exportCaption($this->GOLES_VISITANTE);
                    $doc->exportCaption($this->GOLES_EXTRA_EQUIPO1);
                    $doc->exportCaption($this->GOLES_EXTRA_EQUIPO2);
                    $doc->exportCaption($this->NOTA_PARTIDO);
                    $doc->exportCaption($this->RESUMEN_PARTIDO);
                    $doc->exportCaption($this->ESTADO_PARTIDO);
                    $doc->exportCaption($this->crea_dato);
                    $doc->exportCaption($this->modifica_dato);
                    $doc->exportCaption($this->usuario_dato);
                    $doc->exportCaption($this->automatico);
                    $doc->exportCaption($this->actualizado);
                }
                $doc->endExportRow();
            }
        }

        // Move to first record
        $recCnt = $startRec - 1;
        $stopRec = ($stopRec > 0) ? $stopRec : PHP_INT_MAX;
        while (!$recordset->EOF && $recCnt < $stopRec) {
            $row = $recordset->fields;
            $recCnt++;
            if ($recCnt >= $startRec) {
                $rowCnt = $recCnt - $startRec + 1;

                // Page break
                if ($this->ExportPageBreakCount > 0) {
                    if ($rowCnt > 1 && ($rowCnt - 1) % $this->ExportPageBreakCount == 0) {
                        $doc->exportPageBreak();
                    }
                }
                $this->loadListRowValues($row);

                // Render row
                $this->RowType = ROWTYPE_VIEW; // Render view
                $this->resetAttributes();
                $this->renderListRow();
                if (!$doc->ExportCustom) {
                    $doc->beginExportRow($rowCnt); // Allow CSS styles if enabled
                    if ($exportPageType == "view") {
                        $doc->exportField($this->ID_TORNEO);
                        $doc->exportField($this->equipo_local);
                        $doc->exportField($this->equipo_visitante);
                        $doc->exportField($this->ID_PARTIDO);
                        $doc->exportField($this->FECHA_PARTIDO);
                        $doc->exportField($this->HORA_PARTIDO);
                        $doc->exportField($this->ESTADIO);
                        $doc->exportField($this->CIUDAD_PARTIDO);
                        $doc->exportField($this->PAIS_PARTIDO);
                        $doc->exportField($this->GOLES_LOCAL);
                        $doc->exportField($this->GOLES_VISITANTE);
                        $doc->exportField($this->GOLES_EXTRA_EQUIPO1);
                        $doc->exportField($this->GOLES_EXTRA_EQUIPO2);
                        $doc->exportField($this->NOTA_PARTIDO);
                        $doc->exportField($this->RESUMEN_PARTIDO);
                        $doc->exportField($this->ESTADO_PARTIDO);
                        $doc->exportField($this->crea_dato);
                        $doc->exportField($this->modifica_dato);
                        $doc->exportField($this->usuario_dato);
                        $doc->exportField($this->automatico);
                        $doc->exportField($this->actualizado);
                    } else {
                        $doc->exportField($this->equipo_local);
                        $doc->exportField($this->equipo_visitante);
                        $doc->exportField($this->ID_PARTIDO);
                        $doc->exportField($this->GOLES_LOCAL);
                        $doc->exportField($this->GOLES_VISITANTE);
                        $doc->exportField($this->GOLES_EXTRA_EQUIPO1);
                        $doc->exportField($this->GOLES_EXTRA_EQUIPO2);
                        $doc->exportField($this->NOTA_PARTIDO);
                        $doc->exportField($this->RESUMEN_PARTIDO);
                        $doc->exportField($this->ESTADO_PARTIDO);
                        $doc->exportField($this->crea_dato);
                        $doc->exportField($this->modifica_dato);
                        $doc->exportField($this->usuario_dato);
                        $doc->exportField($this->automatico);
                        $doc->exportField($this->actualizado);
                    }
                    $doc->endExportRow($rowCnt);
                }
            }

            // Call Row Export server event
            if ($doc->ExportCustom) {
                $this->ExportDoc = &$doc;
                $this->rowExport($row);
            }
            $recordset->moveNext();
        }
        if (!$doc->ExportCustom) {
            $doc->exportTableFooter();
        }
    }

    // Get file data
    public function getFileData($fldparm, $key, $resize, $width = 0, $height = 0, $plugins = [])
    {
        global $DownloadFileName;

        // No binary fields
        return false;
    }

    // Table level events

    // Recordset Selecting event
    public function recordsetSelecting(&$filter)
    {
        // Enter your code here
    }

    // Recordset Selected event
    public function recordsetSelected(&$rs)
    {
        //Log("Recordset Selected");
    }

    // Recordset Search Validated event
    public function recordsetSearchValidated()
    {
        // Example:
        //$this->MyField1->AdvancedSearch->SearchValue = "your search criteria"; // Search value
    }

    // Recordset Searching event
    public function recordsetSearching(&$filter)
    {
        // Enter your code here
    }

    // Row_Selecting event
    public function rowSelecting(&$filter)
    {
        // Enter your code here
    }

    // Row Selected event
    public function rowSelected(&$rs)
    {
        //Log("Row Selected");
    }

    // Row Inserting event
    public function rowInserting($rsold, &$rsnew)
    {
        // Enter your code here
        // To cancel, set return value to false
        return true;
    }

    // Row Inserted event
    public function rowInserted($rsold, &$rsnew)
    {
    }

    // Row Updating event
    public function rowUpdating($rsold, &$rsnew)
    {
        // Enter your code here
        // To cancel, set return value to false
        return true;
    }

    // Row Updated event
    public function rowUpdated($rsold, &$rsnew)
    {
       if (ConvertToBool($this->automatico->CurrentValue) && $this->ESTADO_PARTIDO->CurrentValue==='Finalizado') {
                //GOLES LOCAL 
                $id_equipo_local= $this->equipo_local->CurrentValue;
                $GOLES_LOCAL1=$this->GOLES_LOCAL->CurrentValue;
                $GOLES_EXTRA_EQUIPO11=$this->GOLES_EXTRA_EQUIPO1->CurrentValue;            
                $GOLES_TOTALES_LOCAL= $GOLES_LOCAL1+$GOLES_EXTRA_EQUIPO11;

                //GOLES VISITANTE
                $id_equipo_visitante= $this->equipo_visitante->CurrentValue;
                $GOLES_VISITANTE1=$this->GOLES_VISITANTE->CurrentValue;          
                $GOLES_EXTRA_EQUIPO22=$this->GOLES_EXTRA_EQUIPO2->CurrentValue;             
                $GOLES_TOTALES_VISITANTE= $GOLES_VISITANTE1 +$GOLES_EXTRA_EQUIPO22;

             //   $this->setSuccessMessage("Equipo local: es el numero ". $id_equipo_local."-".$GOLES_TOTALES_LOCAL." Equipo Visitante: es el numero ".$id_equipo_visitante."-".$GOLES_TOTALES_VISITANTE);

             //TABLA EQUIPO TORNEO LOCAL
                  $sql = "
               SELECT * FROM equipotorneo
                 WHERE ID_EQUIPO='". $id_equipo_local. "' ; ";
                 $response= ExecuteRow($sql);
                $goles_favor_local=$response["GF"];
                $goles_contra_local=$response["GC"];
                $goles_diferencia_local=$response["GD"];
                $partidos_jugados_local=$response["PARTIDOS_JUGADOS"];
                $partidos_ganados_local=$response["PARTIDOS_GANADOS"];
                $partidos_empatados_local=$response["PARTIDOS_EMPATADOS"];
                $partidos_perdidos_local=$response["PARTIDOS_PERDIDOS"];

             //TABLA EQUIPO TORNEO VISITANTE
                 $sql2 = "
               SELECT * FROM equipotorneo
                 WHERE ID_EQUIPO='". $id_equipo_visitante. "' ; ";
                 $response= ExecuteRow($sql2);
                $goles_favor_visitante=$response["GF"];
                $goles_contra_visitante=$response["GC"];
                $goles_diferencia_visitante=$response["GD"];
                $partidos_jugados_visitante=$response["PARTIDOS_JUGADOS"];
                $partidos_ganados_visitante=$response["PARTIDOS_GANADOS"];
                $partidos_empatados_visitante=$response["PARTIDOS_EMPATADOS"];
                $partidos_perdidos_visitante=$response["PARTIDOS_PERDIDOS"];

                //CALCULOS
                $goles_favor_local+=$GOLES_TOTALES_LOCAL;
                $goles_contra_local+=$GOLES_TOTALES_VISITANTE;
                $goles_diferencia_local= $goles_favor_local-$goles_contra_local;
                $goles_favor_visitante+=$GOLES_TOTALES_VISITANTE;
                $goles_contra_visitante+=$GOLES_TOTALES_LOCAL;
                $goles_diferencia_visitante= $goles_favor_visitante-$goles_contra_visitante;
                if($GOLES_TOTALES_LOCAL> $GOLES_TOTALES_VISITANTE){
                    $partidos_jugados_local++;
                    $partidos_jugados_visitante++;
                    $partidos_ganados_local++;
                    $partidos_perdidos_visitante++;                 
                }else if($GOLES_TOTALES_LOCAL< $GOLES_TOTALES_VISITANTE){
                    $partidos_jugados_local++;
                    $partidos_jugados_visitante++;
                    $partidos_ganados_visitante++;
                    $partidos_perdidos_local++;
                }else if($GOLES_TOTALES_LOCAL== $GOLES_TOTALES_VISITANTE){
                    $partidos_jugados_local++;
                    $partidos_jugados_visitante++;
                    $partidos_empatados_local++;
                    $partidos_empatados_visitante++;
                }    
                 $sql_local = "
                 UPDATE equipotorneo
                 SET
                PARTIDOS_JUGADOS = '". $partidos_jugados_local. "',
                PARTIDOS_GANADOS = '". $partidos_ganados_local. "', 
                PARTIDOS_EMPATADOS = '". $partidos_empatados_local. "', 
                PARTIDOS_PERDIDOS = '". $partidos_perdidos_local. "',
                GF = '". $goles_favor_local. "',
                GC = '".  $goles_contra_local. "',
                GD = '".  $goles_diferencia_local. "'
                WHERE ID_EQUIPO ='". $id_equipo_local. "';
                ";
                 Execute($sql_local);
                 $sql_visitante = "
                 UPDATE equipotorneo
                 SET
                PARTIDOS_JUGADOS = '". $partidos_jugados_visitante. "',
                PARTIDOS_GANADOS = '". $partidos_ganados_visitante. "', 
                PARTIDOS_EMPATADOS = '". $partidos_empatados_visitante. "', 
                PARTIDOS_PERDIDOS = '". $partidos_perdidos_visitante. "',
                GF = '". $goles_favor_visitante. "',
                GC = '".  $goles_contra_visitante. "',
                GD = '".  $goles_diferencia_visitante. "'
                WHERE ID_EQUIPO ='". $id_equipo_visitante. "';
                ";
                 Execute($sql_visitante);
                     $sql_final = "
                     UPDATE partidos
                     SET
                    actualizado = '1'                
                    WHERE ID_PARTIDO ='".  $this->ID_PARTIDO->CurrentValue. "';
                    ";
                     Execute( $sql_final);
                     $this->setSuccessMessage("Actualizado automáticamente");
                }
    }

    // Row Update Conflict event
    public function rowUpdateConflict($rsold, &$rsnew)
    {
        // Enter your code here
        // To ignore conflict, set return value to false
        return true;
    }

    // Grid Inserting event
    public function gridInserting()
    {
        // Enter your code here
        // To reject grid insert, set return value to false
        return true;
    }

    // Grid Inserted event
    public function gridInserted($rsnew)
    {
        //Log("Grid Inserted");
    }

    // Grid Updating event
    public function gridUpdating($rsold)
    {
        // Enter your code here
        // To reject grid update, set return value to false
        return true;
    }

    // Grid Updated event
    public function gridUpdated($rsold, $rsnew)
    {
        //Log("Grid Updated");
    }

    // Row Deleting event
    public function rowDeleting(&$rs)
    {
        // Enter your code here
        // To cancel, set return value to False
        return true;
    }

    // Row Deleted event
    public function rowDeleted(&$rs)
    {
        //Log("Row Deleted");
    }

    // Email Sending event
    public function emailSending($email, &$args)
    {
        //var_dump($email, $args); exit();
        return true;
    }

    // Lookup Selecting event
    public function lookupSelecting($fld, &$filter)
    {
        //var_dump($fld->Name, $fld->Lookup, $filter); // Uncomment to view the filter
        // Enter your code here
    }

    // Row Rendering event
    public function rowRendering()
    {
        // Enter your code here
    }

    // Row Rendered event
    public function rowRendered()
    {
        // To view properties of field class, use:
        //var_dump($this-><FieldName>);
    }

    // User ID Filtering event
    public function userIdFiltering(&$filter)
    {
        // Enter your code here
    }
}
