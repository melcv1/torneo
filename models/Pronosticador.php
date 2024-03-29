<?php

namespace PHPMaker2022\project11;

use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\FetchMode;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * Table class for pronosticador
 */
class Pronosticador extends DbTable
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

    // Audit trail
    public $AuditTrailOnAdd = true;
    public $AuditTrailOnEdit = true;
    public $AuditTrailOnDelete = true;
    public $AuditTrailOnView = false;
    public $AuditTrailOnViewData = false;
    public $AuditTrailOnSearch = false;

    // Export
    public $ExportDoc;

    // Fields
    public $ID_ENCUESTA;
    public $ID_EQUIPOTORNEO;
    public $EQUIPO;
    public $GRUPO;
    public $POSICION;
    public $NUMERACION;
    public $crea_dato;
    public $modifica_dato;
    public $usuario_dato;
    public $ID_PARTICIPANTE;

    // Page ID
    public $PageID = ""; // To be overridden by subclass

    // Constructor
    public function __construct()
    {
        global $Language, $CurrentLanguage, $CurrentLocale;
        parent::__construct();

        // Language object
        $Language = Container("language");
        $this->TableVar = 'pronosticador';
        $this->TableName = 'pronosticador';
        $this->TableType = 'TABLE';

        // Update Table
        $this->UpdateTable = "`pronosticador`";
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

        // ID_ENCUESTA
        $this->ID_ENCUESTA = new DbField(
            'pronosticador',
            'pronosticador',
            'x_ID_ENCUESTA',
            'ID_ENCUESTA',
            '`ID_ENCUESTA`',
            '`ID_ENCUESTA`',
            3,
            11,
            -1,
            false,
            '`ID_ENCUESTA`',
            false,
            false,
            false,
            'FORMATTED TEXT',
            'NO'
        );
        $this->ID_ENCUESTA->InputTextType = "text";
        $this->ID_ENCUESTA->IsAutoIncrement = true; // Autoincrement field
        $this->ID_ENCUESTA->IsPrimaryKey = true; // Primary key field
        $this->ID_ENCUESTA->DefaultErrorMessage = $Language->phrase("IncorrectInteger");
        $this->Fields['ID_ENCUESTA'] = &$this->ID_ENCUESTA;

        // ID_EQUIPOTORNEO
        $this->ID_EQUIPOTORNEO = new DbField(
            'pronosticador',
            'pronosticador',
            'x_ID_EQUIPOTORNEO',
            'ID_EQUIPOTORNEO',
            '`ID_EQUIPOTORNEO`',
            '`ID_EQUIPOTORNEO`',
            3,
            11,
            -1,
            false,
            '`ID_EQUIPOTORNEO`',
            false,
            false,
            false,
            'FORMATTED TEXT',
            'SELECT'
        );
        $this->ID_EQUIPOTORNEO->InputTextType = "text";
        $this->ID_EQUIPOTORNEO->Nullable = false; // NOT NULL field
        $this->ID_EQUIPOTORNEO->Required = true; // Required field
        $this->ID_EQUIPOTORNEO->UsePleaseSelect = true; // Use PleaseSelect by default
        $this->ID_EQUIPOTORNEO->PleaseSelectText = $Language->phrase("PleaseSelect"); // "PleaseSelect" text
        switch ($CurrentLanguage) {
            case "en-US":
                $this->ID_EQUIPOTORNEO->Lookup = new Lookup('ID_EQUIPOTORNEO', 'equipotorneo', false, 'ID_EQUIPO_TORNEO', ["ID_EQUIPO","GRUPO","",""], [], ["x_GRUPO"], [], [], [], [], '', '', "CONCAT(COALESCE(`ID_EQUIPO`, ''),'" . ValueSeparator(1, $this->ID_EQUIPOTORNEO) . "',COALESCE(`GRUPO`,''))");
                break;
            default:
                $this->ID_EQUIPOTORNEO->Lookup = new Lookup('ID_EQUIPOTORNEO', 'equipotorneo', false, 'ID_EQUIPO_TORNEO', ["ID_EQUIPO","GRUPO","",""], [], ["x_GRUPO"], [], [], [], [], '', '', "CONCAT(COALESCE(`ID_EQUIPO`, ''),'" . ValueSeparator(1, $this->ID_EQUIPOTORNEO) . "',COALESCE(`GRUPO`,''))");
                break;
        }
        $this->ID_EQUIPOTORNEO->DefaultErrorMessage = $Language->phrase("IncorrectInteger");
        $this->Fields['ID_EQUIPOTORNEO'] = &$this->ID_EQUIPOTORNEO;

        // EQUIPO
        $this->EQUIPO = new DbField(
            'pronosticador',
            'pronosticador',
            'x_EQUIPO',
            'EQUIPO',
            '`EQUIPO`',
            '`EQUIPO`',
            201,
            256,
            -1,
            false,
            '`EV__EQUIPO`',
            true,
            true,
            true,
            'FORMATTED TEXT',
            'SELECT'
        );
        $this->EQUIPO->InputTextType = "text";
        $this->EQUIPO->UsePleaseSelect = true; // Use PleaseSelect by default
        $this->EQUIPO->PleaseSelectText = $Language->phrase("PleaseSelect"); // "PleaseSelect" text
        switch ($CurrentLanguage) {
            case "en-US":
                $this->EQUIPO->Lookup = new Lookup('EQUIPO', 'equipo', false, 'NOM_EQUIPO_CORTO', ["NOM_EQUIPO_CORTO","","",""], [], [], [], [], [], [], '', '', "`NOM_EQUIPO_CORTO`");
                break;
            default:
                $this->EQUIPO->Lookup = new Lookup('EQUIPO', 'equipo', false, 'NOM_EQUIPO_CORTO', ["NOM_EQUIPO_CORTO","","",""], [], [], [], [], [], [], '', '', "`NOM_EQUIPO_CORTO`");
                break;
        }
        $this->Fields['EQUIPO'] = &$this->EQUIPO;

        // GRUPO
        $this->GRUPO = new DbField(
            'pronosticador',
            'pronosticador',
            'x_GRUPO',
            'GRUPO',
            '`GRUPO`',
            '`GRUPO`',
            200,
            1,
            -1,
            false,
            '`GRUPO`',
            false,
            false,
            false,
            'FORMATTED TEXT',
            'SELECT'
        );
        $this->GRUPO->InputTextType = "text";
        $this->GRUPO->UsePleaseSelect = true; // Use PleaseSelect by default
        $this->GRUPO->PleaseSelectText = $Language->phrase("PleaseSelect"); // "PleaseSelect" text
        switch ($CurrentLanguage) {
            case "en-US":
                $this->GRUPO->Lookup = new Lookup('GRUPO', 'equipotorneo', false, 'GRUPO', ["GRUPO","","",""], ["x_ID_EQUIPOTORNEO"], [], ["ID_EQUIPO_TORNEO"], ["x_ID_EQUIPO_TORNEO"], [], [], '', '', "`GRUPO`");
                break;
            default:
                $this->GRUPO->Lookup = new Lookup('GRUPO', 'equipotorneo', false, 'GRUPO', ["GRUPO","","",""], ["x_ID_EQUIPOTORNEO"], [], ["ID_EQUIPO_TORNEO"], ["x_ID_EQUIPO_TORNEO"], [], [], '', '', "`GRUPO`");
                break;
        }
        $this->Fields['GRUPO'] = &$this->GRUPO;

        // POSICION
        $this->POSICION = new DbField(
            'pronosticador',
            'pronosticador',
            'x_POSICION',
            'POSICION',
            '`POSICION`',
            '`POSICION`',
            201,
            256,
            -1,
            false,
            '`POSICION`',
            false,
            false,
            false,
            'FORMATTED TEXT',
            'SELECT'
        );
        $this->POSICION->InputTextType = "text";
        $this->POSICION->UsePleaseSelect = true; // Use PleaseSelect by default
        $this->POSICION->PleaseSelectText = $Language->phrase("PleaseSelect"); // "PleaseSelect" text
        switch ($CurrentLanguage) {
            case "en-US":
                $this->POSICION->Lookup = new Lookup('POSICION', 'pronosticador', false, '', ["","","",""], [], [], [], [], [], [], '', '', "");
                break;
            default:
                $this->POSICION->Lookup = new Lookup('POSICION', 'pronosticador', false, '', ["","","",""], [], [], [], [], [], [], '', '', "");
                break;
        }
        $this->POSICION->OptionCount = 5;
        $this->Fields['POSICION'] = &$this->POSICION;

        // NUMERACION
        $this->NUMERACION = new DbField(
            'pronosticador',
            'pronosticador',
            'x_NUMERACION',
            'NUMERACION',
            '`NUMERACION`',
            '`NUMERACION`',
            200,
            4,
            -1,
            false,
            '`NUMERACION`',
            false,
            false,
            false,
            'FORMATTED TEXT',
            'SELECT'
        );
        $this->NUMERACION->InputTextType = "text";
        $this->NUMERACION->UsePleaseSelect = true; // Use PleaseSelect by default
        $this->NUMERACION->PleaseSelectText = $Language->phrase("PleaseSelect"); // "PleaseSelect" text
        switch ($CurrentLanguage) {
            case "en-US":
                $this->NUMERACION->Lookup = new Lookup('NUMERACION', 'pronosticador', false, '', ["","","",""], [], [], [], [], [], [], '', '', "");
                break;
            default:
                $this->NUMERACION->Lookup = new Lookup('NUMERACION', 'pronosticador', false, '', ["","","",""], [], [], [], [], [], [], '', '', "");
                break;
        }
        $this->NUMERACION->OptionCount = 31;
        $this->Fields['NUMERACION'] = &$this->NUMERACION;

        // crea_dato
        $this->crea_dato = new DbField(
            'pronosticador',
            'pronosticador',
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
            'pronosticador',
            'pronosticador',
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
            'pronosticador',
            'pronosticador',
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
            'TEXTAREA'
        );
        $this->usuario_dato->InputTextType = "text";
        $this->Fields['usuario_dato'] = &$this->usuario_dato;

        // ID_PARTICIPANTE
        $this->ID_PARTICIPANTE = new DbField(
            'pronosticador',
            'pronosticador',
            'x_ID_PARTICIPANTE',
            'ID_PARTICIPANTE',
            '`ID_PARTICIPANTE`',
            '`ID_PARTICIPANTE`',
            3,
            11,
            -1,
            false,
            '`ID_PARTICIPANTE`',
            false,
            false,
            false,
            'FORMATTED TEXT',
            'SELECT'
        );
        $this->ID_PARTICIPANTE->InputTextType = "text";
        $this->ID_PARTICIPANTE->UsePleaseSelect = true; // Use PleaseSelect by default
        $this->ID_PARTICIPANTE->PleaseSelectText = $Language->phrase("PleaseSelect"); // "PleaseSelect" text
        switch ($CurrentLanguage) {
            case "en-US":
                $this->ID_PARTICIPANTE->Lookup = new Lookup('ID_PARTICIPANTE', 'participante', false, 'ID_PARTICIPANTE', ["NOMBRE","APELLIDO","",""], [], [], [], [], [], [], '', '', "CONCAT(COALESCE(`NOMBRE`, ''),'" . ValueSeparator(1, $this->ID_PARTICIPANTE) . "',COALESCE(`APELLIDO`,''))");
                break;
            default:
                $this->ID_PARTICIPANTE->Lookup = new Lookup('ID_PARTICIPANTE', 'participante', false, 'ID_PARTICIPANTE', ["NOMBRE","APELLIDO","",""], [], [], [], [], [], [], '', '', "CONCAT(COALESCE(`NOMBRE`, ''),'" . ValueSeparator(1, $this->ID_PARTICIPANTE) . "',COALESCE(`APELLIDO`,''))");
                break;
        }
        $this->ID_PARTICIPANTE->DefaultErrorMessage = $Language->phrase("IncorrectInteger");
        $this->Fields['ID_PARTICIPANTE'] = &$this->ID_PARTICIPANTE;

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
        return ($this->SqlFrom != "") ? $this->SqlFrom : "`pronosticador`";
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
        $from = "(SELECT *, (SELECT `NOM_EQUIPO_CORTO` FROM `equipo` `TMP_LOOKUPTABLE` WHERE `TMP_LOOKUPTABLE`.`NOM_EQUIPO_CORTO` = `pronosticador`.`EQUIPO` LIMIT 1) AS `EV__EQUIPO` FROM `pronosticador`)";
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
        if ($this->BasicSearch->getKeyword() != "") {
            return true;
        }
        if (
            $this->EQUIPO->AdvancedSearch->SearchValue != "" ||
            $this->EQUIPO->AdvancedSearch->SearchValue2 != "" ||
            ContainsString($where, " " . $this->EQUIPO->VirtualExpression . " ")
        ) {
            return true;
        }
        if (ContainsString($orderBy, " " . $this->EQUIPO->VirtualExpression . " ")) {
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
            $this->ID_ENCUESTA->setDbValue($conn->lastInsertId());
            $rs['ID_ENCUESTA'] = $this->ID_ENCUESTA->DbValue;
            if ($this->AuditTrailOnAdd) {
                $this->writeAuditTrailOnAdd($rs);
            }
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
        if ($success && $this->AuditTrailOnEdit && $rsold) {
            $rsaudit = $rs;
            $fldname = 'ID_ENCUESTA';
            if (!array_key_exists($fldname, $rsaudit)) {
                $rsaudit[$fldname] = $rsold[$fldname];
            }
            $this->writeAuditTrailOnEdit($rsold, $rsaudit);
        }
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
            if (array_key_exists('ID_ENCUESTA', $rs)) {
                AddFilter($where, QuotedName('ID_ENCUESTA', $this->Dbid) . '=' . QuotedValue($rs['ID_ENCUESTA'], $this->ID_ENCUESTA->DataType, $this->Dbid));
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
        if ($success && $this->AuditTrailOnDelete) {
            $this->writeAuditTrailOnDelete($rs);
        }
        return $success;
    }

    // Load DbValue from recordset or array
    protected function loadDbValues($row)
    {
        if (!is_array($row)) {
            return;
        }
        $this->ID_ENCUESTA->DbValue = $row['ID_ENCUESTA'];
        $this->ID_EQUIPOTORNEO->DbValue = $row['ID_EQUIPOTORNEO'];
        $this->EQUIPO->DbValue = $row['EQUIPO'];
        $this->GRUPO->DbValue = $row['GRUPO'];
        $this->POSICION->DbValue = $row['POSICION'];
        $this->NUMERACION->DbValue = $row['NUMERACION'];
        $this->crea_dato->DbValue = $row['crea_dato'];
        $this->modifica_dato->DbValue = $row['modifica_dato'];
        $this->usuario_dato->DbValue = $row['usuario_dato'];
        $this->ID_PARTICIPANTE->DbValue = $row['ID_PARTICIPANTE'];
    }

    // Delete uploaded files
    public function deleteUploadedFiles($row)
    {
        $this->loadDbValues($row);
    }

    // Record filter WHERE clause
    protected function sqlKeyFilter()
    {
        return "`ID_ENCUESTA` = @ID_ENCUESTA@";
    }

    // Get Key
    public function getKey($current = false)
    {
        $keys = [];
        $val = $current ? $this->ID_ENCUESTA->CurrentValue : $this->ID_ENCUESTA->OldValue;
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
                $this->ID_ENCUESTA->CurrentValue = $keys[0];
            } else {
                $this->ID_ENCUESTA->OldValue = $keys[0];
            }
        }
    }

    // Get record filter
    public function getRecordFilter($row = null)
    {
        $keyFilter = $this->sqlKeyFilter();
        if (is_array($row)) {
            $val = array_key_exists('ID_ENCUESTA', $row) ? $row['ID_ENCUESTA'] : null;
        } else {
            $val = $this->ID_ENCUESTA->OldValue !== null ? $this->ID_ENCUESTA->OldValue : $this->ID_ENCUESTA->CurrentValue;
        }
        if (!is_numeric($val)) {
            return "0=1"; // Invalid key
        }
        if ($val === null) {
            return "0=1"; // Invalid key
        } else {
            $keyFilter = str_replace("@ID_ENCUESTA@", AdjustSql($val, $this->Dbid), $keyFilter); // Replace key value
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
        return $_SESSION[$name] ?? GetUrl("pronosticadorlist");
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
        if ($pageName == "pronosticadorview") {
            return $Language->phrase("View");
        } elseif ($pageName == "pronosticadoredit") {
            return $Language->phrase("Edit");
        } elseif ($pageName == "pronosticadoradd") {
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
                return "PronosticadorView";
            case Config("API_ADD_ACTION"):
                return "PronosticadorAdd";
            case Config("API_EDIT_ACTION"):
                return "PronosticadorEdit";
            case Config("API_DELETE_ACTION"):
                return "PronosticadorDelete";
            case Config("API_LIST_ACTION"):
                return "PronosticadorList";
            default:
                return "";
        }
    }

    // List URL
    public function getListUrl()
    {
        return "pronosticadorlist";
    }

    // View URL
    public function getViewUrl($parm = "")
    {
        if ($parm != "") {
            $url = $this->keyUrl("pronosticadorview", $this->getUrlParm($parm));
        } else {
            $url = $this->keyUrl("pronosticadorview", $this->getUrlParm(Config("TABLE_SHOW_DETAIL") . "="));
        }
        return $this->addMasterUrl($url);
    }

    // Add URL
    public function getAddUrl($parm = "")
    {
        if ($parm != "") {
            $url = "pronosticadoradd?" . $this->getUrlParm($parm);
        } else {
            $url = "pronosticadoradd";
        }
        return $this->addMasterUrl($url);
    }

    // Edit URL
    public function getEditUrl($parm = "")
    {
        $url = $this->keyUrl("pronosticadoredit", $this->getUrlParm($parm));
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
        $url = $this->keyUrl("pronosticadoradd", $this->getUrlParm($parm));
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
        return $this->keyUrl("pronosticadordelete", $this->getUrlParm());
    }

    // Add master url
    public function addMasterUrl($url)
    {
        return $url;
    }

    public function keyToJson($htmlEncode = false)
    {
        $json = "";
        $json .= "\"ID_ENCUESTA\":" . JsonEncode($this->ID_ENCUESTA->CurrentValue, "number");
        $json = "{" . $json . "}";
        if ($htmlEncode) {
            $json = HtmlEncode($json);
        }
        return $json;
    }

    // Add key value to URL
    public function keyUrl($url, $parm = "")
    {
        if ($this->ID_ENCUESTA->CurrentValue !== null) {
            $url .= "/" . $this->encodeKeyValue($this->ID_ENCUESTA->CurrentValue);
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
            if (($keyValue = Param("ID_ENCUESTA") ?? Route("ID_ENCUESTA")) !== null) {
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
                $this->ID_ENCUESTA->CurrentValue = $key;
            } else {
                $this->ID_ENCUESTA->OldValue = $key;
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
        $this->ID_ENCUESTA->setDbValue($row['ID_ENCUESTA']);
        $this->ID_EQUIPOTORNEO->setDbValue($row['ID_EQUIPOTORNEO']);
        $this->EQUIPO->setDbValue($row['EQUIPO']);
        $this->GRUPO->setDbValue($row['GRUPO']);
        $this->POSICION->setDbValue($row['POSICION']);
        $this->NUMERACION->setDbValue($row['NUMERACION']);
        $this->crea_dato->setDbValue($row['crea_dato']);
        $this->modifica_dato->setDbValue($row['modifica_dato']);
        $this->usuario_dato->setDbValue($row['usuario_dato']);
        $this->ID_PARTICIPANTE->setDbValue($row['ID_PARTICIPANTE']);
    }

    // Render list row values
    public function renderListRow()
    {
        global $Security, $CurrentLanguage, $Language;

        // Call Row Rendering event
        $this->rowRendering();

        // Common render codes

        // ID_ENCUESTA

        // ID_EQUIPOTORNEO

        // EQUIPO

        // GRUPO

        // POSICION

        // NUMERACION

        // crea_dato

        // modifica_dato

        // usuario_dato

        // ID_PARTICIPANTE

        // ID_ENCUESTA
        $this->ID_ENCUESTA->ViewValue = $this->ID_ENCUESTA->CurrentValue;
        $this->ID_ENCUESTA->ViewCustomAttributes = "";

        // ID_EQUIPOTORNEO
        $curVal = strval($this->ID_EQUIPOTORNEO->CurrentValue);
        if ($curVal != "") {
            $this->ID_EQUIPOTORNEO->ViewValue = $this->ID_EQUIPOTORNEO->lookupCacheOption($curVal);
            if ($this->ID_EQUIPOTORNEO->ViewValue === null) { // Lookup from database
                $filterWrk = "`ID_EQUIPO_TORNEO`" . SearchString("=", $curVal, DATATYPE_NUMBER, "");
                $sqlWrk = $this->ID_EQUIPOTORNEO->Lookup->getSql(false, $filterWrk, '', $this, true, true);
                $conn = Conn();
                $config = $conn->getConfiguration();
                $config->setResultCacheImpl($this->Cache);
                $rswrk = $conn->executeCacheQuery($sqlWrk, [], [], $this->CacheProfile)->fetchAll();
                $ari = count($rswrk);
                if ($ari > 0) { // Lookup values found
                    $arwrk = $this->ID_EQUIPOTORNEO->Lookup->renderViewRow($rswrk[0]);
                    $this->ID_EQUIPOTORNEO->ViewValue = $this->ID_EQUIPOTORNEO->displayValue($arwrk);
                } else {
                    $this->ID_EQUIPOTORNEO->ViewValue = FormatNumber($this->ID_EQUIPOTORNEO->CurrentValue, $this->ID_EQUIPOTORNEO->formatPattern());
                }
            }
        } else {
            $this->ID_EQUIPOTORNEO->ViewValue = null;
        }
        $this->ID_EQUIPOTORNEO->ViewCustomAttributes = "";

        // EQUIPO
        if ($this->EQUIPO->VirtualValue != "") {
            $this->EQUIPO->ViewValue = $this->EQUIPO->VirtualValue;
        } else {
            $curVal = strval($this->EQUIPO->CurrentValue);
            if ($curVal != "") {
                $this->EQUIPO->ViewValue = $this->EQUIPO->lookupCacheOption($curVal);
                if ($this->EQUIPO->ViewValue === null) { // Lookup from database
                    $filterWrk = "`NOM_EQUIPO_CORTO`" . SearchString("=", $curVal, DATATYPE_MEMO, "");
                    $sqlWrk = $this->EQUIPO->Lookup->getSql(false, $filterWrk, '', $this, true, true);
                    $conn = Conn();
                    $config = $conn->getConfiguration();
                    $config->setResultCacheImpl($this->Cache);
                    $rswrk = $conn->executeCacheQuery($sqlWrk, [], [], $this->CacheProfile)->fetchAll();
                    $ari = count($rswrk);
                    if ($ari > 0) { // Lookup values found
                        $arwrk = $this->EQUIPO->Lookup->renderViewRow($rswrk[0]);
                        $this->EQUIPO->ViewValue = $this->EQUIPO->displayValue($arwrk);
                    } else {
                        $this->EQUIPO->ViewValue = $this->EQUIPO->CurrentValue;
                    }
                }
            } else {
                $this->EQUIPO->ViewValue = null;
            }
        }
        $this->EQUIPO->ViewCustomAttributes = "";

        // GRUPO
        $curVal = strval($this->GRUPO->CurrentValue);
        if ($curVal != "") {
            $this->GRUPO->ViewValue = $this->GRUPO->lookupCacheOption($curVal);
            if ($this->GRUPO->ViewValue === null) { // Lookup from database
                $filterWrk = "`GRUPO`" . SearchString("=", $curVal, DATATYPE_MEMO, "");
                $sqlWrk = $this->GRUPO->Lookup->getSql(false, $filterWrk, '', $this, true, true);
                $conn = Conn();
                $config = $conn->getConfiguration();
                $config->setResultCacheImpl($this->Cache);
                $rswrk = $conn->executeCacheQuery($sqlWrk, [], [], $this->CacheProfile)->fetchAll();
                $ari = count($rswrk);
                if ($ari > 0) { // Lookup values found
                    $arwrk = $this->GRUPO->Lookup->renderViewRow($rswrk[0]);
                    $this->GRUPO->ViewValue = $this->GRUPO->displayValue($arwrk);
                } else {
                    $this->GRUPO->ViewValue = $this->GRUPO->CurrentValue;
                }
            }
        } else {
            $this->GRUPO->ViewValue = null;
        }
        $this->GRUPO->ViewCustomAttributes = "";

        // POSICION
        if (strval($this->POSICION->CurrentValue) != "") {
            $this->POSICION->ViewValue = $this->POSICION->optionCaption($this->POSICION->CurrentValue);
        } else {
            $this->POSICION->ViewValue = null;
        }
        $this->POSICION->ViewCustomAttributes = "";

        // NUMERACION
        if (strval($this->NUMERACION->CurrentValue) != "") {
            $this->NUMERACION->ViewValue = $this->NUMERACION->optionCaption($this->NUMERACION->CurrentValue);
        } else {
            $this->NUMERACION->ViewValue = null;
        }
        $this->NUMERACION->ViewCustomAttributes = "";

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

        // ID_PARTICIPANTE
        $curVal = strval($this->ID_PARTICIPANTE->CurrentValue);
        if ($curVal != "") {
            $this->ID_PARTICIPANTE->ViewValue = $this->ID_PARTICIPANTE->lookupCacheOption($curVal);
            if ($this->ID_PARTICIPANTE->ViewValue === null) { // Lookup from database
                $filterWrk = "`ID_PARTICIPANTE`" . SearchString("=", $curVal, DATATYPE_NUMBER, "");
                $sqlWrk = $this->ID_PARTICIPANTE->Lookup->getSql(false, $filterWrk, '', $this, true, true);
                $conn = Conn();
                $config = $conn->getConfiguration();
                $config->setResultCacheImpl($this->Cache);
                $rswrk = $conn->executeCacheQuery($sqlWrk, [], [], $this->CacheProfile)->fetchAll();
                $ari = count($rswrk);
                if ($ari > 0) { // Lookup values found
                    $arwrk = $this->ID_PARTICIPANTE->Lookup->renderViewRow($rswrk[0]);
                    $this->ID_PARTICIPANTE->ViewValue = $this->ID_PARTICIPANTE->displayValue($arwrk);
                } else {
                    $this->ID_PARTICIPANTE->ViewValue = FormatNumber($this->ID_PARTICIPANTE->CurrentValue, $this->ID_PARTICIPANTE->formatPattern());
                }
            }
        } else {
            $this->ID_PARTICIPANTE->ViewValue = null;
        }
        $this->ID_PARTICIPANTE->ViewCustomAttributes = "";

        // ID_ENCUESTA
        $this->ID_ENCUESTA->LinkCustomAttributes = "";
        $this->ID_ENCUESTA->HrefValue = "";
        $this->ID_ENCUESTA->TooltipValue = "";

        // ID_EQUIPOTORNEO
        $this->ID_EQUIPOTORNEO->LinkCustomAttributes = "";
        $this->ID_EQUIPOTORNEO->HrefValue = "";
        $this->ID_EQUIPOTORNEO->TooltipValue = "";

        // EQUIPO
        $this->EQUIPO->LinkCustomAttributes = "";
        $this->EQUIPO->HrefValue = "";
        $this->EQUIPO->TooltipValue = "";

        // GRUPO
        $this->GRUPO->LinkCustomAttributes = "";
        $this->GRUPO->HrefValue = "";
        $this->GRUPO->TooltipValue = "";

        // POSICION
        $this->POSICION->LinkCustomAttributes = "";
        $this->POSICION->HrefValue = "";
        $this->POSICION->TooltipValue = "";

        // NUMERACION
        $this->NUMERACION->LinkCustomAttributes = "";
        $this->NUMERACION->HrefValue = "";
        $this->NUMERACION->TooltipValue = "";

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

        // ID_PARTICIPANTE
        $this->ID_PARTICIPANTE->LinkCustomAttributes = "";
        $this->ID_PARTICIPANTE->HrefValue = "";
        $this->ID_PARTICIPANTE->TooltipValue = "";

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

        // ID_ENCUESTA
        $this->ID_ENCUESTA->setupEditAttributes();
        $this->ID_ENCUESTA->EditCustomAttributes = "";
        $this->ID_ENCUESTA->EditValue = $this->ID_ENCUESTA->CurrentValue;
        $this->ID_ENCUESTA->ViewCustomAttributes = "";

        // ID_EQUIPOTORNEO
        $this->ID_EQUIPOTORNEO->setupEditAttributes();
        $this->ID_EQUIPOTORNEO->EditCustomAttributes = "";
        $this->ID_EQUIPOTORNEO->PlaceHolder = RemoveHtml($this->ID_EQUIPOTORNEO->caption());

        // EQUIPO
        $this->EQUIPO->setupEditAttributes();
        $this->EQUIPO->EditCustomAttributes = "";
        $this->EQUIPO->PlaceHolder = RemoveHtml($this->EQUIPO->caption());

        // GRUPO
        $this->GRUPO->setupEditAttributes();
        $this->GRUPO->EditCustomAttributes = "";
        $this->GRUPO->PlaceHolder = RemoveHtml($this->GRUPO->caption());

        // POSICION
        $this->POSICION->setupEditAttributes();
        $this->POSICION->EditCustomAttributes = "";
        $this->POSICION->EditValue = $this->POSICION->options(true);
        $this->POSICION->PlaceHolder = RemoveHtml($this->POSICION->caption());

        // NUMERACION
        $this->NUMERACION->setupEditAttributes();
        $this->NUMERACION->EditCustomAttributes = "";
        $this->NUMERACION->EditValue = $this->NUMERACION->options(true);
        $this->NUMERACION->PlaceHolder = RemoveHtml($this->NUMERACION->caption());

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

        // ID_PARTICIPANTE
        $this->ID_PARTICIPANTE->setupEditAttributes();
        $this->ID_PARTICIPANTE->EditCustomAttributes = "";
        $this->ID_PARTICIPANTE->PlaceHolder = RemoveHtml($this->ID_PARTICIPANTE->caption());

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
                    $doc->exportCaption($this->ID_ENCUESTA);
                    $doc->exportCaption($this->ID_EQUIPOTORNEO);
                    $doc->exportCaption($this->EQUIPO);
                    $doc->exportCaption($this->GRUPO);
                    $doc->exportCaption($this->POSICION);
                    $doc->exportCaption($this->NUMERACION);
                    $doc->exportCaption($this->crea_dato);
                    $doc->exportCaption($this->modifica_dato);
                    $doc->exportCaption($this->usuario_dato);
                    $doc->exportCaption($this->ID_PARTICIPANTE);
                } else {
                    $doc->exportCaption($this->ID_ENCUESTA);
                    $doc->exportCaption($this->ID_EQUIPOTORNEO);
                    $doc->exportCaption($this->GRUPO);
                    $doc->exportCaption($this->NUMERACION);
                    $doc->exportCaption($this->crea_dato);
                    $doc->exportCaption($this->modifica_dato);
                    $doc->exportCaption($this->ID_PARTICIPANTE);
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
                        $doc->exportField($this->ID_ENCUESTA);
                        $doc->exportField($this->ID_EQUIPOTORNEO);
                        $doc->exportField($this->EQUIPO);
                        $doc->exportField($this->GRUPO);
                        $doc->exportField($this->POSICION);
                        $doc->exportField($this->NUMERACION);
                        $doc->exportField($this->crea_dato);
                        $doc->exportField($this->modifica_dato);
                        $doc->exportField($this->usuario_dato);
                        $doc->exportField($this->ID_PARTICIPANTE);
                    } else {
                        $doc->exportField($this->ID_ENCUESTA);
                        $doc->exportField($this->ID_EQUIPOTORNEO);
                        $doc->exportField($this->GRUPO);
                        $doc->exportField($this->NUMERACION);
                        $doc->exportField($this->crea_dato);
                        $doc->exportField($this->modifica_dato);
                        $doc->exportField($this->ID_PARTICIPANTE);
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

    // Write Audit Trail start/end for grid update
    public function writeAuditTrailDummy($typ)
    {
        $table = 'pronosticador';
        $usr = CurrentUserName();
        WriteAuditLog($usr, $typ, $table, "", "", "", "");
    }

    // Write Audit Trail (add page)
    public function writeAuditTrailOnAdd(&$rs)
    {
        global $Language;
        if (!$this->AuditTrailOnAdd) {
            return;
        }
        $table = 'pronosticador';

        // Get key value
        $key = "";
        if ($key != "") {
            $key .= Config("COMPOSITE_KEY_SEPARATOR");
        }
        $key .= $rs['ID_ENCUESTA'];

        // Write Audit Trail
        $usr = CurrentUserName();
        foreach (array_keys($rs) as $fldname) {
            if (array_key_exists($fldname, $this->Fields) && $this->Fields[$fldname]->DataType != DATATYPE_BLOB) { // Ignore BLOB fields
                if ($this->Fields[$fldname]->HtmlTag == "PASSWORD") {
                    $newvalue = $Language->phrase("PasswordMask"); // Password Field
                } elseif ($this->Fields[$fldname]->DataType == DATATYPE_MEMO) {
                    if (Config("AUDIT_TRAIL_TO_DATABASE")) {
                        $newvalue = $rs[$fldname];
                    } else {
                        $newvalue = "[MEMO]"; // Memo Field
                    }
                } elseif ($this->Fields[$fldname]->DataType == DATATYPE_XML) {
                    $newvalue = "[XML]"; // XML Field
                } else {
                    $newvalue = $rs[$fldname];
                }
                WriteAuditLog($usr, "A", $table, $fldname, $key, "", $newvalue);
            }
        }
    }

    // Write Audit Trail (edit page)
    public function writeAuditTrailOnEdit(&$rsold, &$rsnew)
    {
        global $Language;
        if (!$this->AuditTrailOnEdit) {
            return;
        }
        $table = 'pronosticador';

        // Get key value
        $key = "";
        if ($key != "") {
            $key .= Config("COMPOSITE_KEY_SEPARATOR");
        }
        $key .= $rsold['ID_ENCUESTA'];

        // Write Audit Trail
        $usr = CurrentUserName();
        foreach (array_keys($rsnew) as $fldname) {
            if (array_key_exists($fldname, $this->Fields) && array_key_exists($fldname, $rsold) && $this->Fields[$fldname]->DataType != DATATYPE_BLOB) { // Ignore BLOB fields
                if ($this->Fields[$fldname]->DataType == DATATYPE_DATE) { // DateTime field
                    $modified = (FormatDateTime($rsold[$fldname], 0) != FormatDateTime($rsnew[$fldname], 0));
                } else {
                    $modified = !CompareValue($rsold[$fldname], $rsnew[$fldname]);
                }
                if ($modified) {
                    if ($this->Fields[$fldname]->HtmlTag == "PASSWORD") { // Password Field
                        $oldvalue = $Language->phrase("PasswordMask");
                        $newvalue = $Language->phrase("PasswordMask");
                    } elseif ($this->Fields[$fldname]->DataType == DATATYPE_MEMO) { // Memo field
                        if (Config("AUDIT_TRAIL_TO_DATABASE")) {
                            $oldvalue = $rsold[$fldname];
                            $newvalue = $rsnew[$fldname];
                        } else {
                            $oldvalue = "[MEMO]";
                            $newvalue = "[MEMO]";
                        }
                    } elseif ($this->Fields[$fldname]->DataType == DATATYPE_XML) { // XML field
                        $oldvalue = "[XML]";
                        $newvalue = "[XML]";
                    } else {
                        $oldvalue = $rsold[$fldname];
                        $newvalue = $rsnew[$fldname];
                    }
                    WriteAuditLog($usr, "U", $table, $fldname, $key, $oldvalue, $newvalue);
                }
            }
        }
    }

    // Write Audit Trail (delete page)
    public function writeAuditTrailOnDelete(&$rs)
    {
        global $Language;
        if (!$this->AuditTrailOnDelete) {
            return;
        }
        $table = 'pronosticador';

        // Get key value
        $key = "";
        if ($key != "") {
            $key .= Config("COMPOSITE_KEY_SEPARATOR");
        }
        $key .= $rs['ID_ENCUESTA'];

        // Write Audit Trail
        $curUser = CurrentUserName();
        foreach (array_keys($rs) as $fldname) {
            if (array_key_exists($fldname, $this->Fields) && $this->Fields[$fldname]->DataType != DATATYPE_BLOB) { // Ignore BLOB fields
                if ($this->Fields[$fldname]->HtmlTag == "PASSWORD") {
                    $oldvalue = $Language->phrase("PasswordMask"); // Password Field
                } elseif ($this->Fields[$fldname]->DataType == DATATYPE_MEMO) {
                    if (Config("AUDIT_TRAIL_TO_DATABASE")) {
                        $oldvalue = $rs[$fldname];
                    } else {
                        $oldvalue = "[MEMO]"; // Memo field
                    }
                } elseif ($this->Fields[$fldname]->DataType == DATATYPE_XML) {
                    $oldvalue = "[XML]"; // XML field
                } else {
                    $oldvalue = $rs[$fldname];
                }
                WriteAuditLog($curUser, "D", $table, $fldname, $key, $oldvalue, "");
            }
        }
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
        //Log("Row Inserted");
    }

    // Row Updating event
    public function rowUpdating($rsold, &$rsnew)
    {
      //  return true;
    }

    // Row Updated event
    public function rowUpdated($rsold, &$rsnew)
    {
        //Log("Row Updated");
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
