<?php

namespace PHPMaker2022\project11;

// Menu Language
if ($Language && function_exists(PROJECT_NAMESPACE . "Config") && $Language->LanguageFolder == Config("LANGUAGE_FOLDER")) {
    $MenuRelativePath = "";
    $MenuLanguage = &$Language;
} else { // Compat reports
    $LANGUAGE_FOLDER = "../lang/";
    $MenuRelativePath = "../";
    $MenuLanguage = Container("language");
}

// Navbar menu
$topMenu = new Menu("navbar", true, true);
$topMenu->addMenuItem(7, "mi_torneo", $MenuLanguage->MenuPhrase("7", "MenuText"), $MenuRelativePath . "torneolist", -1, "", IsLoggedIn() || AllowListMenu('{1FEE5CED-11BB-4991-94A0-946354AE0202}torneo'), false, false, "", "", true, false);
$topMenu->addMenuItem(3, "mi_equipo", $MenuLanguage->MenuPhrase("3", "MenuText"), $MenuRelativePath . "equipolist", -1, "", IsLoggedIn() || AllowListMenu('{1FEE5CED-11BB-4991-94A0-946354AE0202}equipo'), false, false, "", "", true, false);
$topMenu->addMenuItem(4, "mi_equipotorneo", $MenuLanguage->MenuPhrase("4", "MenuText"), $MenuRelativePath . "equipotorneolist", -1, "", IsLoggedIn() || AllowListMenu('{1FEE5CED-11BB-4991-94A0-946354AE0202}equipotorneo'), false, false, "", "", true, false);
$topMenu->addMenuItem(6, "mi_partidos", $MenuLanguage->MenuPhrase("6", "MenuText"), $MenuRelativePath . "partidoslist", -1, "", IsLoggedIn() || AllowListMenu('{1FEE5CED-11BB-4991-94A0-946354AE0202}partidos'), false, false, "", "", true, false);
$topMenu->addMenuItem(9, "mi_estadio", $MenuLanguage->MenuPhrase("9", "MenuText"), $MenuRelativePath . "estadiolist", -1, "", IsLoggedIn() || AllowListMenu('{1FEE5CED-11BB-4991-94A0-946354AE0202}estadio'), false, false, "", "", true, false);
$topMenu->addMenuItem(5, "mi_participante", $MenuLanguage->MenuPhrase("5", "MenuText"), $MenuRelativePath . "participantelist", -1, "", IsLoggedIn() || AllowListMenu('{1FEE5CED-11BB-4991-94A0-946354AE0202}participante'), false, false, "", "", true, false);
$topMenu->addMenuItem(2, "mi_pronosticador", $MenuLanguage->MenuPhrase("2", "MenuText"), $MenuRelativePath . "pronosticadorlist", -1, "", IsLoggedIn() || AllowListMenu('{1FEE5CED-11BB-4991-94A0-946354AE0202}pronosticador'), false, false, "", "", true, false);
echo $topMenu->toScript();

// Sidebar menu
$sideMenu = new Menu("menu", true, false);
$sideMenu->addMenuItem(7, "mi_torneo", $MenuLanguage->MenuPhrase("7", "MenuText"), $MenuRelativePath . "torneolist", -1, "", IsLoggedIn() || AllowListMenu('{1FEE5CED-11BB-4991-94A0-946354AE0202}torneo'), false, false, "", "", true, true);
$sideMenu->addMenuItem(3, "mi_equipo", $MenuLanguage->MenuPhrase("3", "MenuText"), $MenuRelativePath . "equipolist", -1, "", IsLoggedIn() || AllowListMenu('{1FEE5CED-11BB-4991-94A0-946354AE0202}equipo'), false, false, "", "", true, true);
$sideMenu->addMenuItem(4, "mi_equipotorneo", $MenuLanguage->MenuPhrase("4", "MenuText"), $MenuRelativePath . "equipotorneolist", -1, "", IsLoggedIn() || AllowListMenu('{1FEE5CED-11BB-4991-94A0-946354AE0202}equipotorneo'), false, false, "", "", true, true);
$sideMenu->addMenuItem(6, "mi_partidos", $MenuLanguage->MenuPhrase("6", "MenuText"), $MenuRelativePath . "partidoslist", -1, "", IsLoggedIn() || AllowListMenu('{1FEE5CED-11BB-4991-94A0-946354AE0202}partidos'), false, false, "", "", true, true);
$sideMenu->addMenuItem(9, "mi_estadio", $MenuLanguage->MenuPhrase("9", "MenuText"), $MenuRelativePath . "estadiolist", -1, "", IsLoggedIn() || AllowListMenu('{1FEE5CED-11BB-4991-94A0-946354AE0202}estadio'), false, false, "", "", true, true);
$sideMenu->addMenuItem(5, "mi_participante", $MenuLanguage->MenuPhrase("5", "MenuText"), $MenuRelativePath . "participantelist", -1, "", IsLoggedIn() || AllowListMenu('{1FEE5CED-11BB-4991-94A0-946354AE0202}participante'), false, false, "", "", true, true);
$sideMenu->addMenuItem(2, "mi_pronosticador", $MenuLanguage->MenuPhrase("2", "MenuText"), $MenuRelativePath . "pronosticadorlist", -1, "", IsLoggedIn() || AllowListMenu('{1FEE5CED-11BB-4991-94A0-946354AE0202}pronosticador'), false, false, "", "", true, true);
echo $sideMenu->toScript();
