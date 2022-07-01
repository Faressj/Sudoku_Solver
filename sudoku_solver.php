<?php
$taille = $argv[1];
$longueur = $taille*$taille;

function main ( $argv ) { // Main
    $map = $argv[2];

    $map = file_get_contents($map);
    if (str_contains($map, "0")) { // Si des 0 déjà dans la map
        echo "Grille impossible à résoudre" . PHP_EOL;
        return False;
    }

    $grid = transformation($map);
    if ($grid == False) {
        echo "Grille impossible à résoudre" . PHP_EOL;
        return;
    }

    $resolvable = initialgridvalid($grid);
    if ($resolvable == False) {
        echo "Grille impossible à résoudre" . PHP_EOL;
        return;
    }

    solve($grid);
    return;
}

function initialgridvalid ( $grid ) { // Si grille initial invalide
    global $longueur;
    
    if ($longueur != count($grid)) { // Si premier argument invalide
        return False;
    }
    
    for ($r = 0; $r < $longueur; $r++) {
        for ($c = 0; $c < $longueur; $c++) {
            $k = $grid[$r][$c];
                for ($i=0; $i<$longueur; $i++) { //CHECK LIGNE
                    if ($i !== $c && $grid[$r][$i] == $k && $k != 0) {
                        return False;
                    }
                }
                for ($i=0; $i<$longueur; $i++) { // CHECK COLONNE
                    if ($i !== $r && $grid[$i][$c] == $k && $k != 0) {
                        return False;
                    }
                }
        }
    }
    for ($r = 0; $r < $longueur; $r++) {// Si valeur invalide dans une case
        for ($c = 0; $c < $longueur; $c++) {
            $k = $grid[$r][$c];
            if ($k > $longueur || $k < 0) {
                return False;
            }
        }
    }
    return True;
}

function transformation ($map) { //Str into array
    global $longueur;
    global $taille;

    if ($taille == 1) { // Cas particulier pour Carré de 1
        $map = str_replace(".", 1, $map);
        $maparr = explode(PHP_EOL, $map);
    }
    
    $map = str_replace(".", 0, $map);
    $maparr = explode(PHP_EOL, $map);
    
    for ($i = 0; $i < count($maparr); $i++) { // Ligne contenant trop de caracteres
        
        if (strlen($maparr[$i]) != $longueur && strlen($maparr[$i]) != 0 ) {
            return False;
        }
        if ($maparr[$i] == PHP_EOL || $maparr[$i] == null) {
            unset($maparr[$i]);
            $i-=1;
        }
    }
    
    for ($i = 0; $i < count($maparr); $i++) {
        $maparr[$i] = str_split($maparr[$i]);
    }
    return $maparr;
}

function untransformation ( $grid ) { //Final result into str
    for ($i = 0; $i < count($grid); $i++) {
        $maparr[$i] = implode($grid[$i]);
    }
    for ($i = 0; $i < count($grid); $i++) {
        echo $maparr[$i] . PHP_EOL;
    }
    return $maparr;
}


function is_valid ( $grid, $r, $c, $k) { // Check si valeur valide
    global $longueur;
    global $taille;

    for ($i=0; $i<$longueur; $i++) {//Check Ligne
        if($grid[$r][$i] == $k){
            return False;
        }
    }
    for ($i=0; $i<$longueur; $i++) {//Check Colonne
        if ($grid[$i][$c] == $k) {
            return False;
        }
    }

    for ($i = floor(($r/$taille))*$taille; $i <= floor(($r/$taille))*$taille+($taille-1); $i++) { // Check Box
        for ($j = floor(($c/$taille))*$taille; $j <= floor(($c/$taille))*$taille+($taille-1); $j++) {
            if ($i != $r && $j != $c && $grid[$i][$j] != 0) {
                if ($grid[$i][$j] == $k) {
                    return False;
                }
            }
        }
    }
    return True;
}

function solve (&$grid, $r=0, $c=0) { // Backtracking
    
    global $longueur;
    if ($r == $longueur) {
        untransformation($grid);
        return True;
    } elseif ($c == $longueur) {
        return solve($grid, $r+1, 0);
    } elseif ($grid[$r][$c] != 0) {
        return solve($grid, $r, $c+1);
    } else {
        for($k = 1; $k<$longueur+1; $k++){
            if (is_valid($grid, $r, $c, $k)) {
                $grid[$r][$c] = $k;
                if (solve($grid, $r, $c+1)) {
                    return True;
                }
                $grid[$r][$c] = 0;
            }
        }
        return False;
    }
}

main($argv);